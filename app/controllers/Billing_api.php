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
        $this->load->helper('MY_ribuan');
        $this->load->helper('MY_bulan');
        // get_auth_bearer();
    }



    public function index()
    {
        // Default endpoint
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'API Controller is working']));
    }

    public function getBill2()
    {
        // activate the following line if you need to check authentication
        // get_auth_bearer();

        // Get query parameter 'no_internet'
        $noInternet = $this->input->get('no_internet', TRUE);

        // Prepare response

        //check if no_internet available
        $query = $this->db->query("SELECT no_pelanggan, status FROM pelanggan WHERE no_pelanggan=?",[$noInternet]);

        if ($query->num_rows())
        {
            $data = $this->db->query("SELECT v.no_pelanggan, v.nama_pelanggan, v.wilayah, v.nama_paket,v.tarif,v.tarif AS trx_amount, 
                v.expired AS expired_date,t.expired AS next_expired, t.bulan_penagihan, v.status,
                IF(v.expired < CURDATE(),'ISOLIR','AKTIF') AS status_berlangganan, v.telp , 
                IF(t.status = 'Lunas' OR t.transaction_status = 'settlement', 'PAID', 'UNPAID') AS payment_status,t.kode_invoice,
                DATE_FORMAT(DATE_SUB(t.expired, INTERVAL 1 MONTH), '%Y-%m-21') AS periode_awal,
                t.expired AS periode_akhir, CURDATE() AS tgl_sekarang

                FROM v_pelanggan v 
                LEFT JOIN temp_invoice t 
                ON v.no_pelanggan=t.no_pelanggan
                WHERE v.no_pelanggan=?
                ORDER BY id_trx DESC
                LIMIT 1", [$noInternet]);
            
            $profil = $this->db->query("SELECT telp_cs FROM profil_perusahaan")->row();


            if ($data->num_rows() > 0) {

                $dt = $data->row();

                // date diff
                // 1. Ubah string tanggal menjadi objek DateTime
                $date1 = new DateTime($dt->expired_date);
                $date2 = new DateTime(date('Y-m-d'));

                // 2. Hitung selisih antara kedua tanggal
                $selisih = $date1->diff($date2);

                // 3. Ambil total selisih dalam satuan hari
                $total_hari = $selisih->days;


                if ($dt->status === 'NONAKTIF' || $selisih->days >= 25) {
                    $res = array(
                        'data' => array(
                            'account' => array(
                                'no_internet' => $dt->no_pelanggan,
                                'nama_pelanggan' => $dt->nama_pelanggan,
                                'telp' => $dt->telp,
                                'status' => $dt->status,
                            ),
                            'subscription' => array(
                                'status' => $dt->status_berlangganan,
                            ),
                            'billing' => array(
                                'status' => $dt->payment_status,
                            ),
                        ),
                        'status' => true,
                        'message' => 'Status ISOLIR! <br> Silahkan hubungi CS kami melalui WhatsApp <br><a href="https://wa.me/'
                        . substr_replace(
                            str_replace(' ', '', $profil->telp_cs),'+62', 0, 1)
                        . '" class="btn btn-sm btn-info">'. $profil->telp_cs .'</a>' ,
                    );
                } else {
                    if ($data->row()->next_expired < date('Y-m-d')) {
                        $res = array(
                            'data' => null,
                            'status' => true,
                            'message' => 'Tagihan belum tersedia!',
                        );
                        
                    } else {

                    if ($dt->tgl_sekarang < $dt->periode_awal) {
                        $res = array(
                            'data' => array(
                                'account' => array(
                                    'no_internet' => $dt->no_pelanggan,
                                    'nama_pelanggan' => $dt->nama_pelanggan,
                                    'telp' => $dt->telp,
                                    'status' => $dt->status,
                                ),
                                'subscription' => array(
                                    'status' => $dt->status_berlangganan,
                                ),
                                'billing' => array(
                                    'status' => $dt->payment_status,
                                ),
                            ),
                            'status' => true,
                            'message' => 'Tagihan Lama dan Baru telah digabung!'
                            . substr_replace(
                                str_replace(' ', '', $profil->telp_cs),'+62', 0, 1)
                            . '" class="btn btn-sm btn-info">'. $profil->telp_cs .'</a>' ,
                        );
                    } else {
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
                                    'billing_periode_start' => $dt->periode_awal,
                                    'billing_periode_end' => $dt->periode_akhir,
                                    'total_amount' => (int) $dt->tarif,
                                    'currency' => 'IDR',
                                    'kode_invoice' => $dt->kode_invoice,
                                    'status' => $dt->payment_status,
                                    'description' => '',
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

                    }
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

    public function getBill()
    {
        // activate the following line if you need to check authentication
        // get_auth_bearer();

        // Get query parameter 'no_internet'
        $noInternet = $this->input->get('no_internet', TRUE);

        // Prepare response

        //check if no_internet available
        $query = $this->db->query("SELECT no_pelanggan, status FROM pelanggan WHERE no_pelanggan=?",[$noInternet]);

        if ($query->num_rows())
        {
            $data = $this->db->query("SELECT v.no_pelanggan, v.nama_pelanggan, v.wilayah, v.nama_paket,v.tarif,v.tarif AS trx_amount, 
                v.expired AS expired_date,t.expired AS next_expired, t.bulan_penagihan, v.status,
                IF(v.expired < CURDATE(),'ISOLIR','AKTIF') AS status_berlangganan, v.telp , 
                IF(t.status = 'Lunas' OR t.transaction_status = 'settlement', 'PAID', 'UNPAID') AS payment_status,t.kode_invoice,
                DATE_FORMAT(DATE_SUB(t.expired, INTERVAL 1 MONTH), '%Y-%m-21') AS periode_awal,
                t.expired AS periode_akhir, CURDATE() AS tgl_sekarang

                FROM v_pelanggan v 
                LEFT JOIN temp_invoice t 
                ON v.no_pelanggan=t.no_pelanggan
                WHERE v.no_pelanggan=?
                ORDER BY id_trx DESC
                LIMIT 1", [$noInternet]);
            
            $profil = $this->db->query("SELECT telp_cs FROM profil_perusahaan")->row();


            if ($data->num_rows() > 0) {

                $dt = $data->row();

                
                    if ($dt->next_expired < date('Y-m-d')) {
                        $res = array(
                            'data' => null,
                            'status' => true,
                            'message' => 'Tagihan belum tersedia!',
                        );
                        
                    } else {
                            // 2026-07-17 < 2026-07-21 && $dt->status_berlangganan = 'ISOLIR'
                        if ($dt->tgl_sekarang < $dt->periode_awal && $dt->status_berlangganan === 'ISOLIR') {

                            $rollOver = $this->rollover_tagihan($dt);

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
                                        'billing_periode_start' => $dt->periode_awal,
                                        'billing_periode_end' => $dt->periode_akhir,
                                        'total_amount' => (int) $rollOver->total,
                                        'currency' => 'IDR',
                                        'kode_invoice' => $dt->kode_invoice,
                                        'status' => $dt->payment_status . ' ROLLOVER',
                                        'status_code' => 206,
                                        'description' => $rollOver->reason,
                                        'notification' => $rollOver->notification,
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
                                'payment_gateway' => 'MIDTRANS',
                                'message' => 'Tagihan Lama dan Baru telah digabung!'
                                . substr_replace(
                                    str_replace(' ', '', $profil->telp_cs),'+62', 0, 1)
                                . '" class="btn btn-sm btn-info">'. $profil->telp_cs .'</a>' ,
                            );
                        } else {
                            $deskripsi = '<p>Pembayaran ini untuk pemakaian internet periode <strong>'. format_tgl($dt->periode_awal) ." s/d ". format_tgl($dt->periode_akhir) . '</strong></p>';

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
                                        'billing_periode_start' => $dt->periode_awal,
                                        'billing_periode_end' => $dt->periode_akhir,
                                        'total_amount' => (int) $dt->tarif,
                                        'currency' => 'IDR',
                                        'kode_invoice' => $dt->kode_invoice,
                                        'status' => $dt->payment_status,
                                        'status_code' => 200,
                                        'description' => $deskripsi,
                                        'notification' => '',
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
                                'payment_gateway' => 'MIDTRANS',
                                'message' => 'Invoice found(s)',
                            );
                            
                        }

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
        if ($this->input->post("is_production", TRUE)) {
            $status = ($transaction_status == 'settlement' || $transaction_status == 'capture') ? 'Lunas' : 'Belum Bayar';
        } else {
            $status = 'Belum Bayar';
        }

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
            // 'raw_response' => json_encode($this->input->post("raw_response", TRUE)),
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

    private function getBilling()
    {
        // Get query parameter 'no_internet'
        $noInternet = $this->input->get('no_internet', TRUE);

        $exist = $this->db->query("SELECT no_pelanggan FROM pelanggan WHERE no_pelanggan=?",[$noInternet])->num_rows();
        
        if ($exist > 0)
        {   
            # 1. cek jika pelanggan sudah expired apa belum
            $exp = $this->db->query("SELECT status_berlangganan FROM v_expired WHERE no_pelanggan=?",[$noInternet]);
            if ($exp->num_rows() > 0) {
                # jika expired,
                # cek apakah tgl aktivasi berada di periode tagihan?
                // if($this->cekAktivasiInPeriodeTagihan()) {

                // } else {
                //     # jika tidak berada di periode tagihan, hitung berapa hari menuju hari pertama periode tagihan
                //     # hitung selisih prorata dan gabungkan tagihan
                // }
            } else {
                # belum expired
            }









            # jika ditemukan no_internet pelanggan, cek tagihan
            $getTagihan = $this->db->query("SELECT t.kode_invoice,t.expired, t.`status`, t.tarif
        FROM temp_invoice t
        WHERE t.no_pelanggan=?
        AND t.`status` != 'Lunas'
        AND t.expired BETWEEN CURDATE() 
        AND (SELECT MAX(expired) FROM temp_invoice WHERE no_pelanggan=?)",[$noInternet, $noInternet]);

            if ($getTagihan->num_rows() > 1) {
                
                /*
                 JIKA pelanggan_klik_perpanjang() MAKA
                    hitung_sisa_hari = tanggal_20_juli_2026 - tanggal_hari_ini
                    
                    JIKA hitung_sisa_hari > 0 MAKA
                        biaya_prorata = (hitung_sisa_hari / 30) * harga_paket_normal
                        total_bayar = biaya_prorata + harga_tagihan_baru_juli_agustus
                        
                        set_masa_aktif_hingga(20-08-2026)
                        buka_isolir()
                    ENDIF
                ENDIF

                 */
            }

        } else {
            $res = array(
                'data' => null,
                'status' => false,
                'message' => 'Nomor Internet tidak ditemukan!',
            );
        }
        // cek apakah pelanggan ISOLIR?

        if (BELUM_ISOLIR) {
            # lakukan pengecekan tagihan normal
        } else {
            # lakukan skema tagihan bundling
        }
    }

    public function cekAktivasiInPeriodeTagihan($tglAktivasi,$periodeAwal,$periodeAkhir)
    {
        $tgl_aktivasi = new DateTime('2026-07-15');
        $tgl_mulai    = new DateTime('2026-07-21');
        $tgl_selesai  = new DateTime('2026-08-20');

        // Melakukan pengecekan rentang tanggal
        if ($tgl_aktivasi >= $tgl_mulai && $tgl_aktivasi <= $tgl_selesai) {
            echo "Tanggal aktivasi berada DI DALAM periode tagihan.";
        } else {
            echo "Tanggal aktivasi berada DI LUAR periode tagihan.";
        }
    
    }


    public function rollover_tagihan($data)
    {
        // 1. Definisikan Parameter Utama
        $harga_bulanan = $data->tarif; // Contoh harga paket per bulan (Rp 300.000)

        // Tanggal-tanggal terkait
        $tgl_periode_awal  = $data->periode_awal;    //'2026-06-21';
        $tgl_periode_akhir = $data->periode_akhir;   //'2026-07-20';
        $tgl_aktivasi     = $data->tgl_sekarang;    //'2026-07-15';

        // 2. Hitung Total Hari dalam Periode Tagihan Lama (untuk pembagi prorata)
        $start_old = new DateTime($tgl_periode_awal);
        $end_old   = new DateTime($tgl_periode_akhir);
        $total_hari_periode_lama = $start_old->diff($end_old)->days; // Biasanya 30 atau 31 hari

        // 3. Hitung Sisa Hari Aktif setelah Aktivasi di Periode Lama
        $active_start = new DateTime($tgl_aktivasi);
        // Kita hitung selisih dari tgl aktivasi ke akhir periode lama
        $sisa_hari_aktif = $active_start->diff($start_old)->days; 

        // Jika aktivasi dilakukan tepat di hari akhir atau lewat, set minimal 0
        if ($active_start > $end_old) {
            $sisa_hari_aktif = 0;
        }

        // 4. Hitung Biaya Prorata Periode Lama
        // Rumus: (Sisa Hari Aktif / Total Hari Periode) * Harga Bulanan
        $biaya_prorata = 0;
        if ($sisa_hari_aktif > 0 && $total_hari_periode_lama > 0) {
            $biaya_prorata = round(($sisa_hari_aktif / $total_hari_periode_lama) * $harga_bulanan);
        }

        // 5. Gabungkan dengan Tagihan Periode Baru (2026-07-20 s/d 2026-08-20)
        // Karena ini periode baru penuh, biayanya adalah 100% harga bulanan
        $biaya_periode_baru = $harga_bulanan;
        $total_tagihan_gabungan = $biaya_prorata + $biaya_periode_baru;

        // periode awal kurang 1 hari
        $datexx = $start_old;
        $datexx->modify('-1 day'); // Mengurangi 1 hari

        $table ="<table class=\"table table-bordered table-condensed\">
                    <thead>
                        <tr>
                            <th>Periode Pemakaian</th>
                            <th style='text-align: right;'>Jumlah <a href=\"#\" data-toggle=\"modal\" data-target=\"#modalDetailTotal\" style=\"color: #777; margin-left: 5px;\">
                                    <i class=\"fa fa-info\"></i>
                                </a>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>" . format_tgl($tgl_aktivasi)  ." s/d ".  $datexx->format('d-m-Y') . "</td>
                            <td style='text-align: right;'>" . ribuan($biaya_prorata) . "</td>
                        </tr>
                        <tr>
                            <td>" . format_tgl($tgl_periode_awal) ." s/d ". format_tgl($tgl_periode_akhir) . "</td>
                            <td style='text-align: right;'>" . ribuan($biaya_periode_baru) . "</td>
                        </tr>
                    </tbody>
                </table>";

                        // <tr>
                        //     <td>Total</td>
                        //     <td style='text-align: right;'>" . ribuan($total_tagihan_gabungan) . "</td>
                        // </tr>

        $notif = "
[NOTIFIKASI RE-AKTIVASI LAYANAN] 

🌐Halo Kak $data->nama_pelanggan, terima kasih telah memilih kembali layanan WiFi kami.
Status akun Anda saat ini sedang terisolir. 
Agar WiFi dapat langsung aktif kembali dengan lancar hingga " . tgl_lokal($tgl_periode_akhir) . " tanpa terputus lagi, 
sistem kami menerapkan metode pembayaran gabungan (bundle) berikut:

📌 Rincian Pembayaran:
Sisa Hari Bulan Ini (" . $sisa_hari_aktif  ." hari s/d ".  $datexx->format('d-m-Y') . "): Rp " . ribuan($biaya_prorata) . " (Dihitung proporsional)
Paket Bulan Depan (" . format_tgl($tgl_periode_awal) ." s/d ". format_tgl($tgl_periode_akhir) . "): Rp " . ribuan($biaya_periode_baru) . "
💰 TOTAL WAJIB BAYAR: Rp " . ribuan($total_tagihan_gabungan) . "

Kenapa pembayarannya digabung?
Kebijakan ini diterapkan agar setelah isolir dibuka hari ini, 
WiFi Kakak tidak langsung mati lagi pada tanggal " . $datexx->format('d-m-Y') . " nanti. 
Kakak bisa internetan tenang tanpa gangguan selama lebih dari satu bulan penuh!

Abaikan pesan ini jika Kakak sudah melakukan pembayaran. 
Sinyal WiFi akan otomatis aktif maksimal 15 menit setelah pembayaran sukses.";

        return (object) array(
            'total' => $total_tagihan_gabungan,
            'reason' => $table,
            'notification' => $notif,
            // 'reason' => "
            
            // Tagihan <br>
            // $tgl_aktivasi - $tgl_periode_awal       = ribuan($biaya_prorata)<br>
            // $tgl_periode_awal - $tgl_periode_akhir  = ribuan($biaya_periode_baru) <br>
            // ----------------------- +
            // Total Rp $total_tagihan_gabungan<br>",
            // // ($sisa_hari_aktif hari) " . ribuan($biaya_prorata) . " + " . ribuan($biaya_periode_baru),
        );

        // --- OUTPUT HASIL ---
        echo "=== DETAIL KALKULASI TAGIHAN ===\n";
        echo "Harga Paket Bulanan      : Rp " . number_format($harga_bulanan, 0, ',', '.') . "\n";
        echo "Total Hari Periode Lama  : " . $total_hari_periode_lama . " hari\n";
        echo "Tanggal Aktivasi Kembali : " . $tgl_aktivasi . "\n";
        echo "Sisa Hari Aktif (Lama)   : " . $sisa_hari_aktif . " hari\n";
        echo "--------------------------------------------------\n";
        echo "1. Biaya Prorata (Sisa Hari): Rp " . number_format($biaya_prorata, 0, ',', '.') . "\n";
        echo "2. Biaya Periode Baru       : Rp " . number_format($biaya_periode_baru, 0, ',', '.') . "\n";
        echo "--------------------------------------------------\n";
        echo "TOTAL YANG HARUS DIBAYAR   : Rp " . number_format($total_tagihan_gabungan, 0, ',', '.') . "\n";


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