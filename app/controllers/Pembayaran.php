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
		
	}


	public function cari(){
		$cari = html_escape($this->input->get('cari'));
		$results = $this->db->query("SELECT no_pelanggan,nama_pelanggan,wilayah,nama_paket,tarif,expired FROM v_pelanggan WHERE no_pelanggan LIKE '$cari%' OR nama_pelanggan LIKE '%$cari%'")->result();
		$row = '';
		foreach ($results as $dt) {
			$row .= "<tr>
					<td>$dt->no_pelanggan. $dt->nama_pelanggan</td>
					<td>$dt->wilayah</td>
					<td class=\"text-navy\"> <a href=\"#\" class=\"ladda-button btn btn-warning btn-xs\" data-style=\"zoom-in\" onclick=\"getDetailInvoice($dt->no_pelanggan)\">Cek</a> </td>
				</tr>";
		}
		echo json_encode($row);
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

	public function get_detail_invoice()
	{
		$this->load->model('billing_model','billingModel');

		$no_internet = html_escape($this->input->post('no_internet'));

		$billData = $this->billingModel->getBillData($no_internet);
		$data = $billData['data'];
		$status = $billData['status'];
		$message = $billData['message'];

		// set class
		$classPaid = ($data['billing']['status'] === 'PAID') ? 'text-success' : 'text-danger';
		$classExpired = ($data['billing']['status'] === 'PAID') ? 'text-success' : 'text-danger';
		$classStatus = ($data['subscription']['status'] === 'AKTIF') ? 'label-success' : 'label-danger';
		// data bisa dilihat di localhost/posonet/billing_api/getBill?no_internet=268
		$html = '<ul class="list-group clear-list">
					<li class="list-group-item fist-item">
						<span class="pull-right">
							<h3><strong class="text-success"><span id="dtNopelNama">'.$data['account']['no_internet'].". ".$data['account']['nama_pelanggan'].'</span></strong></h3>
						</span>
						<strong>No Internet/Pelanggan</strong>
					</li>

					<li class="list-group-item fist-item">
						<span class="pull-right">
							<strong class="text-success"><span id="dtPaket">'.$data['billing']['kode_invoice'].'<span></strong>
						</span>
						<strong>Kode Invoice</strong>
					</li>

					<li class="list-group-item fist-item">
						<span class="pull-right">
							<strong class="text-success"><span id="dtPaket">'.$data['subscription']['paket'].'<span></strong>
						</span>
						<strong>Paket</strong>
					</li>

					<li class="list-group-item">
						<span class="pull-right">
							<strong class="'.$classExpired.'"><span id="dtExpired">'.$data['subscription']['expired_date'].'</span> <span class="label label-xs ' . $classStatus . '"> '.$data['subscription']['status'].'</span></strong>
						</span>
						<strong>Last Expired</strong>
					</li>

					<li class="list-group-item">
						<span class="pull-right">
							<strong class="' . $classPaid . '"><span id="dtStatusBayar">'.$data['billing']['status'].'</span></strong>
						</span>
						<strong>Status Bayar</strong>
					</li>

					<li class="list-group-item">
						<span class="pull-right">
							<strong class="text-danger">
								<span id="dtStatusBayar">'.
								tgl_lokal($data['billing']['billing_periode_start']).' - '.tgl_lokal($data['billing']['billing_periode_end']).'
								</span>
							</strong>
						</span>
						<strong>Periode Pemakaian</strong>
					</li>
					
					<li class="list-group-item">
						<span class="pull-right" id="dtTarif" style="font-size: 12pt; font-weight: bold;">
							'. number_format($data['billing']['total_amount'], 0, ",", ".").'
						</span>
						<strong>Tagihan Rp</strong>
					</li>

					

					<li class="list-group-item">
						<span class="pull-right">
							<a href="#" class="ladda-button btn btn-primary" data-style="zoom-in" onclick="payNow()">Proses</a>
						</span>
						<strong></strong>
					</li>
				</ul>';

		echo json_encode(
			array(
				'html' => $html,
				'data' => $billData,
			)
		);

	}


}
