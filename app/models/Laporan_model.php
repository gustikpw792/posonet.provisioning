<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Laporan_model extends CI_Model {

  function __construct()
  {
      parent::__construct();
  }

  /*  Untuk FPDF
   *  LAPORAN PELANGGAN
   */

  public function lap_pelanggan_by_wilayah($kode_wilayah)
  {
    return $this->db->query("SELECT * FROM v_pelanggan WHERE id_wilayah = $kode_wilayah")->result();
  }

  public function lap_pelanggan_by_status($id_status)
  {
    return $this->db->query("SELECT * FROM v_pelanggan WHERE id_status = '$id_status'")->result();
  }

  public function lap_pelanggan_group_all()
  {
    return $this->db->query("SELECT * FROM v_pelanggan ORDER BY id_wilayah ASC")->result();
  }

  public function lap_pelanggan_by_bln_instalasi($tgl_pasang)
  {
    return $this->db->query("SELECT * FROM v_pelanggan WHERE tgl_pasang LIKE '$tgl_pasang%' ORDER BY id_wilayah ASC")->result();
  }

  public function lap_pelanggan_antara_tgl($tgl_awal, $tgl_akhir)
  {
    return $this->db->query("SELECT *
      FROM v_pelanggan
      WHERE tgl_pasang
      BETWEEN '$tgl_awal' AND '$tgl_akhir'
      ORDER BY id_wilayah ASC")->result();
  }

  /*  Untuk FPDF
   *
   */
   public function getAll()
   {
      return $this->db->query("SELECT * FROM bagian ORDER BY id_bagian ASC")->result();
   }
}
