<?php defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set("Asia/Makassar");

require 'vendor/autoload.php';

use GuzzleHttp\Client;

class Api_rest_client extends CI_Controller
{

	private $_client;

	function __construct()
	{
		parent::__construct();
		if (!is_logged_in()) {
			redirect('login');
		}
		
		$this->olt = $this->config->item('olt');
		$this->ros = $this->config->item('mikrotik');

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

	public function backup_cfg(){
		$data = $this->api->backup_config_olt();
		echo json_encode($data);
		// backup config dari olt ke mikrotik melalui ftp. kemudian download file di mikrotik bernama startrun.dat
	}

	public function offline()
	{
		$req = $this->db->query("SELECT * FROM v_onu_offline")->result();
		$data = array();
		foreach ($req as $br) {
			$row = array();
			$row[] = "<div class=\"btn-group\">
							<button type=\"button\" class=\"btn btn-xs dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">
								Action <span class=\"caret\"></span>
							</button>
							<ul class=\"dropdown-menu\">
								<li><a href=\"javascript:void(0)\" onclick=\"show_raw_content('wanip','$br->gpon_onu')\">Show WAN IP</a></li>
								<li><a href=\"javascript:void(0)\" onclick=\"show_raw_content('attenuation','$br->gpon_onu')\">Show Attenuation</a></li>
								<li><a href=\"javascript:void(0)\" onclick=\"show_raw_content('detail-info','$br->gpon_onu')\">Show Detail Information</a></li>
								<li><a href=\"javascript:void(0)\" onclick=\"show_raw_content('iphost','$br->gpon_onu')\">Show IP Host</a></li>
								<li><a href=\"javascript:void(0)\" onclick=\"show_raw_content('onu-run','$br->gpon_onu')\">Show Run</a></li>
								<li><a href=\"javascript:void(0)\" onclick=\"getReplaceOnt('$br->gpon_onu')\"><span class=\"fa fa-exchange\"></span> Replace ONT</a></li>
								<li role=\"separator\" class=\"divider\"></li>
								<li><a href=\"javascript:void(0)\" onclick=\"makeTickets('$br->gpon_onu')\"><span class=\"fa fa-ticket\"></span> Make Ticket</a></li>
							</ul>
						</div>";
			$row[] = $br->gpon_onu;
			$row[] = $br->no_pelanggan.". ". $br->nama_pelanggan;
			$row[] = $br->ont_phase_state;

			if ($br->expired < date('Y-m-d')) {
				$warna = 'text-danger';
			}
			elseif ($br->expired == date('Y-m-d')) {
				$warna = 'text-warning';
			}
			elseif ($br->expired > date('Y-m-d')) {
				$warna = 'text-default';
			}

			$row[] = "<strong><span class='$warna'>$br->expired</span></strong>";

			$data[] = $row;
		}

		$output = array(
			"total" => count($data),
			"data" => $data,
			"status" => (count($data) == 0) ? "404" : "200"
		);

		echo json_encode($output);
	}
	
	public function los()
	{
		$req = $this->db->query("SELECT * FROM v_onu_los")->result();
		$data = array();
		foreach ($req as $br) {
			$row = array();
			$row[] = "<div class=\"btn-group\">
							<button type=\"button\" class=\"btn btn-xs dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">
								Action <span class=\"caret\"></span>
							</button>
							<ul class=\"dropdown-menu\">
								<li><a href=\"javascript:void(0)\" onclick=\"show_raw_content('attenuation','$br->gpon_onu')\">Show Attenuation</a></li>
								<li><a href=\"javascript:void(0)\" onclick=\"show_raw_content('wanip','$br->gpon_onu')\">Show WAN IP</a></li>
								<li><a href=\"javascript:void(0)\" onclick=\"show_raw_content('iphost','$br->gpon_onu')\">Show IP Host</a></li>
								<li><a href=\"javascript:void(0)\" onclick=\"show_raw_content('detail-info','$br->gpon_onu')\">Show Detail Information</a></li>
								<li><a href=\"javascript:void(0)\" onclick=\"getReplaceOnt('$br->gpon_onu')\"><span class=\"fa fa-exchange\"></span> Replace ONT</a></li>
								<li role=\"separator\" class=\"divider\"></li>
								<li><a href=\"javascript:void(0)\" onclick=\"makeTickets('$br->gpon_onu')\"><span class=\"fa fa-ticket\"></span> Make Ticket</a></li>
							</ul>
						</div>";
			$row[] = $br->gpon_onu;
			$row[] = $br->no_pelanggan.". ". $br->nama_pelanggan." <span class='btn btn-xs btn-outline'><small class='text-muted'>$br->onu_type</small></span>";
			$row[] = $br->expired;
			$row[] = $br->ont_phase_state;
			
			$ontLocation = ($br->lokasi_map == '' || $br->lokasi_map == null) ? "javascript:void(0)" : urldecode($br->lokasi_map);
			// $row[] = "<a href=\"$odpLocation\" target=\"_blank\" class=\"btn btn-xs btn-danger\"><span class=\"fa fa-map\"></span> $br->odp_number</a>";
			$row[] = (empty($br->latlong) || $br->latlong == null) ? ' <a href="#" class="btn btn-xs btn-outline btn-danger" title="ODP kosong"><small>ODP</small></a>' : ' <a href="https://www.google.com/maps/?q=' . $br->latlong . '" class="btn btn-xs btn-outline btn-primary" target="_blank" title="Klik untuk melihat lokasi ODP"><small>' .$br->odp_name . '</small></a>';
			$row[] = "<a href=\"$ontLocation\" target=\"_blank\" class=\"btn btn-sm\"><span class=\"fa fa-map\"></span> Lokasi ONT</a>";

			$data[] = $row;
		}

		//smart detection when interface los
		$ifaceLos = $this->db->query("SELECT l.gpon_olt, COUNT(l.gpon_onu) AS ont_los, i.ont AS all_ont 
			FROM v_onu_los AS l JOIN v_interfaces AS i 
			GROUP BY gpon_olt")->result();
		
		$ifaceFounds = "";
		$status = false;
		$count = 0;

		foreach ($ifaceLos as $il) {
			if ($il->ont_los > ($il->all_ont * 0.05) ) {
				$status = true;
				$ifaceFounds .= $il->gpon_olt . ' ';
				$count++;
			}
		}

		$templateNotif = 	"<div class=\"alert alert-danger alert-dismissable\">
								<button aria-hidden=\"true\" data-dismiss=\"alert\" class=\"close\" type=\"button\">×</button>
								Interface LOS <strong>$ifaceFounds</strong>. Silahkan cek SFP atau Jalur Distribusi!
							</div>";

		$output = array(
			"interface_los" => array(
				"status"=> $status,
				"data"	=> (!$status) ? "" : $templateNotif,
			),
			"data" => $data,
			"status" => (count($data) == 0) ? "404" : "200"
		);

		echo json_encode($output);
	}

	public function expired()
	{
		$req = $this->db->query("SELECT * FROM v_expired WHERE ont_phase_state='working'")->result();
		$data = array();
		foreach ($req as $br) {
			$row = array();
			$row[] = "<div class=\"btn-group\">
							<button type=\"button\" class=\"btn btn-xs dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">
								Action <span class=\"caret\"></span>
							</button>
							<ul class=\"dropdown-menu\">
								<li><a href=\"javascript:void(0)\" onclick=\"extendPaket('$br->gpon_onu')\"><span class=\"fa fa-calendar\"></span> Perpanjang Paket</a></li>
								<li><a href=\"javascript:void(0)\" onclick=\"reboot('$br->gpon_onu')\"><span class=\"fa fa-refresh\"></span> Reboot</a></li>
								<li role=\"separator\" class=\"divider\"></li>
								<li><a href=\"javascript:void(0)\" onclick=\"show_raw_content('attenuation','$br->gpon_onu')\">Show Attenuation</a></li>
								<li><a href=\"javascript:void(0)\" onclick=\"show_raw_content('wanip','$br->gpon_onu')\">Show WAN IP</a></li>
								<li><a href=\"javascript:void(0)\" onclick=\"show_raw_content('iphost','$br->gpon_onu')\">Show IP Host</a></li>
								<li><a href=\"javascript:void(0)\" onclick=\"show_raw_content('detail-info','$br->gpon_onu')\">Show Detail Information</a></li>
							</ul>
						</div>";
			$row[] = $br->gpon_onu;
			$row[] = $br->no_pelanggan.". ". $br->nama_pelanggan;
			$row[] = $br->expired;

			$data[] = $row;
		}

		$output = array(
			"data" => $data,
			"status" => (count($data) == 0) ? "404" : "200"
		);

		echo json_encode($output);
	}



	

	/**
	 * FUNGSI UNCONFIG DAN RECONFIG
	 * PINDAH PORT DAN MANUAL DELETE
	 */

	public function newUnconfig(){
		$uncfg = $this->api->pon_onu_uncfg();
		$data = array();
		$reconfig = $pindah_port = 0;

		foreach ($uncfg->data as $key) {
			// check if interface
			$cek = $this->check_onu($key->interface, $key->sn);

			$row = array();
			// $row[] = "<button type=\"button\" class=\"btn btn-primary btn-xs\" onclick=\"regis('$key->interface','$key->model','$key->sn')\" $cek->button>$cek->caption</button>";
			$row[] = "<a href=\"#\" class=\"btn btn-primary btn-xs\" onclick=\"regis('$key->interface','$key->model','$key->sn')\" $cek->button>$cek->caption</a>";
			$row[] = $key->interface;
			$row[] = $key->model;
			$row[] = $key->sn;
			$row[] = $cek->name;
			$row[] = $cek->onutype;
			$row[] = $cek->paket;
			$row[] = $cek->mode;

			$data[] = $row;

			//menghitung jumlah pindah-port dan reconfig
			if($cek->mode == 'reconfig'){
				$reconfig++;
			}
			if($cek->mode == 'pindah-port'){
				$pindah_port++;
			}
		}

		$output = array(
			"data" => $data,
			"reconfig" => $reconfig,
			"pindah_port" => $pindah_port,
			"message" => $uncfg->message,
			"status" => $uncfg->status,
		);

		echo json_encode($output);
	}

	public function check_onu($gpon_olt_baru, $sn){
		$cekdb = $this->db->query("SELECT no_pelanggan, nama_pelanggan, serial_number, name, gpon_onu, onu_type, description, username, password, mikrotik_profile, nama_paket, vlan_profile, cvlan, id_paket
				FROM v_pelanggan
				WHERE serial_number = '$sn'");

		//jika ditemukan sn yang sama
		if($cekdb->num_rows() > 0) {
			$data = $cekdb->row();

			//get tcont and gemport from paket
			$tcont =  $this->db->query("SELECT tcont, gemport FROM paket WHERE id_paket=$data->id_paket")->row();

			//interface sama atau tidak?
			$gpon_olt_lama = preg_split('/:/', $data->gpon_onu)[0];

			if($gpon_olt_lama == $gpon_olt_baru){
				$caption = "Configuring..";
				$mode = 'reconfig';
			} else{
				$caption = "Pindah Port $gpon_olt_lama >> $gpon_olt_baru ...";
				$mode = 'pindah-port';
			}

			$output = (object) [
				'caption' 	=> $caption, 
				'mode' 		=> $mode,
				'onutype' 	=> $data->onu_type,
				'paket' 	=> $data->nama_paket,
				'name' 		=> $data->no_pelanggan.'. '.$data->nama_pelanggan,
				'button' 	=> 'disabled',
				'db_data'	=> $data,
				'tcont'		=> $tcont,
			];
		} else {
			$output = (object) [
				'caption' 	=> 'Config', 
				'mode' 		=> 'new-config',
				'onutype' 	=> '---',
				'paket' 	=> '---',
				'name' 		=> '---',
				'button' 	=> '',
				'db_data'	=> null,
				'tcont'		=> null,
			];
		}

		return $output;
	}

	public function reconfig(){
		$uncfg = $this->api->pon_onu_uncfg();

		$alldata = array();
		$countReconfig = $countPindahPort = 0;
		$info = '';

		foreach ($uncfg->data as $key) {
			// check if interface
			$cek = $this->check_onu($key->interface, $key->sn);
			$data = $cek->db_data;
			$tcont = $cek->tcont;
			
			if ($cek->mode == 'reconfig') {

				$gpon_olt_old = preg_split('/:/', $data->gpon_onu)[0];
				$onu_index_old = preg_split('/:/', $data->gpon_onu)[1];

				$newGponOnu = $this->getNewOnuIndex($key->interface);
				$secret 	= $this->_make_ppp_secret($data->no_pelanggan, $data->nama_pelanggan, $data->serial_number);
				//reconfig
				$configData = array(
					'gpon_olt' 		=> $key->interface,
					'onu_index' 	=> $newGponOnu->new_index,
					'onu_type' 		=> $data->onu_type,
					'sn' 			=> $key->sn,
					'gpon_onu' 		=> $newGponOnu->registration_onu,
					'name' 			=> $secret->name,
					'description' 	=> $data->description,
					'username' 		=> $secret->username,
					'password' 		=> $secret->password,
					'vlan_profile' 	=> $data->vlan_profile,
					'cvlan' 		=> $data->cvlan,
					'tcont' 		=> $tcont->tcont,
					'gemport' 		=> $tcont->gemport,
					'mode_config'	=> $cek->mode,
				);

				$recon = $this->api->reconfig_onu($configData);
				
				if ($recon->status) {
					$info = $recon->data;
					// make secret mikrotik
        			if ($this->ros['ROS_VERSION'] == 6) {
						$remove_old_secret = $this->routermodel->remove_secret("$data->username");
						$create_new_secret = $this->routermodel->create_ppp_secret($secret->username, $secret->password, 'pppoe', $data->mikrotik_profile);
					} elseif ($this->ros['ROS_VERSION'] == 7) {
						$remove_old_secret = $this->routermodel->deleteRestSecret((object) array('name' => $data->username));
						$create_new_secret = $this->routermodel->putRestSecret(
							(object) array(
								'name'      => $secret->username,
								'password'  => $secret->password,
								'profile'   => $data->mikrotik_profile,
								'service'   => 'pppoe'
							)
						);
					} else {
						echo json_encode(['error' => 'RouterOS version not match! Api_rest_client Line 335']);
					}
					//update gpon_onu di database
					$query 	= "UPDATE pelanggan 
						SET name='$secret->name', gpon_olt='$key->interface', gpon_onu='$info->gpon_onu', username='$secret->username', password='$secret->password', remote_web_state='disabled', ont_phase_state='Registering'  
						WHERE serial_number='$key->sn'";
					$update = $this->db->query($query);

					$countReconfig++;
				}

			}

			elseif ($cek->mode == 'pindah-port') {
				//pindah port
				$newGponOnu = $this->getNewOnuIndex($key->interface);
				$secret 	= $this->_make_ppp_secret($data->no_pelanggan, $data->nama_pelanggan, $data->serial_number);

				$configData = array(
					'gpon_olt' 		=> $key->interface,
					'onu_index' 	=> $newGponOnu->new_index,
					'onu_type' 		=> $data->onu_type,
					'sn' 			=> $key->sn,
					'gpon_onu' 		=> $newGponOnu->registration_onu,
					'name' 			=> $secret->name,
					'description' 	=> $data->description,
					'username' 		=> $secret->username,
					'password' 		=> $secret->password,
					'vlan_profile' 	=> $data->vlan_profile,
					'cvlan' 		=> $data->cvlan,
					'tcont' 		=> $tcont->tcont,
					'gemport' 		=> $tcont->gemport,
					'mode_config'	=> $cek->mode,
				);
				//reconfig first
				$recon = $this->api->reconfig_onu($configData);


				if($recon->status){
					$info = $recon->data;
					//remove old config
					$gpon_olt_old = preg_split('/:/', $data->gpon_onu)[0];
					$onu_index_old = preg_split('/:/', $data->gpon_onu)[1];

					$remove_old_onu = $this->api->remove_onu($gpon_olt_old, $onu_index_old);
        			
					if ($this->ros['ROS_VERSION'] == 6) {
						$remove_old_secret = $this->routermodel->remove_secret("$data->username");
						$create_new_secret = $this->routermodel->create_ppp_secret($secret->username, $secret->password, 'pppoe', $data->mikrotik_profile);
					} elseif ($this->ros['ROS_VERSION'] == 7) {
						$remove_old_secret = $this->routermodel->deleteRestSecret((object) array('name' => $data->username));
						$create_new_secret = $this->routermodel->putRestSecret(
							(object) array(
								'name'      => $secret->username,
								'password'  => $secret->password,
								'profile'   => $data->mikrotik_profile,
								'service'   => 'pppoe'
							)
						);
					} else {
						echo json_encode(['error' => 'RouterOS version not match! Api_rest_client Line 390']);
					}

					//update gpon_onu di database
					$this->db->query("UPDATE pelanggan 
						SET name='$secret->name', gpon_olt='$key->interface', gpon_onu='$info->gpon_onu', username='$secret->username', password='$secret->password', remote_web_state='disabled', ont_phase_state='Registering' 
						WHERE serial_number='$key->sn'");
					
					$countPindahPort++;
				}
				
			}

			// $alldata = $configData;
			
		}

		echo json_encode([
			'message' => "$countPindahPort Onu berhasil pindah port. <br>$countReconfig Onu berhasih Reconfig",
			'status' => true,
			'info' => $info
		]);
	}

	public function getNewOnuIndex($gpon_olt=false){

		if ($gpon_olt == "") {
			return 0;
		}

		$data = $this->api->raw_show_gpon_onu_state_by($gpon_olt);

		if(strpos($data, "No related information to show.")){
			return (object) [
				'new_index' 		=> 1,
				'registration_onu' 	=> "$gpon_olt:1",
			];
		} else {
			// Pisahkan baris-baris data
			$lines = explode("\n", $data);

			$onuIndexList = array();

			foreach ($lines as $line) {
				// Cari baris yang mengandung OnuIndex
				if (preg_match('/\d+\/\d+\/\d+:(\d+)/', $line, $matches)) {
					$onuIndexList[] = $matches[1];
				}
			}

			// OnuIndex terakhir
			$lastOnuIndex = end($onuIndexList);

			// OnuIndex yang terlewati
			$skippedOnuIndexes = array_values(array_diff(range($onuIndexList[0], $lastOnuIndex), $onuIndexList));

			// Tampilkan hasil
			// echo "OnuIndex List: " . implode(", ", $onuIndexList) . "<br>";
			// echo "OnuIndex Terakhir: " . $lastOnuIndex . "<br>";
			// echo "OnuIndex Terlewati: " . implode(", ", $skippedOnuIndexes) . "<br>";
			$new_index = 0;
			if($skippedOnuIndexes == null){
				$new_index = $lastOnuIndex + 1;
			} else {
				$new_index = $skippedOnuIndexes[0];
			}

			return (object) [
				'new_index' 		=> $new_index,
				'registration_onu' 	=> "$gpon_olt:$new_index",
			];

			// echo json_encode( (object) [
			// 	'new_index' 		=> $new_index,
			// 	'registration_onu' 	=> "$gpon_olt:$new_index",
			// ]);

		}
	}


	/**
	 * END
	 */



	public function onutype()
	{
		ini_set('max_execution_time', 1200);

		$response = $this->_client->get('onutype');
		$body = json_decode($response->getBody());

		$data = "";
		foreach ($body as $d) {
			$data .= "<option value='$d->onu_type'>$d->onu_type</option>";
		}

		$output = array(
			"data" => $data,
			"message" => "Onu Type from OLT",
			"status" => '200',
		);

		echo json_encode($output);
	}

	public function no_onu()
	{
		$gpon_onu = $this->input->post('gpon_onu');
		$permanent = $this->input->post('permanent');
		//ambil username pppoe utk delete secret di mikrotik
		$qry = $this->db->query("SELECT username, name, id_pelanggan, no_pelanggan, nama_pelanggan, telp, tgl_instalasi, nama_paket, expired, lokasi_map FROM v_pelanggan WHERE gpon_onu = '$gpon_onu'")->row();
		//delete onu di olt
		$delete_onu = $this->api->delete_onu($gpon_onu, $qry->username);
		//delete onu di mikrotik
		if ($this->ros['ROS_VERSION'] == 6) {
			$delete_secret = $this->routermodel->remove_secret("$qry->username");
		}
		elseif ($this->ros['ROS_VERSION'] == 7) {
			$delete_secret = $this->routermodel->deleteRestSecret((object) array('name' => $qry->username));
		}

		// save to Log table
		$logData = $qry;


		//delete onu di database sql
		if ($permanent == 'yes'){
			$this->api->saveLogEvent('DELETE PERMANENT', "$logData->no_pelanggan. $logData->nama_pelanggan [$logData->nama_paket / $logData->tgl_instalasi / $logData->expired / $logData->lokasi_map / $logData->telp /ID $logData->id_pelanggan] by " . $this->session->username);
			$delete_cust = $this->db->query("DELETE FROM pelanggan WHERE gpon_onu = '$gpon_onu'");
			
		} else {
			$this->api->saveLogEvent('DELETE MANUAL', "$logData->no_pelanggan. $logData->nama_pelanggan [$logData->nama_paket / $logData->tgl_instalasi / $logData->expired / $logData->lokasi_map / $logData->telp /ID $logData->id_pelanggan] by " . $this->session->username);
			$updateOntPhase = $this->db->query("UPDATE pelanggan SET ont_phase_state='Unconfigured', remote_web_state='disabled' WHERE gpon_onu = '$gpon_onu'");
		}
		/**
		 * KIRIM PESAN KE TELEGRAM
		 */

		$template = "*DELETE ONU*
gpon-onu_%s
Name : %s
Tgl Delete : %s

_handled by %s_";

		$teletext = sprintf($template, $gpon_onu, $qry->name, date('Y-m-d H:i:s'), $this->session->username);

		// $sendToTelegram = "";
		$sendToTelegram = $this->telegrambot->sendToAdmin($teletext);

		echo json_encode([
			"data" => $gpon_onu . '  & ' . $qry->username,
			"message" => $teletext,
			// "message" => $delete_onu->message,
			"status" => true,
		]);
	}

	public function raw_attenuation()
	{
		$gpon_onu = $this->input->post('data');
		$request = $this->api->raw_attenuation($gpon_onu);

		echo json_encode([
			"data" => "<pre>$request[raw]</pre>",
			"header" => "Attenuation gpon-onu_" . $gpon_onu,
			"status" => true,
		]);
	}

	public function raw_onu_runcfg()
	{
		$gpon_onu = $this->input->post('data');
		$request1 = $this->api->raw_onu_run_conf_interface($gpon_onu);
		$request2 = $this->api->raw_onu_run_cfg($gpon_onu);

		echo json_encode([
			"data" => "<pre>$request1 \n\n$request2</pre>",
			"header" => "ONU Running Config gpon-onu_" . $gpon_onu,
			"status" => true,
		]);
	}
	
	public function raw_iphost()
	{
		$gpon_onu = $this->input->post('data');
		$request = $this->api->raw_iphost($gpon_onu, '1');

		echo json_encode([
			"data" => "<pre>$request</pre>",
			"header" => "IP Host 1 gpon-onu_" . $gpon_onu,
			"status" => true,
		]);
	}

	public function raw_wanip()
	{
		$gpon_onu = $this->input->post('data');
		$request = $this->api->raw_wanip($gpon_onu);



		echo json_encode([
			"data" => "<pre>$request[raw]</pre>",
			"header" => "WAN IP gpon-onu_" . $gpon_onu,
			"status" => true,
		]);
	}

	public function raw_detailinfo()
	{
		$gpon_onu = $this->input->post('data');
		$request = $this->api->raw_detailinfo($gpon_onu);

		echo json_encode([
			"data" => "<pre>$request[body]</pre>",
			"header" => "Showing Detail Information gpon-onu_" . $gpon_onu,
			"tes" => $request['results'],
			"status" => true,
		]);
	}
	
	public function raw_card()
	{
		$request = $this->api->raw_show_card();

		echo json_encode([
			"data" => "<pre>$request</pre>",
			"header" => "Showing Card",
			"status" => true,
		]);
	}
	public function raw_vlan_summary()
	{
		$request = $this->api->raw_show_vlan_summary();

		echo json_encode([
			"data" => "<pre>$request</pre>",
			"header" => "Showing VLAN Summary",
			"status" => true,
		]);
	}
	public function raw_gpon_onu_profile_vlan()
	{
		$request = $this->api->raw_show_gpon_onu_profile_vlan();

		echo json_encode([
			"data" => "<pre>$request</pre>",
			"header" => "Showing GPON ONU VLAN Profile",
			"status" => true,
		]);
	}

	public function get_profile_vlan()
	{
		ini_set('max_execution_time', 1200);
		
		$data = $this->api->raw_show_gpon_onu_profile_vlan();

		// $data = "\r\nProfile name:  netmedia143\r\nTag mode:      tag\r\nCVLAN:         143\r\nCVLAN priority:7\r\n\r\nProfile name:  netmedia142\r\nTag mode:      tag\r\nCVLAN:         142\r\nCVLAN priority:7\r\n\r\nZXAN#";

		$matches = array();
		preg_match_all('/Profile name:\s+(\S+)\s+Tag mode:\s+(\S+)\s+CVLAN:\s+(\d+)\s+CVLAN priority:(\d+)/', $data, $matches, PREG_SET_ORDER);

		$profiles = array();
		$options = "<option>select VLAN</option>";

		foreach ($matches as $match) {
			$profile = new stdClass();
			$profile->profile_name = $match[1];
			$profile->tag_mode = $match[2];
			$profile->cvlan = (int)$match[3];
			$profile->cvlan_priority = (int)$match[4];
			$profiles[] = $profile;
			$options .= "<option value='".(int)$match[3]."'>".$match[1]."</option>";
		}



		echo json_encode([
			"data" => $profiles,
			"select_option" => $options,
			"header" => "Showing GPON ONU VLAN Profile",
			"status" => true,
		]);
	}

	public function raw_gpon_profile_tcont()
	{
		$request = $this->api->raw_show_gpon_profile_tcont();

		echo json_encode([
			"data" => "<pre>$request</pre>",
			"header" => "Showing GPON Profile TCONT",
			"status" => true,
		]);
	}
	public function raw_gpon_profile_traffic()
	{
		$request = $this->api->raw_show_gpon_profile_traffic();

		echo json_encode([
			"data" => "<pre>$request</pre>",
			"header" => "Showing GPON Profile Traffic",
			"status" => true,
		]);
	}
	public function raw_onu_type()
	{
		$request = $this->api->raw_show_onu_type();

		echo json_encode([
			"data" => "<pre>$request</pre>",
			"header" => "Showing GPON ONU Type",
			"status" => true,
		]);
	}
	public function raw_ip_route()
	{
		$request = $this->api->raw_show_ip_route();

		echo json_encode([
			"data" => "<pre>$request</pre>",
			"header" => "Showing IP Route",
			"status" => true,
		]);
	}

	public function reboot()
	{
		$gpon_onu = $this->input->post('gpon_onu');
		$request = $this->api->reboot($gpon_onu);

		echo json_encode([
			"message" => $request->message,
			"status" => true,
		]);
	}

	public function restore_factory()
	{
		$gpon_onu = $this->input->post('gpon_onu');
		$request = $this->api->restore_factory($gpon_onu);

		echo json_encode([
			"message" => $request->message,
			"status" => true,
		]);
	}


	public function remote_onu()
	{
		$gpon_onu = $this->input->post('gpon_onu');
		$remote_state = $this->input->post('remote_state');
		$host_id = $this->input->post('host_id');

		$request = $this->api->remote_onu($gpon_onu, $remote_state, $host_id);

		$state = ($remote_state == 'enable') ? 'enabled' : 'disabled';

		if ($request->ip_address == '0.0.0.0') {
			//ambil ip dari router berdasarkan username pppoe
			$userPpp = $this->db->query("SELECT username FROM pelanggan WHERE gpon_onu='$gpon_onu'")->row()->username;

			$getActiveConnection = $this->routermodel->get_ppp_ip_address($userPpp);
			$ipRemote='';
			foreach ($getActiveConnection as $key) {
				$ipRemote = $key['address'];
			}

		} else {
			$ipRemote = $request->ip_address;
		}

		$query = $this->db->query("UPDATE pelanggan 
		SET ip_address='$request->ip_address', remote_web_state='$state'
		WHERE gpon_onu='$gpon_onu'");

		$link = '';

		if ($remote_state == 'enable')
		{
			$statusIp = $this->_is_public_ip_access();

			if ($statusIp['public_access']) {
				$link = 'http://'. $statusIp['remote_ip'] . ":" . $this->ros['PORT_REMOTEWEB'];
				$this->routermodel->setPublicRemoteOnt(
					(object) [
						'to-addresses' => $ipRemote,
					],$ipRemote
				);
			} else if ($statusIp['public_access'] == false && $_SERVER['HTTP_HOST'] != 'localhost' && $statusIp['message'] != 'Accessed locally') {
				$link = 'http://'. $statusIp['remote_ip'] . ":" . $this->ros['PORT_REMOTEWEB'];
				$this->routermodel->setPublicRemoteOnt(
					(object) [
						'to-addresses' => $ipRemote,
					],$ipRemote
				);
			} else {
				$link = "http://$ipRemote";
			}
		}

		echo json_encode([
			"message" => $request->message,
			"link" => $link,
			"status" => true,
		]);
	}

	private function _is_public_ip_access()
	{
		$server_ip = $_SERVER['SERVER_ADDR'] ?? gethostbyname(gethostname());
		// $server_ip = $_SERVER['SERVER_ADDR'] ?? gethostbyname($_SERVER['HTTP_HOST']);
		// $remote_ip = $_SERVER['HTTP_HOST'] ?? $_SERVER['REMOTE_ADDR'] ?? null;
		$remote_ip = $_SERVER['HTTP_HOST'] ?? $_SERVER['REMOTE_ADDR'] ?? null;

		// If accessed from the same IP, it's local
		if ($server_ip == $_SERVER['REMOTE_ADDR']) {
			return ['public_access' => false, 'message' => 'Accessed locally', 'server_ip' => $server_ip, 'remote_ip' => $remote_ip];
		}
		// Remove port from remote_ip if present
		if (strpos($remote_ip, ':') !== false) {
			$remote_ip = explode(':', $remote_ip)[0];
		}
		// Check if remote IP is private
		if (filter_var($remote_ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
			return ['public_access' => true, 'message' => "Accessed via public IP",'server_ip' => $server_ip, 'remote_ip' => $remote_ip];
		} else {
			return ['public_access' => false, 'message' => "Accessed via private IP",'server_ip' => $server_ip, 'remote_ip' => $remote_ip];
		}
	}


	

	/***
	 * CHECK ONT MANUAL DELETE
	***/ 

	public function restore_config_ont($sn_uncfg)
	{
		//check SN ont Uncfg apakah sama dengan yang ada di database
		$checkSN = $this->db->query("SELECT id_pelanggan, nama_pelanggan, serial_number,gpon_olt, nama_paket, id_paket, vlan_profile, cvlan
				FROM v_pelanggan
				WHERE serial_number = '$sn_uncfg'");
		// jika ditemukan, ambil data lama dari database kemudian konfig ulang di olt
		if ($checkSN->num_rows() > 0 ) {
			$dt = $checkSN->row();
			$data = array(
				'gpon_olt' => $dt->gpon_olt, 
				'onu_type' => $dt->onu_type, 
				'sn' => $dt->serial_number, 
				'name' => $dt->name, 
				'description' => $dt->description, 
				'ppp_profile' => $dt->ppp_profile, 
				'access_mode' => $dt->access_mode, 
				'vlan_profile' => $dt->vlan_profile, 
				'cvlan' => $dt->cvlan, 
			);

			return $data;

		}
	}

	/*** ONU STATE berjalan setiap 5 menit dan melaporkan status ont ke database
 * 
 */
	public function onustate()
	{
		$gpon_olt = $this->input->get('gpon_olt');
		$request = $this->api->gpon_onu_state($gpon_olt='');

		$los = $offline = $online = array();
		$interfaces = array();

		foreach ($request->data as $row) {
			$interface = explode(":",$row->onu_index)[0];

			if (!in_array($interface,$interfaces)){
				array_push($interfaces, $interface);
			}

			if ($row->phase_state == "LOS" || $row->phase_state == "syncMib") {
				$los[] = $row->onu_index;
			} 
			
			if ($row->phase_state == "DyingGasp" || $row->phase_state == "offline" || $row->phase_state == "syncMib") {
				$offline[] = $row->onu_index;
			}
			
			if($row->phase_state == "working") {
				$online[] = $row->onu_index;
			}
			
			$this->api->update_pelanggan(array('gpon_onu' => $row->onu_index), array('ont_phase_state' => $row->phase_state));
		}

		
		//kosongkan tabel
		$this->db->truncate("olt_interfaces");
		foreach ($interfaces as $gpon_olt) {
			$this->db->insert('olt_interfaces',['gpon_olt' => $gpon_olt]);
			$this->db->insert_id();
		}

		echo json_encode([
			"offline" 		=> "offline ".count($offline),
			"online" 		=> "online " . count($online),
			"los" 			=> "LOS " . count($los),
			"total" 		=> "ont " . $request->onu_number,
			"status" 		=> "true",
			"interfaces" 	=> $interfaces,
			"no_internet"	=> $this->db->get('v_no_internet')->num_rows(),
			// "baseinfo" => $onuBaseInfos,
		]);

	}

	/**
	 * get baseinfo untuk mengetahui sn mana yang tdk ada didalam database aplikasi tapi sudah di regis pada olt
	 * 
	 * */ 

	public function getUnsyncOnu(){
		//get all active interfaces
		$interfaces = $this->db->query("SELECT gpon_olt FROM olt_interfaces")->result();
		$snDatabase = $this->getSnFromDB();
		$founds = array();

		foreach ($interfaces as $gpon_olt) {
			$baseInfo = $this->baseinfo($gpon_olt->gpon_olt);
			foreach ($baseInfo as $sn) {
				if (!in_array($sn->auth_info,$snDatabase)) {
					$data = array(
						'serial_number' => $sn->auth_info,
						'gpon_onu' => $sn->gpon_onu,
						'onu_type' => $sn->type,
						'ont_phase_state' => 'Unconfigured',
					);
					//insert to pelanggan
					$this->db->insert('pelanggan',$data);
					$founds[] = $data;
					// echo $sn->auth_info.' | '.$sn->gpon_onu.' | '.$sn->type.' | '.$sn->mode;
				// 	// array_push($founds,$sn->auth_info);
				}
			}
		}

		echo json_encode([
			'founds' => $founds,
			'status' => true
		]);

	}

	public function closeAllRemoteWeb()
	{
		$res = $this->api->closeAllRemoteWeb();
		echo json_encode($res);
	}

	public function baseinfo($gpon_olt){
		return $this->api->raw_gpon_onu_baseinfo($gpon_olt);
		// echo json_encode($data);
	}

	public function getSnFromDB(){
		$snDatabase = $this->db->query("SELECT GROUP_CONCAT(serial_number) as sn FROM pelanggan")->row()->sn;
		$sn = explode(',',$snDatabase);
		return $sn;
		// echo json_encode($sn);
	}

	/* 
	FUNGSI UNTUK MERUBAH SEMUA KONEKSI PELANGGAN ke EXPIRE DI MIKROTIK
	*/

	public function setToExpire(){
		ini_set('max_execution_time', 1200);
		if ($this->ros['ROS_VERSION'] == 6) {
			// Run on ROS 6
			echo json_encode($this->routermodel->match_paket());
		} elseif ($this->ros['ROS_VERSION'] == 7) {
			// Run on ROS 7
			echo json_encode($this->routermodel->match_paket_rest());
		} else {
			echo json_encode(['error' => 'RouterOS version not match! Api_rest_client Line 875']);
		}
		

	}
	/* 
	FUNGSI PERPANJANGAN PAKET
	
	*/
	public function getExtendPaket() {
		$gpon_onu = $this->input->post('gpon_onu', TRUE);

		$res = $this->db->query("SELECT id_pelanggan, name, gpon_onu, onu_type, nama_paket, expired, telp
			FROM v_pelanggan
			WHERE gpon_onu = '$gpon_onu'")->row();

		echo json_encode([
			'name' => $res->name,
			'gpon_onu' => $res->gpon_onu,
			'onu_type' => $res->onu_type,
			'nama_paket' => $res->nama_paket,
			'expired' => $res->expired,
			'telp' => $res->telp,
		]);
	}

	public function setExtendPaket()
	{
		$data = (object) array(
			'gpon_onu' => $this->input->post('gpon_onu'),
			'expired' => $this->input->post('expired'),
			'username' => $this->session->username,
		);
		
		$extend = $this->api->extendThisPaket($data);
		
		echo json_encode($extend);
	}

	public function pon_power_onurx(){
		$interfaces = $this->db->query("SELECT SUBSTRING_INDEX(gpon_onu,':',1) as gpon_olt 
		FROM pelanggan
		GROUP BY SUBSTRING_INDEX(gpon_onu,':',1) ASC")->result();

		$no = 0;
		foreach ($interfaces as $d) {
			$data = $this->api->pon_power_onurx($d->gpon_olt);
			$this->_get_onurx($data);
			$no++;
		}

		echo json_encode(['message' => "Success updating ONU-RX total $no interfaces", 'status' => true]);
	}

	private function _get_onurx($gpon_olt){
		$rxPowerData = [];

		// Define a regular expression pattern
		$pattern = '/gpon-onu_(\d+\/\d+\/\d+:\d+)\s+(\S+)/';

		// Loop through the data array
		foreach ($gpon_olt as $line) {
			// Check if the line matches the pattern
			if (preg_match($pattern, $line, $matches)) {
				$onu = $matches[1]; // Extract ONU identifier
				$rxPower = (strpos($matches[2], '(dbm)')) ? str_replace('(dbm)','', $matches[2]) : $matches[2]; // Extract Rx power
				// $rxPowerData[$onu] = $rxPower;
				$this->db->query("UPDATE pelanggan SET onu_db='$rxPower' WHERE gpon_onu='$onu'");
				// echo "ONU: $onu, Rx Power: $rxPower<br>";
			}
		}
	}

	public function getReplaceOnt(){
		$gpon_onu = $this->input->post('gpon_onu');
		$res = $this->db->query("SELECT name, gpon_onu, onu_type, serial_number
			FROM v_pelanggan
			WHERE gpon_onu = '$gpon_onu'")->row();

		echo json_encode([
			'gpon_onu' => $res->gpon_onu,
			'name' => $res->name,
			'onu_type' => $res->onu_type,
			'serial_number' => $res->serial_number,
		]);
	}

	public function replaceOnt(){
		// $gpon_onu = '1/1/1:3';
		// $new_sn = 'ZTEGC7795C63';
		// $onu_type = 'ZTE-F660';
		$gpon_onu = html_escape($this->input->post('gpon_onu'));
		$onu_type = html_escape($this->input->post('rep_onutype'));
		$old_sn = html_escape($this->input->post('rep_old_sn'));
		$new_sn = html_escape($this->input->post('rep_new_sn'));

		$old_secret = $this->db->query("SELECT no_pelanggan, nama_pelanggan, username, mikrotik_profile, description, vlan_profile, cvlan, id_paket FROM v_pelanggan WHERE gpon_onu='$gpon_onu'")->row();
		
		$new_secret = $this->_make_ppp_secret($old_secret->no_pelanggan, $old_secret->nama_pelanggan, $new_sn);

		$tcont =  $this->db->query("SELECT tcont, gemport FROM paket WHERE id_paket=$old_secret->id_paket")->row();
		
		$data = array(
			'gpon_olt' => preg_split('/:/', $gpon_onu)[0],
			'onu_index' => preg_split('/:/', $gpon_onu)[1],
			'onu_type' => $onu_type,
			'sn' => $new_sn,
			'gpon_onu' => $gpon_onu,
			'name' => $new_secret->name,
			'description' => $old_secret->description,
			'username' => $new_secret->username,
			'password' => $new_secret->password,
			'mikrotik_profile' => $old_secret->mikrotik_profile,
			'vlan_profile' => $old_secret->vlan_profile,
			'cvlan' => $old_secret->cvlan,
			'tcont' => $tcont->tcont,
			'gemport' => $tcont->gemport,
		);

		// sebelum replace ont, onu lama akan dihapus di olt

		$remove_old_onu = $this->api->remove_onu($data['gpon_olt'], $data['onu_index']);

		
		if ($remove_old_onu->status) {
			if ($this->ros['ROS_VERSION'] == 6) {
				$remove_old_secret = $this->routermodel->remove_secret("$old_secret->username");
				$create_new_secret = $this->routermodel->create_ppp_secret($data['username'], $data['password'], 'pppoe', $data['mikrotik_profile']);
			} elseif ($this->ros['ROS_VERSION'] == 7) {
				$remove_old_secret = $this->routermodel->deleteRestSecret((object) array('name' => $old_secret->username));
				$create_new_secret = $this->routermodel->putRestSecret(
					(object) array(
						'name' 		=> $data['username'], 
						'password' 	=> $data['password'], 
						'service' 	=> 'pppoe', 
						'profile' 	=> $data['mikrotik_profile']
					));
			}
			
			// reconfig onu will delete old onu first, after that will config onu with different sn but same gpon_onu
			$reconfig_onu = $this->api->reconfig_onu($data);
			
			//update database to new configuratin
			$query = "UPDATE pelanggan SET 
				serial_number='" . $data['sn'] . "', 
				onu_type='" . $data['onu_type'] . "', 
				gpon_olt='" . $data['gpon_olt'] . "', 
				name='" . $data['name'] . "', 
				username='" . $data['username'] . "', 
				password='" . $data['password'] . "',
				ppp_profile='" . $data['mikrotik_profile'] . "',
				remote_web_state='disabled'
			WHERE gpon_onu='$gpon_onu'";

			$this->db->query($query);

			//save to Log
			$this->api->saveLogEvent('Replace ONT', "Success! OLD-SN=" . $old_sn ." to NEW-SN=". $new_sn . " by " . $this->session->username);

			echo json_encode($reconfig_onu);
		} else {
			echo json_encode(['status' => false]);
		}
				// echo json_encode($query);
		
	}

	/**
	 * @param no_pelanggan String
	 * @param nama_pelanggan String
	 * @param sn String
	 * 
	 */

	private function _make_ppp_secret($no_pelanggan, $nama_pelanggan, $sn){
		//buat username dengan kombinasi NOPEL+NAME+NEW-SN
		$name = $no_pelanggan.'. '.str_replace("'", "", $nama_pelanggan);
		// Ambil 10 karakter pertama dari $name dan ganti spasi dengan '-'
		$nameSubstring = str_replace(' ', '-', $name);
		$nameSubstring = substr($nameSubstring, 0, 15);
		// Ambil substring dari $sn mulai dari karakter ke-4
		$snSubstring = substr($sn, 4);

		// Username
		$username = $nameSubstring . $snSubstring;
		// Buat sandi dengan '.' di awal dan '!' di akhir
		$password = '.' . $snSubstring . '!';

		return (object) [
			'name' 		=> $name, 
			'username' 	=> $username, 
			'password' 	=> $password
		];
	}


	public function tickets(){
		$gpon_onu = html_escape($this->input->post('tic_gpon_onu'));
		$keluhan = html_escape($this->input->post('tic_keluhan'));

		$plgn = $this->db->query("SELECT no_pelanggan, nama_pelanggan, lokasi_map, wilayah, telp 
		FROM v_pelanggan 
		WHERE gpon_onu='$gpon_onu'")->row();

		$text = "GANGGUAN
		Maps: $plgn->lokasi_maps
		Nama: $plgn->no_pelanggan. $plgn->nama_pelanggan
		Kontak: $plgn->telp
		Wilayah: $plgn->wilayah
		Keluhan: $keluhan
		";

		echo json_encode([
			'status' => true,
			'teks' => $text,
		]);

	}

	public function getTicketsD(){
		$gpon_onu = $this->input->post('gpon_onu');
		// $keluhan = html_escape($this->input->post('tic_keluhan'));

		$plgn = $this->db->query("SELECT no_pelanggan, nama_pelanggan, lokasi_map, wilayah, telp, ont_phase_state 
		FROM v_pelanggan 
		WHERE gpon_onu='$gpon_onu'")->row();

		$icon = ($plgn->ont_phase_state == 'LOS') ? "\xF0\x9F\x9A\xA8" : "\xF0\x9F\x9A\xA8";

		$template = "%s *TICKET*
%s

*%s*
%s

%s
Ont Phase : %s
Ket	: ";

		$text = sprintf(
			$template,
			$icon,
			urldecode($plgn->lokasi_map),
			$plgn->no_pelanggan .'. '. $plgn->nama_pelanggan,
			$plgn->telp,
			$plgn->wilayah,
			$plgn->ont_phase_state
		);

		echo json_encode([
			'status' => true,
			'gpon' => $gpon_onu,
			'teks' => $text,
		]);

	}

	/***
	 * SEND TICKET TO TELEGRAM GROUP
	 */

	public function sendTicket(){
		$ticket = $this->input->post('tic_scripts');

		$response = $this->telegrambot->sendToGroup($ticket);

		echo json_encode([
			'status' => true,
			'data' => $response,
		]);
	}


	public function showInterfaces() {
		$q = $this->db->query("SELECT * FROM v_interfaces")->result();
		$row = '';
		$total_ont = $total_interface = 0;
		$table = "<table class=\"table\">
					<thead>
					<tr>
						<th>GPON_OLT</th>
						<th>ONU</th>
					</tr>
					</thead>
					<tbody>";
		foreach ($q as $d) {
			$row .= 
			"<tr>
				<td>$d->gpon_olt</td>
				<td>$d->ont</td>
			</tr>";
			$total_ont = $total_ont + $d->ont;
			$total_interface++;
		}

		$table .= $row . "<tr>
					<td>Interface Active $total_interface</td>
					<td>Total Onu $total_ont</td>
				</tr>
			</tbody>
		</table>";

		echo json_encode([
			"data" => "<pre>$table</pre>",
			"header" => "Showing Active Interfaces",
			"status" => true,
		]);
	}

	public function showLogEvent() {
		$q = $this->db->query("SELECT * FROM log ORDER BY id_log DESC LIMIT 100")->result();
		$row = '';
		$number = 0;
		$table = "
		<div class=\"table-responsive\" style=\"height:400px\">
		<table class=\"table table-hover\" id=\"log-table\">
					<thead>
					<tr>
						<th>#</th>
						<th>Time</th>
						<th>Topic</th>
						<th>Messages</th>
					</tr>
					</thead>
					<tbody>";
		$btn = '';
		foreach ($q as $d) {
			if ($d->topic == 'PSB') {
				$btn = "<button type=\"button\" class=\"btn btn-xs btn-outline btn-success\">$d->topic</button>";
				$time = $d->time;
				$msg = $d->message;
			}
			if ($d->topic == 'DELETE MANUAL') {
				$btn = "<button type=\"button\" class=\"btn btn-xs btn-outline btn-warning\">$d->topic</button>";
				$time = $d->time;
				$msg = $d->message;
			}
			if ($d->topic == 'DELETE PERMANENT') {
				$btn = "<button type=\"button\" class=\"btn btn-xs btn-outline btn-danger\">$d->topic</button>";
				$time = $d->time;
				$msg = $d->message;
			}
			if ($d->topic == 'Payment') {
				$btn = "<button type=\"button\" class=\"btn btn-xs btn-outline btn-default\">$d->topic</button>";
				$time = $d->time;
				$msg = $d->message;
			}
			if ($d->topic == 'Replace ONT') {
				$btn = "<button type=\"button\" class=\"btn btn-xs btn-outline btn-warning\">$d->topic</button>";
				$time = $d->time;
				$msg = $d->message;
			}
			if ($d->topic == 'MIDTRANS' || $d->topic == 'MIDTRANS-TESTMODE') {
				$btn = "<button type=\"button\" class=\"btn btn-xs btn-outline btn-info\">$d->topic</button>";
				$msg = json_decode($d->message, true);
				$time = $msg['settlement_time'];
				if ($msg['payment_type'] == 'bank_transfer') {
					foreach (json_decode($msg['va_numbers']) as $d) {
						$type = strtoupper("$d->bank  $d->va_number");
					}
				} else if ($msg['payment_type'] == 'cstore') {
					$type = strtoupper($msg['store'] . ' ' . $msg['payment_code']);
				} else if ($msg['payment_type'] == 'qris') {
					$type = strtoupper($msg['issuer']);
				} else {
					$type = strtoupper($msg['payment_type']);

				}
				$msg = "<strong>$msg[order_id]</strong>" . ' ' . ucwords($msg['transaction_status']) . " | " . ucwords($msg['payment_type']) . " $type";
			}

			$row .= 
			"<tr>
				<td>$number</td>
				<td>$time</td>
				<td>$btn</td>
				<td>$msg</td>
			</tr>";
			$number++;
		}

		$table .= $row . "
			</tbody>
		</table></div>";

		echo json_encode([
			"data" => $table,
			"header" => "Showing Log Activity",
			"status" => true,
		]);
	}


	public function changeSsid($mode){
		
		
		if ($mode == 'wpa_key') {
			$data = array(
				'mode' => $mode,
				'gpon_onu' => $this->input->post('cs_gpon_onu'),
				'wpa_key' => $this->input->post('wpa_keyx'),
				'ssid' => 'ZTE' //
			);

			$reply = $this->api->changeSsidOlt($data);
			echo json_encode($reply);
			
		} elseif ($mode == 'ssid') {
			$data = array(
				'mode' => $mode,
				'gpon_onu' => $this->input->post('cs_gpon_onu'),
				'ssid' => $this->input->post('ssidy'),
				'wpa_key' => '12345678'
			);

			$reply = $this->api->changeSsidOlt($data);
			echo json_encode($reply);

		} elseif ($mode == 'both') {
			$data = array(
				'mode' => $mode,
				'gpon_onu' => $this->input->post('cs_gpon_onu'),
				'ssid' => $this->input->post('ssidz'),
				'wpa_key' => $this->input->post('wpa_keyz'),
			);

			$reply = $this->api->changeSsidOlt($data);
			echo json_encode($reply);
		}

	}

	public function noInternet() {
		$q = $this->db->get('v_no_internet');
		$data = $q->result();
		$status = ($q->num_rows() > 0) ? true : false;

		$table = "<table class=\"table table-hover\">
					<thead>
					<tr>
						<th>Interface</th>
						<th>Nama Pelanggan</th>
						<th>Cause</th>
					</tr>
					</thead>
					<tbody>";
		$no = 0;
		$row = '';
		foreach ($data as $d) {
			$row .= 
			"<tr>
				<td><a href=\"javascript:void(0)\" class=\"btn btn-xs btn-default\" onclick=\"gotoSearch('$d->gpon_onu')\">$d->gpon_onu</a></td>
				<td>$d->no_pelanggan. $d->nama_pelanggan</td>
				<td>ONT <span class=\"label label-primary\">$d->ont_phase_state</span> but Internet <span class=\"label label-default\">$d->active_connection</span></td>
			</tr>";
			$no++;
		}

		$table .= $row . "<tr>
					<td>Total</td>
					<td>$no Disconnected</td>
					<td></td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td colspan=\"3\"><pre>Check WAN IP, ONT Attenuation & ONU Detail Information</pre> </td>
				</tr>
			</tbody>
		</table>";

		echo json_encode([
			"data" => $table,
			"header" => "Showing Client with NO INTERNET ACCESS!",
			"status" => $status,
		]);
	}

	/*
	CARA MEMASUKAN DATA PELANGGAN OLT MELALUI FILE .DAT
	DAN MENGUPDATE DATA PELANGGAN DI DATABASE
	*/

	public function import_olt_config()
	{
		$file = FCPATH . 'assets/posonet/startrun.dat';
		// Membaca isi file
		$data = file_get_contents($file); 

		// Menggunakan regular expression untuk mengambil data
		preg_match_all('/interface gpon-onu_(.*?)\n\s+name (.*?)\n\s+description (.*?)\n/s', $data, $matches, PREG_SET_ORDER);


		$count = 0;
		$hasil = array();
		// Menampilkan hasil
		foreach ($matches as $match) {
			$hasil[] = (object) array(
				'interface' => trim($match[1]),
				'no_pelanggan' => $this->takeNoPelanggan(trim($match[2])),
				'name' => trim($match[2]),
				'description' => trim($match[3]),
				'mode' => '',
				'username' => '',
				'password' => '',
				'vlan_profile' => '',
				'type' => '',
				'sn' => '',
			);
			$count++;
		}

		$ponmng = $this->get_pon_mng();

		foreach ($hasil as &$item1) {
			foreach ($ponmng as $item2) {
				if ($item1->interface === $item2->interface) {
					$item1->mode = $item2->mode;
					$item1->username = $item2->username;
					$item1->password = $item2->password;
					$item1->vlan_profile = $item2->vlan_profile;
					$item1->type = $item2->type;
					$item1->sn = $item2->sn;
					break;
				}
			}
		}

		return $hasil;
		// echo json_encode($hasil);
	}

	public function get_pon_mng()
	{
		$file = FCPATH . 'assets/posonet/startrun.dat';
		// Membaca isi file
		$data = file_get_contents($file);

		$pattern = '/pon-onu-mng gpon-onu_(.*?)\n.*?mode (.*?) username (.*?) password (.*?!).*?vlan-profile (.*?) /s';
		preg_match_all($pattern, $data, $matches, PREG_SET_ORDER);

		// $hasil = array();

		foreach ($matches as $match) {
			$getSerial = $this->takeSerialNumber($match[4]);
			$type = $getSerial->type;
			$sn = $getSerial->sn;

			$row[] = (object) array(
				'interface' => $match[1],
				'mode' => $match[2],
				'username' => $match[3],
				'password' => $match[4],
				'vlan_profile' => $match[5],
				'type' => $type,
				'sn' => $sn,
			);
		}
		// echo json_encode($hasil);
		return $row;
	}

	public function takeNoPelanggan($name = "")
	{
		// $name = "503 HASAN LAIBE";
		$pattern = '/^\d+/'; // Pola pencocokan untuk nomor pelanggan (hanya digit di awal string)

		if (preg_match($pattern, $name, $matches)) {
			return $matches[0];
		} else {
			return "";
		}
	}

	public function takeSerialNumber($password = "")
	{
		// $password = ".C91CC378!";
		$query = str_replace(array('.', '!'), '', $password);
		$file = FCPATH . 'assets/posonet/startrun.dat';
		// Membaca isi file
		$data = file_get_contents($file);

		$pattern = "/\s+ onu (.*?) type (.*?) sn (\w*" . preg_quote($query) . "$)/m";

		if (preg_match($pattern, $data, $matches)) {
			return (object) array(
				'onu' => $matches[1],
				'type' => $matches[2],
				'sn' => $matches[3],
			);
		}
	}

	public function parsingKedatabase()
	{
		$query = $this->db->query("SELECT id_pelanggan, no_pelanggan, nama_pelanggan
				FROM pelanggan
				WHERE status != 'NONAKTIF'
				ORDER BY no_pelanggan ASC")->result();

		$olt = $this->import_olt_config();
		$baris = array();
		foreach ($query as $row) {
			foreach ($olt as $row2) {
				if ($row->no_pelanggan === $row2->no_pelanggan) {
					// echo $row->nama_pelanggan . " = " . "$row2->name $row2->interface $row2->description $row2->mode $row2->username $row2->password $row2->type $row2->sn $row2->vlan_profile" . "<br>";
					$baris = array(
						'gpon_olt' => explode(":", $row2->interface)[0],
						'gpon_onu' => $row2->interface,
						'name' => $row2->name,
						'description' => $row2->description,
						'access_mode' => $row2->mode,
						'username' => $row2->username,
						'password' => $row2->password,
						'onu_type' => $row2->type,
						'serial_number' => $row2->sn,
						'vlan_profile' => $row2->vlan_profile,
						'remote_web_state' => 'disabled',
					);
					$this->api->update_pelanggan(array('id_pelanggan' => $row->id_pelanggan), $baris);
				}
			}
		}
	}




	public function newcode($wil)
	{
		// $nopel = $this->db->query("SELECT group_concat(no_pelanggan) AS no_pelanggan FROM pelanggan WHERE id_wilayah='$wil'")->row()->no_pelanggan;
		// $kodewil = $this->db->query("SELECT kode_wilayah FROM wilayah WHERE id_wilayah='$wil'")->row()->kode_wilayah;
		// $data = explode(',', $nopel);
		// $kdMin = $kodewil . '00';
		// $kdMax = $kodewil . '99';
		// $last = max($data);
		
		
		// ex
		$kodewil = 0;
		$data = range(0,98);
		$kdMin = $kodewil . '00';
		$kdMax = $kodewil . '99';
		$last = max($data);
		

		if ($data != null) {
			$missingNumber = [];
			for ($i = $kdMin; $i < $last; $i++) { 
				if (!in_array($i,$data)) {
					array_push($missingNumber, $i);
				}
			}

			if ($missingNumber == null) { 
				if ($last >= $kdMax) {
					echo "mencapai batas max";
					exit;
				} else {
					echo $last + 1;
				}
			} else { 
				echo $missingNumber[0];
			}

			// echo json_encode($missingNumber);
		} else {
			echo $kodewil.'01';
		}
		
	}

	public function reconnectwa(){
		// echo json_encode($this->kirimwa->get_device());
		echo json_encode($this->kirimwa->reconnectwa());
	}

	public function pesan(){
		$this->load->model('api_telegrambot_model','telegrambot');

		// $res = $this->telegrambot->getUpdates();
		// $res = $this->telegrambot->getUp();
		// $res = $this->telegrambot->sendMessage();
		// $res = $this->telegrambot->sendMessages();
		$res = $this->telegrambot->sendKontak();
		echo $res;
	}

	public function nodewa(){
		$this->load->model('Api_whatsapp_model','nodewa');
		$data = "⏰ Perubahan Masa aktif Paket
Name : 112. DIDIK SETYADI
Profile : UPTO-15M
Expired to : 2023-11-20
Tgl Input : 2023-10-19 08:49:08";
		echo json_encode($this->nodewa->sendWa($data));
	}

	public function getsecrets(){
		// echo json_encode($this->routermodel->get_ppp_secret());
		$this->routermodel->change_ppp_secret_profile_by_id('*F8','Expired');
	}

	public function testnodewa(){
		// $this->load->model('Api_whatsapp_model','nodewa');
		$data = "⏰ Perubahan Masa aktif Paket
Name : 112. DIDIK SETYADI
Profile : UPTO-15M
Expired to : 2023-11-20
Tgl Input : 2023-10-19 08:49:08";
		echo json_encode($data);
	}


}
