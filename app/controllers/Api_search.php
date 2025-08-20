<?php defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set("Asia/Makassar");

class Api_search extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		if (!is_logged_in()) {
			redirect('login');
		}
		$this->load->model('api_model', 'api');
		$this->load->helper(array('MY_ribuan', 'MY_bulan'));
	}

	public function index()
	{
		set_status_header(401);
	}

	public function cari_plgn()
	{
		if (!empty($_GET['q'])) {
			$q = $_GET['q'];
			$list = $this->api->get_data($q);
			$data = array();
			$data2 = array();

			foreach ($list as $br) {
				$row = array();

				$row["id"] = $br->id_pelanggan;
				$row["text"] = "$br->kode_pelanggan | $br->nama_lengkap";
				$row["items_detail"] = array(
					'id' => $br->id_pelanggan,
					'kode' => $br->kode_pelanggan,
					'nama' => $br->nama_lengkap,
					'wilayah' => $br->wilayah,
					'alamat' => $br->alamat,
					'status' => $br->status,
				);
				$data[] = $row;
			}

			$output = array(
				'items' => $data,
			);

			echo json_encode($output);
		}
	}

	public function cek_tunggakan()
	{
		$kode_pelanggan = $this->input->post('cek');
		$cekPelanggan = $this->db->query("SELECT alamat, wilayah, nama_lengkap, kode_pelanggan, tarif, status, telp, keterangan FROM v_pelanggan WHERE kode_pelanggan = '$kode_pelanggan'");
		if ($cekPelanggan->num_rows() > 0) {
			// Cek Tunggakan
			$query = $this->api->cek_tunggakan_by($kode_pelanggan);
			$banyak_tunggakan = $query->num_rows();
			// $detail_pelanggan = $query->first_row('array');
			$detail_pelanggan = $cekPelanggan->row();
			$tunggakan_akhir = $query->last_row('array');
			$total = 0;
			$dataTunggakan = array();
			$row2 = "";
			foreach ($query->result() as $dt) {
				// total tunggakan
				$total += $dt->tarif;
				$r = array();
				$r[] = $dt->kode_invoice;
				$r[] = substr($dt->bulan_penagihan, 0, 7);
				$r[] = $dt->status;
				$r[] = number_format($dt->tarif, 0, ",", ".");
				$r[] = "<button class='btn btn-warning btn-xs' onclick=\"pemutihan('$dt->kode_invoice')\">Putihkan?</button>";
				$dataTunggakan[] = $r;

				// gunakan ini jika tidak menggunakan datatable processing
				// $row .= "<tr>
				// <td>$dt->kode_invoice</td>
				// <td>".substr($dt->bulan_penagihan,0,7)."</td>
				// <td>$dt->status</td>
				// <td>".number_format($dt->tarif,0,",",".")."</td>
				// <td><button class='btn btn-warning btn-xs' onclick=\"pemutihan('$dt->kode_invoice')\">Exclude</button></td>
				// </tr>
				// ";
			}
			$footerTotal = "<tr class='pull-right'>
				<td class=\"font-bold text-right\">Total </td>
				<td class=\"font-bold text-left\">Rp " . ribuan($total) . ",-</td>
			</tr>";

			// Cek 5 Pembayaran Terakhir
			$limit = 5;
			$query2 = $this->api->cek_pembayaran_terakhir($kode_pelanggan, $limit);

			foreach ($query2 as $dt2) {
				$row2 .= "<tr class='$dt2->kode_invoice'>
				<td>$dt2->kode_invoice</td>
				<td>" . substr($dt2->bulan_penagihan, 0, 7) . "</td>
				<td>$dt2->status</td>
				<td>" . number_format($dt2->tarif, 0, ",", ".") . "</td>
				<td><a href=\"javascript:void(0)\" onclick=\"invoice($dt2->kode_invoice)\" title=\"Lihat Kwitansi?\" class=\"btn btn-xs btn-primary\">Invoice</a></td>
				</tr>";
			}
			// Output
			$output = array(
				'result' => true,
				'message' => 'Pelanggan ditemukan!',
				'tunggakan' =>  $dataTunggakan,
				'footertotal' => $footerTotal,
				'banyak_tunggakan' =>  $banyak_tunggakan,
				'detail_pelanggan' =>  $detail_pelanggan,
				// 'tunggakan_akhir' =>  $tunggakan_akhir->bulan_penagihan,
				'total_tunggakan' =>  ribuan($total),
				'pembayaran_terakhir' =>  $row2,
			);
			echo json_encode($output);
		} else {
			$output = array(
				'result' => false,
				'message' => 'Pelanggan tidak ditemukan!',
				'tunggakan' =>  null,
				'banyak_tunggakan' =>  0,
				'detail_pelanggan' =>  null,
				'total_tunggakan' =>  0,
				'pembayaran_terakhir' =>  null,
			);
			echo json_encode($output);
		}
	}

	public function pemutihan()
	{
		$invoiceKey = html_escape($this->input->post('invoice_key'));
		$invoice = html_escape($this->input->post('kode_invoice'));
		$ket = html_escape($this->input->post('keterangan'));
		$tgl = date('Y-m-d');


		if ($this->_confirm_pass($invoiceKey)) {
			$exist = ($this->db->query("SELECT * FROM detail_setoran WHERE kode_invoice = '$invoice'")->num_rows() == 0) ? false : true;
			if (!$exist) {
				// Update status to 'Diputihkan' from table temp_invoice
				$updateTemp = $this->db->query("UPDATE temp_invoice SET status = 'Diputihkan', tgl_bayar = '$tgl', keterangan = '$ket' WHERE kode_invoice = '$invoice'");
				// Get data from table temp_invoice and prepare send to detail_setoran
				$getTemp = $this->db->query("SELECT * FROM temp_invoice WHERE kode_invoice = '$invoice'")->row();
				//cek if master_setoran have data. if not, create master_setoran data first time
				$msHaveDataExist = $this->db->query("SELECT * FROM master_setoran")->num_rows();
				$tgll = date('Y-m-d');
				if ($msHaveDataExist == 0) {
					$this->db->query("INSERT INTO master_setoran (tgl_setoran,id_kolektor,keterangan) VALUES('$tgll',$getTemp->id_kolektor,'Created by system because master_setoran data is empty!')");
				}
				// Get last id_master_setoran by id_kolektor (last inserted from master setoran)
				$findIdMasterSetoran = $this->db->query("SELECT m.id_master_setoran, m.id_kolektor
										FROM master_setoran m
										WHERE m.tgl_setoran = (SELECT MAX(tgl_setoran) FROM master_setoran WHERE id_kolektor = '$getTemp->id_kolektor')")->row();
				if ($findIdMasterSetoran->id_master_setoran == null) {
					$url = site_url('dashboard/master_setoran');
					echo json_encode(['status' => false, 'msg' => "Master setoran untuk tagihan ini belum dibuat! <br>Klik <a href='$url' >DISINI</a>", 'keterangan' => $ket, 'kode_invoice' => $invoice]);
					exit;
				} else {
					$fid = $findIdMasterSetoran->id_master_setoran;
				}

				$data = array(
					'id_master_setoran' => $fid,
					'kode_invoice' => $getTemp->kode_invoice,
					'kode_pelanggan' => $getTemp->kode_pelanggan,
					'bulan_penagihan' => $getTemp->bulan_penagihan,
					'status' => 'Diputihkan',
					'tgl_bayar' => $tgl,
					'keterangan' => $ket,
					'remark' => 0,
				);

				// echo json_encode($data);
				$this->db->insert('detail_setoran', $data);
				// $insert = $this->db->query("INSERT INTO pemutihan SELECT * FROM v_temp_invoice_for_pemutihan WHERE kode_invoice = '$invoice'");
				// $updatePemutihan = $this->db->query("UPDATE pemutihan SET status = 'Diputihkan', tgl_bayar = '$tgl', keterangan = '$ket' WHERE kode_invoice = '$invoice'");
				echo json_encode(['status' => true, 'msg' => "Tagihan <b> $invoice </b> telah diputihkan!", 'keterangan' => $ket, 'kode_invoice' => $invoice]);
			} else {
				echo json_encode(['status' => false, 'msg' => "Tagihan <b> $invoice </b> sudah diputihkan sebelumnya!", 'keterangan' => $ket, 'kode_invoice' => $invoice]);
			}
		} else {
			echo json_encode(['status' => false, 'msg' => 'Sandi invoice salah!', 'keterangan' => $ket, 'kode_invoice' => $invoice]);
		}
	}

	private function _confirm_pass($invoiceKey = '')
	{
		$getKey = $this->api->getSettings('invoice_key', $invoiceKey); // option_name , option value // Validasi InvoiceKey pada database. jika sama, buat kwitansi
		if ($invoiceKey != '' && $getKey->num_rows() === 1) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/*
	--------------------------------
	UNTUK FUNGSI PADA DASHBOARD
	--------------------------------
	1) TOTAL PELANGGAN
		$status =
			1 = aktif,
			2 = putus_sementara,
			3 = putus,
			null(semua status)

		$wilayah = (number) id_wilayah
			null = (semua wilayah)

		*** Cara Penggunaan ***
		a) total_pelanggan(1/44) 	-> menghasilkan jumlah pelanggan 'Aktif' berdasarkan 'Wilayah'
		b) total_pelanggan(1) 		-> menghasilkan jumlah pelanggan 'Aktif' secara keseluruhan
		c) total_pelanggan(null) 	-> menghasilkan jumlah pelanggan secara keseluruhan (entah aktif maupun non-aktif)
		c) total_pelanggan(null/44) 	-> menghasilkan jumlah pelanggan dengan 'Status' secara keseluruhan berdasarkan 'Wilayah'
  */

	private function _total_pelanggan($status = '', $wilayah = '')
	{
		if ($status == '')
			$status = 'null';
		if ($wilayah == '')
			$wilayah = 'null';

		return $this->api->count_pelanggan($status, $wilayah);
	}

	private function _total_wilayah()
	{
		return $this->api->count_wilayah();
	}

	public function dashboard_data()
	{
		$row = $data = $pelanggan = array();
		$wilayah = "";
		$bulan_penagihan = $this->input->post('bulan_penagihan');
		$row["total_pelanggan"] = $this->_total_pelanggan()->total_pelanggan;
		$row["pelanggan_aktif"] = $this->_total_pelanggan(1)->total_pelanggan;
		$row["pelanggan_putus_sementara"] = $this->_total_pelanggan(2)->total_pelanggan;
		$row["pelanggan_non_aktif"] = $this->_total_pelanggan(3)->total_pelanggan;
		$bywilayah = $this->api->count_bywilayah();
		$pelanggan = $row;

		foreach ($bywilayah as $wil) {
			$wilayah .= "<li><a role=\"menuitem\" href=\"#\"> $wil->wilayah <span class=\"text-success font-bold pull-right\">$wil->jumlah</span></a></li>";
		}

		// Untuk myDoughnutChart
		// @chart.js
		$bgcolor 	= array(
			'#FF6384', '#36A2EB', '#FFCE56', '#7B241C', '#D84315', '#633974', '#1A5276', '#117864', '#9A7D0A', '#5F6A6A',
			'#9C640C', '#1C2833', '#21618C', '#F4511E', '#00897B', '#039BE5', '#33691E', '#212121', '#1A237E', '#B71C1C',
			'#001f4d', '#003d4d', '#006600', '#663300', '#336600', '#ff6600', '#e6e600', '#660033', '#550080', '#24248f'
		);
		$hovercolor = array(
			'#FF6384', '#36A2EB', '#FFCE56', '#CD6155', '#FF5722', '#9B59B6', '#2980B9', '#16A085', '#F1C40F', '#BDC3C7',
			'#D35400', '#566573', '#3498DB', '#FF5722', '#26A69A', '#29B6F6', '#4CAF50', '#9E9E9E', '#3F51B5', '#F44336',
			'#0052cc', '#00a3cc', '#009900', '#b35900', '#59b300', '#ff8533', '#ffff00', '#cc0066', '#9900e6', '#4747d1'
		);

		$i = 0;
		$pie = $labels = $bgColor = $hovColor = array();
		foreach ($bywilayah as $p) {
			if ($i == count($bgcolor)) {
				$i = 0;
			}
			$labels[] 	= strtoupper($p->wilayah);
			$pie[] 		= $p->jumlah;
			$bgColor[] 	= $bgcolor[$i];
			$hovColor[] = $hovercolor[$i];
			$i++;
		}

		$output = array(
			'pelanggan' => $pelanggan,
			'total_wilayah' => $this->_total_wilayah()->total_wilayah,
			'wilayah' => $wilayah,
			'pencapaian' => $this->_pencapaian($bulan_penagihan),
			// Untuk DoughnutChart
			'doughnutchart_data' => array(
				'labels' => $labels,
				'datasets' => array(array(
					'data' => $pie,
					'backgroundColor' => $bgColor,
					'hoverBackgroundColor' => $hovColor,
				))
			),
			'line_chart_data' => $this->_setoran_summary(),
			'chart_des' => array(
				'total_setoran' => $this->_total_setoran_perbulan(),
				'max_setoran' => $this->_max_setoran(),
				'update_on' => date('d.m.Y'),
				'last_month_summary' => $this->_last_month_summary(),
			),
		);

		echo json_encode($output);
	}

	private function _setoran_summary()
	{
		$tahun = date('Y');
		// $bulan = 4;
		$bulan = (int) date('m');
		$res_kolektor = $this->api->get_kolektor();
		foreach ($res_kolektor as $u) {
			$nama_kolektor = ($u->nama_kolektor != NULL) ? $u->nama_kolektor : 'Unknown';
			for ($bln = 1; $bln <= $bulan; $bln++) {
				$ro = $this->api->get_setoran_summary($tahun, $bln, $u->id_kolektor);
				ini_set('display_errors', 0); // jika setoran ganjil maka terjadi error. fungsi ini utk mematikannya
				$data[$bln - 1] = ($ro->subtotal != NULL) ? $ro->subtotal : 0;
			}

			$r = rand(90, 254);
			$g = rand(110, 254);
			$b = rand(160, 254);
			$datasets[] = array(
				'label' => $nama_kolektor,
				'backgroundColor' => "rgba($r,$g,$b,0.3)",
				'borderColor' => "rgba($r,$g,$b,0.7)",
				'pointBackgroundColor' => "rgba($r,$g,$b,1)",
				'pointBorderColor' => '#fff',
				'data' => $data,
			);
		}
		ini_set('display_errors', 0); // jika setoran ganjil maka terjadi error. fungsi ini utk mematikannya

		for ($x = 1; $x <= $bulan; $x++) {
			$labels[] = bulan($x);
		}

		$output = array(
			'labels' => $labels,
			'datasets' => $datasets,
		);

		return $output;
		// echo json_encode($output);
	}

	private function _total_setoran_perbulan()
	{
		$bulan = date('Y-m');
		$bln = (int) date('m');
		$q = $this->api->total_setoran_by($bulan);
		$total = ($q->remark != NULL) ? $q->remark : 0;
		return array('bulan' => bulan($bln) . ' ' . date('Y'), 'total_remark' => 'Rp ' . ribuan($q->total_remark), 'total' => 'IDR ' . ribuan($total));
	}

	private function _max_setoran()
	{
		$q = $this->api->get_max_setoran();
		return array('kolektor' => ucwords($q->kolektor), 'bulan' => bulan((int) substr($q->bulan, 5, 2)), 'total' => 'Rp ' . ribuan($q->max_setoran));
	}

	private function _last_month_summary()
	{
		$d = strtotime("-1 Months"); // Last month
		$bulan = date('Y-m', $d);
		$thn = (int) substr($bulan, 0, 4);
		$bln = (int) substr($bulan, 5, 2);
		$q = $this->api->total_setoran_by($bulan);
		$total = ($q->total == null) ? 0 : $q->total;
		$data = array(
			'bulan' => bulan($bln) . ' ' . $thn,
			'total' => 'IDR ' . ribuan($total),
			'total_remark' => 'IDR ' . ribuan($q->total_remark)
		);
		return $data;
	}

	private function _pencapaian($bulan_penagihan)
	{
		$q = $this->api->get_target($bulan_penagihan);
		$target = ($q['target']->target == NULL) ? "<span class='text-danger'><sub>Kwitansi belum dicetak <a href='" . base_url('dashboard/kwitansi') . "'>Cetak?</a></sub></span>" : $q['target']->target;
		$target_nonaktif = ($q['target_nonaktif']->target == NULL) ? 0 : $q['target_nonaktif']->target;
		$capai = ($q['capai']->capai == NULL) ? 0 : $q['capai']->capai;
		$capai_remark = ($q['capai']->capai_remark == NULL) ? 0 : $q['capai']->capai_remark;

		$rate_success	= ($q['target']->target != NULL) ? 100 - ((($target - $capai_remark) / $target) * 100) : 0;
		$rate_margin	= ($q['target']->target != NULL) ? ((($target - $capai) / $target) * 100) : 0;

		$msg = '';
		if ($rate_success >= 90) {
			$msg = "<div class=\"stat-percent font-bold text-navy\">" . round($rate_success, 2) . "% <i class=\"fa fa-bolt\"></i></div>";
		} elseif ($rate_success >= 80 && $rate_success < 90) {
			$msg = "<div class=\"stat-percent font-bold text-success\">" . round($rate_success, 2) . "% <i class=\"fa fa-bolt\"></i></div>";
		}
		elseif ($rate_success >= 70 && $rate_success < 80) {
			$msg = "<div class=\"stat-percent font-bold text-warning\">" . round($rate_success, 2) . "% <i class=\"fa fa-bolt\"></i></div>";
		}
		elseif ($rate_success < 70) {
			$msg = "<div class=\"stat-percent font-bold text-danger\">" . round($rate_success, 2) . "% <i class=\"fa fa-bolt\"></i></div>";
		}

		$msg1 = '';
		if ($rate_margin <= 10) {
			$msg1 = "<div class=\"stat-percent font-bold text-navy\">" . round($rate_margin, 2) . "% <i class=\"fa fa-bolt\"></i></div>";
		} elseif ($rate_margin > 10 && $rate_margin <= 20) {
			$msg1 = "<div class=\"stat-percent font-bold text-success\">" . round($rate_margin, 2) . "% <i class=\"fa fa-bolt\"></i></div>";
		}
		elseif ($rate_margin > 20 && $rate_margin <= 80) {
			$msg1 = "<div class=\"stat-percent font-bold text-warning\">" . round($rate_margin, 2) . "% <i class=\"fa fa-bolt\"></i></div>";
		}
		elseif ($rate_margin > 80) {
			$msg1 = "<div class=\"stat-percent font-bold text-danger\">" . round($rate_margin, 2) . "% <i class=\"fa fa-bolt\"></i></div>";
		}

		// Update statistik ke database
		$bulan = date('Y-m');
		$cek_stat = $this->api->cek_statistik($bulan);
		$data = array(
			'bulan' => date('Y-m-d'),
			'target' => $target,
			// 'target_nonaktif' => $target_nonaktif,
			'capaian' => $capai,
			'capaian_remark' => $capai_remark,
			'rate_success' => round($rate_success, 2),
			'rate_margin' => round($rate_margin, 2)
		);

		$jml = $cek_stat->num_rows();
		if ($jml == 0) {
			$save = $this->api->save_statistik($data);
		} elseif ($jml > 0) {
			$id = $cek_stat->row();
			$update = $this->api->update_statistik(array('id_statistik' => $id->id_statistik), $data);
		}

		// Return ke dashboard
		$ret = array(
			'target' => ribuan($target),
			'target_nonaktif' => ribuan($target_nonaktif),
			'tercapai' => ribuan($capai),
			'tercapai_remark' => ribuan($capai_remark),
			'tercapai_percent' => $msg,
			'margin' => $msg1
		);

		return $ret;
	}

	public function dashboard_donat()
	{
		$row = $data = $pelanggan = array();
		$wilayah = "";
		$row["total_pelanggan"] = $this->_total_pelanggan()->total_pelanggan;
		$row["pelanggan_aktif"] = $this->_total_pelanggan(1)->total_pelanggan;
		// $row["pelanggan_putus_sementara"] = $this->_total_pelanggan(2)->total_pelanggan;
		$row["pelanggan_non_aktif"] = $this->_total_pelanggan(2)->total_pelanggan;
		$bywilayah = $this->api->count_bywilayah();
		$pelanggan = $row;

		foreach ($bywilayah as $wil) {
			$wilayah .= "<li><a role=\"menuitem\" href=\"" . site_url('dashboard/pelanggan?search=') . $wil->wilayah . "\" target=\"_blank\"> $wil->wilayah <span class=\"text-success font-bold pull-right\">$wil->jumlah</span></a></li>";
		}

		// Untuk myDoughnutChart
		// @chart.js
		$bgcolor 	= array(
			'#FF6384', '#36A2EB', '#FFCE56', '#7B241C', '#D84315', '#633974', '#1A5276', '#117864', '#9A7D0A', '#5F6A6A',
			'#9C640C', '#1C2833', '#21618C', '#F4511E', '#00897B', '#039BE5', '#33691E', '#212121', '#1A237E', '#B71C1C',
			'#001f4d', '#003d4d', '#006600', '#663300', '#336600', '#ff6600', '#e6e600', '#660033', '#550080', '#24248f'
		);
		$hovercolor = array(
			'#FF6384', '#36A2EB', '#FFCE56', '#CD6155', '#FF5722', '#9B59B6', '#2980B9', '#16A085', '#F1C40F', '#BDC3C7',
			'#D35400', '#566573', '#3498DB', '#FF5722', '#26A69A', '#29B6F6', '#4CAF50', '#9E9E9E', '#3F51B5', '#F44336',
			'#0052cc', '#00a3cc', '#009900', '#b35900', '#59b300', '#ff8533', '#ffff00', '#cc0066', '#9900e6', '#4747d1'
		);

		$i = 0;
		$pie = $labels = $bgColor = $hovColor = array();
		foreach ($bywilayah as $p) {
			if ($i == count($bgcolor)) {
				$i = 0;
			}
			$labels[] 	= strtoupper($p->wilayah);
			$pie[] 		= $p->jumlah;
			$bgColor[] 	= $bgcolor[$i];
			$hovColor[] = $hovercolor[$i];
			$i++;
		}

		$output = array(
			'pelanggan' => $pelanggan,
			'total_wilayah' => $this->_total_wilayah()->total_wilayah,
			'wilayah' => $wilayah,
			// Untuk DoughnutChart
			'doughnutchart_data' => array(
				'labels' => $labels,
				'datasets' => array(array(
					'data' => $pie,
					'backgroundColor' => $bgColor,
					'hoverBackgroundColor' => $hovColor,
				))
			),
		);

		echo json_encode($output);
	}

	public function dashboard_summary()
	{
		$bulan_penagihan = $this->input->post('bulan_penagihan');
		$output = array(
			'pencapaian' => $this->_pencapaian($bulan_penagihan),
			'pengeluaran' => array(
				'konten' => '', //$this->pengeluaran_konten($bulan_penagihan),
				'penggajian' => null,
			),
		);
		echo json_encode($output);
	}

	public function dashboard_line()
	{
		$output = array(
			'line_chart_data' => $this->_setoran_summary(),
			'chart_des' => array(
				'total_setoran' => $this->_total_setoran_perbulan(),
				'max_setoran' => $this->_max_setoran(),
				'update_on' => date('d.m.Y'),
				'last_month_summary' => $this->_last_month_summary(),
			),
		);

		echo json_encode($output);
	}

	/* 
		PENGELUARAN KONTEN/VOUCHER STB
	*/

	public function pengeluaran_konten($tgl_pengeluaran)
	{
		$vchr = $this->db->query("SELECT SUBSTR(k.voucher_buy,1,7) AS tgl_pembelian, SUM(k.harga_voucher) AS total
			FROM konten k
			WHERE SUBSTR(k.voucher_buy,1,7) = SUBSTR('$tgl_pengeluaran',1,7)")->row();
		$vchr_ext = $this->db->query("SELECT SUBSTR(k.voucher_buy_ext,1,7) AS tgl_pembelian_ext, SUM(k.harga_voucher_ext) AS total_ext
			FROM konten k
			WHERE SUBSTR(k.voucher_buy_ext,1,7) = SUBSTR('$tgl_pengeluaran',1,7)")->row();
		$total_pembelian = $vchr->total + $vchr_ext->total_ext;

		$data = array('jumlah' => ($total_pembelian == null) ? 0 : $total_pembelian, 'bulan' => substr($tgl_pengeluaran, 0, 7));
		return $data;
	}

	/*
	|
	|
	|	SCRIPT PERCOBAAN
	|
	|
	*/
}
