<?php
defined('BASEPATH') or exit('No direct script access allowed');

class TestRedis extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->config->load('redis', TRUE);
        
        // Load driver cache dengan adapter Redis jika belum di-autoload
        $this->load->driver('cache', array('adapter' => 'redis'));
    }

    public function index()
    {
        // 1. Cek apakah Redis didukung oleh server
        if (!$this->cache->redis->is_supported()) {
            echo "Redis tidak didukung di server ini.";
            return;
        }

        // 2. Menyimpan data ke Redis (Key, Value, Waktu_Kadaluarsa_dalam_Detik)
        // Menyimpan string 'CodeIgniter 3 & Redis' selama 60 detik
        $this->cache->redis->save('nama_aplikasi', 'CodeIgniter 3 & Redis', 60);

        // Menyimpan data array (otomatis di-serialize oleh CI)
        $data_user = array('id' => 1, 'username' => 'budi', 'role' => 'admin');
        $this->cache->redis->save('user_1', $data_user, 300);
        
        echo $nama_olt = $this->config->item('olt_name', 'redis');
        echo "Data berhasil disimpan ke Redis!<br><br>";
        echo "<a href='" . site_url('testredis/tampil') . "'>Lihat Data</a>";
    }

    public function tampil()
    {
        // 3. Mengambil data dari Redis
        $aplikasi = $this->cache->redis->get('nama_aplikasi');
        $user      = $this->cache->redis->get('user_1');

        echo "<h3>Menampilkan Data dari Redis:</h3>";
        echo "Nama Aplikasi: " . ($aplikasi ? $aplikasi : "Data sudah kadaluarsa/dihapus") . "<br>";

        echo "Data User: ";
        if ($user) {
            print_r($user);
        } else {
            echo "Data sudah kadaluarsa/dihapus";
        }

        echo "<br><br><a href='" . site_url('testredis/hapus') . "'>Hapus Data</a>";
    }

    public function hapus()
    {
        // 4. Menghapus data spesifik dari Redis
        $this->cache->redis->delete('nama_aplikasi');
        $this->cache->redis->delete('user_1');

        // Jika ingin menghapus semua cache di Redis, gunakan:
        // $this->cache->redis->clean();

        echo "Data Redis berhasil dihapus!<br><br>";
        echo "<a href='" . site_url('testredis/tampil') . "'>Cek Data Lagi</a>";
    }

    public function onustate()
    {
        $this->load->model('api_rest_client_model','olt');

        echo json_encode($this->olt->gpon_onu_state());
    }
}
