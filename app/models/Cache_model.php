<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cache_model extends CI_Model
{

    private $olt_name;
    private $cache_key_onustate;
    private $cache_key_onutype;
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
        $this->cache_key_onustate = 'onustate_' . $this->olt_name;
        $this->cache_key_onutype = 'onutype_' . $this->olt_name;
        $this->cache_ttl = $this->config->item('cache_ttl', 'redis');
    }

    /**
     * Mengambil data ter-parsing dari cache Redis
     */
    public function get_cached_data_onustate()
    {
        // Mengembalikan array jika ada, atau FALSE jika kosong/expired
        return $this->cache->redis->get($this->cache_key_onustate);
    }

    public function get_cached_data_onutype()
    {
        // Mengembalikan array jika ada, atau FALSE jika kosong/expired
        return $this->cache->redis->get($this->cache_key_onutype);
    }

    /**
     * Menyimpan data array hasil parsing ke Redis
     * @param array $data Data terstruktur yang ingin dicache
     * @param int $custom_ttl Jika ingin mengubah waktu expired secara dinamis (opsional)
     */
    public function save_to_cache_onustate($data, $custom_ttl = NULL)
    {
        // $custom_ttl_cfg = $this->config->item('cache_ttl', 'redis');

        $ttl = ($custom_ttl !== NULL) ? $custom_ttl : $this->cache_ttl;
        return $this->cache->redis->save($this->cache_key_onustate, $data, $ttl);
    }

    public function save_to_cache_onutype($data, $custom_ttl = NULL)
    {
        // $custom_ttl = $this->config->item('cache_ttl', 'redis');

        $ttl = ($custom_ttl !== NULL) ? $custom_ttl : $this->cache_ttl;
        return $this->cache->redis->save($this->cache_key_onutype, $data, $ttl);
    }

    /**
     * Menghapus cache OLT yang aktif saat ini secara manual (Force Refresh)
     */
    public function clear_cache()
    {
        $this->cache->redis->delete($this->cache_key_onustate);
        return $this->cache->redis->delete($this->cache_key_onutype);
    }

    /**
     * Mengambil nama OLT yang saat ini sedang aktif di config
     */
    public function get_current_olt_name()
    {
        return $this->olt_name;
    }
}
