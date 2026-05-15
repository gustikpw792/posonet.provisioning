<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Maps_model extends CI_Model {
    
    private $client;
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
        
        // Inisialisasi Guzzle Client
        $this->client = new Client([
            'timeout' => 10,
            'allow_redirects' => [
                'max' => 5,
                'strict' => true,
                'referer' => true,
                'protocols' => ['http', 'https'],
                'track_redirects' => true
            ],
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ]
        ]);
    }
    
    /**
     * Ambil semua data link dari database
     */
    public function get_all_maps_links() {
        // Sesuaikan dengan nama tabel dan kolom Anda
        $query = $this->db->query("SELECT * FROM pelanggan WHERE lokasi_map != ''");
        return $query->result_array();
    }
    
    /**
     * Update data dengan latitude dan longitude
     */
    public function update_coordinates($id, $latitude, $longitude) {
        $data = [
            'latitude' => $latitude,
            'longitude' => $longitude,
            // 'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $this->db->where('id_pelanggan', $id);
        return $this->db->update('pelanggan', $data);
    }
    
    /**
     * Ekstrak koordinat dari URL Google Maps
     */
    public function extract_coordinates($url) {
        if (empty($url)) {
            return [
                'error' => 'Koordinat tidak ditemukan',
                'url_decode' => '',
                'url' => $url,
                'latitude' => '',
                'longitude' => '',
            ];
        }

        try {
            // 1. Ikuti redirect untuk mendapatkan URL akhir
            $response = $this->client->get($url, [
                'on_stats' => function (\GuzzleHttp\TransferStats $stats) use (&$effectiveUrl) {
                    $effectiveUrl = $stats->getEffectiveUri()->__toString();
                }
            ]);
            
            // 2. Parse URL untuk mendapatkan koordinat
            $coordinates = $this->parse_coordinates($effectiveUrl);
            
            return $coordinates;
            
        } catch (RequestException $e) {
            log_message('error', 'Guzzle Error: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
    
    /**
     * Parse koordinat dari berbagai format URL Google Maps
     */
    private function parse_coordinates($url) {

    // Decode URL jika perlu
    $decoded_url = str_replace(' ','',urldecode($url));

    // Pattern untuk mencari koordinat dalam berbagai format
    $patterns = [
        '/q=(.*?)\&/',

        // https://maps.app.goo.gl/4YSaE1ek15mFYFNs5
        // https://www.google.com/maps/place/-2.072461,120.681886/data=!4m6!3m5!1s0!7e2!8m2!3d-2.0724606!4d120.6818862?utm_source=mstt_1&entry=gps&coh=192189&g_ep=CAESBzI1LjE5LjYYACD67A0qdSw5NDI1OTU1MSw5NDIyMzI5OSw5NDIxNjQxMyw5NDIxMjQ5Niw5NDIwNzM5NCw5NDIwNzUwNiw5NDIwODUwNiw5NDIxNzUyMyw5NDIxODY1Myw5NDIyOTgzOSw0NzA4NDM5Myw5NDIxMzIwMCw5NDI1ODMyNUICSUQ%3D&skid=cfcdff39-4f0d-4d3b-a522-6a9f6861ed4f
        '/\/place\/(.*?\d+\.\d+,.*?\d+\.\d+)\/data/',

        // https://maps.app.goo.gl/3r3R1qp7DJRGH8AX8
        // https://www.google.com/maps/search/-2.123158,+120.704909?entry=tts
        '/\/search\/(.*?\d+\.\d+,.*?\d+\.\d+)/',
        // '/\/search\/(.*?,.*?)\?/',

        // https://maps.app.goo.gl/LpfbWpdjxJ9YDc5v7
        // https://www.google.com/maps/place/-2.110262,120.694700/data=!4m6!3m5!1s0!7e2!8m2!3d-2.1102616!4d120.69470009999999!17m2!4m1!1e3!18m1!1e1?utm_source=mstt_1&entry=gps&coh=192189&g_ep=CAESBzI1LjQ5LjYYACD67A0qmwEsOTQyNTk1NTEsOTQyNjc3MjcsOTQyNzU0MDcsOTQyOTIxOTUsOTQyOTk1MzIsOTQyODQ1MTEsOTQyODA1NzYsOTQyMDczOTQsOTQyMDc1MDYsOTQyMDg1MDYsOTQyMTg2NTMsOTQyMjk4MzksOTQyNzUxNjgsOTQyNzk2MTksOTQyNjI3MzksMTAwNzkyNTcyLDEwMDc5MTQ3OUICSUQ%3D&skid=7e9ee793-3331-456d-810f-84f55b2cbca2
        // '/place\/(.*?\d+\.\d+,.*?\d+\.\d+)/',

        // https://goo.gl/maps/uNF89z8kYcbvXJAHA
        // https://www.google.com/maps/place/2%C2%B007'24.9%22S+120%C2%B042'16.7%22E/@-2.1235807,120.7040948,189m/data=!3m2!1e3!4b1!4m6!3m5!1s0x0:0x0!7e2!8m2!3d-2.1235821!4d120.7046425?shorturl=1
        // '/place\/.*?\/@(.*?\d+\.\d+,.*?\d+\.\d+)/',
        '/place\/.*?\/@(-?\d+\.\d+,-?\d+\.\d+).*?\//',

        // Berhasil untuk link ini https://maps.app.goo.gl/6jJR8aU3pw2WQUTq8
        // https://www.google.com/maps/place/Rumah+Makan+Mataram/@-2.11692,120.700455,776m/data=!3m1!1e3!4m6!3m5!1s0x2d91d8087ae25f9f:0x1349232db7dbc591!8m2!3d-2.1176241!4d120.699944!16s%2Fg%2F11j7xbndrx?entry=ttu&g_ep=EgoyMDI2MDIwMS4wIKXMDSoKLDEwMDc5MjA3M0gBUAM%3D
        '/@(.*?\d+\.\d+,.*?\d+\.\d+)/',
        // '/@(.*?,.*?)/',
        
        // Berhasil untuk link ini https://maps.app.goo.gl/cEZht58f1eaw5KJ7A
        // https://www.google.com/maps/search/-2.110062,+120.707107?coh=277535&entry=tts&g_ep=EgoyMDI2MDIwMS4wIPu8ASoKLDEwMDc5MjA3M0gBUAM%3D&skid=3270ce99-cdfb-4100-a89d-9accc76b1170
        // '/\/search\/(.*?)\?/',
        '/search\/(.*?)\?/',
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $decoded_url, $matches)) {
            // return $matches;
            $matches = explode(',' , $matches[1]);
            return [
                'latitude' => $matches[0],
                'longitude' => $matches[1],
                'url_decode' => $decoded_url,
            ];
        }
        // else {
        //     $this->extract_coordinates($decoded_url);
        // }
        
    }

    return [
        'error' => 'Koordinat tidak ditemukan',
        'url_decode' => $decoded_url,
        'url' => $url,
        'latitude' => '',
        'longitude' => '',
    ];

        
    }
    
    /**
     * Proses batch semua data
     */
    public function process_all_data() {
        $results = [];
        $data = $this->get_all_maps_links();
        
        foreach ($data as $item) {
            $url = urldecode($item['lokasi_map']); // Sesuaikan dengan nama kolom
            
            // Ekstrak koordinat
            $coordinates = $this->extract_coordinates($url);
            
            if (!isset($coordinates['error'])) {
                // Update database
                $this->update_coordinates(
                    $item['id_pelanggan'],
                    $coordinates['latitude'],
                    $coordinates['longitude']
                );
                
                $results[] = [
                    'id' => $item['id_pelanggan'],
                    'latitude' => $coordinates['latitude'],
                    'longitude' => $coordinates['longitude'],
                    'status' => 'success'
                ];
            } else {
                $results[] = [
                    'id' => $item['id_pelanggan'],
                    'error' => $coordinates['error'],
                    'status' => 'failed'
                ];
            }
            
            // Delay untuk menghindari blokir (opsional)
            usleep(500000); // 0.5 detik
        }
        
        return $results;
    }
}