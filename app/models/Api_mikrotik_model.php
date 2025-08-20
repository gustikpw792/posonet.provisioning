<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require 'vendor/autoload.php';

use GuzzleHttp\Client as GClient;

use \RouterOS\Client as RClient;
use \RouterOS\Query;


class Api_mikrotik_model extends CI_Model
{

  private $_client;
  private $_clientMtik;

  function __construct()
  {
    parent::__construct();
    $this->load->database();

    // MIKROTIK
    $this->mikrotik = $this->config->item('mikrotik');
    
    $this->_mikrotik = new \RouterOS\Config([
      'host' => $this->mikrotik['HOST'],
      'user' => $this->mikrotik['USERNAME'],
      'pass' => $this->mikrotik['PASSWORD'],
      'port' => $this->mikrotik['PORT'],
    ]);
    
    $this->_restClient = new GClient([
      'base_uri' => $this->mikrotik['REST_URL'],
      'timeout' => 9.0
    ]);

    $this->_clientMtik = new RClient($this->_mikrotik);

  }

  public function close_connection_ppp($username = false)
  {
    //get by name for return .id
    $query = (new Query('/ppp/active/print',))
            ->where('name', $username);
    $connections = $this->_clientMtik->query($query)->read();
    
    if ($connections != null) {

      foreach ($connections as $active) {
        $query2 = (new Query('/ppp/active/remove',))
        ->equal('.id', $active['.id']);
        
        $this->_clientMtik->query($query2)->read();
      }

    }
    // /ppp active remove numbers=[/ppp active find where name=PAPA-ENJELC91C27BD]
    return $connections;
  }

  public function change_ppp_secret_profile($username = false, $paket)
  {
    //get by name for return .id
    $query = (new Query('/ppp/secret/print',))
    ->where('name', $username);
    
    $secrets = $this->_clientMtik->query($query)->read();

    foreach ($secrets as $secret) {
      
      $query2 = (new Query('/ppp/secret/set',))
      ->equal('.id', $secret['.id'])
      ->equal('profile', $paket);
      
      $this->_clientMtik->query($query2)->read();
      
    }
    
    return $secrets;
  }

  public function change_ppp_secret_profile_by_id($id, $paket)
  {
    $query = (new Query('/ppp/secret/set',))
      ->equal('.id', $id)
      ->equal('profile', $paket);
      
    $this->_clientMtik->query($query)->read();
  }


  public function get_ppp_secret(){
    $query = new Query('/ppp/secret/print');
    return $this->_clientMtik->query($query)->read();
  }

  public function create_ppp_secret($username, $password, $service, $profile){
    $query = new Query('/ppp/secret/add');
    $query->equal('name', $username);
    $query->equal('password', $password);
    $query->equal('service', $service);
    $query->equal('profile', $profile);


    return $this->_clientMtik->query($query)->read();
  }

  public function remove_secret($username){
    //get by name for return .id
    $query = (new Query('/ppp/secret/print',))
      ->where('name', $username);
    $secrets = $this->_clientMtik->query($query)->read();
    $rem = false;
    
    foreach ($secrets as $secret) {

      $remove = (new Query('/ppp/secret/remove',))
      ->equal('.id', $secret['.id']);
      $rem = $this->_clientMtik->query($remove)->read();

    }

    return $rem;
  }

  public function match_paket(){
    /***
     * apabila paket ppp secret mikrotik tidak sama dengan yang ada di database. 
     * maka ubah ppp secret di mikrotik dengan yang di database
     * kemudian close connection
     */
    $no = 0;
    $changedProfile = '';
    $secretMtik = $this->get_ppp_secret();

    foreach ($secretMtik as $d) {
      $name = $this->db->escape($d['name']);
      $cekdb = $this->db->query("SELECT id_pelanggan, username, mikrotik_profile, status_berlangganan FROM v_pelanggan WHERE username=$name");
      
      // jika name sama maka cek profile
      if ($cekdb->num_rows() > 0 ){
        $data = $cekdb->row();

        if ($data->status_berlangganan == 'Expired' && $d['profile'] != 'Expired') {
          // set to Expired
          $this->change_ppp_secret_profile_by_id($d['.id'], 'Expired');
          // close connection ppp
          $this->close_connection_ppp($d['name']);
          
          $changedProfile .= "$name, ";
          $no++;
        } elseif ($data->status_berlangganan == 'Active' && $d['profile'] != $data->mikrotik_profile) {
          $this->change_ppp_secret_profile_by_id($d['.id'], $data->mikrotik_profile);
          $this->close_connection_ppp($d['name']);
          
          $changedProfile .= "$name, ";
          $no++;
        } else {
          // nothing changed!
        }
      }
    }
    return "$no Profile changed! [$changedProfile]";
  }

  /* 
  Set to Expire on ROS7 REST
  */
  public function match_paket_rest(){
    /***
     * apabila paket ppp secret mikrotik tidak sama dengan yang ada di database. 
     * maka ubah ppp secret di mikrotik dengan yang di database
     * kemudian close connection
     */
    $no = 0;
    $notMatch = $changedToExpired = '';
    $secretMtik = json_decode($this->getRestSecret(), true);
    // return json_decode($secretMtik);
    // exit();
    
    foreach ($secretMtik as $d) {
      $name = $this->db->escape($d['name']);
      $cekdb = $this->db->query("SELECT id_pelanggan, username, mikrotik_profile, status_berlangganan FROM v_pelanggan WHERE username=$name");
      
      // echo "M=$d->name<br>";
      
      // jika name sama maka cek profile
      if ($cekdb->num_rows() > 0 ){
        $data = $cekdb->row();
        
        // echo "M=$d[name] | DB=$data->username <br>";

        if ($data->status_berlangganan == 'Expired' && $d['profile'] != 'Expired') {
          // set to Expired
          $this->patchRestSecretById($d['.id'], 
          (object) array(
            'profile' => 'Expired'
          ));

          // close connection ppp
          $this->pppCloseConnection($d['name']);
          
          $changedToExpired .= "$name, ";
          $no++;
        } elseif ($data->status_berlangganan == 'Active' && $d['profile'] != $data->mikrotik_profile) {
          $this->patchRestSecretById($d['.id'], 
          (object) array(
            'profile' => $data->mikrotik_profile
          ));

          $this->pppCloseConnection($d['name']);
          
          $notMatch .= "$name, ";
          $no++;
        } else {
          // nothing changed!
        }
      }
    }
    return "$no PPP Secret Profile changed! <br>Expired = [$changedToExpired]<br>Not Match = [$notMatch]";
  }

  


  public function get_ppp_ip_address($username = false)
  {
    //get by name for return .id
    $query = (new Query('/ppp/active/print',))
    ->where('name', $username);
    
    return $this->_clientMtik->query($query)->read();
  }

  /**
	 * REST HTTP ROUTEROS 7,9^
	 */

   function getRestSecret($username=false) {
    $query = (!$username) ? "" : "?name=$username";

    $response = $this->_restClient->get("ppp/secret$query",
    [
      'auth' => [$this->mikrotik['USERNAME'], $this->mikrotik['PASSWORD']]
    ]);

    return $response->getBody();
   }

   function putRestSecret($data) {
    /** Contoh data
      *  $data = (object) array(
      *		'name'      => "COBA",
      *		'password'  =>".COBA!",
      *		'profile'   => "UPTO-10M",
      *		'service'   => 'pppoe'
      *  );
      * 
     */

    $response = $this->_restClient->put('ppp/secret',
    [
      'auth' => [$this->mikrotik['USERNAME'], $this->mikrotik['PASSWORD']],
      'headers' => ['Content-type: application/json'],
      'body' => json_encode($data),
    ]);
    return true;
   }


   function patchRestSecret($data) {

    /* 
    $data = (object) array(
      'name' => '123.NAME'
      'profile' => 'Expired'
    ); 
    */

    $getId =  json_decode($this->getRestSecret($data->name), true);
    $id = '';

    foreach ($getId as $key) {
      $id = $key['.id'];
    }
    
    $response = $this->_restClient->patch("ppp/secret/$id",
    [
      'auth' => [$this->mikrotik['USERNAME'], $this->mikrotik['PASSWORD']],
      'headers' => ['Content-type: application/json'],
      'body' => json_encode($data),
    ]);

    return $response->getBody();
   }

   function patchRestSecretById($id, $data) {
    /* 
    $data = (object) array(
      'profile' => 'Expired'
    ); 
    */

    $response = $this->_restClient->patch("ppp/secret/$id",
    [
      'auth' => [$this->mikrotik['USERNAME'], $this->mikrotik['PASSWORD']],
      'headers' => ['Content-type: application/json'],
      'body' => json_encode($data),
    ]);

    return $response->getBody();
   }

   function deleteRestSecret($data) {

    /**Contoh data yang diminta harus dalam bentuk object
     * $data = (object) array('name' => $data->username);
     * 
    */

    $getId =  json_decode($this->getRestSecret($data->name), true);
    $id = '';

    foreach ($getId as $key) {
      $id = $key['.id'];
    }
    
    if ($id!='') {
      $response = $this->_restClient->delete("ppp/secret/$id",
      [
        'auth' => [$this->mikrotik['USERNAME'], $this->mikrotik['PASSWORD']]
      ]);
  
      return $response->getBody();

    }

   }

   function getRestActiveConnection($username=false) {
    $query = (!$username) ? "" : "?name=$username";

    $response = $this->_restClient->get("ppp/active$query",
    [
      'auth' => [$this->mikrotik['USERNAME'], $this->mikrotik['PASSWORD']]
    ]);

    return $response->getBody();
   }
   
   function pppCloseConnection($data) {
     
    /**
     * $data = $data->username;
     * 
    */

    $getId =  json_decode($this->getRestActiveConnection($data), true);
    $id = '';

    foreach ($getId as $key) {
      $id = $key['.id'];
    }
    
    if ($id!='') {
      $response = $this->_restClient->delete("ppp/active/$id",
      [
        'auth' => [$this->mikrotik['USERNAME'], $this->mikrotik['PASSWORD']]
      ]);
  
      return $response->getBody();

    }

   }

   function getRemoteNAT($comment='REMOTEONT') {
    //  http://192.168.50.1:8090/rest/ip/firewal/nat?comment=REMOTEONT-dontdelete

    $query = ($comment == '') ? "" : "?comment=$comment";
   
    $response = $this->_restClient->get("ip/firewal/nat$query",
    [
      'auth' => [$this->mikrotik['USERNAME'], $this->mikrotik['PASSWORD']]
    ]);
   
    return $response->getBody();
   }

   function patchRemoteNATById($id, $data) {
    /* 
    $data = (object) array(
      'to-addresses' => '10.50.10.100'
    ); 
    */

    $response = $this->_restClient->patch("ip/firewal/nat/$id",
    [
      'auth' => [$this->mikrotik['USERNAME'], $this->mikrotik['PASSWORD']],
      'headers' => ['Content-type: application/json'],
      'body' => json_encode($data),
    ]);

    return $response->getBody();
   }

   public function setPublicRemoteOnt($data,$dst) {
    /**
     * $data = (object) array(
     *   'to-addresses' => '10.50.10.100'
     **/
    foreach (json_decode($this->getRemoteNAT(),true) as $key) {
      $id = $key['.id'];
    }

    if ($id != '') {
      return $this->patchRemoteNATById($id, $data);
    } else {
      // create new remote nat
      $putData = [
        'action' => 'dst-nat',
        'chain' => 'dstnat',
        'dst-address' => $this->mikrotik['HOST'],
        'dst-port' => $this->mikrotik['PORT_REMOTEWEB'],
        'protocol' => 'tcp',
        'to-addresses' => $dst,
        'to-ports' => '80',
        'comment' => 'REMOTEONT',
      ];

      $response = $this->_restClient->put("ip/firewal/nat",
      [
        'auth' => [$this->mikrotik['USERNAME'], $this->mikrotik['PASSWORD']],
        'headers' => ['Content-type: application/json'],
        'body' => json_encode($putData),
      ]);
      return $response->getBody();
    }
   }

}
