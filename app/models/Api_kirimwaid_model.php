<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require 'vendor/autoload.php';

// use GuzzleHttp\Client as GClient;


class Api_kirimwaid_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();

        $this->load->helper(array('MY_ribuan', 'MY_bulan'));
        $this->wa = $this->config->item('kirimwa_id');
    }

    public function apiKirimWaRequest(array $params)
    {
        $httpStreamOptions = [
            'method' => $params['method'] ?? 'GET',
            'header' => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . ($params['token'] ?? '')
            ],
            'timeout' => 15,
            'ignore_errors' => true
        ];

        if ($httpStreamOptions['method'] === 'POST') {
            $httpStreamOptions['header'][] = sprintf('Content-Length: %d', strlen($params['payload'] ?? ''));
            $httpStreamOptions['content'] = $params['payload'];
        }

        // Join the headers using CRLF
        $httpStreamOptions['header'] = implode("\r\n", $httpStreamOptions['header']) . "\r\n";

        $stream = stream_context_create(['http' => $httpStreamOptions]);
        $response = file_get_contents($params['url'], false, $stream);

        // Headers response are created magically and injected into
        // variable named $http_response_header
        $httpStatus = $http_response_header[0];

        preg_match('#HTTP/[\d\.]+\s(\d{3})#i', $httpStatus, $matches);

        if (!isset($matches[1])) {
            throw new Exception('Can not fetch HTTP response header.');
        }

        $statusCode = (int)$matches[1];
        if ($statusCode >= 200 && $statusCode < 300) {
            return ['body' => $response, 'statusCode' => $statusCode, 'headers' => $http_response_header];
        }

        throw new Exception($response, $statusCode);
    }

    public function post_device()
    {
        try {
            $reqParams = [
                'token' => $this->wa['API_TOKEN'],
                'url' => 'https://api.kirimwa.id/v1/devices',
                'method' => 'POST',
                'payload' => json_encode([
                    'device_id' => $this->wa['DEVICE_ID']
                ])
            ];

            $response = $this->apiKirimWaRequest($reqParams);
            echo $response['body'];
        } catch (Exception $e) {
            print_r($e);
        }
    }

    public function get_device()
    {
        try {
            $reqParams = [
                'token' => $this->wa['API_TOKEN'],
                'url' => 'https://api.kirimwa.id/v1/devices'
            ];

            $response = $this->apiKirimWaRequest($reqParams);
            return ['exception' => false, 'message' => $response['body']];
        } catch (Exception $e) {
            return ['exception' => true, 'message' => $e];
        }
    }

    public function get_device_byid()
    {
        try {
            $reqParams = [
                'token' => $this->wa['API_TOKEN'],
                'url' => sprint('https://api.kirimwa.id/v1/devices/%s', $this->wa['DEVICE_ID'])
            ];

            $response = $this->apiKirimWaRequest($reqParams);
            return ['exception' => false, 'message' => $response['body']];
        } catch (Exception $e) {
            return ['exception' => true, 'message' => $e];
        }
    }

    public function delete_device()
    {
        try {
            $reqParams = [
                'token' => $this->wa['API_TOKEN'],
                'url' => sprintf('https://api.kirimwa.id/v1/devices/%s', $this->wa['DEVICE_ID']),
                'method' => 'DELETE'
            ];

            $response = $this->apiKirimWaRequest($reqParams);
            echo $response['body'];
        } catch (Exception $e) {
            print_r($e);
        }
    }

    public function get_qr()
    {
        try {
            $query = http_build_query(['device_id' => $this->wa['DEVICE_ID']]);
            $reqParams = [
                'token' => $this->wa['API_TOKEN'],
                'url' => sprintf('https://api.kirimwa.id/v1/qr?%s', $query)
            ];

            $response = $this->apiKirimWaRequest($reqParams);
            echo $response['body'];
        } catch (Exception $e) {
            print_r($e);
        }
    }

    public function reconnect(){
        try {
            $reqParams = [
                'token' => $this->wa['API_TOKEN'],
                'url' => 'https://api.kirimwa.id/v1/reconnect',
                'method' => 'POST',
                'payload' => json_encode([
                    'device_id' => $this->wa['DEVICE_ID']
                ])
            ];

            $response = $this->apiKirimWaRequest($reqParams);
            return ['exception' => false, 'message' => $response['body']];
        } catch (Exception $e) {
            return ['exception' => true, 'message' => $e];
        }
    }

    public function test_post_messages()
    {
        try {
            $reqParams = [
                'token' => $this->wa['API_TOKEN'],
                'url' => 'https://api.kirimwa.id/v1/messages',
                'method' => 'POST',
                'payload' => json_encode([
                    'message' => 'Halo ini adalah pesan dari api.kirimwa.id.',
                    'phone_number' => '6281340310250',
                    'message_type' => 'text',
                    'device_id' => $this->wa['DEVICE_ID']
                ])
            ];

            $response = $this->apiKirimWaRequest($reqParams);
            echo $response['body'];
        } catch (Exception $e) {
            print_r($e);
        }
    }
    
    public function post_messages($data)
    {
        $nomorTelepon ='';
        if (substr($data['phone_number'], 0, 1) === '0') {
            // Menghapus angka awal '0' pada nomor telepon
            $nomorTelepon = substr($data['phone_number'], 1);
        }

        // Menambahkan kode negara '62' pada nomor telepon
        $telp = '62' . $nomorTelepon;

        try {
            $reqParams = [
                'token' => $this->wa['API_TOKEN'],
                'url' => 'https://api.kirimwa.id/v1/messages',
                'method' => 'POST',
                'payload' => json_encode([
                    'message' => $data['message'],
                    'phone_number' => $telp,
                    'message_type' => 'text',
                    'device_id' => $this->wa['DEVICE_ID']
                ])
            ];
            $response = $this->apiKirimWaRequest($reqParams);
            // return $response['body'];
            return ['param' => $reqParams, 'body' => $response['body']];
        } catch (Exception $e) {
            return $e.'catch aktif';
        }
    }

    public function batch_messages()
    {
        try {
            $reqParams = [
                'token' => $this->wa['API_TOKEN'],
                'url' => 'https://api.kirimwa.id/v1/batch-messages',
                'method' => 'POST',
                'payload' => json_encode([
                    'messages' => [
                        [
                            // Scheduled image message
                            'message' => 'https://rioastamal.net/portfolio/img/rioastamal.jpg?build=202001140325',
                            'caption' => 'Hallo',
                            'phone_number' => '6285298470228',
                            'send_at' => '2022-02-25T00:06:25+08:00'
                        ],
                        [
                            // Scheduled image message
                            'message' => 'https://rioastamal.net/portfolio/img/rioastamal.jpg?build=202001140325',
                            'caption' => 'Hallos',
                            'phone_number' => '6281340310250',
                            'send_at' => '2022-02-25T00:06:25+08:00'
                        ]
                    ],
                    'device_id' => $this->wa['DEVICE_ID']
                ])
            ];

            $response = $this->apiKirimWaRequest($reqParams);
            echo $response['body'];
        } catch (Exception $e) {
            print_r($e);
        }
    }

}