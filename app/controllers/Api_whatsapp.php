<?php defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set("Asia/Makassar");

require 'vendor/autoload.php';

use GuzzleHttp\Client;

class Api_whatsapp extends CI_Controller
{

	private $_client;

	function __construct()
	{
		parent::__construct();
		
		$this->olt = $this->config->item('olt');

		$this->_client = new Client([
			'base_uri' => $this->olt['BASE_URI']
		]);

		$this->load->model('api_rest_client_model', 'api');
		$this->load->model('api_mikrotik_model', 'routermodel');
		$this->load->model('api_kirimwaid_model', 'kirimwa');
		$this->load->model('api_telegrambot_model', 'telegrambot');
		$this->load->helper(array('MY_ribuan', 'MY_bulan'));
	}

	public function index()
	{
		set_status_header(401);
	}

	public function get_info()
	{
		// $nopel = $this->input->post('nopel');
		echo json_encode("dari server web");
	}
	
	public function reply()
	{
		$nopel = $this->input->post('username');
		$sn = $this->input->post('email');
		echo json_encode("dari server web $nopel dan $sn");
	}

	public function info()
	{
		// $data = (object) ['no_pelanggan' => '250'];
		$data = json_decode(file_get_contents('php://input'));

		//cek db by no pelanggan
		$res = $this->db->query("SELECT * FROM v_pelanggan WHERE no_pelanggan='$data->no_pelanggan'");
		$d = $res->row();

		if ($res->num_rows() > 0) {
			//cek gpon_onu_detail
			$detail = $this->api->raw_detailinfo($d->gpon_onu)['results'];
			$att = $this->api->raw_attenuation($d->gpon_onu);
			$wanip = $this->api->raw_wanip($d->gpon_onu);

	
			$phase = array('DyingGasp','working','LOS','logging','syncMib','offline');
			$icon = array('🆙','⏰','❌','✔️','📌','🚨','🎫','📢','📝','⚠','🔄','🔁','✔','🆕','🆘','🆙');

			if ($detail['phase_state'] == 'LOS' || $detail['phase_state'] == 'logging' || $detail['phase_state'] == 'syncMib') {
				$state = "*$detail[phase_state]* ❌";
			} else {
				$state = "*$detail[phase_state]*";
			}

			$reply = "📋 *ONU Information*
	
Interface	: $detail[onu_interface]
Name		: *$detail[Name]*
Type		: $detail[Type]
SN			: $detail[SerialNumber]
Distance	: $detail[ONUDistance]
Phase State	: $state
Online Duration: $detail[online_duration]

OLT Rx		: $att[rx_olt] dBm
ONU Rx		: *$att[rx_onu] dBm*
WAN IP		: *$wanip[current_ip]*
";
	
			echo json_encode($reply);
			
		}
	}


	public function expired()
	{
		// $data = (object) [
		// 	'no_pelanggan' => '607',
		// 	'expired' => '2024-01-20',
		// ]; // studio

		$data = json_decode(file_get_contents('php://input'));

		//cek db by no pelanggan
		$res = $this->db->query("SELECT * FROM v_pelanggan WHERE no_pelanggan='$data->no_pelanggan'");
		$d = $res->row();

		if ($res->num_rows() > 0) {
			//cek gpon_onu_detail
			$gpon_onu = $d->gpon_onu;
			$expired = $data->expired;
		
			$extend = $this->api->extendThisPaket($gpon_onu, $expired);
		

			$reply = "⏰ *Perpanjang Paket Berhasil*

Name	: $d->name
Expired : $expired
Tgl Input : " . date('Y-m-d H:i:s') . "
			
_Note by system : $extend[message]_";

			echo json_encode($reply);
			
		}
	}

	public function reboot()
	{
		$data = (object) [
			'no_pelanggan' => '607'
		]; // studio

		// $data = json_decode(file_get_contents('php://input'));

		//cek db by no pelanggan
		$res = $this->db->query("SELECT * FROM v_pelanggan WHERE no_pelanggan='$data->no_pelanggan'");
		$d = $res->row();

		if ($res->num_rows() > 0) {
			//cek gpon_onu_detail
			$reboot = $this->api->reboot($d->gpon_onu);
		

			$reply = "🔁 *Restart ONT Berhasil*

Name	: $d->name
			
_Note by system : " . $reboot->message ."_";

			echo json_encode($reply);
			
		}
	}

	public function reset()
	{
		// $data = (object) [
		// 	'no_pelanggan' => '607'
		// ]; // studio

		$data = json_decode(file_get_contents('php://input'));

		//cek db by no pelanggan
		$res = $this->db->query("SELECT * FROM v_pelanggan WHERE no_pelanggan='$data->no_pelanggan'");
		$d = $res->row();

		if ($res->num_rows() > 0) {
			//cek gpon_onu_detail
			$extend = $this->api->restore_factory($d->gpon_onu);
		

			$reply = "↩ *Restore Factory ONT Berhasil*

Name	: $d->name
			
_Note by system : $extend[message]_";

			echo json_encode($reply);
			
		}
	}

	

}
