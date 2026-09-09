<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cache_model extends CI_Model
{

    private $olt_name;
    private $cache_key;
    private $cache_ttl = 900; // Default 15 menit (dalam detik)

    public function __construct()
    {
        parent::__construct();
        // 1. Memuat driver cache Redis bawaan CI
        $this->load->driver('cache', array('adapter' => 'redis'));

        // 2. Memuat file konfigurasi redis.php
        $this->config->load('redis', TRUE);

        // 3. Ambil nama OLT dan susun Key Redis secara dinamis
        $this->olt_name  = $this->config->item('olt_name','redis') ? $this->config->item('olt_name','redis') : 'DEFAULT_OLT';
        $this->cache_key = 'olt_cache_' . $this->olt_name;
    }

    /**
     * Mengambil data ter-parsing dari cache Redis
     */
    public function get_cached_data()
    {
        // Mengembalikan array jika ada, atau FALSE jika kosong/expired
        return $this->cache->redis->get($this->cache_key);
    }

    /**
     * Menyimpan data array hasil parsing ke Redis
     * @param array $data Data terstruktur yang ingin dicache
     * @param int $custom_ttl Jika ingin mengubah waktu expired secara dinamis (opsional)
     */
    public function save_to_cache($data, $custom_ttl = NULL)
    {
        $custom_ttl = $this->config->item('cache_ttl', 'redis');
        
        $ttl = ($custom_ttl !== NULL) ? $custom_ttl : $this->cache->ttl;
        return $this->cache->redis->save($this->cache_key, $data, $ttl);
    }

    /**
     * Menghapus cache OLT yang aktif saat ini secara manual (Force Refresh)
     */
    public function clear_cache()
    {
        return $this->cache->redis->delete($this->cache_key);
    }

    /**
     * Mengambil nama OLT yang saat ini sedang aktif di config
     */
    public function get_current_olt_name()
    {
        return $this->olt_name;
    }
}
