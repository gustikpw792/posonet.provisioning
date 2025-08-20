<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Users_model extends CI_Model {

  var $table = 'users';
  var $table2 = 'v_users';
  var $column = array('id_users','username','nama_lengkap','level','aktif');
  var $order = array('id_users'=>'DESC');

  function __construct()
  {
      parent::__construct();
      $this->load->database();
  }
  // serverside datatable
  private function _get_datatables_query()
  {
    $this->db->from($this->table2);
    $i = 0;
    foreach ($this->column as $item) // loop column
    {
      if($_POST['search']['value']) // if datatable send POST for search
      {
        if($i===0) // first loop
        {
          $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
          $this->db->like($item, $_POST['search']['value']);
        }
        else
        {
          $this->db->or_like($item, $_POST['search']['value']);
        }
        if(count($this->column) - 1 == $i) //last loop
          $this->db->group_end(); //close bracket
      }
      $column[$i] = $item; // set column array variable to order processing
      $i++;
    }
    if(isset($_POST['order'])) // here order processing
    {
      $this->db->order_by($column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
    }
    else if(isset($this->order))
    {
      $order = $this->order;
      $this->db->order_by(key($order), $order[key($order)]);
    }
  }

  function get_datatables()
  {
    $this->_get_datatables_query();
    if($_POST['length'] != -1)
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
    $this->db->from($this->table2);
    return $this->db->count_all_results();
  }
// batas serverside datatable
  //CRUD users
  public function get_by_id($id_users)
  {
    $this->db->from('users');
    $this->db->where('id_users',$id_users);
    $query = $this->db->get();

    return $query->row();
  }

  public function save($data)
  {
    $this->db->insert('users', $data);
    return $this->db->insert_id();
  }

  public function update($where, $data)
  {
    $this->db->update('users', $data, $where);
    return $this->db->affected_rows();
  }

  public function delete_by_id($id_users)
  {
    $this->db->where('id_users', $id_users);
    $this->db->delete('users');
  }

  public function getUser($user,$pass)
  {
    $query = $this->db->query("SELECT * FROM v_users WHERE username='$user' AND password='$pass'");
    return $query;
  }

  public function v_get_by_id($id_users)
  {
    $this->db->from('v_users');
    $this->db->where('id_users',$id_users);
    $query = $this->db->get();

    return $query->row();
  }

}
