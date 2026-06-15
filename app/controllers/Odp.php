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
			
			$row[] = (empty($br->odp_latlong) || $br->odp_latlong == null) ? '' : ' <a href="https://www.google.com/maps/?q=' . $br->odp_latlong . '" target="_blank" title="Klik untuk melihat lokasi ODP"><strong>' .$br->odp_latlong . '</strong></a>';
			// $row[] = "<span class='font-bold'>$br->latlong</span>";
			
			$row[] = "<small>$br->type</small>";
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
			'type' => $this->input->post('type'),
			'capacity' => $this->input->post('capacity'),
			'id_odp_parent' => $this->input->post('id_odp_parent'),
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
			'type' => $this->input->post('type'),
			'capacity' => $this->input->post('capacity'),
			'id_odp_parent' => $this->input->post('id_odp_parent'),
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
			'latlong' => $q->odp_latlong,
			'description' => $q->description,
			'type' => $q->type,
			'capacity' => $q->capacity,
			'id_odp_parent' => $q->id_odp_parent,
			'odp_parent_name' => $q->odp_parent_name,
		);
		echo json_encode($data);
	}

	/**
	 * Select2 server-side request
	 */

	public function s2_get_data_for_select2() {
        $search = $this->input->get('search');
        $page = $this->input->get('page');
        $limit = 10; // Number of items per page
        $offset = ($page - 1) * $limit;

        // Fetch data from your model based on search term and pagination
        $data = $this->odp->s2_get_items($search, $limit, $offset);
        $total_count = $this->odp->s2_count_items($search); // Total items for pagination

        // Format data for Select2
        $formatted_data = array();
        foreach ($data as $item) {
            $formatted_data[] = array(
                'id' => $item->id_odp, // The ID of the item
                'text' => $item->odp_name // The text to display in Select2
            );
        }

        echo json_encode(array(
            'items' => $formatted_data,
            'total_count' => $total_count
        ));
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
