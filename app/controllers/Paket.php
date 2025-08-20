<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Paket extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		if (!is_logged_in()) {
			redirect('login?_rdr='.urlencode(current_url()));
		}
		$this->load->model('paket_model','paket');
		$this->load->model('Provisioning_model','prov');
		$this->load->helper(array('MY_ribuan','MY_bulan'));
	}

	public function index()
	{
		set_status_header(401);
	}

	public function ajax_list() {
	$list = $this->paket->get_datatables();
	$data = array();
	$no = $_POST['start'];
	foreach ($list as $br) {
		$no++;
		$row = array();
		$row[] = $br->id_paket;
		$row[] = "<span class='font-bold'>$br->nama_paket</span>";
		$row[] = "<span class='font-bold'>$br->mikrotik_profile</span>";
		$row[] = 'Rp.'.ribuan($br->tarif);
		$row[] = $br->tcont;
		$row[] = $br->gemport;
		$row[] = $br->keterangan;
	//add html for action
		$row[] = "<div class=\"btn-group\">
                            <button data-toggle=\"dropdown\" class=\"btn btn-default btn-xs dropdown-toggle\" aria-expanded=\"false\">Action <span class=\"caret\"></span></button>
                            <ul class=\"dropdown-menu\">
                                <li><a href=\"javascript:void(0)\" onclick=\"views('$br->id_paket')\"><i class=\"glyphicon glyphicon-eye-open\"></i> Lihat Detail</a></li>
                                <li><a href=\"javascript:void(0)\" onclick=\"edits('$br->id_paket')\"><i class=\"glyphicon glyphicon-pencil\"></i> Edit</a></li>
                                <li><a href=\"javascript:void(0)\" onclick=\"deletes('$br->id_paket')\"><i class=\"glyphicon glyphicon-trash\"></i> Hapus</a></li>
                            </ul>
                        </div>";
		$data[] = $row;
		}
		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->paket->count_all(),
						"recordsFiltered" => $this->paket->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}

	public function get_tcont()
	{
		$text = "";
		$data = $this->prov->getTcont();

		foreach ($data as $key) {
			$text .= "<option value='tcont $key->type profile $key->profile_name'>tcont $key->type profile $key->profile_name</option>";
		}

		echo $text;
	}

	public function save_paket()
	{
		$this->_validate();
		$data = array(
			'nama_paket' => $this->input->post('nama_paket'),
			'mikrotik_profile' => $this->input->post('mikrotik_profile'),
			// 'speed_max' => $this->input->post('speed_max'),
			'tarif' => $this->input->post('tarif'),
			'tcont' => $this->input->post('tcont'),
			'gemport' => $this->input->post('gemport'),
			'keterangan' => $this->input->post('keterangan'),
		);
		$insert = $this->paket->save($data);
		echo json_encode(array("status" => TRUE));
	}

	public function update_paket()
	{
		$this->_validate();
		$data = array(
			'nama_paket' => $this->input->post('nama_paket'),
			'mikrotik_profile' => $this->input->post('mikrotik_profile'),
			// 'speed_max' => $this->input->post('speed_max'),
			'tarif' => $this->input->post('tarif'),
			'tcont' => $this->input->post('tcont'),
			'gemport' => $this->input->post('gemport'),
			'keterangan' => $this->input->post('keterangan'),
		);
		$this->paket->update(array('id_paket' => $this->input->post('id_paket')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function delete_paket($id_paket)
	{
		$this->paket->delete_by_id($id_paket);
		echo json_encode(array("status" => TRUE));
	}

	public function get_edit($id_paket=FALSE)
	{
		$data= $this->paket->get_by_id($id_paket);
		echo json_encode($data);
	}

	private function _validate()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;
		//id tidak divalidasi karena auto_increment
		if($this->input->post('nama_paket') == '') {
			$data['inputerror'][] = 'nama_paket';
			$data['error_string'][] = 'Enter this field!';
			$data['status'] = FALSE;
		}
		if($this->input->post('mikrotik_profile') == '') {
			$data['inputerror'][] = 'speed_max';
			$data['error_string'][] = 'Enter this field!';
			$data['status'] = FALSE;
		}
		if($this->input->post('tarif') == '') {
			$data['inputerror'][] = 'tarif';
			$data['error_string'][] = 'Enter this field!';
			$data['status'] = FALSE;
		}
		if($this->input->post('tcont') == '') {
			$data['inputerror'][] = 'tcont';
			$data['error_string'][] = 'Enter this field!';
			$data['status'] = FALSE;
		}
		if($this->input->post('gemport') == '') {
			$data['inputerror'][] = 'gemport';
			$data['error_string'][] = 'Enter this field!';
			$data['status'] = FALSE;
		}
		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}

}
