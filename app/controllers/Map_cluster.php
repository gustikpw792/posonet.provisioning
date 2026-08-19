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

    function update_all_coordinate()
    {
        $this->load->model('Maps_model', 'maps_model');
        // Untuk 300 data, mungkin perlu waktu
        set_time_limit(300); // 5 menit

        // $results = $this->maps_model->process_all_data();
        // $results = $this->maps_model->extract_coordinates('https://maps.app.goo.gl/boUz8TqiXSfZnks36');
        $results = $this->maps_model->extract_coordinates('https://maps.app.goo.gl/G7zhjyD1AvmfKNus7');
        // $results = $this->maps_model->parse_coordinates('"https://www.google.com/maps/place/Kedai+Qieo,+Mayoa,+Kec.+Pamona+Sel.,+Kabupaten+Poso,+Sulawesi+Tengah/data=!4m6!3m5!1s0x2d91d79fd5435fb3:0x81e2e3b1397522b2!7e2!8m2!3d-2.1492253!4d120.7321891?utm_source=mstt_1&entry=gps&coh=192189&g_ep=CAESBzI1LjI3LjQYACDXggMqmQEsOTQyNTk1NTEsOTQyNzUzMTEsOTQyMjMyOTksOTQyMTY0MTMsOTQyODA1NzIsOTQyMTI0OTYsOTQyMDczOTQsOTQyMDc1MDYsOTQyMDg1MDYsOTQyMTc1MjMsOTQyMTg2NTMsOTQyMjk4MzksOTQyNzUxNjgsOTQyNjI3MzksNDcwODQzOTMsOTQyMTMyMDAsOTQyNTgzMjVCAklE&skid=2aa68360-cc82-421d-92e8-8a345df1823c');

        // echo json_encode(count($results));
        echo json_encode($results);
    }

}