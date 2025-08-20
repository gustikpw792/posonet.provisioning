<?php defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('ribuan'))
{
	function ribuan($harga) // untuk menambahkan titik pada angka ribuan ex. 30.000
  {
		$CI =& get_instance();
    $rp = $harga;
    $lenght = strlen($rp);
    $ribu =  substr($rp,-3);
    if ($lenght==4) {
      $puluh =  substr($rp,0,1);
      return $puluh.'.'.$ribu;
    } elseif ($lenght==5) {
      $puluh =  substr($rp,0,2);
      return $puluh.'.'.$ribu;
    } elseif ($lenght==6) {
      $puluh =  substr($rp,0,3);
      return $puluh.'.'.$ribu;
    } elseif ($lenght==7) {
      $satuan =  substr($rp,0,1);
      $puluh =  substr($rp,1,3);
      return $satuan.'.'.$puluh.'.'.$ribu;
    } elseif ($lenght==8) {
      $satuan =  substr($rp,0,2);
      $puluh =  substr($rp,2,3);
      return $satuan.'.'.$puluh.'.'.$ribu;
    } elseif ($lenght==9) {
      $satuan =  substr($rp,0,3);
      $puluh =  substr($rp,3,3);
      return $satuan.'.'.$puluh.'.'.$ribu;
    } else {
      return $rp;
    }
  }
}
