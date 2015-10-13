<?php
if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Master_karyawan extends CI_Controller
{
	function __construct()
	{
		parent::__construct();

		$this->load->model('home_m');
		$this->load->model('master_karyawan_m');
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
		$menuId = $this->home_m->get_menu_id('master_karyawan/home');
		$data['menu_id'] = $menuId[0]->menu_id;
		$data['menu_parent'] = $menuId[0]->parent;
		$data['menu_nama'] = $menuId[0]->menu_nama;
		$this->auth->restrict ($data['menu_id']);
		$this->auth->cek_menu ( $data['menu_id'] );
        $data['dept'] = $this->master_karyawan_m->get_dept();
       
		if(isset($_POST["btnSimpan"])){
			$this->simpan();
		}elseif(isset($_POST["btnUbah"])){
			$this->ubah();
		}elseif(isset($_POST["btnHapus"])){
			$this->hapus();
		}else{
			$data['multilevel'] = $this->user_m->get_data(0,$this->session->userdata('usergroup'));
			$data['menu_all'] = $this->user_m->get_menu_all(0);
			
			$this->template->set ( 'title', $data['menu_nama'] );
			$this->template->load ( 'template/template3', 'master/master_karyawan_v',$data );
		}
	}
	public function getKywAll(){
		$this->CI =& get_instance();//and a.kcab_id<>'1100'
		$rows = $this->master_karyawan_m->getKywAll();
		$data['data'] = array();
		foreach( $rows as $row ) {
			$array = array(
					'idKyw' => trim($row->id_kyw),
					'namaKyw' => trim($row->nama_kyw),
					'deptKyw' =>  trim($row->dept_kyw)				
			);
	
			array_push($data['data'],$array);
		}
		$this->output->set_output(json_encode($data));
	}
    function simpan(){
        $namaKyw			= trim($this->input->post('namaKyw'));
        $deptKyw		= trim($this->input->post('deptKyw'));
       
        $modelidKyw = $this->master_karyawan_m->getIdKyw();
        $data = array(
            'id_kyw'		      		=>$modelidKyw,
            'nama_kyw'		        	=>$namaKyw,
            'dept_kyw'		        	=>$deptKyw
        );
        $model = $this->master_karyawan_m->insertKyw($data);
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
    function ubah(){
    	$kywId			= trim($this->input->post('kywId'));
    	$namaKyw			= trim($this->input->post('namaKyw'));
        $deptKyw		= trim($this->input->post('deptKyw'));
    	 
    	$data = array(
            'nama_kyw'		        	=>$namaKyw,
            'dept_kyw'		        	=>$deptKyw
        );
    	
    	$model = $this->master_karyawan_m->updateKyw($data,$kywId);
    	if($model){
    		$array = array(
    				'act'	=>1,
    				'notif' =>'<div class="Metronic-alerts alert alert-success fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>Data berhasil diubah.</div>'
    		);
    	}else{
    		$array = array(
    				'act'	=>0,
    				'notif' =>'<div class="Metronic-alerts alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>Data gagal diubah.</div>'
    		);
    	}
    	$this->output->set_output(json_encode($array));
    }
    function hapus(){
    	$this->CI =& get_instance();
    	$kywId			= trim($this->input->post('idKyw'));
    	$model = $this->master_karyawan_m->deleteKyw( $kywId);
    	if($model){
    		$array = array(
    				'act'	=>1,
    				'notif' =>'<div class="Metronic-alerts alert alert-success fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>Data berhasil dihapus.</div>'
    		);
    	}else{
    		$array = array(
    				'act'	=>0,
    				'notif' =>'<div class="Metronic-alerts alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>Data gagal dihapus.</div>'
    		);
    	}
    	$this->output->set_output(json_encode($array));
    }
	

}

/* End of file sec_user.php */
/* Location: ./application/controllers/sec_user.php */