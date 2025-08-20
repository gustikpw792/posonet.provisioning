<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Provisioning extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		if (!is_logged_in()) {
			redirect('login?_rdr=' . urlencode(current_url()));
		}
		$this->load->model('Provisioning_model', 'prov');
	}

	public function index()
	{
		set_status_header(401);
	}

	// bisa dihapus, cuma pakai tes
	public function get_tcont()
	{
		$data = $this->prov->getTcont();
		echo json_encode($data);
	}

	public function add_tcont()
	{

	}

	public function delete_tcont()
	{

	}
	
	public function ajax_list()
	{
		$list = $this->karyawan->get_datatables();
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $br) {
			$no++;
			$row = array();
			$row[] = $br->id_karyawan;
			$row[] = "<span class='font-bold'>$br->kode_karyawan</span>";
			$row[] = "<span class='font-bold'>$br->nama_lengkap</span>";
			// $row[] = $br->bagian;
			$row[] = $br->telp;
			$row[] = $sts = ($br->status == 'AKTIF') ? '<span class="label label-success">' . $br->status . '</span>' : '<span class="label label-danger">' . $br->status . '</span>';
			//add html for action
			$row[] = "<div class=\"btn-group\">
                            <button data-toggle=\"dropdown\" class=\"btn btn-default btn-xs dropdown-toggle\" aria-expanded=\"false\">Action <span class=\"caret\"></span></button>
                            <ul class=\"dropdown-menu\">
                                <li><a href=\"javascript:void(0)\" onclick=\"views('$br->id_karyawan')\"><i class=\"glyphicon glyphicon-eye-open\"></i> Lihat Detail</a></li>
                                <li><a href=\"javascript:void(0)\" onclick=\"edits('$br->id_karyawan')\"><i class=\"glyphicon glyphicon-pencil\"></i> Edit</a></li>
                                <li><a href=\"javascript:void(0)\" onclick=\"deletes('$br->id_karyawan')\"><i class=\"glyphicon glyphicon-trash\"></i> Hapus</a></li>
                            </ul>
                        </div>";
			$data[] = $row;
		}
		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->karyawan->count_all(),
			"recordsFiltered" => $this->karyawan->count_filtered(),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}

	public function save_karyawan()
	{
		$this->_validate();
		$data = array(
			'kode_karyawan' => $this->input->post('kode_karyawan'),
			'nama_lengkap' => $this->input->post('nama_karyawan'),
			'status' => $this->input->post('status'),
			'tgl_masuk' => $this->input->post('tgl_masuk'),
			'tgl_berakhir' => $this->input->post('tgl_berakhir'),
			'alamat' => $this->input->post('alamat'),
			'telp' => $this->input->post('telp'),
		);
		$insert = $this->karyawan->save($data);
		echo json_encode(array("status" => TRUE));
	}

	public function update_karyawan()
	{
		$this->_validate();
		$data = array(
			'kode_karyawan' => $this->input->post('kode_karyawan'),
			'nama_lengkap' => $this->input->post('nama_karyawan'),
			'status' => $this->input->post('status'),
			'tgl_masuk' => $this->input->post('tgl_masuk'),
			'tgl_berakhir' => $this->input->post('tgl_berakhir'),
			'alamat' => $this->input->post('alamat'),
			'telp' => $this->input->post('telp'),
		);
		$this->karyawan->update(array('id_karyawan' => $this->input->post('id_karyawan')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function delete_karyawan($id_karyawan)
	{
		$this->karyawan->delete_by_id($id_karyawan);
		echo json_encode(array("status" => TRUE));
	}

	public function get_edit($id_karyawan = FALSE)
	{
		$data = $this->karyawan->get_by_id($id_karyawan);
		echo json_encode($data);
	}

	private function _validate()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;
		//id tidak divalidasi karena auto_increment
		if ($this->input->post('kode_karyawan') == '') {
			$data['inputerror'][] = 'kode_karyawan';
			$data['error_string'][] = 'Enter this field!';
			$data['status'] = FALSE;
		}
		if ($this->input->post('nama_karyawan') == '') {
			$data['inputerror'][] = 'nama_karyawan';
			$data['error_string'][] = 'Enter this field!';
			$data['status'] = FALSE;
		}
		if ($this->input->post('status') == '') {
			$data['inputerror'][] = 'status';
			$data['error_string'][] = 'Enter this field!';
			$data['status'] = FALSE;
		}
		if ($this->input->post('telp') == '') {
			$data['inputerror'][] = 'telp';
			$data['error_string'][] = 'Enter this field!';
			$data['status'] = FALSE;
		}
		if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}
}
