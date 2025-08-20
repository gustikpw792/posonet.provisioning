
<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Getselect extends CI_Controller
{
  function __construct()
  {
    parent::__construct();
    if (!is_logged_in()) {
      redirect('login');
    }
    $this->load->model('Getselect_model', 'getselect');
  }

  public function index()
  {
    set_status_header(401);
  }

  public function pilih($tabel, $kolom, $placeholder = false) //ambil data dari tipe data ENUM('data1','data2','etc')
  {
    $data['hasil'] = $this->getselect->get_option($tabel, $kolom);
    $string = ($placeholder == true) ? '<option value="">Please Select</option>' : '';
    foreach ($data['hasil'] as $row) {
      $string .= '<option value="' . $row->$kolom . '">' . $row->$kolom . '</option>';
    }
    echo $string;
  }

  public function pilih_mul($tabel, $kolomid, $kolom, $placeholder = false) // ambil data berdasarkan tipe kolom 'MUL'
  {
    $data['hasil'] = $this->getselect->get_option_mul($tabel);
    $string = ($placeholder == true) ? '<option value="">Please Select</option>' : '';
    foreach ($data['hasil'] as $row) {
      $data1 = $row->$kolomid;
      $data2 = $row->$kolom;
      $string .= "<option value=" . $data1 . ">" . $data2 . "</option>";
    }
    echo $string;
  }

  public function pilih_mul_kriteria($tabel, $kolomid, $kol_kriteria, $kriteria, $placeholder = false) // ambil data berdasarkan tipe kolom 'MUL'
  // karyawan/id_karyawan/bagian2/kameraman
  {
    $data['hasil'] = $this->getselect->get_option_mul2($tabel, $kol_kriteria, $kriteria);
    $string = ($placeholder == true) ? '<option value="">Please Select</option>' : '';
    foreach ($data['hasil'] as $row) {
      $data1 = $row->$kolomid;
      $data2 = $row->nama_karyawan;
      $string .= "<option value=" . $data1 . ">" . $data2 . "</option>";
    }
    echo $string;
  }

  public function pilih_mul_dua($tabel, $kolomid, $kolom1, $kolom2, $placeholder = false) // ambil data berdasarkan tipe kolom 'MUL'
  {
    $data['hasil'] = $this->getselect->get_option_mul($tabel, $kolomid, $kolom1);
    $string = ($placeholder == true) ? '<option value="">Please Select</option>' : '';

    foreach ($data['hasil'] as $row) {
      $data1 = $row->$kolomid;
      $data2 = $row->$kolom1;
      $data3 = $row->$kolom2;
      $string .= "<option value=" . $data1 . ">" . $data3 . " | " . $data2 . "</option>";
    }
    echo $string;
  }

  public function pilih_mul_sorted($tabel, $kolomid, $kolom1, $kolom2, $kriteria, $placeholder = false) // ambil data berdasarkan tipe kolom 'MUL'
  //liputan/id_liputan/liputan/tgl_kegiatan/2017-05
  {
    $kol_kriteria = $kolom2;
    $data['hasil'] = $this->getselect->get_option_mul_sorted($tabel, $kol_kriteria, $kriteria);
    $string = ($placeholder == true) ? '<option value="">Please Select</option>' : '';
    foreach ($data['hasil'] as $row) {
      $data1 = $row->$kolomid;
      $data2 = $row->$kolom1;
      $data3 = $row->$kolom2;
      $string .= "<option value=" . $data1 . ">" . $data3 . " | " . $data2 . "</option>";
    }
    echo $string;
  }

  public function pilih_mul_dua_sorted($tabel, $kolomid, $kolom1, $kolom2, $kolomKriteria, $kriteria, $placeholder = false) // ambil data berdasarkan tipe kolom 'MUL'
  //karyawan/id_karyawan/kode_karyawan/nama_karyawan/jabatan/5
  {
    $data['hasil'] = $this->getselect->get_option_mul_sorted($tabel, $kolomKriteria, $kriteria);
    $string = ($placeholder == true) ? '<option value="">Please Select</option>' : '';
    foreach ($data['hasil'] as $row) {
      $data1 = $row->$kolomid;
      $data2 = $row->$kolom1;
      $data3 = $row->$kolom2;
      $string .= "<option value=" . $data1 . ">" . $data3 . " | " . $data2 . "</option>";
    }
    echo $string;
  }

  function get_enum_values($tabel, $kolom, $placeholder = false) // mengambil nilai ENUM('data1','data2','etc') pada database
  {
    $hasil = $this->getselect->get_enum_valuesx($tabel, $kolom);
    $string = ($placeholder == true) ? '<option value="">Please Select</option>' : '';
    foreach ($hasil as $row) {
      $string .= '<option value="' . $row . '">' . $row . '</option>';
    }
    echo $string;
  }
}
