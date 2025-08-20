<?php
defined('BASEPATH') or exit('No direct script access allowed');

require 'vendor/autoload.php';

use Ifsnop\Mysqldump as IMysqldump;

class Backup extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		if (!is_logged_in()) {
			redirect('login?_rdr=' . urlencode(current_url()));
		}
		date_default_timezone_set('Asia/Makassar');
		$this->load->helper(array('MY_ribuan'));

		$this->load->model('api_rest_client_model', 'api');

	}

	public function index()
	{
		set_status_header(401);
	}

	public function backup()
	{
		$host = $this->db->hostname;;
		$user = $this->db->username;
		$pass = $this->db->password;
		$db = $this->db->database;

		$path = FCPATH . "assets/backup/";
		if (!is_dir($path)) {
			mkdir(FCPATH . 'assets/backup/', 0777, true);
		}

		$filename = "backup#$db#" . date('Y-m-d#H_i_s') . ".sql";

		try {
			$dump = new IMysqldump\Mysqldump("mysql:host=$host;dbname=$db", "$user", "$pass");
			$dump->start("$path$filename");
			echo json_encode([
				'status' => true,
				'message' => 'Database berhasil dibackup!',
				'path' => base_url() . $path . $filename
			]);
		} catch (\Throwable $e) {
			// echo 'mysqldump-php error: ' . $e->getMessage();
			echo json_encode([
				'status' => true,
				'message' => 'mysqldump-php error: ' . $e->getMessage(),
			]);
		}
	}

	public function listfiles()
	{
		$this->load->helper('MY_bulan');

		$pathFolder = FCPATH . "assets/backup/";
		if (!is_dir($pathFolder)) {
			mkdir(FCPATH . 'assets/backup/', 0777, true);
		}

		$asd = scandir(FCPATH . 'assets/backup/');
		$length = count($asd);
		$data = array();
		$status = FALSE;
		for ($i = 2; $i < $length; $i++) {
			$row = array();
			$fileurl = base_url('assets/backup/') . urlencode($asd[$i]);
			$imageFileType = pathinfo($fileurl, PATHINFO_EXTENSION);
			if ($imageFileType == 'sql') {
				$status = TRUE;
				$dd = explode("#", $asd[$i]);
				$time = str_replace('.sql', '', $dd[3]);
				$row[] = $dd[1];
				$row[] = bulan_tahun($dd[2]);
				$row[] = $dd[2] . ' ' . $time;
				$row[] = ribuan(filesize(FCPATH . 'assets/backup/' . $asd[$i])) . ' bytes';
				$row[] = "	<a class=\"btn btn-xs btn-info\" href=\"$fileurl\" target=\"_blank\"><i class=\"fa fa-eye\"></i> View</a>
									<a class=\"btn btn-xs btn-primary\" href=\"$fileurl\"><i class=\"fa fa-download\"></i> Download</a>";
				// <a class=\"btn btn-xs btn-danger\"  href=\"javascript:void(0)\" title=\"Hapus Kwitansi\" onclick=\"hapusFile('".$asd[$i]."')\"><i class=\"fa fa-trash\"></i> Delete</a>";
			} else {
				$status = FALSE;
			}

			if ($status == TRUE) {
				$data[] = $row;
			}
		}

		$output = array('data' => $data,);
		echo json_encode($output);
	}

	/*
	CARA MEMASUKAN DATA PELANGGAN OLT MELALUI FILE .DAT
	DAN MENGUPDATE DATA PELANGGAN DI DATABASE
	*/

	public function import_olt_config()
	{
		$file = FCPATH . 'assets/posonet/startrun_backup_olt1.dat';
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
		$file = FCPATH . 'assets/posonet/startrun_backup_olt1.dat';
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
		$file = FCPATH . 'assets/posonet/startrun_backup_olt1.dat';
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


	public function tes(){
		$file = FCPATH . 'assets/posonet/startrun_backup_olt1.dat';
		// Membaca isi file
		$data = file_get_contents($file);
		// Pisahkan data menjadi bagian gpon-olt dan gpon-onu
		$olt_data = preg_split('/^(interface gpon-olt_.+)$/m', $data, -1, PREG_SPLIT_DELIM_CAPTURE);
		$onu_data = preg_split('/^(interface gpon-onu_.+)$/m', $data, -1, PREG_SPLIT_DELIM_CAPTURE);

		// Hapus elemen pertama yang kosong
		array_shift($olt_data);
		array_shift($onu_data);

		// echo json_encode($olt_data);
		// return;
		// Buat struktur data untuk menyimpan informasi
		$gpon_info = [];

		// Iterasi melalui data gpon-olt
		foreach ($olt_data as $olt_block) {
			preg_match('/interface gpon-olt_(\d+\/\d+\/\d+)\s+onu (\d+) type ([^\s]+) sn ([^\s]+)/', $olt_block, $olt_matches);
			// $olt_key = "interface gpon-onu_{$olt_matches[2]}";
			// $gpon_info[$olt_key] = [
			// 	'onu' => $olt_matches[2],
			// 	'type' => $olt_matches[3],
			// 	'sn' => $olt_matches[4],
			// ];
		}

		echo json_encode($olt_matches);
		return;

		// Iterasi melalui data gpon-onu
		foreach ($onu_data as $onu_block) {
			preg_match('/interface gpon-onu_(\d+\/\d+\/\d+:\d+)\s+name (.+)\s+description (.+)\s+user-vlan (\d+) vlan (\d+)/', $onu_block, $onu_matches);
			$onu_key = "interface gpon-onu_{$onu_matches[1]}";
			if (array_key_exists($onu_key, $gpon_info)) {
				$gpon_info[$onu_key]['name'] = $onu_matches[2];
				$gpon_info[$onu_key]['description'] = $onu_matches[3];
				$gpon_info[$onu_key]['user-vlan'] = $onu_matches[4];
				$gpon_info[$onu_key]['vlan'] = $onu_matches[5];
			}
		}

		// Hasilnya adalah $gpon_info yang berisi informasi yang digabungkan
		// print_r($gpon_info);

		// Struktur data JSON yang akan dihasilkan
		$json_data = [
			"data" => []
		];

		// Iterasi melalui struktur data yang telah diproses
		foreach ($gpon_info as $key => $info) {
			$json_data["data"][] = [
				"name" => $info['name'],
				"description" => $info['description'],
				"user-vlan" => $info['user-vlan'],
				"vlan" => $info['vlan'],
				"gpon_onu" => str_replace("interface gpon-onu_", "", $key),
				"onu" => $info['onu'],
				"type" => $info['type'],
				"sn" => $info['sn'],
			];
		}

		echo json_encode($json_output);
		// Konversi data ke JSON
		// $json_output = json_encode($json_data, JSON_PRETTY_PRINT);

		// data": [
		// 		{
		// 			"name": "028. Brayen T",
		// 			"description ": "p=UP-10MB &e=20/03/2023 &h=0 &a=pppoe",
		// 			"user-vlan ": "101",
		// 			"vlan": 101,
		// 			"gpon_onu": "1/1/1:1",
		// 			"onu": "1",
		// 			"type": "ZTE-F609",
		// 			"sn": "ZTEGC82699D1",
		// 		},
		// 		{
		// 			"name": "010. Ici Wengku",
		// 			"description ": "p=2M &e=20/03/2023 &h=0 &a=pppoe",
		// 			"user-vlan ": "101",
		// 			"vlan": 101,
		// 			"gpon_onu": "1/1/1:3",
		// 			"onu": "3",
		// 			"type": "ZTE-F609",
		// 			"sn": "ZTEGC8988EEE",
		// 		},
		// 		{
		// 			"name": "155. Exel aditya Baru",
		// 			"description ": "p=UP-15MB &e=20/03/2023 &h=0 &a=pppoe",
		// 			"user-vlan ": "101",
		// 			"vlan": 101,
		// 			"gpon_onu": "1/1/1:5",
		// 			"onu": "5",
		// 			"type": "ZTE-F609",
		// 			"sn": "ZTEGCB064018",
		// 		},
		// 	]
		}
}
