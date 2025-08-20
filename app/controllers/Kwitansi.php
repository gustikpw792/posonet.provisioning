<?php defined('BASEPATH') or exit('No direct script access allowed');
class Kwitansi extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		if (!is_logged_in()) {
			redirect('login?_rdr=' . urlencode(current_url()));
		}
		$this->load->library(array('fpdf', 'ci_qr_code'));
		$this->load->model('kwitansi_model', 'kwitansi');
		$this->load->helper('download');
		$this->load->helper(array('MY_ribuan', 'MY_bulan'));
	}

	public function index()
	{
		set_status_header(401);
	}

	private function _invoiceCode($wilayah, $bulanPenagihan)
	{ // 'KWA'
		$tgl_skrg = substr(str_replace('-', '', $bulanPenagihan), 2, 4); // 1710
		$kode = $wilayah . $tgl_skrg; // KWA1710
		$angkaRandom = rand(1, 999999);
		return $nextInvoiceNum = $kode . sprintf('%06s', $angkaRandom); // Jadinya 'KWA1710999999' 9999999 = invoice random
	}

	private function _generateQr($invoice)
	{
		$pathFolder = FCPATH . 'assets/tempQr/img/';
		if (!is_dir($pathFolder)) {
			mkdir(FCPATH . 'assets/tempQr/' . 'img', 0777, true);
			mkdir(FCPATH . 'assets/tempQr/' . 'logs', 0777, true);
		}
		$pathFile = base_url('assets/tempQr/img/' . $invoice . '.png');
		if (!file_exists($pathFile)) {
			$params['data'] = $invoice;
			$params['level'] = 'H';
			$params['size'] = 10;
			$params['savename'] = FCPATH . 'assets/tempQr/img/' . $invoice . '.png';
			$this->ci_qr_code->generate($params);
		}
	}

	//memasukan kode invoice, kode_plgn di database temp_kwitansi
	public function createInvCode()
	{
		date_default_timezone_set("Asia/Hong_Kong");
		$bulanPenagihan = html_escape($this->input->post('bulan_penagihan') . '-02');
		$kode_wilayah = html_escape($this->input->post('wilayah'));
		$sort = html_escape($this->input->post('urutkan'));
		$invoiceKey = html_escape($this->input->post('sandi'));
		// $cekKey = $this->kwitansi->getSettings('invoice_key',$invoiceKey); // option_name , option value // Validasi InvoiceKey pada database. jika sama, buat kwitansi

		if ($this->_confirm_pass($invoiceKey)) {
			if ($this->_cekBulanPenagihan($kode_wilayah, $bulanPenagihan) !== 0) {
				$pesan = array(
					'pesan' => 'Maaf, Wilayah "' . $kode_wilayah . '" dibulan "' . $bulanPenagihan . '" sudah ter-Registrasi! <br>Silahkan Pilih pada Panel <strong>Generated Kwitansi</strong>',
					'title' => 'Already exist!',
					'msgtype' => 'error'
				);
			} else {
				$idWil = $this->kwitansi->cekIDWil($kode_wilayah);
				$kolektor = $this->kwitansi->getKolektorIdBy($idWil->id_wilayah);
				if ($kolektor == null || $kolektor->id_kolektor == null || $kolektor->id_kolektor == '') {
					$pesan = array(
						'pesan' => 'Maaf, Wilayah ini <strong>Belum Memiliki Kolektor!</strong> <br> Klik <strong><a href="' . site_url('dashboard/kolektor') . '">DISINI</a></strong> untuk menambahkan Kolektor',
						'title' => 'Kolektor belum ada!',
						'msgtype' => 'error'
					);
				} else {
					$q = $this->kwitansi->count_pel($idWil->id_wilayah); // Hitung jumlah pelanggan berdasarkan wilayah
					$dateExp = $this->db->query("SELECT option_value FROM settings WHERE option_name = 'tgl_expired_paket' ")->row();
					if ($q->jumlah != 0) {
						$takeKode = $this->kwitansi->plgn_ByWilayah($idWil->id_wilayah, $sort); // ambil semua kode pelanggan berdasarkan wilayah
						try {
							$this->db->trans_begin();
							foreach ($takeKode as $kode) {
								$cekinv = $this->_invoiceCode($kode_wilayah, $bulanPenagihan);
								$data = array(
									'kode_invoice' => $cekinv,
									'no_pelanggan' => $kode->no_pelanggan,
									'bulan_penagihan' => $bulanPenagihan,
									'expired' => date('Y-m', strtotime('+1 months', strtotime($bulanPenagihan))) . '-' . $dateExp->option_value,
									'kode_wilayah' => $kode_wilayah,
									'tarif' => $kode->tarif,
								);
								$this->db->insert('temp_invoice', $data);
							}

							// setelah data invoice dimasukan pada database, generateInvoice membuat file PDF dan menyimpannya pada server
							$pathf = $this->generateInvoice($idWil->id_wilayah, substr($bulanPenagihan, 0, 7), $sort, $kode_wilayah);
							if (file_exists($pathf['namafile']) && $pathf['rollback'] == true) {
								unlink($pathf['namafile']);
								throw new Exception("DB rolling back!");
							}
							$this->db->trans_commit();
							$pesan = array(
								'pesan' => 'Sukses, <strong>' . $q->jumlah . '</strong> data telah dimasukan!',
								'title' => 'Berhasil!',
								'msgtype' => 'success'
							);
						} catch (Exception $e) {
							$pesan = array(
								'pesan' => 'System Error, terjadi kesalahan dalam pembuatan kwitansi! Hubungi developer!',
								'title' => 'Gagal!',
								'msgtype' => 'error',
								'exception' => $e->getMessage()
							);
							$this->db->trans_rollback();
							$this->hapusTempAll();
						}
					} else {
						$pesan = array(
							'pesan' => 'Maaf, Wilayah "' . $kode_wilayah . '" belum memiliki Pelanggan!',
							'title' => 'Not Founds!',
							'msgtype' => 'error'
						);
					}
				}
			}
		} else {
			$pesan = array(
				'pesan' => 'Maaf, Anda tidak memiliki akses untuk mencetak Kwitansi! <br> Sandi Kwitansi Salah!',
				'title' => 'Not Authorized!',
				'msgtype' => 'error'
			);
		}
		echo json_encode($pesan);
	}

	private function _cekBulanPenagihan($kode_wilayah, $bulan_penagihan) //2017-10
	{
		// melakukan validasi data ke temp_invoice jika ada $bulan_penagihan dan wilayah yang sama, registrasi Invoice dibatalkan
		return $this->kwitansi->cekBlnPenagihan($kode_wilayah, $bulan_penagihan)->num_rows();
	}

	public function generateInvoice($wilayah, $bulanPenagihan, $sort, $kode_wilayah)
	{
		// Buat QR Code PNG
		ini_set('max_execution_time', 1200); // terjadi error ketika generate kwitansi > 30 detik. ini untuk mengaturnya
		$query = $this->db->query("SELECT kode_invoice FROM temp_invoice WHERE kode_wilayah = '$kode_wilayah' AND bulan_penagihan LIKE '$bulanPenagihan%' ");
		$jumlah = 0;
		foreach ($query->result() as $qr) {
			$this->_generateQr($qr->kode_invoice);
			$jumlah++;
		}

		// Buat PDF
		if ($query->num_rows() === $jumlah) {
			$bulan = array('', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
			$pelanggan = $this->kwitansi->plgn_ByWilayahQr($wilayah, $sort); // ambil semua kode pelanggan berdasarkan wilayah
			$profil = $this->kwitansi->profil_perusahaan();
			// $kolektor = $this->kwitansi->findCollector($wilayah);
			//$terms = $this->_invoiceTerms(); //BELUM DIPAKAI
			$rek = $this->db->query("SELECT * FROM settings WHERE option_name LIKE 'bri_%'")->result();
			$rekening = array();
			foreach ($rek as $rk) {
				if ($rk->option_name === 'bri_bank') {
					$rekening['nama_bank'] = $rk->option_value;
				}
				if ($rk->option_name === 'bri_nama_pemilik_rekening') {
					$rekening['pemilik_rekening'] = $rk->option_value;
				}
				if ($rk->option_name === 'bri_no_rekening') {
					$rekening['no_rekening'] = $rk->option_value;
				}
			}

			$terms = array(); //BELUM DIPAKAI
			// echo json_encode($pelanggan);
			$data = [];
			$namafile = '';
			foreach ($pelanggan as $dt) {
				$row = [];
				$kdplgn = $dt->no_pelanggan;
				$dataInvoice = $this->kwitansi->cek_temp_invoice($kdplgn, $bulanPenagihan); // ambil dati temp_invoice
				$row['kode_invoice'] = $dataInvoice->kode_invoice;
				$row['no_pelanggan'] = $dataInvoice->no_pelanggan;
				$row['nama_pelanggan'] = $dt->nama_pelanggan;
				$ww = explode(' ', $dt->wilayah);
				$row['wilayah'] = $ww[0];
				$row['nama_paket'] = $dt->nama_paket;
				$row['tarif'] = $dt->tarif;
				$row['status'] = $dt->status;
				$row['tgl_instalasi'] = $dt->tgl_instalasi;
				$row['expired'] = $dt->expired;
				$row['serial_number'] = $dt->serial_number;
				$row['status_map'] = strlen($dt->lokasi_map) <= 5 ? 'Belum Ada' : 'Ada';
				$row['lokasi_map'] = strlen($dt->lokasi_map) <= 5 ? '#' : $dt->lokasi_map;
				// $blnPenagihan = (int) substr($dataInvoice->bulan_penagihan, 5, 2);
				$row['keterangan'] = $dt->keterangan;
				$row['bulan_penagihan'] = bulan_tahun($dataInvoice->bulan_penagihan);
				$row['masa_aktif'] = tgl_lokal($dataInvoice->expired);
				$row['tgl_cetak'] = $dataInvoice->bulan_penagihan;
				$row['url_gambar'] = base_url() . '/assets/tempQr/img/' . $dataInvoice->kode_invoice . '.png';
				//pemisah angka ribuan
				$row['tarif_rp'] = "Rp. " . ribuan($dt->tarif) . ",-";
				$row['tarif_rp_trx'] = "Rp. " . ribuan($dt->tarif + $dataInvoice->no_pelanggan) . ",-";
				$row['sort'] = $dt->sort;
				// Pengaturan nama file, dll
				$kodewil = 'WIL';
				if (strlen($dt->no_pelanggan) == 5) {
					$kodewil = substr($dt->no_pelanggan, 0, 2);
				} elseif (strlen($dt->no_pelanggan) == 4) {
					$kodewil = substr($dt->no_pelanggan, 0, 1);
				} elseif (strlen($dt->no_pelanggan) == 3) {
					$kodewil = substr($dt->no_pelanggan, 0, 1);
				} elseif (strlen($dt->no_pelanggan) == 2) {
					$kodewil = substr($dt->no_pelanggan, 0, 1);
				} else {
					$kodewil = substr($dt->no_pelanggan, 0, 1);
				}
				$namafile = FCPATH . 'assets/invoice/' . $bulanPenagihan . '_' . $kodewil . '_' . str_replace(' ', '-', $dt->wilayah) . '.pdf';

				$data[] = $row;
			}

			$kirim = array(
				'logo' => base_url() . '/assets/posonet/img/primahomelogo3.png',
				'company' => $profil,
				'cust' => $data,
				'terms' => $terms,
				'rekening' => $rekening,
				'namafile' => $namafile,
				'outputMode' => 'FILE', // STREAM = just temporarly open in browser | FILE = save to storage server
			);
			$fl = $this->load->view('admin/kwitansi/invoice_inet', $kirim, true);
			if (strlen($fl) > 0) { // jika ada teks error, return rollback db
				return ['namafile' => $namafile, 'rollback' => true, 'message' => $fl];
			} else {
				return ['namafile' => $namafile, 'rollback' => false, 'message' => $fl];
			}
		} else {
			return ['namafile' => FCPATH . 'assets/invoice/XXX.txt', 'rollback' => true, 'message' => 'Data invoice tidak ada!'];
		}
	}

	public function files2()
	{
		$this->load->helper('MY_bulan');
		//check if folder not exists
		$pathFolder = FCPATH . 'assets/invoice/';
		if (!is_dir($pathFolder)) {
			mkdir(FCPATH . 'assets/invoice', 0777, true);
		}

		$asd = scandir(FCPATH . 'assets/invoice/');
		$length = count($asd);
		$data = array();
		$status = FALSE;
		for ($i = 2; $i < $length; $i++) {
			$row = array();
			$fileurl = base_url('assets/invoice/') . $asd[$i];
			$imageFileType = pathinfo($fileurl, PATHINFO_EXTENSION);
			if ($imageFileType == 'pdf') {
				$status = TRUE;
				$dd = explode("_", $asd[$i]);
				$wilayah = str_replace('.pdf', '', $dd[2]);
				$row[] = str_replace('-', ' ', $wilayah);
				$row[] = bulan_tahun($dd[0]);
				$row[] = $dd[0];
				$row[] = "<a class=\"btn btn-xs btn-info\" href=\"$fileurl\" target=\"_blank\"><i class=\"fa fa-eye\"></i> Print view</a> 
				<div class=\"btn-group\">
                            <button data-toggle=\"dropdown\" class=\"btn btn-default btn-xs dropdown-toggle\" aria-expanded=\"false\">Action <span class=\"caret\"></span></button>
                            <ul class=\"dropdown-menu\">
                                <li><a class=\"\" href=\"$fileurl\"><i class=\"fa fa-download\"></i> Download</a></li>
                                <li><a class=\"\"  href=\"javascript:void(0)\" title=\"Hapus Kwitansi\" onclick=\"hapusFile('" . $asd[$i] . "')\"><i class=\"fa fa-trash\"></i> Delete</a></li>
                            </ul>
                        </div>";
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

	public function hapusFile($namaFile, $self = false)
	{
		$sandi = html_escape($this->input->post('sandi'));
		$data = array();

		if ($this->_confirm_pass($sandi)) {
			$pathh = FCPATH . 'assets/invoice/' . $namaFile;
			try {
				if (file_exists($pathh)) {
					// menghapus data pada database berdasarkan bulan Penagihan
					$dd = explode("_", $namaFile);
					$blnPenagihan = $dd[0] . '-02';
					$kode_wilayah = $dd[1];
					$del = $this->db->query("DELETE FROM temp_invoice WHERE kode_wilayah='$kode_wilayah' 
						AND bulan_penagihan='$blnPenagihan'");
					// menghapus file pada server sesuai isi database
					if(unlink($pathh)){
						$data = array('status' => TRUE, 'msg' => "Bulan=$blnPenagihan <br>KodeWilayah=$kode_wilayah <br>Berhasil menghapus kwitansi! [302]");
					} else {
						$data = array('status' => FALSE, 'msg' => 'Gagal menghapus file kwitansi! [304]');
					}
				} else {
					$data = array('status' => FALSE, 'msg' => 'File sudah tidak tersedia! [307]');
				}
			} catch (\Throwable $th) {
				echo json_encode($th);
			}
		} else {
			$data = array('status' => FALSE, 'msg' => 'Gagal menghapus kwitansi. Sandi salah! [313]');
		}

		if ($self == false) {
			echo json_encode($data);
		} else {
			return true;
		}
		
	}

	public function hapusTempAll($self = false)
	{
		$dir = FCPATH . 'assets/tempQr/img/*.png';
		$files = glob($dir); // get all file names
		foreach ($files as $file) { // iterate files
			if (is_file($file)) {
				if ($file != FCPATH . 'assets/tempQr/img/A2006000000.png') {
					unlink($file); // delete file
				}
			}
		}
		if ($self == false) {
			echo json_encode(array("status" => TRUE));
		}
	}

	public function openFile($link = '')
	{
		if (file_exists($link)) {
			readfile($link, "r");
		} else {
			echo "Unable to open file! | Not Founds!";
		}
	}

	private function _invoiceTerms()
	{
		$qterms = $this->kwitansi->getSettings_serial('invoice_terms')->row();
		return unserialize($qterms->option_value);
	}

	public function getDetailTagihan($scanedQR = "")
	{
		if ($scanedQR != "") {
			$query = $this->kwitansi->getDetailTagihan($scanedQR);
			if ($query->num_rows() !== 0) {
				$q = $query->row();
				$status = ($q->status == 'Lunas') ? "<i class='fa fa-check fa-2x text-success' data-toggle='tooltip' title='$q->status'></i>" : "<i class='fa fa-clock-o fa-2x' data-toggle='tooltip' title='$q->status'></i>";
				$row = "<tr class='$q->kode_invoice'>
				<td><input name='kode_invoice[]' value='$q->kode_invoice' hidden>$q->kode_invoice</td>
				<td>$q->no_pelanggan</td>
				<td>$q->nama_pelanggan</td>
				<td>$q->wilayah</td>
				<td>" . str_replace('-02', '', $q->bulan_penagihan) . "</td>
				<td>$status <input name='status[]' value='$q->status' hidden></td>
				<td>$q->tarif <input type='number' id='$q->kode_invoice' value='$q->tarif' hidden></td>
				<td><input type='number' name='jmlSetoran[]' class='form-control input-sm' value='$q->tarif'></td>
				<td class='text-center'>
					<a href='javascript:void(0)' class='btn btn-xs btn-info' onclick=\"addKet('$q->kode_invoice')\"><i class='fa fa-info'></i> Add Info</a>
					<a href='javascript:void(0)' class='btn btn-xs btn-danger' onclick=\"hapusTr('$q->kode_invoice')\"><i class='fa fa-trash'></i> Hapus</a>
					<textarea name='keterangan[]' class='form-control input-sm $q->kode_invoice' placeholder='Tambahkan keterangan kwitansi ini' style='display:none'>$q->keterangan</textarea>
					<input name='hash[]' value='$q->kode_invoice' hidden>
				</td>
				</tr>";
				$hash = $q->kode_invoice;
			} else {
				$row = $hash = "";
			}
		} else {
			$row = $hash = "";
		}

		$data = array(
			'data' => $row,
			'hash' => $hash,
			'tarif' => (int) ($q->tarif) ? $q->tarif : 0,
		);
		echo json_encode($data);
	}

	private function _confirm_pass($invoiceKey = '')
	{
		$getKey = $this->kwitansi->getSettings('invoice_key', $invoiceKey); // option_name , option value // Validasi InvoiceKey pada database. jika sama, buat kwitansi
		if ($invoiceKey != '' && $getKey->num_rows() === 1) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function tes2()
	{
		$this->load->view('admin/kwitansi/qrscan');
	}

	// private function _saveqr($wilayah, $bulanPenagihan)
	// {
	// 	$query = $this->db->query("SELECT kode_invoice FROM temp_invoice WHERE no_pelanggan LIKE '%$wilayah%' AND bulan_penagihan LIKE '%$bulanPenagihan%' ");
	// 	$jumlah = 0;
	// 	foreach ($query->result() as $qr) {
	// 		$this->_generateQr($qr->kode_invoice);
	// 		$jumlah++;
	// 	}

	// 	if ($query->num_rows() === $jumlah) {
	// 		return TRUE;
	// 	} else {
	// 		return FALSE;
	// 	}

	// }

	public function kwitansikawua()
	{
		$cust = array();
		for ($i = 1; $i < 15; $i++) {
			$cust[] = array(
				'kode_invoice' => '02007849039',
				'no_pelanggan' => '10' . $i,
				'nama_pelanggan' => 'PELANGGAN ' . $i,
				'alamat' => 'TRANS PANDAYORA' . $i,
				'wilayah' => 'PANDAYORA',
				'paket' => 1,
				'tarif' => 100000,
				'terbilang' => "Seratus Ribu Rupiah ",
				'keterangan' => "LUNAS S/D AGUSTUS 2020. Ini adalah keterangan penagihan gais!",
				'bulan_penagihan' => "Juni 2019",
				'masa_aktif' => "20 Juli 2020",
				// 'tgl_instalasi' => "2019-10-20",
				'tgl_instalasi' => "0000-00-00",
				'expired' => "2019-11-20",
				'serial_number' => "ZTEGC84ED8" . $i,
				'status_map' => "Ada",
				'lokasi_map' => "https://www.youtube.com/",
				'telp' => "0852 9847 1111",
				'url_gambar' => base_url() . '/assets/tempQr/img/A2006000000.png',
				'tarif_rp' => "Rp. 100.000,-",
				'tarif_rp_trx' => "Rp. 100.10" . $i . ",-",
			);
		}

		$rek = $this->db->query("SELECT * FROM settings WHERE option_name LIKE 'bri_%'")->result();
			$rekening = array();
			foreach ($rek as $rk) {
				if ($rk->option_name === 'bri_bank') {
					$rekening['nama_bank'] = $rk->option_value;
				}
				if ($rk->option_name === 'bri_nama_pemilik_rekening') {
					$rekening['pemilik_rekening'] = $rk->option_value;
				}
				if ($rk->option_name === 'bri_no_rekening') {
					$rekening['no_rekening'] = $rk->option_value;
				}
			}
		


		$data = array(
			'logo' => base_url() . '/assets/posonet/img/primahomelogo3.png',
			'rekening' => $rekening,
			'cust' => $cust,
			'company' => $this->kwitansi->profil_perusahaan(),
			'terms' => array(),
			'namafile' => FCPATH . 'assets/invoice/KWA_TEST.pdf',
			'outputMode' => 'STREAM', // STREAM = just temporarly open in browser | FILE = save to storage server
		);
		// echo json_encode($data);
		// var_dump($data);
		$this->load->view('admin/kwitansi/invoice_inet', $data);
	}
}
