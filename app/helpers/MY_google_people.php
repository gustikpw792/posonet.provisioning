<?php defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Makassar');

if ( ! function_exists('bulan'))
{
   /* NAMA BULAN INDONESIA
	 * Return nama bulan dalam bahasa Indonesia
	 * @var integer
	**/
	function bulan($int)
	{
		$CI =& get_instance();
		$bulan = array('',
  		'Januari','Februari','Maret',
			'April','Mei','Juni',
			'Juli','Agustus','September',
			'Oktober','November','Desember'
		);
		$index = (int) $int;
		return $bulan[$index];
   }
}

if ( ! function_exists('bulan_singkat'))
{
   /* NAMA BULAN INDONESIA (SINGKAT)
	 * Return nama bulan dalam bahasa Indonesia
	 * @var integer
	**/
	function bulan_singkat($int)
	{
		$CI =& get_instance();
		$bln = array("","Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");

		return $bln[$int];
   }
}

if ( ! function_exists('bulan_romawi'))
{
   /* KODE ROMAWI BULAN
	 * Return kode romawi bulan
	 * @var integer
	**/
	function bulan_romawi($int)
	{
		$CI =& get_instance();
		$bln = array("","I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");

		return $bln[$int];
   }
}

if ( ! function_exists('bulan_tahun'))
{
   /* NAMA BULAN TAHUN
	 * Return bulan tahun Ex. 'Januari 2018'
	 * @var String Ex. '2018-01'
	**/
	function bulan_tahun($thn_bln)
	{
		$CI =& get_instance();
		$bulan = array('',
  		'Januari','Februari','Maret',
			'April','Mei','Juni',
			'Juli','Agustus','September',
			'Oktober','November','Desember'
		);

		$bln = (int) substr($thn_bln,5,2);
		$thn = substr($thn_bln,0,4);

		return $bulan[$bln].' '.$thn;
  }
}

if ( ! function_exists('tgl_sekarang'))
{
  /* Tanggal NamaBulan Tahun
	 * Return bulan tahun Ex. '15 Januari 2018'
	 * @var null
	**/
	function tgl_sekarang()
	{
		$CI =& get_instance();
		$bulan = array('',
  		'Januari','Februari','Maret',
			'April','Mei','Juni',
			'Juli','Agustus','September',
			'Oktober','November','Desember'
		);

		$tgl = date('d');
		$bln = (int) date('m');
		$thn = date('Y');

		return $tgl.' '.$bulan[$bln].' '.$thn;
  }
}

if ( ! function_exists('tgl_lokal'))
{
  /* Tanggal NamaBulan Tahun (Sesuai request)
	 * Return bulan tahun Ex. '15 Januari 2018'
	 * @var String Ex. '2018-01-15'
	**/
	function tgl_lokal($thn_bln_tgl)
	{
		$CI =& get_instance();
		$bulan = array('',
  		'Januari','Februari','Maret',
			'April','Mei','Juni',
			'Juli','Agustus','September',
			'Oktober','November','Desember'
		);

		// '2018-01-15'
		// '0123456789'
		$tgl = substr($thn_bln_tgl,8,2);
		$bln = (int) substr($thn_bln_tgl,5,2);
		$thn = substr($thn_bln_tgl,0,4);

		return $tgl.' '.$bulan[$bln].' '.$thn;
  }
}
