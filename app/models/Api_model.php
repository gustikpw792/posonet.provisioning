<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Api_model extends CI_Model
{

  function __construct()
  {
    parent::__construct();
    $this->load->database();
  }

  // ini untuk api midtrans
  public function updateDataTempInvoice($where, $data)
  {
    $this->db->update('temp_invoice', $data, $where);
    return $this->db->affected_rows();
  }
  // ini untuk api midtrans
  public function getPaymentDetails($order_id)
  {
    $this->db->from('v_temp_invoice');
    $this->db->where('order_id',$order_id);
    $query = $this->db->get();

    return $query->row();
  }

  public function get_data($q)
  {
    $this->db->select('id_pelanggan, no_pelanggan, nama_lengkap, wilayah, alamat, status');
    $this->db->from('v_pelanggan');
    $this->db->like('no_pelanggan', $q, 'both'); // artinya WHERE kode_plgn LIKE '$q%'
    $this->db->or_like('nama_lengkap', $q, 'both'); // artinya WHERE kode_plgn LIKE '$q%'
    // after = $wilayah%    <- lihat posisi persennya
    // before = %$wilayah
    // both = %$wilayah%
    $this->db->limit(10);
    $query = $this->db->get();
    return $query->result();
  }

  public function cek_tunggakan_by($no_pelanggan)
  {
    $query = $this->db->query("SELECT *
      FROM v_temp_invoice t
      WHERE t.no_pelanggan = '$no_pelanggan'
      AND t.`status` = 'Belum Bayar'
      ORDER BY t.bulan_penagihan ASC
    ");

    return $query;
  }

  public function auth_pin_sms($no_pelanggan, $pin)
  {
    $query = $this->db->query("SELECT p.no_pelanggan,p.nama_lengkap,p.pin_sms,p.id_pelanggan
      FROM v_pelanggan p
      WHERE p.no_pelanggan = '$no_pelanggan'
      AND p.pin_sms = '$pin'
    ");

    return $query;
  }

  public function insert_aduan_sms($data)
  {
    $this->db->insert('pengaduan', $data);
    return $this->db->insert_id();
  }

  // Digunakan untuk tabel pelanggan, kolom telp atau pin_sms
  public function update_plgn_sms($where, $data)
  {
    $this->db->update('pelanggan', $data, $where);
    return $this->db->affected_rows();
  }

  public function cek_pembayaran_terakhir($no_pelanggan, $limit)
  {
    $query = $this->db->query("SELECT *
      FROM v_temp_invoice t
      WHERE t.no_pelanggan = '$no_pelanggan'
      AND t.`status` = 'Lunas'
      ORDER BY t.bulan_penagihan DESC
      LIMIT $limit
    ");

    return $query->result();
  }

  /*
    UNTUK FUNGSI DASHBOARD
    * PELANGGAN
  */
  public function count_pelanggan($status = '', $wilayah = '')
  {
    $where = '';

    if (($status == 'null' || $status == '') && ($wilayah == 'null' || $wilayah == ''))
      $where = '';

    else if ($status != 'null' && $wilayah == 'null')
      $where = "WHERE p.status = $status";

    else if (($status == 'null' || $status == '') && $wilayah != 'null')
      $where = "WHERE p.id_wilayah = $wilayah";

    else if (($status != 'null' || $status != '') && ($wilayah != 'null' || $wilayah != ''))
      $where = "WHERE p.status = $status AND p.id_wilayah = $wilayah";

    return $this->db->query(" SELECT COUNT(p.no_pelanggan) AS total_pelanggan
                              FROM v_pelanggan p
                              $where")->row();
  }

  public function count_wilayah()
  {
    return $this->db->query("SELECT COUNT(w.kode_wilayah) AS total_wilayah FROM wilayah w")->row();
  }

  public function count_bywilayah()
  {
    return $this->db->query("SELECT v.kode_wilayah,v.wilayah,count(v.id_wilayah) AS jumlah
                            FROM v_pelanggan v
                            GROUP BY v.id_wilayah
                            ")->result();
  }


  /* UNTUK CHART.JS
   * Line Chart
   *
   */

  public function get_setoran_summary($tahun, $bulan, $id_kolektor)
  {
    return $this->db->query("SELECT t.nama_kolektor, SUM(t.tarif) AS subtotal
            FROM v_detail_setoran t
            WHERE t.`status` = 'Lunas'
            AND YEAR(t.tgl_setoran) = '$tahun'
            AND MONTH(t.tgl_setoran) = '$bulan'
            AND t.id_kolektor = '$id_kolektor'
            GROUP BY t.id_kolektor")->row();
  }

  public function get_kolektor() // get kolektor
  {
    return $this->db->query("SELECT k.id_kolektor, y.nama_lengkap AS nama_kolektor
            FROM kolektor k, karyawan y
            WHERE k.id_karyawan = y.id_karyawan
            ORDER BY y.nama_lengkap")->result();
    // Script dibawah membutuhkan 2 Sec query. bisa lebih lama jika data dalam database semakin banyak.
    // return $this->db->query("SELECT t.id_kolektor, t.nama_kolektor
    //       FROM v_detail_setoran t
    //       GROUP BY t.id_kolektor")->result();
  }

  public function total_setoran_by($bulan)
  {
    return $this->db->query("SELECT SUBSTR(t.tgl_setoran,1,7) AS bulan, SUM(t.tarif) AS total, SUM(t.remark) AS total_remark
         FROM v_detail_setoran t
         WHERE t.tgl_setoran LIKE '$bulan%'")->row();
  }

  public function get_max_setoran()
  {
    return $this->db->query('SELECT b.nama_kolektor,MAX(b.subtotal_remark) AS max_setoran, b.bulan
         FROM v_setoran_bulan_ini b')->row();
  }

  /* PEMUTUSAN
    * Jika menunggak lebih dari 2 bulan
    */

  public function cek_pemutusan()
  {
    return $this->db->query("SELECT * FROM v_tunggakan WHERE banyak_tunggakan >= 2 ORDER BY banyak_tunggakan DESC")->result();
  }


  /* Mengambil pengaturan bernilai serialize
    *
    */

  public function getSettings_serial($option_name)
  {
    $query = $this->db->query("SELECT option_name,option_value FROM settings WHERE option_name='$option_name' ");
    return $query;
  }

  public function getSettings($option_name, $option_value)
  {
    $query = $this->db->query("SELECT option_name,option_value FROM settings WHERE option_name='$option_name' AND option_value='$option_value'");
    return $query;
  }

  public function updateSettings($where, $data)
  {
    $this->db->update('settings', $data, $where);
    return $this->db->affected_rows();
  }

  // MENCARI PERSENTASE TARGET / PENCAPAIAN SETORAN
  public function get_target($bulan_penagihan)
  {
    // $sql_target = "SELECT SUM(i.tarif) AS target, DATE(NOW()) AS bulan
    //   FROM v_temp_invoice i
    //   WHERE i.bulan_penagihan LIKE '$bulan_penagihan%'"; // Query ini membutuhkan 4,656 sec
    $sql_target = "SELECT SUM(t.tarif) AS target, v.bulan_penagihan AS bulan
      FROM paket t, temp_invoice v, pelanggan p
      WHERE t.id_paket = p.id_paket
      AND v.no_pelanggan = p.no_pelanggan
      AND v.bulan_penagihan LIKE '$bulan_penagihan%'";

    $sql_target_aktif = "SELECT SUM(tarif) AS target FROM v_pelanggan WHERE STATUS = 'AKTIF'";
    $sql_target_nonaktif = "SELECT SUM(tarif) AS target FROM v_pelanggan WHERE STATUS = 'NONAKTIF'";

    $sql_capai = "SELECT SUM(t.tarif) AS capai, SUM(d.remark) AS capai_remark, m.tgl_setoran AS bulan
      FROM paket t
      JOIN pelanggan p ON t.id_paket = p.id_paket
      JOIN detail_setoran d ON d.no_pelanggan = p.no_pelanggan
      JOIN master_setoran m ON d.id_master_setoran = m.id_master_setoran
      WHERE m.tgl_setoran LIKE '$bulan_penagihan%'"; // 1,141 sec

    // $sql_capai = "SELECT SUM(i.tarif) AS capai, DATE(NOW()) AS bulan
    //   FROM v_detail_setoran i
    //   WHERE i.`status` = 'Lunas'
    //   AND i.tgl_setoran LIKE '$bulan_penagihan%' "; // 1,203 sec
    // AND YEAR(i.tgl_bayar) = YEAR(NOW())
    // AND MONTH(i.tgl_bayar) = MONTH(NOW())";

    $tn = $this->db->query($sql_target_nonaktif)->row();
    $t = $this->db->query($sql_target_aktif)->row();
    $c = $this->db->query($sql_capai)->row();

    return array('target' => $t, 'target_nonaktif' => $tn, 'capai' => $c);
  }

  public function cek_statistik($bulan)
  {
    return $this->db->query("SELECT * FROM statistik_bulanan WHERE bulan LIKE '$bulan%'");
  }

  public function save_statistik($data)
  {
    $this->db->insert('statistik_bulanan', $data);
    return $this->db->insert_id();
  }

  public function update_statistik($where, $data)
  {
    $this->db->update('statistik_bulanan', $data, $where);
    return $this->db->affected_rows();
  }

  // CONTOH MULTY DATABASE
  public function dbdua()
  {
    $DB2 = $this->load->database('sms', TRUE);
    return $DB2->query("SELECT * FROM inbox")->result();
  }
}
