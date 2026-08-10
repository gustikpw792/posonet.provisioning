<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Whatsapp_model extends CI_Model {

    function __construct() {
        // START telegram api configuration
        $this->warow = array();
        $this->waapi = $this->db->query("SELECT * FROM settings 
        where option_name LIKE 'wa_%' ")->result();

        foreach ($this->waapi as $key) {
			$this->warow["$key->option_name"] = $key->option_value;
		}
        // END load telegram api configuration
    }

    /**
     * Helper untuk mengubah format nomor HP ke format WAHA (628xxx@c.us)
     */
    private function format_phone_number($number)
    {
        // 1. Hapus semua karakter selain angka
        $number = preg_replace('/[^0-9]/', '', $number);

        // 2. Ubah awalan '08' menjadi '628'
        if (substr($number, 0, 2) === '08') {
            $number = '62' . substr($number, 1);
        }
        // 3. Jika diinput '8xxx', tambahkan '62' di depannya
        elseif (substr($number, 0, 1) === '8') {
            $number = '62' . $number;
        }

        // 4. Tambahkan akhiran @c.us jika belum ada
        return $number . '@c.us';
    }

    public function send_message($data)
    {
        if (empty($this->warow['wa_mode']) || $this->warow['wa_mode'] == 'false') {
            return $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status'  => 'error',
                        'message' => 'Mode WhatsApp belum aktif!'
                    ]));
        }


        $groupId = $this->warow['wa_chat_id_group'];

        // Contoh input nomor HP dari request (bisa dari POST data $this->input->post('phone'))
        $rawPhone = '081340310250'; // Menerima: '081234567890', '+6281234567890', atau '81234567890'
        $formattedChatId = $this->format_phone_number($rawPhone); // Hasil: '6281234567890@c.us'

        // Inisialisasi Guzzle Client
        $client = new Client([
            'base_uri' => $this->warow['wa_base_url'],
            'timeout'  => 10.0,
        ]);

        $endpoint = '/api/sendText';
        $apiKey   = $this->warow['wa_api_key'];

        $payload = [
            'chatId'  => $groupId, #$formattedChatId,
            'text'    => $data, #Halo! Ini pesan dikirim dengan nomor HP yang di-format otomatis.',
            'session' => $this->warow['wa_session'],
        ];

        try {
            $response = $client->post($endpoint, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-Api-Key'    => $apiKey,
                ],
                'json' => $payload
            ]);

            $body = json_decode($response->getBody(), true);

            return $body;

        } catch (RequestException $e) {
            $errorMessage = $e->getMessage();
            $statusCode   = 500;

            if ($e->hasResponse()) {
                $statusCode   = $e->getResponse()->getStatusCode();
                $errorMessage = (string) $e->getResponse()->getBody();
            }

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header($statusCode)
                ->set_output(json_encode([
                    'status'  => 'error',
                    'message' => $errorMessage
                ]));
        }
    }
}