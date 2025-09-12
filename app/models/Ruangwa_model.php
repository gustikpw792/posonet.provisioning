<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
date_default_timezone_set("Asia/Makassar");

class Ruangwa_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->endpoint = $this->config->item('ruang_wa'); // Load endpoint configuration
    }


    public function getQr()
    {
        $client = new Client();
        $options = [
        'form_params' => [
        'token' => $this->token,
        ],'timeout' => 2];
        $request = new Request('POST', 'https://app.ruangwa.id/api/qrcode_image');
        $res = $client->sendAsync($request, $options)->wait();
        return $res->getBody();
    }

    public function sendMessageSuccess($data)
    {
        if ($data['wamode']) 
        {
            $client = new Client();
            $options = [
            'form_params' => [
                'token' => $this->endpoint['token'],
                'number' => $data['number'],
                'message' => sprintf($this->endpoint['msg_success'],$data['expired']),
                'date' => date('Y-m-d'),
                'time' => date('h:i:s')
            ]];

            $request = new Request('POST', $this->endpoint['api_url'].'/send_message');
            $res = $client->sendAsync($request, $options)->wait();
            return $res->getBody();
            // return $options;
        }
    }

    

}