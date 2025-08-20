<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require 'vendor/autoload.php';

use GuzzleHttp\Client;

class Provisioning_model extends CI_Model
{

  private $_client;

  function __construct()
  {
    parent::__construct();
    $this->load->database();

    $this->load->model('api_telegrambot_model','telegram');

    $this->olt = $this->config->item('olt');

    $this->_client = new Client([
      'base_uri' => $this->olt['BASE_URI']
    ]);

    $this->mikrotik = $this->config->item('mikrotik');

    $this->_mikrotik = new \RouterOS\Config([
      'host' => $this->mikrotik['HOST'],
      'user' => $this->mikrotik['USERNAME'],
      'pass' => $this->mikrotik['PASSWORD'],
      'port' => $this->mikrotik['PORT'],
    ]);
  }

  public function getTcont() {
    $response = $this->_client->request('GET', 'getTcont');
    return json_decode($response->getBody());
  }

  
}
