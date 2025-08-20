<?php defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('is_logged_in'))
{
	function is_logged_in() {
	  $CI =& get_instance();
		return $CI->session->userdata('ses_admin');
	}

}
