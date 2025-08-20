<?php defined('BASEPATH') or exit('No direct script access allowed');
class Pembayaran extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		if (!is_logged_in()) {
			redirect('login?_rdr=' . urlencode(current_url()));
		}
		$this->load->model('detail_setoran_model', 'setoran');
		$this->load->model('profil_perusahaan_model', 'dsh');
		$this->load->model('api_rest_client_model', 'olt');
		$this->load->helper('MY_ribuan');;
	}

	public function index()
	{
		set_status_header(401);
	}

	public function detail($id_master_setoran = FALSE)
	{
		$data['profilP'] = $this->dsh->get_by_id(1);
		$data['detail_setoran'] = $this->setoran->getDetailKolektor($id_master_setoran);
		$data['paket'] = $this->db->query("SELECT * FROM paket ORDER BY tarif ASC")->result();
		$data['transfer'] = $this->db->query("SELECT * FROM karyawan WHERE id_karyawan = 3")->row();
		$data['active'] = "pembayaran";
		$this->load->view("admin/templates/header", $data);
		$this->load->view("admin/templates/navigation");
		$this->load->view("admin/pembayaran/pembayaran");
		$this->load->view("admin/templates/footer");
		$this->load->view("admin/pembayaran/js_pembayaran");
		// echo json_encode($data);
	}


	public function cari(){
		$cari = html_escape($this->input->post('cari'));
		$results = $this->db->query("SELECT no_pelanggan,nama_pelanggan,wilayah,nama_paket,tarif,expired FROM v_pelanggan WHERE no_pelanggan LIKE '$cari%' OR nama_pelanggan LIKE '%$cari%'")->result();
		$row = '';
		foreach ($results as $dt) {
			$row .= "<tr>
<td>$dt->no_pelanggan. $dt->nama_pelanggan</td>
<td>$dt->wilayah</td>
<td class=\"text-navy\"> <a href=\"#\" class=\"ladda-button btn btn-warning btn-xs\" data-style=\"zoom-in\" onclick=\"getDetailInvoice($dt->no_pelanggan)\">Cek</a> </td>
</tr>";
		}
		echo $row;
	}


	public function getDetailInvoice() {
		$nopel = html_escape($this->input->post('no_pelanggan'));
		
		$rp = $this->db->query("SELECT * FROM v_pelanggan WHERE no_pelanggan = $nopel")->row();
		$rs = $this->db->query("SELECT * FROM v_temp_invoice WHERE bulan_penagihan = (SELECT MAX(bulan_penagihan) FROM v_temp_invoice) AND no_pelanggan = $nopel")->row();

		$data = '<ul class="list-group clear-list">
                                        <li class="list-group-item fist-item">
                                            <span class="pull-right">
                                                <h3><strong class="text-success"><span id="dtNopelNama">'.$rs->no_pelanggan."-".$rs->nama_pelanggan.'</span></strong></h3>
                                            </span>
                                            <strong>No Internet/Pelanggan</strong>
                                        </li>

                                        <li class="list-group-item fist-item">
                                            <span class="pull-right">
                                                <strong class="text-success"><span id="dtPaket">'.$rs->kode_invoice.'<span></strong>
                                            </span>
                                            <strong>Kode Invoice</strong>
                                        </li>

                                        <li class="list-group-item fist-item">
                                            <span class="pull-right">
                                                <strong class="text-success"><span id="dtPaket">'.$rp->nama_paket.'<span></strong>
                                            </span>
                                            <strong>Paket</strong>
                                        </li>

                                        <li class="list-group-item">
                                            <span class="pull-right">
                                                <strong class="text-warning"><span id="dtExpired">'.$rp->expired.'</span></strong>
                                            </span>
                                            <strong>Last Expired</strong>
                                        </li>
                                        
                                        <li class="list-group-item">
                                            <span class="pull-right" id="dtTarif" style="font-size: 12pt; font-weight: bold;">
                                                '. number_format($rp->tarif, 0, ",", ".").'
                                            </span>
                                            <strong>Tagihan Rp</strong>
                                        </li>

                                        <li class="list-group-item">
                                            <span class="pull-right">
                                                <strong class="text-danger"><span id="dtStatusBayar">'.$rs->status.'</span></strong>
                                            </span>
                                            <strong>Status Bayar</strong>
                                        </li>

                                        <li class="list-group-item">
                                            <span class="pull-right">
                                                <a href="#" class="ladda-button btn btn-warning" data-style="zoom-in" onclick="proses_pembayaran('.$rs->no_pelanggan.')">Bayar Sekarang</a>
                                            </span>
                                            <strong></strong>
                                        </li>
                                    </ul>';
		
		echo $data;
	}








	/*
		CRUD detail Setoran
	*/
	public function checkInvoice($id, $kodeInvoice, $id_kolektor, $id_karyawan_kolektor)
	{
		// cek data di temp_invoice & detail_setoran
		$cek = $this->setoran->cekInvoiceCode($id, $kodeInvoice, $id_kolektor);
		if ($cek == 3) {
			$data = array(
				'status' => TRUE,
				'message' => 'Duplikat input! Data sudah ada dalam setoran sebelumnya.',
				'title' => 'Sudah diinput!',
				'data' => null,
				'code' => 3,
			);
		} else if ($cek == 0) {
			$data = array(
				'status' => TRUE,
				'message' => 'INVOICE belum terdaftar dalam database!',
				'title' => 'Unknown Invoice!',
				'data' => null,
				'code' => 0,
			);
		} else if ($cek == 4) {
			$simpan = $this->_save_detail_setoran($id, $kodeInvoice, $id_kolektor, $id_karyawan_kolektor);
			$data = array(
				'status' => TRUE,
				'message' => 'Invoice manifested!',
				'title' => 'Sukses!',
				'data' => $simpan,
				'code' => 4,
			);
		} else if ($cek == 2) {
			$data = array(
				'status' => TRUE,
				'message' => 'Invoice & Kolektor tidak sesuai atau Wilayah INVOICE tidak sesuai wilayah KOLEKTOR!',
				'title' => 'Salah input!',
				'data' => null,
				'code' => 2,
			);
		}

		echo json_encode($data);
	}

	private function _save_detail_setoran($id, $kodeInvoice, $id_kolektor, $id_karyawan_kolektor)
	{
		$dataTemp = $this->setoran->getDataInvoiceBy($kodeInvoice);
		if ($dataTemp->num_rows() == 1) {
			$dt = $dataTemp->row();
			$data = array(
				'id_master_setoran' => $id,
				'kode_invoice' => $dt->kode_invoice,
				'no_pelanggan' => $dt->no_pelanggan,
				'bulan_penagihan' => $dt->bulan_penagihan,
				'status' => 'Lunas',
				'metode_pembayaran' => 'kolektor',
				'id_kolektor' => $id_kolektor,
				'penerima' => $id_karyawan_kolektor,
				'tgl_input' => date('Y-m-d'),
				'remark' => $dt->tarif,
				'kode_wilayah' => $dt->kode_wilayah,
				'tarif' => $dt->tarif,
				'expired' => $dt->expired,
			);

			$dataUpdate = array(
				'status' => 'Lunas',
				'tgl_penyetoran' => date('Y-m-d'),
			);

			$this->update_status_temp_invoice($kodeInvoice, $dataUpdate);
			$insert = $this->setoran->save($data);
			$extend = $this->olt->perpanjangPaketFromDetailSetoran($dt->no_pelanggan, $dt->expired);
			$update = $this->db->query("UPDATE FROM pelanggan SET expired='$dt->expired' WHERE no_pelanggan='$dt->no_pelanggan'")->affected_rows();
			return array('status' => TRUE, 'msg' => $extend);
		} else {
			return array('status' => FALSE, 'msg' => '');
		}
	}

	public function update_status_temp_invoice($kodeInvoice, $dataUpdate)
	{
		$update = $this->setoran->updateStatusTemp(array('kode_invoice' => $kodeInvoice), $dataUpdate);
	}

	public function get_edit($id = FALSE)
	{
		$data = $this->setoran->get_by_id($id);
		echo json_encode($data);
	}

	public function list_setoran($id_master_setoran)
	{
		$list = $this->setoran->listSetoranBy($id_master_setoran);
		$this->_updateTotalSetoran($id_master_setoran);

		$row = '';
		if ($list->num_rows() > 0) {
			foreach ($list->result() as $d) {
				if ($d->metode_pembayaran == 'transfer') {
					$metode = '<span class="label label-info"><i class="fa fa-exchange"></i> ' . ucwords($d->metode_pembayaran) . '</span>';
				} elseif ($d->metode_pembayaran == 'kolektor') {
					$metode = '<span class="label label-primary"><i class="fa fa-handshake-o"></i> ' . ucwords($d->metode_pembayaran) . '</span>';
				} elseif ($d->metode_pembayaran == 'antar') {
					$metode = '<span class="label label-success"><i class="fa fa-home"></i> ' . ucwords($d->metode_pembayaran) . '</span>';
				} else {
					$metode = '<span class="label label-default"><i class="fa fa-times"></i> ' . ucwords($d->metode_pembayaran) . '</span>';
				}
				// $status = ($d->status == 'Lunas') ? "<i class='fa fa-check fa-2x text-success' data-toggle='tooltip' title='$d->status'></i>" : "<i class='fa fa-clock-o fa-2x' data-toggle='tooltip' title='$d->status'></i>" ;
				$row .= "<tr class='success' title='Keterangan : $d->keterangan'>
				<td><div class=\"i-checks\"><label> <input type=\"checkbox\" name=\"checked_inv[]\" value=\"$d->kode_invoice\"> <i></i> $d->kode_invoice </label></div></td>
				<td>$d->no_pelanggan</td>
				<td>$d->nama_pelanggan</td>
				<td>" . substr($d->bulan_penagihan, 0, 7) . "</td>
				<td>" . $metode . " <span class=\"label label-default\"><i class=\"fa fa-user\" aria-hidden=\"true\"></i> " . ucwords($d->nama_penerima) . "</span></td>
				<td>" . number_format($d->tarif, 0, ",", ".") . "</td>
				<td>" . number_format($d->tarif, 0, ",", ".") . "</td>
				<td><span class='text-warning'>$d->keterangan</span></td>
				<td class='text-center'>
					<div class=\"btn-group\">
						<button class=\"btn btn-xs btn-info\" type=\"button\" onclick=\"getKeterangan('$d->no_pelanggan','$d->kode_invoice')\" title=\"Tambah keterangan?\"><i class='fa fa-info'></i> Ket</button>
						<button class=\"btn btn-xs btn-danger\" type=\"button\" onclick=\"delete_by('$d->kode_invoice')\" title=\"Hapus hasil scan?\"><i class='fa fa-trash'></i> Hapus</button>
					</div>
				</td>
				</tr>";
			}
		} else {
			$row .= '<tr class="danger">
				<td colspan="9" class="text-center text-danger"><h3>Tidak ada data! Silahkan Scan Robekan Kwitansi pada Kamera!</h3></td>
			</tr>';
		}

		$data = array(
			'data' => $row,
		);

		echo json_encode($data);
	}

	public function invoiceCountBy($id_master_setoran)
	{
		$q = $this->setoran->getInvoiceCountBy($id_master_setoran);

		$row = '';
		$no = 0;
		$totaljml = 0;
		$totalrp = 0;
		if ($q->num_rows() > 0) {
			foreach ($q->result() as $d) {
				$no++;
				$row .= "
				<tr>
					<td>$no</td>
					<td class='text-right'>" . number_format($d->tarif, 0, ",", ".") . "</td>
					<td class='text-right'>$d->jumlah</td>
					<td class='text-right'>" . number_format($d->subtotal, 0, ",", ".") . "</td>
				</tr>";
				$totaljml += $d->jumlah;
				$totalrp += $d->subtotal;
			}
		} else {
			$row .= '<tr class="noData">
				<td colspan="4" class="text-center text-danger"><h3>Tidak ada data!</h3></td>
			</tr>';
		}

		$row .= "<tr><td colspan='2' class='info'>Total Setoran</td><td class='text-right'><strong>$totaljml</strong></td><td class='text-right'><strong>Rp " . number_format($totalrp, 0, ",", ".") . "</strong></td></tr>";
		$row .= "<tr><td colspan='2' class='warning'>Total Komisi</td><td class='text-right'><strong>x 3.000</strong></td><td class='text-right'><strong>Rp " . number_format($totaljml * 3000, 0, ",", ".") . "</strong></td></tr>";
		$row .= "<tr><td colspan='2' class='success'>Setoran Masuk</td><td class='text-right'><strong></strong></td><td class='text-right'><strong>Rp " . number_format($totalrp - ($totaljml * 3000), 0, ",", ".") . "</strong></td></tr>";

		$data = array(
			'data' => $row,
			'total_kwitansi' => $totaljml,
			'total_rp' => $totalrp,
		);

		echo json_encode($data);
	}

	public function invoiceCountBy2($id_master_setoran)
	{
		$q = $this->setoran->getInvoiceCountBy2($id_master_setoran);
		echo json_encode(['data' => "<strong class='text-primary'>" . ribuan($q->total_remark) . "</strong>"]);
	}

	public function get_keterangan($no_pelanggan, $kode_invoice)
	{
		$q = $this->setoran->getKeteranganPlgn($no_pelanggan);
		$inv = html_escape($kode_invoice);
		$qremark = $this->db->query("SELECT remark, metode_pembayaran, penerima FROM detail_setoran WHERE kode_invoice = '$inv' ")->row();
		$remark = ($qremark->remark == null) ? $q->tarif : $qremark->remark;
		$data = array(
			'no_pelanggan' => $q->no_pelanggan,
			'keterangan' => $q->keterangan,
			'remark' => $remark,
			'metode_pembayaran' => $qremark->metode_pembayaran,
			'penerima' => $qremark->penerima,
		);
		echo json_encode($data);
	}

	public function save_keterangan()
	{
		$data = array(
			'keterangan' => html_escape($this->input->post('md_keterangan')),
		);

		$data3 = array(
			'metode_pembayaran' => html_escape($this->input->post('metode_pembayaran')),
			'penerima_kolektor' => html_escape($this->input->post('id_karyawan_kolektor')),
			'penerima' => html_escape($this->input->post('id_karyawan')),
			'keterangan' => html_escape($this->input->post('md_keterangan')),
		);

		if ($data3['metode_pembayaran'] == 'antar') {
			$penerima = $data3['penerima'];
		} elseif ($data3['metode_pembayaran'] == 'kolektor') {
			$penerima =	$data3['penerima_kolektor'];
		} else {
			$penerima = 3; //by default 3 adalah id_karyawan BANK BRI di tabel karyawan
		}

		if ($data3['metode_pembayaran'] == 'transfer') {
			$metode = '<span class="label label-info"><i class="fa fa-exchange"></i> ' . ucwords($data3['metode_pembayaran']) . '</span>';
		} elseif ($data3['metode_pembayaran'] == 'kolektor') {
			$metode = '<span class="label label-primary"><i class="fa fa-handshake-o"></i> ' . ucwords($data3['metode_pembayaran']) . '</span>';
		} elseif ($data3['metode_pembayaran'] == 'antar') {
			$metode = '<span class="label label-success"><i class="fa fa-home"></i> ' . ucwords($data3['metode_pembayaran']) . '</span>';
		} else {
			$metode = '<span class="label label-default"><i class="fa fa-times"></i> ' . ucwords($data3['metode_pembayaran']) . '</span>';
		}

		if (html_escape($this->input->post('remark') == 0)) {
			$data2 = array(
				'metode_pembayaran' => $data3['metode_pembayaran'],
				'penerima' => $penerima,
				'status' => 'Diputihkan',
				'keterangan' => html_escape($this->input->post('md_keterangan')),
				'remark' => html_escape($this->input->post('remark')),
			);
			$this->db->update('temp_invoice', ['status' => 'Diputihkan'], ['kode_invoice' => html_escape($this->input->post('md_kode_invoice'))]);
			$this->db->update('detail_setoran', ['status' => 'Diputihkan'], ['kode_invoice' => html_escape($this->input->post('md_kode_invoice'))]);
		} else {
			$data2 = array(
				'metode_pembayaran' => $data3['metode_pembayaran'],
				'penerima' => $penerima,
				'status' => 'Lunas',
				'keterangan' => $data3['keterangan'],
				'remark' =>	html_escape($this->input->post('remark')),
			);
			$this->db->update('temp_invoice', ['status' => 'Lunas'], ['kode_invoice' => html_escape($this->input->post('md_kode_invoice'))]);
			$this->db->update('detail_setoran', ['status' => 'Lunas'], ['kode_invoice' => html_escape($this->input->post('md_kode_invoice'))]);
		}

		$kodeInv = html_escape($this->input->post('md_kode_invoice'));

		$this->setoran->updateKeteranganPlgn(array('no_pelanggan' => html_escape($this->input->post('md_no_pelanggan'))), $data);
		$this->setoran->updateKeterangan(array('kode_invoice' => $kodeInv), $data2);
		// get nama penerima from v_detail_setoran
		$receiver = $this->db->query("SELECT * FROM v_detail_setoran WHERE kode_invoice = '$kodeInv'")->row();
		echo json_encode(
			array(
				"status" => TRUE,
				"remark" => number_format($data2['remark'], 0, ",", "."),
				"keterangan" => "<span class='text-warning'>" . $data2['keterangan'] . "</span>",
				// "datatiga" => $data3,
				"metode_pembayaran" => $metode . " <span class=\"label label-default\"><i class=\"fa fa-user\" aria-hidden=\"true\"></i> " . ucwords($receiver->nama_penerima) . "</span>",
			)
		);
	}

	public function delete_by($kode_invoice)
	{
		$dataUpdate = array(
			'status' => 'Belum Bayar',
			'tgl_penyetoran' => '0000-00-00',
		);
		try {
			$this->db->trans_begin();
			$this->update_status_temp_invoice($kode_invoice, $dataUpdate);
			$this->setoran->deleteBy($kode_invoice);
			echo json_encode(array("status" => TRUE));
			$this->db->trans_commit();
		} catch (\Throwable $th) {
			$this->db->trans_rollback();
			echo json_encode(array("status" => FALSE));
		}
	}

	public function delAllBy($id_master_setoran)
	{
		$this->setoran->deleteAllBy($id_master_setoran);
		echo json_encode(array("status" => TRUE));
	}

	private function _updateTotalSetoran($id_master_setoran)
	{
		$q = $this->setoran->hitungSetoranBy($id_master_setoran);
		$data = array(
			'total_setoran' => $q->total_setoran,
			'total_setoran_remark' => $q->total_remark,
			'lembar' => $q->lembar,
			'komisi' => $q->komisi,
		);
		$this->setoran->updateMasterSetoran(array('id_master_setoran' => $id_master_setoran), $data);
		return json_encode(array("status" => TRUE));
	}

	public function tesdatax($id_master_setoran)
	{
		$list = $this->setoran->listSetoranBy($id_master_setoran);
		$this->_updateTotalSetoran($id_master_setoran);
		$no = 1;
		$dt = array();
		if ($list->num_rows() > 0) {
			foreach ($list->result() as $d) {
				$row = array();
				// $status = ($d->status == 'Lunas') ? "<i class='fa fa-check fa-2x text-success' data-toggle='tooltip' title='$d->status'></i>" : "<i class='fa fa-clock-o fa-2x' data-toggle='tooltip' title='$d->status'></i>" ;
				// $row[] = "<input type=\"checkbox\" name=\"checked_inv[]\" value=\"$d->kode_invoice\" class=\"i-checks\">";
				$row[] = $no;
				$row[] = "<b>$d->kode_invoice</b>";
				$row[] = $d->no_pelanggan;
				$row[] = $d->nama_pelanggan;
				$row[] = substr($d->bulan_penagihan, 0, 7);
				if ($d->metode_pembayaran == 'transfer') {
					$metode = '<span class="label label-info"><i class="fa fa-exchange"></i> ' . ucwords($d->metode_pembayaran) . '</span>';
				} elseif ($d->metode_pembayaran == 'kolektor') {
					$metode = '<span class="label label-primary"><i class="fa fa-handshake-o"></i> ' . ucwords($d->metode_pembayaran) . '</span>';
				} elseif ($d->metode_pembayaran == 'antar') {
					$metode = '<span class="label label-success"><i class="fa fa-home"></i> ' . ucwords($d->metode_pembayaran) . '</span>';
				} else {
					$metode = '<span class="label label-default"><i class="fa fa-times"></i> ' . ucwords($d->metode_pembayaran) . '</span>';
				}
				$row[] = $metode . ' <span class="label label-default"><i class="fa fa-user" aria-hidden="true"></i> ' . ucwords($d->nama_penerima) . '</span>';
				$row[] = number_format($d->tarif, 0, ",", ".");
				$row[] = ($d->remark == 0) ? 'DIPUTIHKAN' : number_format($d->remark, 0, ",", ".");
				$row[] = "<span class='text-warning'>$d->keterangan</span>";
				$row[] = "<div class=\"btn-group text-center\">
						<button class=\"btn btn-xs btn-info\" type=\"button\" onclick=\"getKeterangan('$d->no_pelanggan','$d->kode_invoice')\" title=\"Tambah keterangan?\"><i class='fa fa-info'></i> Ket</button>
						<button class=\"btn btn-xs btn-danger\" type=\"button\" onclick=\"delete_by('$d->kode_invoice')\" title=\"Hapus hasil scan?\"><i class='fa fa-trash'></i> Hapus</button>
					</div>";
				$dt[] = $row;
				$no++;
			}
		} else {
			$dt = array(
				"draw" => 0,
				"recordsTotal" => 0,
				"recordsFiltered" => 0,
				"data" => [],
			);
			// $row[] = '<h3>Tidak ada data! Silahkan Scan Robekan Kwitansi pada Kamera!</h3>';
			// $dt[] = $row;
		}

		$data = array(
			'data' => $dt,
		);

		echo json_encode($data);
	}

	public function get_last_inserted($id_master_setoran, $kode_invoice)
	{
		$qd = $this->setoran->getLastInserted($id_master_setoran, $kode_invoice);
		if ($qd->num_rows() !== 0) {
			$d = $qd->row();
			$count = $this->setoran->getLastNum($id_master_setoran);
			// $count++;
			// $status = ($d->status == 'Lunas') ? "<i class='fa fa-check fa-2x text-success' data-toggle='tooltip' title='$d->status'></i>" : "<i class='fa fa-clock-o fa-2x' data-toggle='tooltip' title='$d->status'></i>" ;

			if ($d->metode_pembayaran == 'transfer') {
				$metode = '<span class="label label-info"><i class="fa fa-exchange"></i> ' . ucwords($d->metode_pembayaran) . '</span>';
			} elseif ($d->metode_pembayaran == 'kolektor') {
				$metode = '<span class="label label-primary"><i class="fa fa-handshake-o"></i> ' . ucwords($d->metode_pembayaran) . '</span>';
			} elseif ($d->metode_pembayaran == 'antar') {
				$metode = '<span class="label label-success"><i class="fa fa-home"></i> ' . ucwords($d->metode_pembayaran) . '</span>';
			} else {
				$metode = '<span class="label label-default"><i class="fa fa-times"></i> ' . ucwords($d->metode_pembayaran) . '</span>';
			}
			$data = array(
				'data' => array(
					$count,
					"<b>$d->kode_invoice</b>",
					$d->no_pelanggan,
					$d->nama_pelanggan,
					substr($d->bulan_penagihan, 0, 7),
					$metode . ' <span class="label label-default"><i class="fa fa-user" aria-hidden="true"></i> ' . ucwords($d->nama_penerima) . '</span>',
					number_format($d->tarif, 0, ",", "."),
					number_format($d->remark, 0, ",", "."),
					"<span class='text-warning'>$d->keterangan</span>",
					"<div class=\"btn-group text-center\">
						<button class=\"btn btn-xs btn-info\" type=\"button\" onclick=\"getKeterangan('$d->no_pelanggan','$d->kode_invoice')\" title=\"Tambah keterangan?\"><i class='fa fa-info'></i> Ket</button>
						<button class=\"btn btn-xs btn-danger\" type=\"button\" onclick=\"delete_by('$d->kode_invoice')\" title=\"Hapus hasil scan?\"><i class='fa fa-trash'></i> Hapus</button>
					</div>",
				),
				'status' => TRUE,
			);
			echo json_encode($data);
		} else {
			echo json_encode(array('status' => FALSE));
		}
	}
}
