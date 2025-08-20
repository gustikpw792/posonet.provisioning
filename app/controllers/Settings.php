<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Settings extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		if (!is_logged_in()) {
			redirect('login?_rdr='.urlencode(current_url()));
		}
		$this->load->model('users_model','users');
		$this->load->model('api_telegrambot_model','tg');
	}

	public function index()
	{
		set_status_header(401);
	}

	public function ping()
	{
		echo "<h4>Ping to OLT</h4>";
		
		$ip =   "10.10.10.2";
		exec("ping -n 3 $ip", $output, $status);

		foreach ($output as $key => $val) {
			echo $val."<br>";
		}
		
		sleep(1);
		
		echo "<h4>Ping to Gateway</h4>";
		
		$ip =   "192.168.50.1";
		exec("ping -n 3 $ip", $output2, $status2);
		
		foreach ($output2 as $key => $val) {
			echo $val."<br>";
		}

		//if 1 = cant ping, 0 = success
		echo $status2;
	}

	public function save_rekening(){
		if ($this->input->post('bank') == 'BRI') {
			$data = array(
				array(
					'option_id' => 20,
					'option_value' => $this->input->post('bank'),
				),
				array(
					'option_id' => 21,
					'option_value' => $this->input->post('norek'),
				),
				array(
					'option_id' => 22,
					'option_value' => $this->input->post('nama_pemilik_pekening'),
				)
			);

			$this->db->update_batch('settings', $data, 'option_id'); 

			echo json_encode([
				"status" => true,
				"message" => "Berhasil update rekening!",
			]);
		}
	}

	public function get_rekening(){
		$query = $this->db->query("SELECT * FROM settings where option_name LIKE 'bri_%'")->result();
		$data = array();

		foreach ($query as $key) {
			$row[] = $key->option_value;
		}

		echo json_encode($row);
	}

	public function get_tg_bot(){
		$query = $this->db->query("SELECT * FROM settings where option_name LIKE 'tg_%'")->result();
		$data = array();

		foreach ($query as $key) {
			$row[$key->option_name] = $key->option_value;
		}

		echo json_encode($row);
	}

	/**
	 * Users Access
	 * Create privillege login access
	 * */ 

	public function ajax_list() {
		$list = $this->users->get_datatables();
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $br) {
			$no++;
			$row = array();
			$row[] = $no;
			$row[] = "<span class='font-bold'>$br->username</span>";
			$row[] = "<span class='font-bold'>$br->level</span>";
			$row[] = "$br->nama_lengkap";
			$row[] = $br->aktif;
			$row[] = "<div class=\"btn-group\">
								<button data-toggle=\"dropdown\" class=\"btn btn-default btn-xs dropdown-toggle\" aria-expanded=\"false\">Action <span class=\"caret\"></span></button>
								<ul class=\"dropdown-menu\">
									<li><a href=\"javascript:void(0)\" onclick=\"edit_user('$br->id_users')\"><i class=\"glyphicon glyphicon-pencil\"></i> Edit</a></li>
									<li><a href=\"javascript:void(0)\" onclick=\"delete_user('$br->id_users')\"><i class=\"glyphicon glyphicon-trash\"></i> Hapus</a></li>
								</ul>
							</div>";
			$data[] = $row;
		}

		$output = array(
				"draw" => $_POST['draw'],
				"recordsTotal" => $this->users->count_all(),
				"recordsFiltered" => $this->users->count_filtered(),
				"data" => $data,
		);
		echo json_encode($output);
	}

	public function save_users()
	{
		$data = array(
			'username' => $this->input->post('username'),
			'password' => md5($this->input->post('password')),
			'id_karyawan' => $this->input->post('id_karyawan'),
			'level' => $this->input->post('level'),
			'aktif' => $this->input->post('aktif'),
			'rules' => 'webapp',
			'akses_wilayah' => json_encode($this->input->post('akses_wilayah[]')),
		);
		$insert = $this->users->save($data);
		echo json_encode(array("status" => TRUE, "message" => "Berhasil menambah User"));
	}

	public function update_users()
	{
		$data = array(
			'username' => $this->input->post('username'),
			'password' => md5($this->input->post('password')),
			'id_karyawan' => $this->input->post('id_karyawan'),
			'level' => $this->input->post('level'),
			'aktif' => $this->input->post('aktif'),
			'rules' => 'webapp',
			'akses_wilayah' => json_encode($this->input->post('akses_wilayah[]')),
		);
		$this->users->update(array('id_users' => $this->input->post('id_users')), $data);
		echo json_encode(array("status" => TRUE, "message" => "Berhasil Update User"));
	}

	public function delete_user($id_users)
	{
		$this->users->delete_by_id($id_users);
		echo json_encode(array("status" => TRUE, "message" => "Berhasil Delete User"));
	}

	public function get_edit_user($id_users=FALSE)
	{
		$q= $this->users->get_by_id($id_users);
		$data = array(
			'id_users' => $q->id_users,
			'username' => $q->username,
			'level' => $q->level,
			'aktif' => $q->aktif,
			'id_karyawan' => $q->id_karyawan,
			'rules' => $q->rules,
			'akses_wilayah' => json_decode($q->akses_wilayah),
		);
		echo json_encode($data);
	}

	//tes
	public function getTgSettings()
	{
		// $data= $this->tg->getTgSettings();
		$data= $this->tg->sendNewClientToAdmin(array(
			'no_pelanggan' => 222,
			'nama_pelanggan' => 'Tes Regis',
			'telp' => '082378901234',
			'tgl_instalasi' => '2024-03-02',
			'id_paket' => 15,
			'expired' => '2024-03-21',
		));
		echo json_encode($data);
	}

}
