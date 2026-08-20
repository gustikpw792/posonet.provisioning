<?php

use phpDocumentor\Reflection\Types\This;

 defined('BASEPATH') or exit('No direct script access allowed');
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
		
	}


	public function cari(){
		$cari = html_escape($this->input->get('cari'));
		$mode = html_escape($this->input->get('mode'));

		if ($mode == 'nama') {
			$results = $this->db->query("SELECT no_pelanggan,nama_pelanggan,wilayah,nama_paket,tarif,expired FROM v_pelanggan WHERE no_pelanggan LIKE '$cari%' OR nama_pelanggan LIKE '%$cari%'")->result();

			$row = '';
			foreach ($results as $dt) {
				$row .= "<tr>
						<td>$dt->no_pelanggan. $dt->nama_pelanggan</td>
						<td>$dt->wilayah</td>
						<td class=\"text-navy\"> <a href=\"#\" class=\"ladda-button btn btn-warning btn-xs\" data-style=\"zoom-in\" onclick=\"getDetailInvoice($dt->no_pelanggan)\">Cek Tagihan</a> </td>
					</tr>";
			}

			$data = array(
				'status' => true,
				'data' => $row,
				'mode' => $mode,
			);
			echo json_encode($data);
		}

	}


	public function get_detail_invoice()
	{
		$no_internet = html_escape($this->input->post('no_internet'));

		echo json_encode($this->_get_detail_invoice($no_internet));
	}


	private function _get_detail_invoice($no_internet)
	{
		$this->load->model('billing_model','billingModel');

		$billData = $this->billingModel->getBillData($no_internet);
		$data = $billData['data'];
		$status = $billData['status'];
		$message = $billData['message'];

		// set class
		$classPaid = ($data['billing']['status'] === 'PAID') ? 'label-success' : 'label-danger';
		$paidIcon = ($data['billing']['status'] === 'PAID') ? '✅' : '❌';
		$classExpired = ($data['billing']['status'] === 'PAID') ? 'text-success' : 'text-danger';
		$classStatus = ($data['subscription']['status'] === 'AKTIF') ? 'label-info' : 'label-danger';
		// data bisa dilihat di localhost/posonet/billing_api/getBill?no_internet=268
		$html = '<div class="bill-card-bs3">
					<!-- Badge AKTIF dipindah ke Kanan Atas Card -->
  					<span class="label '. $classStatus .' badge-top-right">' . $data['subscription']['status'] . '</span>

					<!-- Header Pelanggan -->
					<div class="bill-header-bs3">
						<small class="text-muted" style="display:block; font-size: 11px;">NAMA PELANGGAN</small>
						<h4 class="customer-title m-t-sm">' . $data['account']['no_internet'] . ". " . $data['account']['nama_pelanggan'] . '</h4>
					</div>

					<!-- Body Detail Tagihan -->
					<div class="bill-body-bs3">
						<div class="bill-row-bs3">
							<span class="bill-label">Kode Invoice</span>
							<span class="bill-val">' . $data['billing']['kode_invoice'] . '</span>
						</div>

						<div class="bill-row-bs3">
							<span class="bill-label">Paket</span>
							<span class="bill-val">' . $data['subscription']['paket'] . '</span>
						</div>

						<div class="bill-row-bs3">
							<span class="bill-label">Last Expired</span>
							<span class="bill-val '. $classExpired. '">
								<span class="' . $classExpired . '">
								' . $data['subscription']['expired_date'] . '
								</span>
							</span>
						</div>

						<div class="bill-row-bs3">
							<span class="bill-label">Status Bayar</span>
							<span class="bill-val">
								<span class="label '. $classPaid .'">
								' . $data['billing']['status'] . ' ' . $paidIcon . '
								</span>
							</span>
						</div>

						<div class="bill-row-bs3">
							<span class="bill-label">Periode Pemakaian</span>
							<span class="bill-val" style="font-size: 12px;">
							' .	tgl_lokal($data['billing']['billing_periode_start']) . ' - ' . tgl_lokal($data['billing']['billing_periode_end']) . '
							</span>
						</div>
					</div>

					<!-- Total Tagihan -->
					<div class="bill-total-bs3">
						<span class="bill-label" style="font-weight: 600; color: #333;">Total Tagihan</span>
						<span class="amount" id="dtTarif">
						' . number_format($data['billing']['total_amount'], 0, ",", ".") . '
						</span>
					</div>

					<!-- Tombol Proses -->
					<button type="button" id="btnProses" class="btn btn-success text-uppercase btn-lg btn-proses-block" onclick="payNow()">
						Proses
					</button>
				</div>';

		return array(
				'html' => $html,
				'data' => $billData,
		);

	}


}
