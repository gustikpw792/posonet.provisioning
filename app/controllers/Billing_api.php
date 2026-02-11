<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Billing_api extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        // Load models, helpers, libraries as needed
        // $this->load->model('Your_model');
        $this->load->helper(array('MY_auth_bearer'));

        $this->api = $this->config->item('public_api');
        $this->load->model('Api_model', 'apiModel');
        // get_auth_bearer();
    }



    public function index()
    {
        // Default endpoint
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'API Controller is working']));
    }

    public function getBill()
    {
        // activate the following line if you need to check authentication
        // get_auth_bearer();

        // Get query parameter 'no_internet'
        $noInternet = $this->input->get('no_internet', TRUE);

        // Prepare response

        //check if no_internet available
        $query = $this->db->query("SELECT no_pelanggan FROM pelanggan WHERE no_pelanggan=?",[$noInternet]);

        if ($query->num_rows())
        {
            $data = $this->db->query("SELECT v.no_pelanggan, v.nama_pelanggan, v.wilayah, v.nama_paket,v.tarif,v.tarif AS trx_amount, 
                v.expired AS expired_date,t.expired AS next_expired, t.bulan_penagihan, v.status,
                IF(v.expired < CURDATE(),'ISOLIR','AKTIF') AS status_berlangganan, v.telp , 
                IF(t.status = 'Lunas' AND t.transaction_status = 'settlement', 'PAID', 'UNPAID') AS payment_status,  
                t.kode_invoice

                FROM v_pelanggan v 
                LEFT JOIN temp_invoice t 
                ON v.no_pelanggan=t.no_pelanggan
                WHERE v.no_pelanggan=?
                ORDER BY id_trx DESC
                LIMIT 1", [$noInternet]);

            if ($data->num_rows() > 0) {
                if ($data->row()->next_expired < date('Y-m-d')) {
                    $res = array(
                        'data' => null,
                        'status' => true,
                        'message' => 'Tagihan belum tersedia!',
                    );
                    
                } else {
                    $dt = $data->row();

                    $profil = $this->db->query("SELECT telp_cs FROM profil_perusahaan")->row();

                    $res = array(
                        'data' => array(
                            'account' => array(
                                'no_internet' => $dt->no_pelanggan,
                                'nama_pelanggan' => $dt->nama_pelanggan,
                                'telp' => $dt->telp,
                                'status' => $dt->status,
                            ),
                            'subscription' => array(
                                'paket' => $dt->nama_paket,
                                'tarif' => (int) $dt->tarif,
                                'expired_date' => $dt->expired_date,
                                'status' => $dt->status_berlangganan,
                            ),
                            'billing' => array(
                                'billing_periode_start' => substr($dt->bulan_penagihan,0,8). '20',
                                'billing_periode_end' => $dt->next_expired,
                                'total_amount' => (int) $dt->tarif,
                                'currency' => 'IDR',
                                'kode_invoice' => $dt->kode_invoice,
                                'status' => $dt->payment_status,
                            ),
                            'profile' => array(
                                'telp_cs' => 
                                substr_replace(
                                    str_replace(' ', '', $profil->telp_cs),
                                    '+62', 0, 1),
                            ),
                        ),
                        'status' => true,
                        'time' => date('Y-m-d H:i:s'),
                        'message' => 'Invoice found(s)',
                        'payment_gateway' => 'MIDTRANS',
                    );
                }
            } else {
                $res = array(
                    'data' => null,
                    'status' => false,
                    'message' => 'Tagihan tidak ditemukan!',
                );
            }
                
        } else {
            $res = array(
                'data' => null,
                'status' => false,
                'message' => 'Nomor Internet tidak ditemukan!',
            );
        }

        $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($res));
    }

    public function updateBillingStatus()
    {
        if (!$this->input->post("order_id", TRUE)) {
            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => false, 'message' => 'Invalid input']));
            exit();
        }

        $transaction_status = $this->input->post("transaction_status", TRUE);
        $status = ($transaction_status == 'settlement' || $transaction_status == 'capture') ? 'Lunas' : 'Belum Bayar';

        $kode_invoice = explode('-', $this->input->post("order_id", TRUE));

        $data = array(
            'order_id' => $this->input->post("order_id", TRUE),   
            'transaction_time' => $this->input->post("transaction_time", TRUE),
            'transaction_status' => $transaction_status,
            'transaction_id' => $this->input->post("transaction_id", TRUE),
            'status_code' => $this->input->post("status_code", TRUE),
            'payment_type' => $this->input->post("payment_type", TRUE),
            'gross_amount' => $this->input->post("gross_amount", TRUE),
            'status' => $status,
            'issuer' => $this->input->post("issuer", TRUE),
            'acquirer' => $this->input->post("acquirer", TRUE),
            'settlement_time' => $this->input->post("settlement_time", TRUE),
            'merchant_id' => $this->input->post("merchant_id", TRUE),
            'store' => $this->input->post("store", TRUE),
            'payment_code' => $this->input->post("payment_code", TRUE),
            'signature_key' => $this->input->post("signature_key", TRUE),
            'va_numbers' => json_encode($this->input->post("va_numbers", TRUE)),
            'expiry_time' => $this->input->post("expiry_time", TRUE),
            'mode' => ($this->input->post("is_production", TRUE)) ? 'PRODUCTION' : 'SANDBOX',
        );

        // Validate input
        if (empty($kode_invoice)) {
            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => false, 'message' => 'Invalid input']));
            exit();
        }
        // Call the model method to update invoice status
        $isLog = false;
        if ($transaction_status == 'settlement' || $transaction_status == 'capture') {
            $cek = $this->db->query("SELECT * FROM temp_invoice WHERE order_id=?", [$data['order_id']]);
            $where = array('order_id' => $data['order_id']);
            $isLog = true;
            
        } else if ($transaction_status == 'pending' || $transaction_status == 'deny') {
            $cek = $this->db->query("SELECT * FROM temp_invoice WHERE kode_invoice=?", [$kode_invoice[1]]);
            $where = array('kode_invoice' => $kode_invoice[1]);
            $isLog = false;
        }
 
        try {

            if ($cek->num_rows() > 0) {
                // Log the data
                if ($isLog) {
                    $tgl = date('Y-m-d H:i:s');
                    $dt = json_encode($data);
                    
                    // Perpanjang paket pelanggan atau transaksi benar2 real
                    if ($this->input->post("is_production", TRUE)) {
                        // call the activation and save transcaction log
                        $per = $this->_goPerpanjang($data['order_id']);
                        // log the transaction
                        $this->db->insert('log', [
                            'time' => $tgl,
                            'topic' => 'MIDTRANS',
                            'message' => $dt
                        ]);

                    } else {
                        $this->db->insert('log', [
                            'time' => $tgl,
                            'topic' => 'MIDTRANS-TESTMODE',
                            'message' => $dt
                        ]);
                    }
                }
                
                // Update the order_id in the database
		        $update = $this->apiModel->updateDataTempInvoice($where, $data);

                $this->output
                    ->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['data' => $data, 'status' => true, 'message' => 'Invoice Order_ID updated successfully']));
                exit();
            } else {
                $this->output
                    ->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['data' => $data, 'status' => false, 'message' => 'Invoice not found']));
                exit();
            }
        } catch (Exception $e) {
            $res = array(
                'status' => false,
                'message' => 'Error updating invoice status: ' . $e->getMessage(),
            );
            $this->output
                    ->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($res));
            exit();
        }

    }

    public function getPaymentDetails()
    {
        // activate the following line if you need to check authentication
        // get_auth_bearer();
        $orderId = $this->input->get('order_id', TRUE);

        // Validate input
        if (empty($orderId)) {
            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode((object) ['status' => false, 'message' => 'Invalid input']));
            exit();
        }
        // Call the model method to get payment details
        try {
            $paymentDetails = $this->apiModel->getPaymentDetails($orderId); 
            
            if ($paymentDetails->num_rows() > 0) {
                $profil = $this->db->get('profil_perusahaan')->row();
                $paymentData = $paymentDetails->row();
                $paymentData->telp_cs = substr_replace(str_replace(' ', '', $profil->telp_cs),'+62', 0, 1);

                $data = array(
                    'data' => $paymentData,
                    'message' => 'Payment details found',
                    'status' => true,
                );

                $this->output
                    ->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data));
            }
            else {
                $data = array(
                    'data' => [], 
                    'message' => 'Payment details not found',
                    'status' => false, 
                );

                $this->output
                    ->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data));
            }
        } catch (Exception $e) {
            $data = array(
                'data' => [], 
                'message' => 'Error retrieving payment details: ' . $e->getMessage(),
                'status' => false, 
            );

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data));
        }       
    }

    private function _goPerpanjang($order_id)
    {
        $getGpon = $this->db->query("SELECT t.kode_invoice,t.no_pelanggan,t.expired,p.gpon_onu
                FROM temp_invoice t, v_pelanggan p
                WHERE t.no_pelanggan=p.no_pelanggan 
                AND t.order_id=?", [$order_id])->row();

        // prepare activation and notification telegram
        $dataGpon = (object) array(
            'gpon_onu' => $getGpon->gpon_onu,
            'expired' => $getGpon->expired,
            'username' => 'payment-gateway', // identity for telegram notification
        );
        // load model
		$this->load->model('api_mikrotik_model', 'routermodel');
        $this->load->model('api_rest_client_model', 'perpanjang');

        return $this->perpanjang->extendThisPaket($dataGpon, true);
    }

    // public function coba()
    // {
    //     // $this->load->model('ruangwa_model','wa');
    //     // $sendwa = $this->wa->sendMessageSuccess(
    //         //     array(
    //             //         'number' => '081340310250',
    //     //         'expired' => '20 Oktober 2025',
    //     //     )
    //     // );

    //     // echo json_encode($sendwa);
    //     $this->load->model('api_telegrambot_model','tele');
    //     // $send = $this->tele->buildNotification('2002');
    //     $send = $this->tele->notifNewClientToAdmin('2002');
	// 	// $send = $this->tele->sendNewClientToAdmin('2002');

    //     echo json_encode($send);
    // }

}