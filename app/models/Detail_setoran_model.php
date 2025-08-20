<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Detail_setoran_model extends CI_Model
{

  var $table = 'detail_setoran';
  var $vtable = 'v_detail_setoran';
  var $column = array('id_master_setoran', 'tgl_setoran', 'wilayah', 'keterangan');
  var $order = array(
    'tgl_setoran' => 'DESC'
  );

  function __construct()
  {
    parent::__construct();
  }

  /*
    CRUD
  */

  public function get_by_id($id)
  {
    $this->db->from($this->table);
    $this->db->where('id_master_setoran', $id);
    $query = $this->db->get();

    return $query->row();
  }

  public function save($data)
  {
    $this->db->insert('detail_setoran', $data);
    return $this->db->insert_id();
  }

  /*
    Custom Request
  */

  public function getDetailKolektor($id_master_setoran)
  {
    return $this->db->query("SELECT * FROM v_master_setoran WHERE id_master_setoran = '$id_master_setoran' ")->row();
  }

  public function getDataInvoiceBy($kode_invoice)
  {
    return $this->db->query("SELECT * FROM v_temp_invoice WHERE kode_invoice = '$kode_invoice' ");
  }

  // public function cekInvoiceCode($id, $kodeInvoice, $id_kolektor)
  // {
  //   /*
  //     qti = 0 : kode invoice belum terdaftar dalam database,
  //     qti = 1 : data ada dalam database,
  //     qds = 2 : data sudah ada di detail_setoran,
  //     qds = 3 : simpan hasil scan kedalam detail_setoran
  //     qtk = 4 : invoice tidak sesuai dengan kolektor
  //   */
  //   // cek di temp_invoice
  //   $qti = $this->db->query("SELECT * FROM temp_invoice WHERE kode_invoice LIKE '%$kodeInvoice%'")->num_rows();
  //   if ($qti == 1) {
  //     // jika data ada di temp_invoice, cek apakah ada di detail_setoran?
  //     $qds = $this->db->query("SELECT * FROM detail_setoran WHERE id_master_setoran = '$id' AND kode_invoice LIKE '%$kodeInvoice%'")->num_rows();
  //     if ($qds == 1) {
  //       return 2;
  //     } else {
  //       return 3;
  //     }
  //   } else if ($qti != 1) {
  //     return 0;
  //   }
  // }

  public function cekInvoiceCode($id, $kodeInvoice, $id_kolektor)
  {
    /*
      qti = 0 : kode invoice belum terdaftar dalam database,
      qti = 1 : data ada terdaftar database,
      qtk = 2 : invoice tidak sesuai dengan kolektor
      qds = 3 : data sudah ada di detail_setoran, tidak bisa 2x input
      qds = 4 : simpan hasil scan kedalam detail_setoran
    */

    $qtk = $this->db->query("SELECT kode_invoice FROM temp_invoice WHERE kode_invoice LIKE '%$kodeInvoice%'");

    // cek di temp_invoice apakah INVOICE ada dalam database
    if ($qtk->num_rows() != 1) {
      // INVOICE tidak ada dalam database
      return 0;
    } elseif ($qtk->num_rows() == 1) { // jika ada dalam temp_invoice

      /*
        cek di temp_invoice apakah INVOICE sesuai dengan KOLEKTOR
        - apakah id_kolektor pada temp_invoice == id_kolektor pada master_setoran?
      */

      // if ($qtk->row()->id_kolektor == $id_kolektor){
      /*
          - jika data kolektor sesuai dengan master setoran,
          - cek apakah invoice sudah ada di detail_setoran?
        */
      $qds = $this->db->query("SELECT kode_invoice FROM detail_setoran WHERE kode_invoice LIKE '%$kodeInvoice%'");
      // jika invoice sudah ada di detail_setoran, maka tidak bisa input 2x. return 4
      if ($qds->num_rows() != 1) {
        // OK, simpan hasil scan kedalam detail_setoran
        return 4;
      } else {
        // data sudah ada di detail_setoran, tidak bisa 2x input
        return 3;
      }
      // } else {
      //   // invoice tidak sesuai dengan kolektor
      //   return 2;
      // }
    }
  }

  public function listSetoranBy($id_master_setoran)
  {
    return $this->db->query("SELECT * FROM v_detail_setoran WHERE id_master_setoran = $id_master_setoran ");
  }

  public function getInvoiceCountBy($id_master_setoran)
  {
    return $this->db->query("SELECT v.id_master_setoran, v.tarif, count(*) AS jumlah, SUM(v.tarif) AS subtotal
      FROM v_detail_setoran v
      WHERE v.id_master_setoran = $id_master_setoran
      GROUP BY v.tarif");
  }

  public function getInvoiceCountBy2($id_master_setoran)
  {
    return $this->db->query("SELECT SUM(d.remark) AS total_remark
      FROM detail_setoran d
      WHERE d.id_master_setoran = $id_master_setoran")->row();
  }

  public function getKeterangan($kode_invoice)
  {
    return $this->db->query("SELECT kode_invoice, no_pelanggan, keterangan FROM detail_setoran WHERE kode_invoice = '$kode_invoice' ")->row();
  }

  public function getKeteranganPlgn($no_pelanggan)
  {
    return $this->db->query("SELECT no_pelanggan, tarif, keterangan FROM v_pelanggan WHERE no_pelanggan = '$no_pelanggan' ")->row();
  }

  public function updateKeterangan($where, $data)
  {
    $this->db->update('detail_setoran', $data, $where);
    return $this->db->affected_rows();
  }

  public function updateKeteranganPlgn($where, $data)
  {
    $this->db->update('pelanggan', $data, $where);
    return $this->db->affected_rows();
  }

  public function deleteBy($kode_invoice)
  {
    $this->db->where('kode_invoice', $kode_invoice);
    $this->db->delete('detail_setoran');
  }

  public function deleteAllBy($id_master_setoran)
  {
    $this->db->where('id_master_setoran', $id_master_setoran);
    $this->db->delete('detail_setoran');
  }

  public function hitungSetoranBy($id_master_setoran)
  {
    return $this->db->query("SELECT SUM(v.tarif) AS total_setoran, SUM(v.remark) AS total_remark, COUNT(*) AS lembar, COUNT(*) * 3000 AS komisi
      FROM v_detail_setoran v
      WHERE v.id_master_setoran = $id_master_setoran")->row();
  }

  public function updateMasterSetoran($where, $data)
  {
    $this->db->update('master_setoran', $data, $where);
    return $this->db->affected_rows();
  }

  public function getLastInserted($id_master_setoran, $kode_invoice)
  {
    return $this->db->query("SELECT * FROM v_detail_setoran WHERE id_master_setoran = '$id_master_setoran' AND kode_invoice = '$kode_invoice'");
  }

  public function getLastNum($id_master_setoran)
  {
    return $this->db->query("SELECT * FROM v_detail_setoran WHERE id_master_setoran = '$id_master_setoran'")->num_rows();
  }

  // Update status LUNAS/BELUM BAYAR pada temp_invoice
  public function updateStatusTemp($where, $dataUpdate)
  {
    $this->db->update('temp_invoice', $dataUpdate, $where);
    return $this->db->affected_rows();
  }

  /*
  public function update($where, $data)
  {
    $this->db->update('settings', $data, $where);
    return $this->db->affected_rows();
  }

  public function delete_by($blnPenagihan)
  {
    $this->db->like('bulan_penagihan', $blnPenagihan, 'after');
    $this->db->delete('temp_invoice');
  }

  public function cekInvoiceCode($cari) {
    $this->db->select_max('invoice','last');  // artinya SELECT max(kode_plgn) as last
    $this->db->from('temp_kwitansi');
    $this->db->like('invoice',$cari,'after'); // artinya WHERE kode_plgn LIKE '$wilayah%'
                                                      // after = $wilayah%    <- lihat posisi persennya(%)
                                                      // before = %$wilayah
                                                      // both = %$wilayah%
    $cek = $this->db->get();
    return $cek->row();
  }

  public function lastPayed($kode_plgn)
  {
    $this->db->select_max('bulan_bayar','lastpayed');
    $this->db->from('pembayaran');
    $this->db->where('no_pelanggan',$kode_plgn);
    $cek = $this->db->get();
    return $cek->row();

  }

  public function cekIDWil($kode_wilayah)
  {
    $query = $this->db->query("SELECT * FROM wilayah WHERE kode_wilayah='$kode_wilayah'");
    return $query->row();
  }

  public function count_pel($id_wilayah)
  {
    $query = $this->db->query(" SELECT count(p.no_pelanggan) AS jumlah FROM pelanggan p WHERE p.wilayah = '$id_wilayah'");
    return $query->row();
  }

  public function save_inv($data)	{
		$this->db->insert('temp_invoice', $data);
		return $this->db->insert_id();
	}

  public function findCollector($wilayah)
  {
    return $query = $this->db->query("SELECT * FROM v_kolektor WHERE wilayah LIKE '%$wilayah%'")->row();
  }

  public function cek_temp_invoice($no_pelanggan,$bulanPenagihan)
  {
    return $query = $this->db->query("SELECT * FROM v_temp_invoice WHERE no_pelanggan LIKE '%$no_pelanggan%' AND bulan_penagihan LIKE '%$bulanPenagihan%' ")->row();
  }

  public function getDetailTagihan($scanedQR)
  {
    return $query = $this->db->query("SELECT * FROM v_temp_invoice WHERE hash='$scanedQR' OR kode_invoice='$scanedQR'");
  }

  public function updateSetoran($where, $data)
  {
    $this->db->update('temp_invoice', $data, $where);
    return $this->db->affected_rows();
  }

  */
}
