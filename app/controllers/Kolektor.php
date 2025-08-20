<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Kolektor extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		if (!is_logged_in()) {
			redirect('login?_rdr=' . urlencode(current_url()));
		}
		$this->load->model('kolektor_model', 'kolektor');
	}

	public function index()
	{
		set_status_header(401);
	}

	public function ajax_list()
	{
		$list = $this->kolektor->get_datatables();
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $br) {
			$no++;
			$row = array();
			$row[] = $no;
			$row[] = "<span class='font-bold'>$br->kode_karyawan</span>";
			$row[] = "<span class='font-bold'>$br->nama_lengkap</span>";
			// Wilayah
			if ($br->wilayah != null || $br->wilayah != '') {
				$wilayah = json_decode($br->wilayah);
				$arrLenght = count($wilayah);
				$strWilayah = '';
				for ($w = 0; $w < $arrLenght; $w++) {
					$q = $this->kolektor->getWilayah($wilayah[$w]);
					$strWilayah .= '<span class="badge badge-primary">' . $q->wilayah . '</span> ';
				}
			} else {
				$strWilayah = '';
			}
			$row[] = $strWilayah;
			$row[] = $br->keterangan;
			//add html for action
			// "<div class=\"btn-group\"><a class=\"btn btn-xs btn-outline btn-primary\" href=\"javascript:void(0)\" onclick=\"views('$br->id_kolektor')\" title=\"Lihat Detail\"><i class=\"glyphicon glyphicon-eye-open\"></i> Lihat</a>
			$row[] = "<div class=\"btn-group\"><a class=\"btn btn-xs btn-outline btn-warning\" href=\"javascript:void(0)\" onclick=\"edits('$br->id_kolektor')\" title=\"Edit\"><i class=\"glyphicon glyphicon-pencil\"></i> Edit</a>
				<a class=\"btn btn-xs btn-outline btn-danger\" href=\"javascript:void(0)\" onclick=\"deletes('$br->id_kolektor')\" title=\"Hapus\" ><i class=\"glyphicon glyphicon-trash\"></i> Delete</a></div>";
			$data[] = $row;
		}
		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->kolektor->count_all(),
			"recordsFiltered" => $this->kolektor->count_filtered(),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}

	public function save_kolektor()
	{
		// $this->_validate();
		$data = array(
			'id_karyawan' => $this->input->post('id_karyawan'),
			'wilayah' => json_encode($this->input->post('wilayah[]')),
			'keterangan' => $this->input->post('keterangan'),
		);
		$insert = $this->kolektor->save($data);
		echo json_encode(array("status" => TRUE));
	}

	public function update_kolektor()
	{
		$this->_validate();
		$data = array(
			'id_karyawan' => $this->input->post('id_karyawan'),
			'wilayah' => json_encode($this->input->post('wilayah[]')),
			'keterangan' => $this->input->post('keterangan'),
		);
		$this->kolektor->update(array('id_kolektor' => $this->input->post('id_kolektor')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function delete_kolektor($id_kolektor)
	{
		$this->kolektor->delete_by_id($id_kolektor);
		echo json_encode(array("status" => TRUE));
	}

	public function get_edit($id_kolektor = FALSE)
	{
		$data = $this->kolektor->get_by_id($id_kolektor);
		echo json_encode($data);
	}

	public function vget_edit($id_kolektor = FALSE)
	{
		$q = $this->kolektor->v_get_by_id($id_kolektor);
		$data = array(
			'id_kolektor' => $q->id_kolektor,
			'id_karyawan' => $q->id_karyawan,
			'wilayah' => json_decode($q->wilayah),
		);
		echo json_encode($data);
	}

	private function _validate()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;
		//id tidak divalidasi karena auto_increment
		// validasi untuk admin
		if ($this->input->post('keterangan') == '') {
			$data['inputerror'][] = 'keterangan';
			$data['error_string'][] = 'Enter this field!';
			$data['status'] = FALSE;
		}

		if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}

	// public function hashing($value='')
	// {
	// 	password_hash('','');
	// 	password_verify('','');
	// }

}
