<?php
if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Approval_advance extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('home_m');
		$this->load->model('approval_advance_m');
		session_start ();
	}
	
	public function index(){
		if($this->auth->is_logged_in () == false){
			$this->login();
		}else{
			$data['multilevel'] = $this->user_m->get_data(0,$this->session->userdata('usergroup'));			
			$this->template->set ( 'title', 'Home' );
			$this->template->load ( 'template/template1', 'global/index',$data );
		}
	}
	
	function home(){
		if($this->auth->is_logged_in () == false){
			$this->login();
		}else{
			$menuId = $this->home_m->get_menu_id('approval_advance/home');
			$data['menu_id'] = $menuId[0]->menu_id;
			$data['menu_parent'] = $menuId[0]->parent;
			$data['menu_nama'] = $menuId[0]->menu_nama;
			$this->auth->restrict ($data['menu_id']);
			$this->auth->cek_menu ($data['menu_id']);
        	
			$data['multilevel'] = $this->user_m->get_data(0,$this->session->userdata('usergroup'));
			$data['menu_all'] = $this->user_m->get_menu_all(0);
			
			$this->template->set ( 'title', $data['menu_nama'] );
			$this->template->load ( 'template/template3', 'approval/approval_advance_v',$data );
		}
	}
	
    function approval(){
        $id	 		= trim($this->input->post('id'));
		$user 		= $this->session->userdata('id_user');
        $flag		= trim($this->input->post('flag'));
		$keterangan = trim($this->input->post('keterangan'));
        $tanggal 	= $this->session->userdata('tgl_y');
		
        $hak 	= $this->session->userdata('usergroup_desc');
		
		if($hak == 'Head Director'){
			$data = array(
            'id_advance'	=> $id,
            'app_hd_id'		=> $user,
            'app_hd_status'	=> $flag,
			'app_hd_tgl'	=> $tanggal,
			'app_hd_ket'	=> $keterangan
			);
		}elseif($hak == 'General Manager'){
			$data = array(
            'id_advance'	=> $id,
            'app_gm_id'		=> $user,
            'app_gm_status'	=> $flag,
			'app_gm_tgl'	=> $tanggal,
			'app_gm_ket'	=> $keterangan
			);	
		}else{
			$data = array(
            'id_advance'			=> $id,
            'app_keuangan_id'		=> $user,
            'app_keuangan_status'	=> $flag,
			'app_keuangan_tgl'		=> $tanggal,
			'app_keuangan_ket'		=> $keterangan
			);
		}
		
        $model = $this->approval_advance_m->updateAdvance($data,$id);
        if($model){
        	$array = array(
        			'act'	=>1,
        			'notif' =>'<div class="Metronic-alerts alert alert-success fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>Data berhasil disimpan.</div>'
        	);
        }else{
        	$array = array(
        			'act'	=>0,
        			'notif' =>'<div class="Metronic-alerts alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>Data gagal disimpan.</div>'
        	);
        }
        $this->output->set_output(json_encode($array));
    }
	
	public function getAllAdv(){
		$this->CI =& get_instance();
		$rows = $this->approval_advance_m->getAdvAll();
		$data['data'] = array();
		foreach( $rows as $row ){
			$query = $this->db->query("select nama_dept as nama from master_dept where id_dept=".$row->dept_kyw."");
			$g = $query->row()->nama;
			
			$array = array(
					'idAdv' 	=> trim($row->id_advance),
					'namaKyw' 	=> trim($row->nama_kyw),
					'deptKyw' 	=> trim($g),
					'jumlah' 	=> trim(number_format($row->jml_uang, 2, ',', '.')),
					'namaAcc' 	=> trim($row->nama_akun_bank),
					'namaBank' 	=> trim($row->nama_bank)
			);
			array_push($data['data'],$array);
		}
		$this->output->set_output(json_encode($data));
	}
}

/* End of file sec_user.php */
/* Location: ./application/controllers/sec_user.php */