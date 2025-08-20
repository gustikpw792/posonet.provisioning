<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require 'vendor/autoload.php';

use GuzzleHttp\Client;

class Api_whatsapp_model extends CI_Model
{

  function __construct()
  {
    parent::__construct();
    // $this->load->database();
    
    $this->wa = $this->config->item('node_whatsappapi');
    
    $this->_client = new Client([
      'base_uri' => $this->wa['URL']
    ]);


  }

  public function sendWa($data){

    $response = $this->_client->request('POST', '/message',[
      'headers' => [
        'Content-Type' => 'application/json'
      ],
      'json' => [
        'secretApp' => $this->wa['SECRET_APP'],
        'phoneNumber' => '6281340310250',
        'message' => $data
    ]
    ]);

    return json_decode($response->getBody());
  }

  public function sendToAdmin($data){
    $response = $this->_client->request('POST', '/message',[
      'headers' => [
        'Content-Type' => 'application/json'
      ],
      'json' => [
        'secretApp' => $this->wa['SECRET_APP'],
        'phoneNumber' => $this->wa['ADMIN_NUMBER'],
        'message' => $data->message
      ],
    ]);

    return json_decode($response->getBody());
  }
  
  public function sendToClient($data){
    $response = $this->_client->request('POST', '/message',[
      'headers' => [
        'Content-Type' => 'application/json'
      ],
      'json' => [
        'secretApp' => $this->wa['SECRET_APP'],
        'phoneNumber' => $data->telp,
        'message' => $data->message
      ],
    ]);

    return json_decode($response->getBody());
  }


  
}
