<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		if (!is_logged_in()) {
			redirect('login?_rdr=' . urlencode(current_url()));
		}
		$this->load->model('profil_perusahaan_model', 'dsh');
	}
	public function index()
	{
		$this->url('dashboard');
	}

	public function url($segmen = null)
	{
		$logses = $this->session->level;

		$leveladmin 	= array(
			'profil_perusahaan', 'karyawan',
			'wilayah', 'status', 'tarif', 'pelanggan', 'jenis_gangguan', 'paket',
			'perbaikan_gangguan', 'kwitansi',
			'appsettings', 'dashboard', 'kolektor', 'pemutusan', 'laporan', 'quicklink',
			'master_setoran', 'pengeluaran', 'users_api', 'backup', 'settings', 'images',
			'wa_notif','pembayaran','odp',
		);

		$levelkolektor	= array('pembayaran','pelanggan');

		$levelteknisi	= array('pelanggan');
		// $levelteknisi	= array('perbaikan', 'jenis_gangguan', 'pengaduan', 'perbaikan_gangguan');

		if ($logses == 'administrator') {
			if (in_array($segmen, $leveladmin)) {
				$this->_create_view($segmen, $folder = 'admin');
			} else {
				set_status_header(404);
			}
		} elseif ($logses == 'kolektor') {
			if (in_array($segmen, $levelkolektor)) {
				$this->_create_view($segmen, $folder = 'admin');
			} else {
				set_status_header(404);
			}
		} elseif ($logses == 'teknisi') {
			if (in_array($segmen, $levelteknisi)) {
				$this->_create_view($segmen, $folder = 'admin');
			} else {
				set_status_header(404);
			}
		} else {
			redirect(site_url('logout'));
		}
	}

	private function _create_view($segmen = null, $folder = null)
	{
		if ($segmen != null && $folder != null) {
			$data['profilP'] = $this->dsh->get_by_id(1);
			$data['active'] = $page = $segmen;
			$this->load->view("$folder/templates/header", $data);
			$this->load->view("$folder/templates/navigation");
			$this->load->view("$folder/$page/$page");
			$this->load->view("$folder/templates/footer");
			$this->load->view("$folder/$page/js_$page");
		} else {
			set_status_header(404);
		}
	}

	// private function _autentikasi()
	// {
	// 	if ($this->session->level=='administrator' || $this->session->level=='kolektor' || $this->session->level=='teknisi') {
	// 		$this->url('dashboard');
	// 	} else {
	// 		redirect(site_url('login'));
	// 		exit();
	// 	}
	// }


}
