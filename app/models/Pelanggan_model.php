<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Pelanggan_model extends CI_Model
{

  var $table = 'pelanggan';
  var $vtable = 'v_pelanggan';
  var $column = array('no_pelanggan', 'nama_pelanggan','expired', 'serial_number', 'status','gpon_onu', 'ont_phase_state','odp_number');
  var $order = array(
    'id_pelanggan' => 'DESC'
  );

  function __construct()
  {
    parent::__construct();
    $this->load->database();
  }
  // serverside datatable
  private function _get_datatables_query()
  {

    $this->db->from($this->vtable);

    $i = 0;

    foreach ($this->column as $item) // loop column
    {
      if ($_POST['search']['value']) // if datatable send POST for search
      {

        if ($i === 0) // first loop
        {
          $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
          $this->db->like($item, $_POST['search']['value']);
        } else {
          $this->db->or_like($item, $_POST['search']['value']);
        }

        if (count($this->column) - 1 == $i) //last loop
          $this->db->group_end(); //close bracket
          // //TAMBAH MANUAL BY KETUT
          // $this->db->where('id_wilayah !=','81');
      }
      $column[$i] = $item; // set column array variable to order processing
      $i++;
    }

    if (isset($_POST['order'])) // here order processing
    {
      $this->db->order_by($column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
    } else if (isset($this->order)) {
      $order = $this->order;
      $this->db->order_by(key($order), $order[key($order)]);
    }
    /**
     * TAMBAH MANUAL BY KETUT 
     * fungsi dibawah ini untuk akses pelanggan berdasarkan akses_wilayah yang berada di tabel user
     * */
    if($this->hakAksesToWilayah()->status) {
      $idW = $this->hakAksesToWilayah()->data;
      $this->db->where_not_in('id_wilayah', $idW);
    }
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


  function get_datatables()
  {
    $this->_get_datatables_query();
    if ($_POST['length'] != -1)
      $this->db->limit($_POST['length'], $_POST['start']);
    $query = $this->db->get();
    return $query->result();
  }

  function count_filtered()
  {
    $this->_get_datatables_query();
    $query = $this->db->get();
    return $query->num_rows();
  }

  public function count_all()
  {
    $this->db->from($this->vtable);
    return $this->db->count_all_results();
  }
  // batas serverside datatable
  //CRUD pelanggan
  public function v_get_by_id($id_pelanggan)
  {
    $this->db->from($this->vtable);
    $this->db->where('id_pelanggan', $id_pelanggan);
    $query = $this->db->get();

    return $query->row();
  }

  public function get_by_id($id_pelanggan)
  {
    $this->db->from($this->table);
    $this->db->where('id_pelanggan', $id_pelanggan);
    $query = $this->db->get();

    return $query->row();
  }

  public function save($data)
  {
    $this->db->insert('pelanggan', $data);
    return $this->db->insert_id();
  }

  public function update($where, $data)
  {
    $this->db->update('pelanggan', $data, $where);
    return $this->db->affected_rows();
  }

  public function delete_by_id($id_pelanggan)
  {
    $this->db->where('id_pelanggan', $id_pelanggan);
    $this->db->delete('pelanggan');
  }

  // public function cekLastKodeWil($wilayah) {
  //   $this->db->select_max('kode_pelanggan','maxKode');  // artinya SELECT max(kode_plgn) as maxKode
  //   $this->db->from($this->table);
  //   $this->db->like('no_pelanggan',$wilayah,'after'); // artinya WHERE kode_plgn LIKE '$wilayah%'
  //                                                     // after = $wilayah%    <- lihat posisi persennya
  //                                                     // before = %$wilayah
  //                                                     // both = %$wilayah%
  //   $cek = $this->db->get();
  //   return $cek->row();
  // }

  // public function cekLastKodeWil_v2($wilayah)
  // {
  //   $q = $this->db->query("SELECT MAX(p.kode_pelanggan) AS maxKode, w.wilayah
  //   FROM pelanggan p, wilayah w
  //   WHERE p.kode_pelanggan LIKE '$wilayah%'
  //   AND w.kode_wilayah = '$wilayah'
  //   ");
  //   return $q->row();
  // }

  public function cekKodeWil($id_wilayah)
  {
    return $this->db->query("SELECT w.id_wilayah,w.kode_wilayah,w.wilayah
    FROM wilayah w
    WHERE w.id_wilayah = '$id_wilayah' ")->row();
  }

  

}
