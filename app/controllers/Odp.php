<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Odp extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		if (!is_logged_in()) {
			redirect('login?_rdr=' . urlencode(current_url()));
		}
		$this->load->model('odp_model', 'odp');
	}

	public function index()
	{
		set_status_header(401);
	}

	public function ajax_list()
	{
		$list = $this->odp->get_datatables();
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $br) {
			$no++;
			$row = array();
			$row[] = $no;
			$row[] = "<span class='font-bold'>$br->odp_name</span>";
			
			$row[] = (empty($br->latlong) || $br->latlong == null) ? '' : ' <a href="https://www.google.com/maps/?q=' . $br->latlong . '" target="_blank" title="Klik untuk melihat lokasi ODP"><strong>' .$br->latlong . '</strong></a>';
			// $row[] = "<span class='font-bold'>$br->latlong</span>";
			
			$row[] = "<small>$br->description</small>";

			//add html for action
			$row[] = "<div class=\"btn-group\"><a class=\"btn btn-xs btn-outline btn-warning\" href=\"javascript:void(0)\" onclick=\"edits('$br->id_odp')\" title=\"Edit\"><i class=\"glyphicon glyphicon-pencil\"></i> Edit</a>
				<a class=\"btn btn-xs btn-outline btn-danger\" href=\"javascript:void(0)\" onclick=\"deletes('$br->id_odp')\" title=\"Hapus\" ><i class=\"glyphicon glyphicon-trash\"></i> Delete</a></div>";
			$data[] = $row;
		}
		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->odp->count_all(),
			"recordsFiltered" => $this->odp->count_filtered(),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}

	public function save_odp()
	{
		// $this->_validate();
		$data = array(
			'id_odp' => $this->input->post('id_odp'),
			'odp_name' => $this->input->post('odp_name'),
			'latlong' => str_replace(' ','',$this->input->post('latlong')),
			'description' => $this->input->post('description'),
		);
		$insert = $this->odp->save($data);
		echo json_encode(array("status" => TRUE));
	}

	public function update_odp()
	{
		$this->_validate();
		$data = array(
			'odp_name' => $this->input->post('odp_name'),
			'latlong' => str_replace(' ','',$this->input->post('latlong')),
			'description' => $this->input->post('description'),
		);
		$this->odp->update(array('id_odp' => $this->input->post('id_odp')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function delete_odp($id_odp)
	{
		$this->odp->delete_by_id($id_odp);
		echo json_encode(array("status" => TRUE));
	}

	public function get_edit($id_odp = FALSE)
	{
		$data = $this->odp->get_by_id($id_odp);
		echo json_encode($data);
	}

	public function vget_edit($id_odp = FALSE)
	{
		$q = $this->odp->v_get_by_id($id_odp);
		$data = array(
			'id_odp' => $q->id_odp,
			'odp_name' => $q->odp_name,
			'latlong' => $q->latlong,
			'description' => $q->description,
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
		if ($this->input->post('odp_name') == '') {
			$data['inputerror'][] = 'odp_name';
			$data['error_string'][] = 'Enter this field!';
			$data['status'] = FALSE;
		}
		if ($this->input->post('latlong') == '') {
			$data['inputerror'][] = 'latlong';
			$data['error_string'][] = 'Enter this field!';
			$data['status'] = FALSE;
		}

		if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}


}
