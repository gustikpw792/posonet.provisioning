<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Billing_model extends CI_Model {

    function __construct()
    {
        parent::__construct();

        $this->load->database();

        $this->load->helper('MY_ribuan');
        $this->load->helper('MY_bulan');
    }

    public function getBillData($noInternet)
    {
        // activate the following line if you need to check authentication
        // get_auth_bearer();

        // Get query parameter 'no_internet'
        // $noInternet = $this->input->get('no_internet', TRUE);

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

        return $res;

        // $this->output
        //         ->set_status_header(200)
        //         ->set_content_type('application/json')
        //         ->set_output(json_encode($res));
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
        $start_periode = new DateTime($tgl_periode_awal);
        $end_periode   = new DateTime($tgl_periode_akhir);
        $total_hari_periode_lama = $start_periode->diff($end_periode)->days; // Biasanya 30 atau 31 hari

        // 3. Hitung Sisa Hari Aktif setelah Aktivasi di Periode Lama
        $active_start = new DateTime($tgl_aktivasi);
        // Kita hitung selisih dari tgl aktivasi ke akhir periode lama
        $sisa_hari_aktif = $active_start->diff($start_periode)->days; 

        // Jika aktivasi dilakukan tepat di hari akhir atau lewat, set minimal 0
        if ($active_start > $end_periode) {
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
        $datexx = $start_periode;
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

    


}