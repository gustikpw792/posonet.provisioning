<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Map_cluster extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

		if (!is_logged_in()) {
			redirect('login?_rdr=' . urlencode(current_url()));
		}
    }



    public function index()
    {
        // Default endpoint
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'API Controller is working']));
    }

    public function fetchCluster() 
    {
        $map = $this->config->item('map_cluster');

    

        $this->load->view('admin/map_cluster/map_cluster', $map);
    }

}