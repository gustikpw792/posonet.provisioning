<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require 'vendor/autoload.php';

use GuzzleHttp\Client;

class Api_rest_client_model extends CI_Model
{

  private $_client;

  function __construct()
  {
    parent::__construct();
    $this->load->database();

    $this->load->model('api_kirimwaid_model','kirimwa');
    $this->load->model('api_telegrambot_model','telegram');

    $this->olt = $this->config->item('olt');

    $this->_client = new Client([
      'base_uri' => $this->olt['BASE_URI']
    ]);

    $this->mikrotik = $this->config->item('mikrotik');

    $this->_mikrotik = new \RouterOS\Config([
      'host' => $this->mikrotik['HOST'],
      'user' => $this->mikrotik['USERNAME'],
      'pass' => $this->mikrotik['PASSWORD'],
      'port' => $this->mikrotik['PORT'],
    ]);
  }

  public function backup_config_olt(){
    $response = $this->_client->request('POST', 'backup_cfg', [
      'form_params' => [
        'routerip' => $this->mikrotik['HOST'],
        'routeruser' => $this->mikrotik['USERNAME'],
        'routerpass' => $this->mikrotik['PASSWORD'],
      ]
    ]);

    return json_decode($response->getBody());
  }

  public function pon_onu_uncfg(){
    $response = $this->_client->get('unconfig');
    return json_decode($response->getBody());
  }

  public function create_onu($data)
  {
    // find mikrotik profile by id_paket in database
    $profile = $this->db->query("SELECT mikrotik_profile FROM paket WHERE id_paket = " . $data['id_paket'])->row();

    $response = $this->_client->request('POST', 'onuadd', [
      'form_params' => [
        'gpon_olt' => $data['gpon_olt'],
        'onu_type' => $data['onu_type'],
        'sn' => $data['serial_number'],
        'name' => str_replace("'", "", $data['no_pelanggan'] . '. ' . $data['nama_pelanggan']),
        'description' => 'p=' . $profile->mikrotik_profile . ' &e=' . date('d/m/Y', strtotime($data['expired'])) . ' &h=0' . ' &a=' . $data['access_mode'],
        'ppp_profile' => $profile->mikrotik_profile,
        'access_mode' => $data['access_mode'],
        'vlan_profile' => $data['vlan_profile'],
        'cvlan' => $data['cvlan'],
        'tcont' => $data['tcont'],
        'gemport' => $data['gemport'],
      ]
    ]);

    return json_decode($response->getBody());
    // return $response->getBody();
  }



  public function reconfig_onu($data){
      $response = $this->_client->request('POST', 'reconfig_onu', [
        'form_params' => (array) $data
      ]);

      /*  [
          'gpon_olt' => $data['gpon_olt'],
          'onu_index' => $data['onu_index'],
          'onu_type' => $data['onu_type'],
          'sn' => $data['sn'],
          'gpon_onu' => $data['gpon_onu'],
          'name' => $data['name'],
          'description' => $data['description'],
          'username' => $data['username'],
          'password' => $data['password'],
          'vlan_profile' => $data['vlan_profile'],
          'cvlan' => $data['cvlan'],
          'tcont' => $data['tcont'],
          'gemport' => $data['gemport'],
        ] */

      return json_decode($response->getBody());
  }


  public function remove_onu($gpon_olt, $onu_index)
  {
    $response = $this->_client->request('DELETE', 'remove_onu', [
      'form_params' => [
        'gpon_olt' => $gpon_olt,
        'onu_index' => $onu_index
      ]
    ]);

    return json_decode($response->getBody());
  }



  public function delete_onu($gpon_onu, $username)
  {
    $interface = preg_split('/:/', $gpon_onu);

    $response = $this->_client->request('DELETE', 'no_onu', [
      'form_params' => [
        'gpon_olt' => $interface[0],
        'onu_index' => $interface[1],
        'username' => $username
      ]
    ]);

    return json_decode($response->getBody());
  }



  public function raw_attenuation($gpon_onu)
  {

    $response = $this->_client->request('GET', 'getrawponpower', [
      'form_params' => [
        'interface' => $gpon_onu,
      ]
    ]);

    $request = $response->getBody();

    $pattern = "/OLT/";

    if (preg_match($pattern, $request)) {
			$rxOnu = $rxOlt = 'N/A';
			if (preg_match("/Rx:([-\d.]+)/", $request, $matches)) {
				$rxOnu = trim($matches[1]);
			}
			if (preg_match("/Rx :([-\d.]+)/", $request, $matches)) {
				$rxOlt = trim($matches[1]);
			}
			$update = $this->update_pelanggan(array('gpon_onu' => $gpon_onu), array('onu_db' => $rxOnu));
			
		}

    return ['raw' => $response->getBody(), 'rx_onu' => $rxOnu, 'rx_olt' => $rxOlt];
  }

  public function raw_gpon_onu_baseinfo($gpon_olt)
  {

    $response = $this->_client->request('GET', 'getrawgponbaseinfo', [
      'form_params' => [
        'interface' => $gpon_olt,
      ]
    ]);

    // $data = json_decode($response->getBody());
    $data = $response->getBody();

    // Pisahkan baris-baris teks menjadi array
    $lines = explode("\r\n", $data);

    $lines = array_slice($lines, 3, count($lines) - 4);
    // Inisialisasi array untuk menyimpan data
    $onuData = array();

    // Loop melalui setiap baris, mengambil data yang diperlukan
    foreach ($lines as $line) {
        // Hilangkan spasi ganda dan pecah baris menjadi kolom
        $columns = preg_split('/\s+/', trim($line));

        // Pastikan baris memiliki cukup kolom (minimal 5)
        if (count($columns) >= 5) {
            // Ambil data yang diperlukan
            $onuIndex = $columns[0];
            $type = $columns[1];
            $mode = $columns[2];
            $authInfo = $columns[3];
            $state = $columns[4];

            // Tambahkan data ke array
            $onuData[] = (object) [
                'onu_index' => explode(":",$onuIndex)[1],
                'gpon_onu' => str_replace("gpon-onu_","",$onuIndex),
                'type' => $type,
                'mode' => $mode,
                'auth_info' => str_replace("SN:","",$authInfo),
                'state' => $state,
            ];
        }
    }
    return $onuData;
  }

  public function pon_power_onurx($gpon_olt)
  {

    $response = $this->_client->request('GET', 'getponpowerbyinterface', [
      'form_params' => [
        'interface' => $gpon_olt,
      ]
    ]);

    $data = json_decode($response->getBody());

    $lines = explode("\r\n", $data);

    return $lines;
  }

  public function raw_iphost($gpon_onu, $host_id)
  {

    $response = $this->_client->request('GET', 'rawiphost', [
      'form_params' => [
        'gpon_onu' => $gpon_onu,
        'host_id' => $host_id,
      ]
    ]);

    return $response->getBody();
  }

  public function raw_onu_run_cfg($gpon_onu)
  {

    $response = $this->_client->request('GET', 'rawonurun', [
      'form_params' => [
        'gpon_onu' => $gpon_onu,
      ]
    ]);

    return $response->getBody();
  }

  public function raw_onu_run_conf_interface($gpon_onu)
  {

    $response = $this->_client->request('GET', 'rawonurunconfinterface', [
      'form_params' => [
        'gpon_onu' => $gpon_onu,
      ]
    ]);

    return $response->getBody();
  }
  
  public function raw_wanip($gpon_onu)
  {

    $response = $this->_client->request('GET', 'rawwanip', [
      'form_params' => [
        'gpon_onu' => $gpon_onu,
      ]
    ]);

    $request = $response->getBody();

    $pattern = "/WAN index/";
    $username = $password = $currentIP = '';

		if (preg_match($pattern, $request)) {
			if (preg_match("/Username:\s+([\w.-]+)/", $request, $matches)) {
				$username = $matches[1];
			}

			if (preg_match("/Password:\s+([\w.!]+)/", $request, $matches)) {
				$password = $matches[1];
			}

			if (preg_match("/Current IP:\s+([\d.]+)/", $request, $matches)) {
				$currentIP = $matches[1];
			}

			$update = $this->update_pelanggan(array('gpon_onu' => $gpon_onu), array('username' => $username, 'password' => $password, 'ip_address' => $currentIP));
		}

    return ['raw' => $response->getBody(),'username' => $username,'password' => $password,'current_ip' => $currentIP];
  }

  public function raw_detailinfo($gpon_onu)
  {

    $response = $this->_client->request('GET', 'rawdetailinfo', [
      'form_params' => [
        'gpon_onu' => $gpon_onu,
      ]
    ]);
    $data = $response->getBody();

    //save data to database
    $lines = explode("\n", $data);

    $result = [];
    foreach ($lines as $line) {
      $line = trim($line);

      if (strpos($line, 'Name:') === 0) {
        $result['Name'] = trim(substr($line, 5));
      } elseif (strpos($line, 'Type:') === 0) {
        $result['Type'] = trim(substr($line, 5));
      } elseif (strpos($line, 'Serial number:') === 0) {
        $result['SerialNumber'] = trim(substr($line, 14));
      } elseif (strpos($line, 'Description:') === 0) {
        $result['Description'] = trim(substr($line, 12));
      } elseif (strpos($line, 'ONU Distance:') === 0) {
        $result['ONUDistance'] = trim(substr($line, 13));
      } elseif (strpos($line, 'Phase state:') === 0) {
        $result['phase_state'] = trim(substr($line, 12));
      } elseif (strpos($line, 'Online Duration:') === 0) {
        $result['online_duration'] = trim(substr($line, 16));
      } elseif (strpos($line, 'ONU interface:') === 0) {
        $result['onu_interface'] = trim(substr($line, 14));
      }
    }

    $this->db->query("UPDATE pelanggan SET name='$result[Name]', onu_type='$result[Type]', serial_number='$result[SerialNumber]', description='$result[Description]', distance='$result[ONUDistance]'
    WHERE gpon_onu='$gpon_onu'");

    return ['body' => $data, 'results' => $result];
  }

  public function raw_show_card()
  {
    $response = $this->_client->request('GET', 'rawshowcard');
    return json_decode($response->getBody());
  }
  public function raw_show_vlan_summary()
  {
    $response = $this->_client->request('GET', 'rawshowvlansummary');
    return json_decode($response->getBody());
  }
  public function raw_show_gpon_onu_profile_vlan()
  {
    $response = $this->_client->request('GET', 'rawshowgpononuprofilevlan');
    return json_decode($response->getBody());
  }
  public function raw_show_gpon_profile_tcont()
  {
    $response = $this->_client->request('GET', 'rawshowgponprofiletcont');
    return json_decode($response->getBody());
  }
  public function raw_show_gpon_profile_traffic()
  {
    $response = $this->_client->request('GET', 'rawshowgponprofiletraffic');
    return json_decode($response->getBody());
  }
  public function raw_show_onu_type()
  {
    $response = $this->_client->request('GET', 'rawshowonutype');
    return json_decode($response->getBody());
  }
  public function raw_show_ip_route()
  {
    $response = $this->_client->request('GET', 'rawshowiproute');
    return json_decode($response->getBody());
  }
  public function raw_show_gpon_onu_state_by($gpon_olt)
  {
    $response = $this->_client->request('POST', 'rawshowgpononustateby', [
      'form_params' => [
        'gpon_olt' => $gpon_olt,
      ]
    ]);
    return json_decode($response->getBody());
  }
  
  public function reboot($gpon_onu)
  {

    $response = $this->_client->request('POST', 'reboot', [
      'form_params' => [
        'gpon_onu' => $gpon_onu,
      ]
    ]);

    return json_decode($response->getBody());
  }

  public function restore_factory($gpon_onu)
  {

    $response = $this->_client->request('POST', 'restore_factory', [
      'form_params' => [
        'gpon_onu' => $gpon_onu,
      ]
    ]);

    return json_decode($response->getBody());
  }

  public function remote_onu($gpon_onu, $remote_state, $host_id)
  {

    $response = $this->_client->request('POST', 'remote_onu', [
      'form_params' => [
        'gpon_onu' => $gpon_onu,
        'remote_state' => $remote_state,
        'host_id' => $host_id,
      ]
    ]);

    return json_decode($response->getBody());
  }

  public function gpon_onu_state_by($interface)
  {
    if ($interface != "") {
      $response = $this->_client->request('GET', 'onustateby', [
        'form_params' => [
          'gpon_olt' => $interface,
        ]
      ]);
    } else {
      $response = $this->_client->request('GET', 'onustate');
    }

    return json_decode($response->getBody());
  }

  public function gpon_onu_state($interface)
  {
    if ($interface != "") {
      $response = $this->_client->request('GET', 'onustate', [
        'form_params' => [
          'gpon_olt' => $interface,
        ]
      ]);
    } else {
      $response = $this->_client->request('GET', 'onustate');
    }

    return json_decode($response->getBody());
  }
  
  public function checkOnuBySN($sn) {
    $response = $this->_client->request('GET', 'rawshowgpononubysn', [
      'form_params' => [
        'sn' => $sn,
        ]
      ]);

    $data = json_decode($response->getBody());
    $pattern = '/gpon-onu_(\d+\/\d+\/\d+:\d+)/';
    preg_match_all($pattern, $data, $matches, PREG_SET_ORDER);
    $result = array();

    foreach ($matches as $match) {
      $result['gpon_onu'] = $match[1];
      $result['gpon_olt'] = explode(':', $match[1])[0];
    }
    return [$result];
  }

  public function changeSsidOlt($data)
  {

    $response = $this->_client->request('POST', 'wpa', [
      'form_params' => [
        'gpon_onu' => $data['gpon_onu'],
        'mode' => $data['mode'],
        'ssid' => $data['ssid'],
        'wpa_key' => $data['wpa_key'],
      ]
    ]);

    return json_decode($response->getBody());
  }

  public function perpanjangPaketFromDetailSetoran($no_pelanggan, $expired, $wamode=false) {
    $qry = $this->db->query("SELECT gpon_onu FROM pelanggan WHERE no_pelanggan='$no_pelanggan'")->row();
    // return [$qry->gpon_onu, $expired];
    $data = (object) array(
			'gpon_onu' => $qry->gpon_onu,
			'expired' => $expired,
			'username' => $this->session->username,
		);

    return $this->extendThisPaket($data, $wamode);
  }

  public function extendThisPaket($data, $wamode=false) {
    
    /*
		* cek tgl sekarang. jika belum lewat tgl expire yg telah ditentukan maka cukup update kolom expired di database.
		* atau cek di tabel v_expired apakan data gpon_onu exist
		*/

    $exp = $this->db->query("SELECT * FROM v_expired WHERE gpon_onu = '$data->gpon_onu'");
    
    $cust = $exp->row();

    if ($exp->num_rows() > 0) {
      //kondisi jika perpanjang sesudah expire
      // update expired
      $updateExp = $this->db->query("UPDATE pelanggan SET expired='$data->expired' WHERE gpon_onu='$data->gpon_onu'");
      // ubah secret dari Expire ke paket asli
      if ($this->mikrotik['ROS_VERSION'] == 6) {
        $restore_paket = $this->routermodel->change_ppp_secret_profile($cust->username, $cust->mikrotik_profile);
      } elseif ($this->mikrotik['ROS_VERSION'] == 7) {
        $restore_paket = $this->routermodel->patchRestSecret(
          (object) array(
            'name' => $cust->username, 
            'profile' => $cust->mikrotik_profile
            )
        );
      } else {
        			echo json_encode(['error' => 'RouterOS version not match! Api_rest_client_model Line 503']);
      }

      if ($cust->ont_phase_state == 'working') {
        // reboot ont (untuk zte F660 versi lama). 
        $request = $this->api->reboot($cust->gpon_onu);
      }

      if ($cust->active_connection == 'connected') {
        // close connection di ppp>active connection
        if ($this->mikrotik['ROS_VERSION'] == 6) {
          $this->routermodel->close_connection_ppp($cust->username);
        } elseif ($this->mikrotik['ROS_VERSION'] == 7) {
          $this->routermodel->pppCloseConnection($cust->username);
        } else {
          echo json_encode(['error' => 'RouterOS version not match! Api_rest_client_model Line 518']);
        }
      }

      /**
       * KIRIM PESAN KE TELEGRAM
       */
      $template = "*Perubahan Masa aktif Paket*\nName : %s\nProfile : %s\nExpired to : %s\nTgl Input : %s \n\n _handled by %s_";
      $teletext = sprintf($template, $cust->no_pelanggan .". ". $cust->nama_pelanggan, $cust->mikrotik_profile, $data->expired, date('Y-m-d H:i:s'), $data->username);
      
      // $sendToTelegram = ""; 
      $sendToTelegram = $this->telegram->sendToAdmin($teletext);
      
      // save to Log table
      $this->saveLogEvent('Payment', "Payment success! $cust->no_pelanggan. $cust->nama_pelanggan $cust->mikrotik_profile Exp=$data->expired by $data->username");
      // update temp_invoice
      // $this->updateTempInvoice($cust->no_pelanggan);

      return [
        'message' => "Paket berhasil diperpanjang ke $data->expired. ONT pelanggan auto restart!",
        // 'kirimwa' => $sendToTelegram,
        'status' => true,
      ];
    }
    //kondisi jika perpanjang sebelum expire
    else {
      $msg = "Paket berhasil diperpanjang ke $data->expired.";
      // jika input tgl expire dibawah tgl sekarang maka langsung ubah ke expire
      if ($data->expired < date('Y-m-d')) {
        $expp = $this->db->query("SELECT no_pelanggan, nama_pelanggan, username FROM pelanggan WHERE gpon_onu = '$data->gpon_onu'")->row();
        
        if ($this->mikrotik['ROS_VERSION'] == 6) {
          $set_expire = $this->routermodel->change_ppp_secret_profile($expp->username, 'Expired');
          $this->routermodel->close_connection_ppp($expp->username);
        } elseif ($this->mikrotik['ROS_VERSION'] == 7) {
          $set_expire = $this->routermodel->patchRestSecret(
            (object) array(
              'name' => $expp->username, 
              'profile' => 'Expired'
              )
          );
          $this->routermodel->pppCloseConnection($expp->username);
        } else {
          echo json_encode(['error' => 'RouterOS version not match! Api_rest_client_model Line 561']);
        }

        $msg = "Paket kembali ke expired";
        $modewa = false;

        // save to Log table
        $this->saveLogEvent('Payment', "Pembayaran gagal! Kembali ke Expire $expp->no_pelanggan. $expp->nama_pelanggan Exp=$data->expired by $data->username");
      }
      
      $plgn = $this->db->query("SELECT telp,no_pelanggan,name,mikrotik_profile FROM v_pelanggan WHERE gpon_onu = '$data->gpon_onu'")->row();

      /**
       * KIRIM PESAN KE TELEGRAM
       */
      $template = "*Perubahan Masa aktif Paket*\nName : %s\nProfile : %s\nExpired to : %s\nTgl Input : %s \n\n _handled by %s_";
      $teletext = sprintf($template, $plgn->name, $plgn->mikrotik_profile, $data->expired, date('Y-m-d H:i:s'), $data->username);

      // $sendToTelegram = "";
      $sendToTelegram = $this->telegram->sendToAdmin($teletext);

      // save to Log table
      $this->saveLogEvent('Payment', "Payment success! $plgn->name $plgn->mikrotik_profile Exp=$data->expired by $data->username");

      $datat = [
        'message' => "Pelanggan Yth, terima kasih telah melakukan pembayaran. Masa aktif Internet Anda telah diperpanjang hingga " . tgl_lokal($data->expired) . ". \n$plgn->no_pelanggan",
        'phone_number' => ($plgn->telp == '') ? '081340310250' : $plgn->telp,
      ];

      $updateExp = $this->db->query("UPDATE pelanggan SET expired='$data->expired' WHERE gpon_onu='$data->gpon_onu'");
      return [
        'message' => $msg, 
        // 'kirimwa' => $sendToTelegram, 
        'status' => true, 
        // 'data' => $datat
      ];
    }
  }

  public function updateTempInvoice($no_pelanggan){
    $yMonth = date('Y-m');
    $now = date('Y-m-d');
    $dateTime = date('Y-m-d H:i:s');
    // get id_kolektor
    $loginSession = $this->session->username;
    $id_karyawan = $this->db->query("SELECT id_karyawan FROM user WHERE username='$loginSession'")->row()->id_karyawan;
    $id_kolektor = $this->db->query("SELECT id_kolektor FROM kolektor WHERE id_karyawan='$id_karyawan'")->row()->id_kolektor;


    $checkMasterSetoran = $this->db->query("SELECT * FROM master_setoran WHERE tgl_setoran LIKE '$yMonth%' AND id_kolektor=$id_kolektor");
    
    if ($checkMasterSetoran->num_rows() == 0) {
      $this->db->insert('master_setoran', [
        'tgl_setoran' => $now,
        'id_kolektor' => $id_kolektor
      ]);
      $newIdMasterSetoran = $this->db->insert_id();
    } 
    else {
      $idMasterSetoran = $checkMasterSetoran->row()->id_master_setoran;
      $idKolektor = $checkMasterSetoran->row()->id_kolektor;
      $getInvoice = $this->db->query("SELECT * FROM v_temp_invoice 
        WHERE no_pelanggan=$no_pelanggan 
        ORDER BY id_trx DESC 
        LIMIT 1")->row();

      $this->db->insert('detail_setoran', [
        'id_master_setoran' => $idMasterSetoran,
        'tgl_input' => $now,
        'id_kolektor' => $id_kolektor,
        'kode_invoice' => $getInvoice->kode_invoice,
        'no_pelanggan' => $getInvoice->no_pelanggan,
        'bulan_penagihan' => $getInvoice->bulan_penagihan,
        'expired' => $getInvoice->expired,
        'tgl_input' => $dateTime,
        'status' => 'Lunas',
        'metode_pembayaran' => 'antar',
        'penerima' => $id_kolektor,
        'kode_wilayah' => $getInvoice->kode_wilayah,
        'remark' => $getInvoice->tarif,
        'tarif' => $getInvoice->tarif,
      ]);
      $idDetailSetoran = $this->db->insert_id();
    }

 
  }


  /**
   * ada 2 kondisi untuk melakukan konfigurasi ulang
   * 1. ketika ditemukan unconfig onu tapi interface gpon olt masih sama dengan database sql
   *    - ini dilakukan jika admin melakukan manual delete (menghapus konfig onu di olt, namun data masih ada dalam database sql)
   * 2. ketika pindah port pon
   */
  public function restore_config_ont($sn, $gpon_olt) {

    $this->checkOnuBySN($sn);
  }

  public function reconfig($sn, $interface_lama, $interface_baru, $onutype, $mode){
    $data = array(
      "sn"          => $sn,
      "gpon_olt_lama"    => $interface_lama,
      "gpon_olt_baru"    => $interface_baru,
      "onu_type"    => $onutype,
      "mode"    => $mode,
    );
    $save = $this->db->insert('u_reconfig_ont', $data);
    return $this->db->affected_rows();
  }

  public function saveLogEvent($topic, $message){
    //Data must be an array
    $data = array(
      'time' => date('Y-m-d H:i:s'),
      'topic' => $topic,
      'message' => $message, 
      //'Payment 275. HAMDAN 250.000 success Exp=2025-03-01',
    );

    $save = $this->db->insert('log', $data);
    return $this->db->affected_rows();
  }



  public function tes_onu($data)
  {
    // find mikrotik profile by id_paket in database
    $profile = $this->db->query("SELECT mikrotik_profile FROM paket WHERE id_paket = " . $data['id_paket'])->row();

    $response = $this->_client->request('POST', 'tes', [
      'form_params' => [
        'gpon_olt' => $data['gpon_olt'],
        'onu_type' => $data['onu_type'],
        'sn' => $data['serial_number'],
        'name' => $data['no_pelanggan'] . '. ' . $data['nama_pelanggan'],
        'description' => 'p=' . $profile->mikrotik_profile . ' &e=' . date('d/m/Y', strtotime($data['expired'])) . ' &h=0' . ' &a=' . $data['access_mode'],
        'ppp_profile' => $profile->mikrotik_profile,
        'access_mode' => $data['access_mode'],
      ]
    ]);


    return json_decode($response->getBody());
    // return $response->getBody();
  }

  public function update_pelanggan($where, $data)
  {
    $this->db->update('pelanggan', $data, $where);
    return $this->db->affected_rows();
  }


  
}
