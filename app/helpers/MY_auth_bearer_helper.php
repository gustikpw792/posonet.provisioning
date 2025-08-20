<?php defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('auth_bearer'))
{
	/**
	 * Check if the request has a valid Bearer token.
	 *
	 * @return bool
	 */
	function get_auth_bearer() {
	  $CI =& get_instance();
	  $headers = $CI->input->request_headers();
	  $key_token = $CI->config->item('public_api')['token'];

	  
	  if (isset($headers['Authorization'])) {
	    $token = str_replace('Bearer ', '', $headers['Authorization']);
	    // Here you would typically validate the token, e.g., check against a database or an external service.
		if ($token === $key_token) {
			return true;
		} else {
			$CI->output
				->set_status_header(403)
				->set_content_type('application/json')
				->set_output(json_encode(['error' => 'Invalid authorization token']));
			exit();
			// return false;
		}
	  } else {
		$CI->output
			->set_status_header(401)
			->set_content_type('application/json')
			->set_output(json_encode(['error' => 'Authorization header not found']));
		exit();
		// return false;
	  }
	}
}
