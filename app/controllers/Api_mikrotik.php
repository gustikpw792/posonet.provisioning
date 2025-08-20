<?php defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set("Asia/Makassar");

require 'vendor/autoload.php';

use \RouterOS\Client;
use \RouterOS\Query;

class Api_mikrotik extends CI_Controller
{

	private $_client;
	private $_mikrotik;

	function __construct()
	{
		parent::__construct();
		if (!is_logged_in()) {
			redirect('login');
		}
		
		$this->mikrotik = $this->config->item('mikrotik');

		$this->_mikrotik = new \RouterOS\Config([
			'host' => $this->mikrotik['HOST'],
			'user' => $this->mikrotik['USERNAME'],
			'pass' => $this->mikrotik['PASSWORD'],
			'port' => $this->mikrotik['PORT'],
		]);

		$this->load->helper(array('MY_ribuan', 'MY_bulan'));
		$this->load->model('Api_mikrotik_model','routermodel');
	}

	public function index()
	{
		set_status_header(401);
	}

	public function all_secret($where=null) {
		$where = '101.-H.-TAHIRC8A9FAF2';

		$client = new Client($this->_mikrotik);
		$query = new Query('/ppp/secret/print');
		
		if ($where != null) {
			$query->where('name', $where);
		}

		$secrets = $client->query($query)->read();

		echo json_encode($secrets);
	}
	
	public function all_ppp_active() {

		$client = new Client($this->_mikrotik);
		$query = new Query('/ppp/active/print');
		$secrets = $client->query($query)->read();

		// buat client dengan status default disconnected
		$this->db->query("UPDATE pelanggan SET active_connection='disconnected'");
		// update ke connected berdasarkan user yang terkoneksi di mikrotik
		foreach ($secrets as $val) {
			$escstring = $this->db->escape($val['name']);
			$this->db->query("UPDATE pelanggan SET ip_address='$val[address]', active_connection='connected' WHERE username=$escstring");
		}
		// echo json_encode($secrets);
		echo json_encode(['status' => true, 'message' => 'Active connection updated!']);
	}

	public function change_password($password=null) {
		$client = new Client($this->_mikrotik);
		// Change password
		$query = (new Query('/ppp/secret/set'))
			->equal('.id', $secret['.id'])
			->equal('password', $password);

		// Update query ordinary have no return
		$client->query($query)->read();
	}

	public function close_connection_ppp($username= '601.-TESC0876376'){
		$res = $this->routermodel->close_connection_ppp($username);
		echo json_encode($res);
	}

	public function restore_paket($username= '601.-TESC0876376'){
		$res = $this->routermodel->restore_paket_from_expire($username,'Expired');
		echo json_encode($res);
	}


	/**
	 * TEST REST HTTP ROUTEROS 7,9^
	 */

	
	function getPPPSecret($username = false) {
	   $body = $this->routermodel->getRestSecret($username);
	   echo $body;
	}

	function putPPPSecret() {
	   $body = $this->routermodel->putRestSecret();
	   echo $body;
	}
	
	function patchPPPSecret($data = false) {
		$data = (object) array(
			"name" => "COBA",
			// "password" => "COBAREST7890X",
			// "profile"=> "UPTO-15M",
			"profile"=> "Expired",
		);
		$body = $this->routermodel->patchRestSecret($data);
		// var_dump($body);
		echo $body;
		
	}
	
	function patchRestSecretById($id='*1F7') {
		
		$data = (object) array(
			'profile' => 'Expired'
		); 
		

		$body = $this->routermodel->patchRestSecretById($id, $data);
		// var_dump($body);
		echo $body;

	}

	function deletePPPSecret($data = false) {
		// $data = (object) array(
		// 	"name" => "COBAREST7",
		// );
		$body = $this->routermodel->deleteRestSecret(
			(object) array("name" => "COBAREST7")
		);
		// var_dump($body);
		echo $body;

	}

	function pppCloseActiveConn($data = false) {
		$data = (object) array(
			"name" => "336.-Tes-Siapa-C71F6297",
		);
		$body = $this->routermodel->pppCloseConnection($data);
		// var_dump($body);
		echo $body;

	}

	public function cekExpire(){
		$data = $this->routermodel->match_paket_rest();
		echo json_encode($data);
	}

	
}
