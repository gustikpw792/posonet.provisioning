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
        $query = $this->db->query("SELECT id_pelanggan, no_pelanggan,nama_pelanggan, lokasi_map,latitude,longitude 
        FROM pelanggan 
        WHERE lokasi_map != '' AND (latitude = '' OR longitude ='')");
        return $query->result_array();
    }

    /**
     * Update data dengan latitude dan longitude
     * @latitude float
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
            // $coordinates = $effectiveUrl;
            
            return $coordinates;
            
        } catch (RequestException $e) {
            log_message('error', 'Guzzle Error: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
    
    /**
     * Parse koordinat dari berbagai format URL Google Maps
     */
    public function parse_coordinates($url) {

    // Decode URL jika perlu
    $url = str_replace(' ','',urldecode($url));
        // Pattern untuk mencari koordinat dalam berbagai format
        // 1. Cek Format Pertama: //@lat,lng
        if (preg_match('/@(-?\d+(?:\.\d+)?),\s*(-?\d+(?:\.\d+)?)/', $url, $matches)) {
            return [
                'latitude'  => $matches[1],
                'longitude' => $matches[2]
            ];
        }

        // 2. Cek Format Kedua: !3dlat!4dlng
        if (preg_match('/!3d(-?\d+(?:\.\d+)?)\!4d(-?\d+(?:\.\d+)?)/', $url, $matches)) {
            // var_dump($matches);

            return [
                'latitude'  => $matches[1],
                'longitude' => $matches[2]
            ];
        }

        // https://maps.app.goo.gl/3r3R1qp7DJRGH8AX8
        // https://www.google.com/maps/search/-2.123158,+120.704909?entry=tts
        if (preg_match('/\/search\/(.*?\d+\.\d+,.*?\d+\.\d+)/', $url, $matches)) {
            // var_dump($matches);

            return [
                'latitude'  => explode(',', $matches[1])[0],
                'longitude' => explode(',', $matches[1])[1],
            ];
        }

        // https://maps.app.goo.gl/4YSaE1ek15mFYFNs5
        // https://www.google.com/maps/place/-2.072461,120.681886/data=!4m6!3m5!1s0!7e2!8m2!3d-2.0724606!4d120.6818862?utm_source=mstt_1&entry=gps&coh=192189&g_ep=CAESBzI1LjE5LjYYACD67A0qdSw5NDI1OTU1MSw5NDIyMzI5OSw5NDIxNjQxMyw5NDIxMjQ5Niw5NDIwNzM5NCw5NDIwNzUwNiw5NDIwODUwNiw5NDIxNzUyMyw5NDIxODY1Myw5NDIyOTgzOSw0NzA4NDM5Myw5NDIxMzIwMCw5NDI1ODMyNUICSUQ%3D&skid=cfcdff39-4f0d-4d3b-a522-6a9f6861ed4f
        if (preg_match('/\/place\/(.*?\d+\.\d+,.*?\d+\.\d+)\/data/', $url, $matches)) {
            var_dump($matches);

            return [
                'latitude'  => explode(',', $matches[1])[0],
                'longitude' => explode(',', $matches[1])[1],
            ];
        }

        // https://goo.gl/maps/uNF89z8kYcbvXJAHA
        // https://www.google.com/maps/place/2%C2%B007'24.9%22S+120%C2%B042'16.7%22E/@-2.1235807,120.7040948,189m/data=!3m2!1e3!4b1!4m6!3m5!1s0x0:0x0!7e2!8m2!3d-2.1235821!4d120.7046425?shorturl=1
        if (preg_match('/place\/.*?\/@(-?\d+\.\d+,-?\d+\.\d+).*?\//', $url, $matches)) {
            var_dump($matches);

            return [
                'latitude'  => explode(',', $matches[1])[0],
                'longitude' => explode(',', $matches[1])[1],
            ];
        }

        return null; // Jika kedua pola tidak cocok
        
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