<?php defined('BASEPATH') or exit('No direct script access allowed');
class Pelanggan extends CI_Controller
{


	function __construct()
	{
		parent::__construct();
		if (!is_logged_in()) {
			redirect('login?_rdr=' . urlencode(current_url()));
		}

		$this->ros = $this->config->item('mikrotik');

		$this->load->model('pelanggan_model', 'pelanggan');
		$this->load->model('api_mikrotik_model', 'routermodel');
		$this->load->model('api_rest_client_model', 'olt');
		$this->load->helper(array('MY_ribuan', 'MY_bulan'));
	}

	public function index()
	{
		set_status_header(401);
	}

	public function ajax_list()
	{
		$list = $this->pelanggan->get_datatables();
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $br) {
			$no++;
			$row = array();
			//aktifkan tombol jika gpon_olt sdh di set
			$btn_mode = ($br->gpon_onu == "") ? 'disabled' : '';

			if ($br->remote_web_state == 'enabled') {
				$remote = "<li><a href=\"javascript:void(0)\" onclick=\"remote('$br->gpon_onu','disable')\"><span class=\"fa fa-globe\"></span> Close Remote Web</a></li>";
			} else {
				$remote = "<li><a href=\"javascript:void(0)\" onclick=\"remote('$br->gpon_onu','enable')\"><span class=\"fa fa-globe\"></span> Open Remote Web</a></li>";
			}

			if ($this->session->level == 'administrator') {
				$akses = 	"<li><a href=\"javascript:void(0)\" onclick=\"extendPaket('$br->gpon_onu')\"><span class=\"fa fa-calendar\"></span> Perpanjang Paket</a></li>
							<li role=\"separator\" class=\"divider\"></li>
							<li><a href=\"javascript:void(0)\" onclick=\"changeSsid('$br->gpon_onu')\"><span class=\"fa fa-wifi\"></span> Change SSID</a></li>
							<li role=\"separator\" class=\"divider\"></li>
							<li><a href=\"javascript:void(0)\" onclick=\"restore_factory('$br->gpon_onu')\"><span class=\"fa fa-undo\"></span> Restore Factory</a></li>
							<li><a href=\"javascript:void(0)\" onclick=\"delonu('$br->gpon_onu','no')\"><span class=\"fa fa-trash\"></span> Delete Manual</a></li>
							<li><a href=\"javascript:void(0)\" onclick=\"delonu('$br->gpon_onu','yes')\"><span class=\"fa fa-trash\"></span> Delete Permanent</a></li>
							<li><a href=\"javascript:void(0)\" onclick=\"getReplaceOnt('$br->gpon_onu')\"><span class=\"fa fa-exchange\"></span> Replace ONT</a></li>";
				
				$editButton = "<li><a href=\"javascript:void(0)\" onclick=\"edits('$br->id_pelanggan')\"><i class=\"glyphicon glyphicon-pencil\"></i> Edit</a></li>
                                <li><a href=\"javascript:void(0)\" onclick=\"deletes('$br->id_pelanggan')\"><i class=\"glyphicon glyphicon-trash\"></i> Hapus</a></li>";
			} elseif ($this->session->level == 'kolektor') {
				$akses = 	"<li><a href=\"javascript:void(0)\" onclick=\"extendPaket('$br->gpon_onu')\"><span class=\"fa fa-calendar\"></span> Perpanjang Paket</a></li>
							<li role=\"separator\" class=\"divider\"></li>
							<li><a href=\"javascript:void(0)\" onclick=\"changeSsid('$br->gpon_onu')\"><span class=\"fa fa-wifi\"></span> Change SSID</a></li>";

				$editButton = "";
			} elseif ($this->session->level == 'teknisi') {
				$akses = 	"
							<li><a href=\"javascript:void(0)\" onclick=\"changeSsid('$br->gpon_onu')\"><span class=\"fa fa-wifi\"></span> Change SSID</a></li>
							<li role=\"separator\" class=\"divider\"></li>
							<li><a href=\"javascript:void(0)\" onclick=\"restore_factory('$br->gpon_onu')\"><span class=\"fa fa-undo\"></span> Restore Factory</a></li>
							<li><a href=\"javascript:void(0)\" onclick=\"getReplaceOnt('$br->gpon_onu')\"><span class=\"fa fa-exchange\"></span> Replace ONT</a></li>
							<li><a href=\"javascript:void(0)\" onclick=\"delonu('$br->gpon_onu','no')\"><span class=\"fa fa-trash\"></span> Delete Manual</a></li>";

				$editButton = "";
			} else {
				$akses = $editButton = "";
			}

			$row[] = "<div class=\"btn-group\">
							<button type=\"button\" class=\"btn btn-xs dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\" $btn_mode>
								<span class=\"fa fa-info\"> <span class=\"caret\"></span>
							</button>
							<ul class=\"dropdown-menu\">
								<li><a href=\"javascript:void(0)\" onclick=\"show_raw_content('wanip','$br->gpon_onu')\">Show WAN IP</a></li>
								<li><a href=\"javascript:void(0)\" onclick=\"show_raw_content('attenuation','$br->gpon_onu')\">Show Attenuation</a></li>
								<li><a href=\"javascript:void(0)\" onclick=\"show_raw_content('detail-info','$br->gpon_onu')\">Show Detail Information</a></li>
								<li><a href=\"javascript:void(0)\" onclick=\"show_raw_content('iphost','$br->gpon_onu')\">Show IP Host</a></li>
								<li><a href=\"javascript:void(0)\" onclick=\"show_raw_content('onu-run','$br->gpon_onu')\">Show Running Config</a></li>
							</ul>
						</div>
						<div class=\"btn-group\">
							<button type=\"button\" class=\"btn btn-xs dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\" $btn_mode>
								<span class=\"fa fa-cog\"> </span><span class=\"caret\"></span>
							</button>
							<ul class=\"dropdown-menu\">
								$remote
								<li><a href=\"javascript:void(0)\" onclick=\"reboot('$br->gpon_onu')\"><span class=\"fa fa-refresh\"></span> Reboot</a></li>
								$akses
								<li role=\"separator\" class=\"divider\"></li>
								<li><a href=\"javascript:void(0)\" onclick=\"makeTickets('$br->gpon_onu')\"><span class=\"fa fa-ticket\"></span> Make Ticket</a></li>
							</ul>
						</div>";
			// $row[] = "<span class='font-bold'>$br->no_pelanggan</span>";
			$row[] = $br->gpon_onu;
			
			//buat text-danger jika active_connection = disconnected
			if($br->active_connection=='disconnected'){
				$nm_pelanggan = "<span class='font-bold text-danger'>$br->no_pelanggan. $br->nama_pelanggan</span>";
			} else {
				$nm_pelanggan = "<span class='font-bold'>$br->no_pelanggan. $br->nama_pelanggan</span>";
			}
			$row[] = $nm_pelanggan . "<span class='btn btn-xs btn-outline'><small class='text-muted'>$br->onu_type</small></span>";
			
			$row[] = $br->wilayah;
			
			if ($br->ont_phase_state == 'working') {
				$phase = '<button class="btn btn-outline btn-primary btn-xs">' . $br->ont_phase_state . '</button> ';
			} else if ($br->ont_phase_state == 'offline' || $br->ont_phase_state == 'DyingGasp' || $br->ont_phase_state == 'syncMib' || $br->ont_phase_state == 'logging') {
				$phase = '<button class="btn btn-outline btn-default btn-xs">' . $br->ont_phase_state . '</button> ';
			} else if ($br->ont_phase_state == 'LOS') {
				$phase = '<button class="btn btn-outline btn-danger btn-xs">' . $br->ont_phase_state . '</button> ';
			} else if ($br->ont_phase_state == 'Unconfigured'){
				$phase = '<button class="btn btn-outline btn-warning btn-xs"> Unconfigured </button>';
			} else {
				$phase = '<div class="progress progress-striped active">
                                <div style="width: 90%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="90" role="progressbar" class="progress-bar progress-bar-warning">
                                    <span class="xsr-only">Registering</span>
                                </div>
                            </div>';
			}
			$row[] = $phase;
			
			$row[] = ($br->onu_db < -27.0) ? "<strong><span class='text-danger'>$br->onu_db</span></strong>" : "<strong><span class='text-success'>$br->onu_db</span></strong>";
			$row[] = $br->distance;
			// paket Pelanggan
			$row[] = $br->nama_paket;
			// $statusMap = (strlen($br->lokasi_map) <= 4) ? '<span class="label label-danger" title="Lokasi belum di-set"><i class="fa fa-map-marker"></i></span>' : '<a href="' . urldecode($br->lokasi_map) . '" target=\"_blank\"><span class="label label-primary"><i class="fa fa-map-marker"></i></span></a>';
			$statusMap = ($br->lokasi_map == null || empty($br->lokasi_map)) ? '<a href="#" class="btn btn-xs btn-outline btn-danger" title="Lokasi belum di-set"><i class="fa fa-map-marker"></i></a>' : '<a href="' . urldecode($br->lokasi_map) . '" class="btn btn-xs btn-outline btn-primary" target=\"_blank\" title="Klik untuk melihat lokasi pelanggan"><small><i class="fa fa-map-marker"></i></small></a>';
			$linkMap = ($br->lokasi_map == null || empty($br->lokasi_map)) ? "<a href=\"#\">Lokasi Kosong</a>" : "<a href=\"" . urldecode($br->lokasi_map) . "\" target=\"_blank\">Lihat Lokasi</a>";
			// $email = (strlen($br->email) <= 4) ? ' <span class="label label-danger" title="Email kosong">@</span>' : ' <span class="label label-primary">@</span>';
			// <!-- $odp = (strlen($br->odp_number) <= 4) ? ' <span class="label label-danger" title="ODP kosong">ODP</span>' : ' <span class="label label-primary">'.$br->odp_number.'</span>'; -->
			$odp_name = (empty($br->odp_number) || $br->odp_number == null) ? 'ODP' : "$br->odp_number";
			$odp = (empty($br->odp_location) || $br->odp_location == null) ? ' <a href="#" class="btn btn-xs btn-outline btn-danger" title="ODP kosong"><small>ODP</small></a>' : ' <a href="https://www.google.com/maps/?q=' . $br->odp_location . '" class="btn btn-xs btn-outline btn-primary" target="_blank" title="Klik untuk melihat lokasi ODP"><small>' .$odp_name . '</small></a>';
			// $ktp = (strlen($br->no_ktp) <= 4) ? ' <span class="label label-danger" title="KTP kosong">KTP</span>' : ' <span class="label label-primary">KTP</span>';
			
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


			// Status Pelanggan
			$statusB = ($br->status == 'AKTIF') ? '<a href="#" class="btn btn-xs btn-outline btn-primary" title="Status Pelanggan AKTIF"><small>A</small></a> ' : '<a href="#" class="btn btn-xs btn-danger" title="Status Pelanggan NON-AKTIF"><small>NA</small></a>';
			// BUTTON GROUP
			$status = "<div class=\"btn-group\">
                            $statusB
                            $statusMap
							$odp
                        </div>";

			$row[] = ribuan($br->tarif);
			$row[] = $status;
			//add html for action
			$row[] = "<div class=\"btn-group\">
                            <button data-toggle=\"dropdown\" class=\"btn btn-default btn-xs dropdown-toggle\" aria-expanded=\"false\">Action <span class=\"caret\"></span></button>
                            <ul class=\"dropdown-menu\">
                                <li><a href=\"javascript:void(0)\" onclick=\"views('$br->id_pelanggan')\"><i class=\"glyphicon glyphicon-eye-open\"></i> Lihat Detail</a></li>
                                $editButton
								<li class=\"divider\"></li>
                                <li>$linkMap</li>
							</ul>
                        </div>";
			$data[] = $row;
		}
		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->pelanggan->count_all(),
			"recordsFiltered" => $this->pelanggan->count_filtered(),
			"data" => $data
		);
		//output to json format
		echo json_encode($output);
	}

	public function save_pelanggan()
	{
		$error = '';
		// $this->_validate();
		// upload ktp
		$ktp_filename = $this->_do_upload();
		// save data
		$data = array(
			'gpon_olt' => $this->input->post('interface'),
			'onu_type' => $this->input->post('onutype'),
			'access_mode' => $this->input->post('service_mode'),
			'serial_number' => $this->input->post('serial_number'),
			'vlan_profile' => $this->input->post('vlan_profile'),
			'cvlan' => $this->input->post('cvlan'),

			'no_pelanggan' => $this->input->post('no_pelanggan'),
			'nama_pelanggan' => $this->input->post('nama_pelanggan'),
			'id_wilayah' => $this->input->post('id_wilayah'),
			'id_paket' => $this->input->post('id_paket'),
			'tgl_instalasi' => $this->input->post('tgl_instalasi'),
			'expired' => $this->input->post('expired'),
			'lokasi_map' => urlencode($this->input->post('lokasi_map')),
			'telp' => $this->input->post('telp'),
			'email' => $this->input->post('email'),
			'status' => $this->input->post('status'),
			'keterangan' => $this->input->post('keterangan'),
			'no_ktp' => $this->input->post('no_ktp'),
			'ktp_filename' => $ktp_filename,
			
			'odp_number' => $this->input->post('odp_number'),
			'odp_location' => str_replace(' ','',$this->input->post('odp_location')),

			'sn_stb' => $this->input->post('sn_stb'),
			'stb_username' => $this->input->post('no_pelanggan'),
			'stb_password' => rand(111111,999999),
			'input_by' => $this->session->username,
			'sort' => $this->input->post('sort')
		);

		
		//insert to db
		if($insert = $this->pelanggan->save($data)){
			//get tcont from table paket
			$idpaket = $data['id_paket'];
			$qry = $this->db->query("SELECT tcont, gemport FROM paket WHERE id_paket=$idpaket")->row();
			$data['tcont'] = $qry->tcont;
			$data['gemport'] = $qry->gemport;
			//regist ont
			$onu = $this->olt->create_onu($data);
		}
		
		if ($onu->status == '200') {
			$data1 = array();
			$data1['name'] = $onu->data->name;
			$data1['username'] = str_replace("'", "",$onu->data->username);
			$data1['password'] = $onu->data->password;
			$data1['gpon_onu'] = $onu->data->gpon_onu;
			$data1['ppp_profile'] = $onu->data->ppp_profile;
			$data1['description'] = $onu->data->description;

			$update = $this->pelanggan->update(array('no_pelanggan' => $this->input->post('no_pelanggan')), $data1);

			//create secret on router
			if ($this->ros['ROS_VERSION'] == 6) {
				$makePPPSecret = $this->routermodel->create_ppp_secret($onu->data->username,$onu->data->password,'pppoe',$onu->data->ppp_profile);
			} elseif ($this->ros['ROS_VERSION'] == 7) {
				$secretData = (object) array(
						'name'      => $onu->data->username,
						'password'  => $onu->data->password,
						'profile'   => $onu->data->ppp_profile,
						'service'   => 'pppoe'
				);

				$makePPPSecret = $this->routermodel->putRestSecret($secretData);
			} else {
				$error = 'RouterOS version not match! Pelanggan Line 241';
			}
			
			// save to Log table
			$this->olt->saveLogEvent('PSB', "New Customer! " . $onu->data->name ." ". $onu->data->ppp_profile . " Exp=" . $data['expired'] . " by " . $data['input_by']);
		}


		//send new client data to Admin telegram
		$this->load->model('Api_telegrambot_model','telegramModel');
		$telegram = $this->telegramModel->sendNewClientToAdmin($data);

		echo json_encode(
			array(
				"status" => TRUE,
				"callback" => $onu,
				"telegram" => ($telegram == null) ? [] : $telegram,
				"message" => $error
			),
		);
	}

	public function update_pelanggan()
	{
		// $this->_validate();
		$id_pelanggan = $this->input->post('id_pelanggan');
		if ($this->input->post('ubahfoto') == 'on') {
			$this->delete_ktp($id_pelanggan);
			$ktp_filename = $this->_do_upload();
		} else {
			$ktp_filename = $this->db->query("SELECT ktp_filename FROM pelanggan WHERE id_pelanggan = $id_pelanggan")->row()->ktp_filename;
		}

		$tgl_pasang = $this->input->post('tgl_instalasi');
		$tgl_pasang = ($tgl_pasang == null || $tgl_pasang == '') ? NULL : $tgl_pasang;
		$data = array(
			'nama_pelanggan' => $this->input->post('nama_pelanggan'),
			'id_paket' => $this->input->post('id_paket'),
			'tgl_instalasi' => $tgl_pasang,
			'expired' => $this->input->post('expired'),
			// 'serial_number' => $this->input->post('serial_number'),
			'lokasi_map' => urlencode($this->input->post('lokasi_map')),
			'telp' => $this->input->post('telp'),
			'email' => $this->input->post('email'),
			'status' => $this->input->post('status'),
			'keterangan' => $this->input->post('keterangan'),
			'no_ktp' => $this->input->post('no_ktp'),
			'ktp_filename' => $ktp_filename,
			
			'odp_number' => $this->input->post('odp_number'),
			'odp_location' => str_replace(' ','',$this->input->post('odp_location')),

			'onu_type' => $this->input->post('onutype'),
			'access_mode' => $this->input->post('service_mode'),
			'vlan_profile' => $this->input->post('vlan_profile'),
			'cvlan' => $this->input->post('cvlan'),
			'id_wilayah' => $this->input->post('id_wilayah'),
			'no_pelanggan' => $this->input->post('no_pelanggan'),
			'sort' => $this->input->post('sort'),
		);

		$this->pelanggan->update(array('id_pelanggan' => $this->input->post('id_pelanggan')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function delete_pelanggan($id_pelanggan)
	{
		$path = $this->delete_ktp($id_pelanggan);
		// delete from database
		$this->pelanggan->delete_by_id($id_pelanggan);
		echo json_encode(array("status" => TRUE));
	}

	public function delete_ktp($id_pelanggan)
	{
		//get filename
		$filename = $this->db->query("SELECT ktp_filename FROM pelanggan WHERE id_pelanggan = $id_pelanggan")->row();
		if ($filename->ktp_filename != '') {
			$path = FCPATH . "/assets/posonet/upload/ktp/" . $filename->ktp_filename;
			//delete file
			if (file_exists($path)) {
				unlink($path);
			}
		}
	}

	public function get_edit($id_pelanggan = FALSE)
	{
		$data = $this->pelanggan->get_by_id($id_pelanggan);
		echo json_encode($data);
	}

	public function vget_edit($id_pelanggan = FALSE)
	{
		$data1 = $this->pelanggan->v_get_by_id($id_pelanggan);
		// paket Pelanggan
		if ($data1->id_paket == '1') {
			$paket = '<a href="javascript:void(0)" class="btn btn-xs btn-default"> Rp.' . ribuan($data1->tarif) . ',-</a>';
		} elseif ($data1->id_paket == '2') {
			$paket = '<a href="javascript:void(0)" class="btn btn-xs btn-success"> Rp.' . ribuan($data1->tarif) . ',-</a>';
		} elseif ($data1->id_paket == '3') {
			$paket = '<a href="javascript:void(0)" class="btn btn-xs btn-primary"> Rp.' . ribuan($data1->tarif) . ',-</a>';
		} elseif ($data1->id_paket == '4' || $data1->id_paket == '5') {
			$paket = '<a href="javascript:void(0)" class="btn btn-xs btn-warning"> Rp.' . ribuan($data1->tarif) . ',-</a>';
		} else {
			$paket = '<a href="javascript:void(0)" class="btn btn-xs btn-danger"> Rp.' . ribuan($data1->tarif) . ',-</a>';
		}
		// Status Pelanggan
		if ($data1->status == 'AKTIF') {
			$status = '<a href="javascript:void(0)" class="btn btn-xs btn-primary">' . $data1->status . '</a>';
		} else {
			$status = '<a href="javascript:void(0)" class="btn btn-xs btn-danger">' . $data1->status . '</a>';
		}

		$data = array(
			'id_pelanggan' => $data1->id_pelanggan,
			'no_pelanggan' => $data1->no_pelanggan,
			'nama_pelanggan' => $data1->nama_pelanggan,
			'alamat' => $data1->alamat,
			'wilayah' => $data1->wilayah,
			'serial_number' => $data1->serial_number,
			'tgl_instalasi' => $data1->tgl_instalasi,
			'telp' => $data1->telp,
			'email' => $data1->email,
			'nama_paket' => $data1->nama_paket,
			'tarif' => $paket,
			'status' => $status,
			'lokasi_map' => ($data1->lokasi_map == '' || $data1->lokasi_map == null) ? "<a href='javascript:void(0)' class='btn btn-sm btn-warning'><span class='fa fa-map-marker'></span> Lokasi ONT Kosong</a>" : "<a href='" . urldecode($data1->lokasi_map) . "' target='_blank' class='btn btn-sm btn-info'><span class='fa fa-map-marker'></span> Buka Lokasi ONT</a>",
			'odp_number' => $data1->odp_number,
			'odp_location' => ($data1->odp_location == '' || $data1->odp_location == null) ? "<a href='javascript:void(0)' class='btn btn-sm btn-warning'><span class='fa fa-map-marker'></span> Lokasi ODP Kosong</a>" : "<a href='" . urldecode($data1->odp_location) . "' target='_blank' class='btn btn-sm btn-info'><span class='fa fa-map-marker'></span> Buka Lokasi ODP</a>",
			'expired' => $data1->expired,
			'keterangan' => $data1->keterangan,
		);
		echo json_encode($data);
	}

	public function getCode($wil)
	{
		$id_wilayah = ucfirst($wil);
		$kodeWil = $this->pelanggan->cekKodeWil($id_wilayah);
		if ($id_wilayah != '' && $id_wilayah == $kodeWil->id_wilayah) {
			// Membuat Kode Pelanggan Baru Berdasarkan Wilayah
			$hasil = $this->db->query("SELECT MAX(no_pelanggan) AS maxKode, wilayah, id_wilayah, kode_wilayah
										FROM v_pelanggan 
										WHERE id_wilayah = $id_wilayah")->row();
			$kdMax = $hasil->maxKode;
			$noUrut = (int) substr($kdMax, 1, 2);
			$noUrut++;
			$newKode['newCode'] = strtoupper($hasil->kode_wilayah . sprintf("%02s", $noUrut));
			echo json_encode($newKode);
		}
	}

	/***
	 * Kode LAMA
	 */

	public function getcodenew($id_wilayah){
		$query = $this->db->query("SELECT kode_wilayah, GROUP_CONCAT(no_pelanggan) AS no_pelanggan FROM v_pelanggan
			WHERE id_wilayah = $id_wilayah
			ORDER BY no_pelanggan ASC");
		$row = $query->row();

		if ($row->kode_wilayah === null) {
			$getcode = $this->db->query("SELECT * FROM wilayah WHERE id_wilayah=$id_wilayah")->row(); 
			echo json_encode(['newCode' => $getcode->kode_wilayah . '01']);
			exit();
		}
		
		$mulai = (int) $row->kode_wilayah."01";

		if ($row->no_pelanggan == '') {
			$data = $mulai;
			echo json_encode(['newCode' => $data]);
			return;
		}

		if(str_contains($row->no_pelanggan,',')) {
			$data = explode(",", $row->no_pelanggan);
			$max = max($data);
		} else {
			$data = [$row->no_pelanggan];
			$max = max($data);
		}
		

		$terlewati = false;
		$last = 0;

			if(count($data) == 1) {
				$hasil = $row->no_pelanggan + 1;
				if(strlen((string) $hasil) == 1) {
					$hasil = "00$hasil";
				} else if(strlen((string) $hasil) == 2) {
					$hasil = "0$hasil";
				}

				echo json_encode(
					['newCode' => $hasil, 'kondisi' => 'a']
				);
			}
			elseif (count($data) > 1) {
	
				for ($i=$mulai; $i <= $max; $i++) { 
					
					if (!in_array($i, $data)){
						$terlewati = true;
						echo json_encode(['newCode' => $i, 'kondisi' => 'b']);
						break;
					}
					$last = $i;
				}
				
				if ($terlewati == false) {

					//jika tidak ada yg terlewati
					$fullKode = (int) $row->kode_wilayah.'99';
					//cek apakah slot no pelanggan full?
					if(($last + 1) >= $fullKode) {
						echo json_encode(['newCode' => 'full', 'kondisi' => 'c']);
						return;
					} else {
						$hasil = $last + 1;
						
						echo json_encode(['newCode' => "$hasil", 'kondisi' => 'd']);
					}
				}
			
			} else {
				echo json_encode(['newCode' => "$mulai" , 'kondisi' => 'e']);
			}
		// }
	}


	/***
	 * Kode BARU
	 */

	public function aicode($id_wilayah){
		//kumpulkan data no_pelanggan dalam bentuk grup
		$query = $this->db->query("SELECT kode_wilayah, GROUP_CONCAT(no_pelanggan) AS no_pelanggan FROM v_pelanggan
			WHERE id_wilayah = $id_wilayah
			ORDER BY no_pelanggan ASC");
		$row = $query->row();
		
		$angkaTerlewat = array();

		$kondisi = '';
		
		if ($row->kode_wilayah != null) {
			$angkaTerendah = $row->kode_wilayah.'00'; //200
			$angkaTertinggi = $row->kode_wilayah.'99'; //299
			// print_r($angkaTertinggi);
			// exit();
			$dataArray = explode(",",$row->no_pelanggan);

			for ($i=$angkaTerendah; $i <= $angkaTertinggi; $i++) { 
				if (!in_array($i, $dataArray)) {
					$angkaTerlewat[] = $i;
				}
			}

			if (empty($angkaTerlewat)) {
				$angkaTerlewat[] = max($dataArray) + 1;
				$kondisi = 'b';
			}

			if (min($angkaTerlewat) > $angkaTertinggi) {
				$angkaTerlewat = ['----FULL----'];
				$kondisi = 'c';
			}

			
		} elseif ($row->kode_wilayah == NULL) {
			$getKode = $this->db->query("SELECT kode_wilayah FROM wilayah WHERE id_wilayah=$id_wilayah");
			
			if ($getKode->row() == null) {
				$angkaTerlewat=['WILAYAH NOT EXIST!'];
				$kondisi = 'e';
				// exit();
			} else {
				$kodeWilayah = $getKode->row()->kode_wilayah;
				//ambil angka terendah
				$angkaTerlewat[] = $kodeWilayah.'00'; //200
				$kondisi = 'd';
	
				//khusus untuk kode wilayah 000
				if ($angkaTerlewat == '000') {
					$angkaTerlewat= ["001"];
					$kondisi = 'a';
				}
			}
			
			
		}

		// print_r(min($angkaTerlewat));
		// print_r($angkaTerlewat);
		echo json_encode(['newCode' => min($angkaTerlewat), 'kondisi' => $kondisi]);
	}


	private function _do_upload()
	{
		$path =	FCPATH . "/assets/posonet/upload/ktp/";
		if (!is_dir($path)) {
			mkdir(FCPATH . "assets/posonet/upload/ktp/", 0777, true);
		}

		$config['upload_path'] = FCPATH . "/assets/posonet/upload/ktp/";
		$config['allowed_types'] = 'gif|jpg|jpeg|png';
		$config['encrypt_name'] = TRUE; // disable this if use manual filename
		// $config['file_name'] = "123.jpg";

		$this->load->library('upload', $config);
		if ($this->upload->do_upload("file_ktp")) {
			$data = array('upload_data' => $this->upload->data());

			$imageName = $data['upload_data']['file_name'];

			return $imageName;
		}
	}



	private function _validate()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;
		//id tidak divalidasi karena auto_increment

		if ($this->input->post('nama_pelanggan') == '') {
			$data['inputerror'][] = 'nama_pelanggan';
			$data['error_string'][] = 'Enter this field!';
			$data['status'] = FALSE;
		}
		// if ($this->input->post('serial_number') == '') {
		// 	$data['inputerror'][] = 'serial_number';
		// 	$data['error_string'][] = 'Enter this field!';
		// 	$data['status'] = FALSE;
		// }
		if ($this->input->post('email') == '') {
			$data['inputerror'][] = 'email';
			$data['error_string'][] = 'Enter this field!';
			$data['status'] = FALSE;
		}
		// if ($this->input->post('id_paket') == '') {
		// 	$data['inputerror'][] = 'id_paket';
		// 	$data['error_string'][] = 'Enter this field!';
		// 	$data['status'] = FALSE;
		// }

		if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}



	/*
	 *	Export to FPDF
	 */

	public function exportpdf()
	{
		$data['info'] = $this->db->query("SELECT * FROM profil_perusahaan WHERE id_profil=1 ")->row();
		$data['data'] = $this->pelanggan->get_datatables();
		$title = ($_POST['search']['value'] == null) ? "PELANGGAN" : $_POST['search']['value'];
		$data['other'] = array('bulan' => bulan(date('m')) . " " . date('Y'), 'title' => $title);
		$this->load->view('admin/pelanggan/report2', $data);
		// echo "<pre>";
		// print_r($data);
		// echo "</pre>";
		// echo $data['info']->nama_perusahaan;
	}

	public function gis_customers()
	{
		$req = $this->db->query("SELECT p.no_pelanggan,p.nama_lengkap,p.wilayah,p.alamat,p.`status`,p.lat,p.`long`
			FROM v_gis_pelanggan p
			WHERE p.lat != '' AND p.`long` != ''
			ORDER BY p.id_wilayah ASC")->result();

		$data = array();
		$index = 0;
		foreach ($req as $k) {
			$index++;
			$row = array();
			$row[] = $index;
			$row[] = $k->lat;
			$row[] = $k->long;
			$row[] = $k->no_pelanggan;
			$row[] = $k->nama_lengkap;
			$row[] = $k->wilayah;
			$row[] = $k->alamat;
			$row[] = $k->status;

			$data[] = $row;
		}

		// $option = array(
		// 	'zoom' => 13, // level zoom peta
		// 	'center' => 'centerMap',  // setting pusat peta ke centerMap
		// 	'mapTypeId' => 'google.maps.MapTypeId.ROADMAP' //menentukan tipe peta
		// );

		$output = array(
			'sites' => $data,
			// 'option' => $data,
		);

		echo json_encode($output);
	}
}
