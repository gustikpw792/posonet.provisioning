<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Getselect_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_option($tabel,$kolom)
    {
      $this->db->select($kolom);
  		$this->db->from($tabel);
  		$query = $this->db->get();
  		return $query->result();
    }

    public function get_option_mul($tabel)
    {
      $this->db->select('*');
  		$this->db->from($tabel);
      
      if ($tabel == 'wilayah') {
        if($this->hakAksesToWilayah()->status) {
          $idW = $this->hakAksesToWilayah()->data;
          $this->db->where_not_in('id_wilayah', $idW);
        }
      }

  		$query = $this->db->get();
  		return $query->result();
    }

    public function get_option_mul2($tabel,$kol_kriteria,$kriteria)
    {
      $this->db->select('*');
      $this->db->from($tabel);
      $this->db->where($kol_kriteria,$kriteria);
  		$query = $this->db->get();
  		return $query->result();
    }

    public function get_option_mul_sorted($tabel,$kol_kriteria,$kriteria)
    {
      $this->db->select('*');
      $this->db->from($tabel);
      $this->db->like($kol_kriteria,$kriteria,'both');
      $query = $this->db->get();
      return $query->result();
    }

    public function get_option_enum($tabel,$kolom)
    {
      // $query = $this->db->query("SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '$tabel' AND COLUMN_NAME = '$kolom'");
      $query = $this->db->query("SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='perpustakaan' AND TABLE_NAME='$tabel' AND COLUMN_NAME='$kolom' AND DATA_TYPE='enum'");
      return $query->row();
    }

    function get_enum_valuesx( $table, $field )
    {
      $type = $this->db->query( "SHOW COLUMNS FROM {$table} WHERE Field = '{$field}'" )->row( 0 )->Type;
      preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
      $enum = explode("','", $matches[1]);
      return $enum;
    }

    function hakAksesToWilayah()
    {
      $username = $this->session->username;
      $hakAksesWilayah = $this->db->query("SELECT * FROM users WHERE username = '$username' ")->row();
      if ($hakAksesWilayah->akses_wilayah == null) {
        return $data = (object) array(
          'status' => false,
          'data' => [],
        );
      } else {
        return $data = (object) array(
          'status' => true,
          'data' => json_decode($hakAksesWilayah->akses_wilayah),
        );
      }
    }

	}
