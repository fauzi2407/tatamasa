<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

class Master_advance_m extends CI_Model {
	public function getProyek() {
		$rows 		=	array(); //will hold all results
		$sql		=	"select * from master_proyek order by id_proyek asc ";
		$query		=	$this->db->query($sql);
		foreach($query->result_array() as $row){
			$rows[] = $row; //add the fetched result to the result array;
		}
		return $rows; // returning rows, not row
	}
    public function getKurs() {
		$rows 		=	array(); //will hold all results
		$sql		=	"select * from master_kurs order by id_kurs asc ";
		$query		=	$this->db->query($sql);
		foreach($query->result_array() as $row){
			$rows[] = $row; //add the fetched result to the result array;
		}
		return $rows; // returning rows, not row
	}
	public function getAdvAll()
	{
		$sql="SELECT ma.id_advance,mk.nama_kyw, ma.jml_uang from master_advance ma left join master_karyawan mk on ma.id_kyw = mk.id_kyw";
		$query=$this->db->query($sql);
		return $query->result(); // returning rows, not row
	}
	public function getKywAll()
	{
		$sql="SELECT mk.id_kyw,mk.nama_kyw,md.nama_dept from master_karyawan mk left join master_dept md on mk.dept_kyw = md.id_dept";
		$query=$this->db->query($sql);
		return $query->result(); // returning rows, not row
	}
    public function getDescKurs($idKurs)
	{
		$this->db->select ( 'nilai_kurs' );
		$this->db->from('master_kurs mk');
		$this->db->where ( 'mk.id_kurs', $idKurs );
		$query = $this->db->get ();
		if($query->num_rows()== '1'){
			return $query->result ();
		}else{
			return false;
		}	
	}
	public function getDescAdv($idAdv)
	{
		$this->db->select ( 'ma.id_kyw, mk.nama_kyw, md.nama_dept, ma.jml_uang, ma.id_proyek, ma.id_kurs, ma.nilai_kurs, ma.tgl_trans, ma.tgl_jt, ma.pay_to, ma.nama_akun_bank, ma.no_akun_bank, ma.nama_bank, ma.keterangan, ma.dok_po, ma.dok_sp, ma.dok_ssp, ma.dok_sspk, ma.dok_sbj, ma.app_keuangan_id, ma.app_hd_id, ma.app_gm_id, ma.app_keuangan_status, ma.app_hd_status, ma.app_gm_status, ma.app_keuangan_tgl, ma.app_hd_tgl, ma.app_gm_tgl, ma.app_keuangan_ket, ma.app_hd_ket, ma.app_gm_ket' );
		$this->db->from('master_advance ma');
		$this->db->join('master_karyawan mk', 'ma.id_kyw=mk.id_kyw', 'LEFT');
		$this->db->join('master_dept md', 'mk.dept_kyw=md.id_dept', 'LEFT');
		$this->db->where ( 'ma.id_advance', $idAdv );
//		$this->db->where ( 'T.STATUS_AKTIF <>', 3 );
		$query = $this->db->get ();
		if($query->num_rows()== '1'){
			return $query->result ();
		}else{
			return false;
		}	
	}
    public function getDescCpa($idAdv)
	{
		$this->db->select ( 'ma.id_advance' );
		$this->db->from('master_advance ma');
		$this->db->join('cpa cp', 'ma.id_advance=cp.id_master', 'LEFT');
		$this->db->where ( 'ma.id_advance', $idAdv );
//		$this->db->where ( 'T.STATUS_AKTIF <>', 3 );
		$query = $this->db->get ();
		if($query->num_rows()> '1'){
			return $query->result ();
            
		}else{
			return false;
		}	
	}
	public function getIdAdv($bulan,$tahun){
		$sql= "select id_advance from master_advance where MONTH(tgl_trans)='$bulan' and YEAR(tgl_trans)='$tahun'";
		$query = $this->db->query($sql);
		$jml = $query->num_rows();
        $kode = "AV";
        $th = substr($tahun,-2);
		if($jml == 0){
			$id_adv = "0000001";
			return $kode."-".$id_adv."-".$bulan.$th;
		}else{
			$sql= "select max(substring(id_advance,4,7)) as id_adv from master_advance";
			$query = $this->db->query($sql);
			$hasil = $query->result();
			$id_adv =  $hasil[0]->id_adv;
			$id_adv = sprintf('%07u',$id_adv+1);
			return $kode."-".$id_adv."-".$bulan.$th;
		}
	}
	public function insertAdv($data){
		
		$this->db->trans_begin();
		$model = $this->db->insert('master_advance', $data);
		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			return false;
		}
		else{
			$this->db->trans_commit();
			return true;
		}
	
	}
    function insertCpa($data){
        $this->db->trans_begin();
		$model = $this->db->insert('cpa', $data);
		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			return false;
		}
		else{
			$this->db->trans_commit();
			return true;
		}
    }
	function updateAdv($data,$advId){
		$this->db->trans_begin();
		$query1 = $this->db->where('id_advance', $advId);
		$query2 = $this->db->update('master_advance', $data);
		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			return false;
		}
		else{
			$this->db->trans_commit();
			return true;
		}
	}
	function deleteAdv($advId){
		$this->db->trans_begin();
		$query1	=	$this->db->where('id_advance',$advId);
		$query2	=   $this->db->delete('master_advance');
		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			return false;
		}
		else{
			$this->db->trans_commit();
			return true;
		}
	}
	
	function cetak_cpa($idAdv){
		$sql="select a.*,b.nama_proyek,c.nama_kyw,d.nama_dept, 
			(select e.userfullname from sec_passwd e where e.userid = a.app_keuangan_id) as financeName,
			(select e.userfullname from sec_passwd e where e.userid = a.app_hd_id) as hdName,
			(select e.userfullname from sec_passwd e where e.userid = a.app_gm_id) as gmName,
			(
			CASE 
			 WHEN app_keuangan_status = '1' THEN 'Approve'
			 WHEN app_keuangan_status = '2' THEN 'Reject'
			 WHEN app_keuangan_status = '3' THEN 'Paid'
			END) AS statusKeuangan,
			(
			CASE 
			 WHEN app_hd_status = '1' THEN 'Approve'
			 WHEN app_hd_status = '2' THEN 'Reject'
			 WHEN app_hd_status = '3' THEN 'Paid'
			END) AS statusHd,
			(
			CASE 
			 WHEN app_gm_status = '1' THEN 'Approve'
			 WHEN app_gm_status = '2' THEN 'Reject'
			 WHEN app_gm_status = '3' THEN 'Paid'
			END) AS statusGm 
			from master_advance a 
			left join master_proyek b on a.id_proyek = b.id_proyek
			left join master_karyawan c on a.id_kyw = c.id_kyw
			left join master_dept d on c.dept_kyw = d.id_dept
			where a.id_advance = '".$idAdv."'";
		$query=$this->db->query($sql);
		return $query->result(); // returning rows, not row
	}
	function cetak_cpa_detail($idAdv){
		$sql="select a.*,b.nama_perk, c.tahun,c.id_proyek,c.kode_perk, 
			  (c.jan+c.feb+c.mar+c.apr+c.mei+c.jun+c.jul+c.agu+c.sep+c.okt+c.nov+c.des) as anggaran,c.terpakai,c.sisa from cpa a 
			  left join perkiraan b on a.kode_perk=b.kode_perk 
			  left join budget_perkiraan c on a.kode_perk=c.kode_perk where a.id_master = '".$idAdv."'";
		$query=$this->db->query($sql);
		return $query->result(); // returning rows, not row
	}
}

/* End of file sec_menu_user_m.php */
/* Location: ./application/models/sec_menu_user.php */