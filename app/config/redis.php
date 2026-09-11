<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Konfigurasi Redis
$config['socket_type'] = 'tcp'; // tcp atau unix
$config['host']        = '127.0.0.1'; // Alamat server Redis Anda
$config['password']    = NULL; // Isi jika Redis Anda menggunakan password
$config['port']        = 6379; // Port standar Redis
$config['timeout']     = 0;
$config['olt_name']    = 'oltc320_pdl';
$config['cache_ttl']   = 900; // dalam detik
