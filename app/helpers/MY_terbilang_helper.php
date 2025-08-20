<?php defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('terbilang'))
{
	function terbilang($satuan) {
	  $huruf = array('','Satu','Dua','Tiga','Empat','Lima','Enam','Tujuh','Delapan','Sembilan','Sepuluh','Sebelas' );
	  if ($satuan < 12) {
	    return ' '.$huruf[$satuan];
	  }
	  elseif ($satuan < 20) {
	    return ' '.$huruf[$satuan-10].'Belas ';
	  }
	  elseif ($satuan < 100) {
	    return ''.$huruf[$satuan/10].' Puluh '.$huruf[$satuan%10];
	  }
	  elseif ($satuan < 200) {
	    return ' Seratus'. $this->terbilang($satuan-100);
	  }
	  elseif ($satuan < 1000) {
	    return $this->terbilang($satuan/100).'Ratus '.$this->terbilang($satuan % 100).' ';
	  }
	  elseif ($satuan < 2000) {
	    return ' Seribu'. $this->terbilang($satuan-1000);
	  }
	  elseif ($satuan < 1000000) {
	    return $this->terbilang($satuan/1000).' Ribu'.$this->terbilang($satuan%1000);
	  }
	  elseif ($satuan < 1000000000) {
	    return $this->terbilang($satuan/1000000).'Juta '.$this->terbilang($satuan % 1000000);
	  }
	  elseif ($satuan <= 1000000000) {
	    return 'Maaf, tidak dapat diproses karena jumlah uang terlalu besar';
	  }
	}

}
