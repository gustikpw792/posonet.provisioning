<?php defined('BASEPATH') or exit('No direct script access allowed');
class Transactions extends CI_Controller
{


	function __construct()
	{
		parent::__construct();
		if (!is_logged_in()) {
			redirect('login?_rdr=' . urlencode(current_url()));
		}

		$this->ros = $this->config->item('mikrotik');

		$this->load->model('transactions_model', 'trx');
		$this->load->helper(array('MY_ribuan', 'MY_bulan'));
	}

	public function index()
	{
		set_status_header(401);
	}

    public function ajax_list()
	{
		$list = $this->trx->get_datatables();
		$data = array();
		$no = $_POST['start'];
        $txt_act = '';

		foreach ($list as $br) {
			$no++;
			$row = array();
			$row[] = $no;
			$row[] = $br->settlement_time;
			$row[] = $br->kode_invoice;
			$row[] = "<span class='font-bold'>$br->order_id</span>";
			$row[] = "<span class='font-bold'>$br->no_pelanggan $br->nama_pelanggan</span>";

            if ($br->activation_status == 'SUCCESS') {
                $txt_act = ' 
                    <button type="button" class="btn btn-primary btn-xs" alt="Pelanggan telah diperpanjang ke !' . $br->client_expiry . '">
                        ' . $br->activation_status . '
                    </button>';
            } else {            
                $txt_act = '
                    <button type="button" class="btn btn-warning btn-xs"  alt="Tgl Expired Pelanggan ' . $br->client_expiry . ' <> Tgl Expired Invoice ' . $br->invoice_expiry . '">
                        ' . $br->activation_status . '
                    </button>';
            }

			$row[] = $txt_act;
			// $row[] = $br->bulan_penagihan;
			// $row[] = $br->client_expiry . '<>' .$br->invoice_expiry;
			$row[] = "<span class='font-bold'>$br->transaction_status</span>";
			$row[] = "<span class='font-bold'>$br->payment_types</span>";
			$row[] = "<span class='font-bold' alt='tessss'>$br->mode</span>";
			
			//add html for action
			// "<div class=\"btn-group\"><a class=\"btn btn-xs btn-outline btn-primary\" href=\"javascript:void(0)\" onclick=\"views('$br->id_kolektor')\" title=\"Lihat Detail\"><i class=\"glyphicon glyphicon-eye-open\"></i> Lihat</a>
			// $row[] = "<div class=\"btn-group\"><a class=\"btn btn-xs btn-outline btn-warning\" href=\"javascript:void(0)\" onclick=\"edits('$br->id_kolektor')\" title=\"Edit\"><i class=\"glyphicon glyphicon-pencil\"></i> Edit</a>
			// 	<a class=\"btn btn-xs btn-outline btn-danger\" href=\"javascript:void(0)\" onclick=\"deletes('$br->id_kolektor')\" title=\"Hapus\" ><i class=\"glyphicon glyphicon-trash\"></i> Delete</a></div>";
			$data[] = $row;
		}
		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->trx->count_all(),
			"recordsFiltered" => $this->trx->count_filtered(),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}



}