<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

class Approval_advance_m extends CI_Model {
	public function getAdvAll()
	{
		$sql="SELECT * from master_advance ma left join master_karyawan mk on ma.id_kyw = mk.id_kyw";
		$query=$this->db->query($sql);
		return $query->result();
	}
	public function getDescAdv($idAdv)
	{
		$sql="select * from master_advance ma left join master_karyawan mk on ma.id_kyw = mk.id_kyw 
		left join master_dept md on mk.dept_kyw = md.id_dept where ma.id_advance = ".$idAdv."";
		$query=$this->db->query($sql);
		return $query->result();
	}
	function updateAdvance($data,$advId){
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
}
/* End of file sec_menu_user_m.php */
/* Location: ./application/models/sec_menu_user.php */