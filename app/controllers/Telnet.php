<?php
defined('BASEPATH') or exit('No direct script access allowed');

require 'vendor/autoload.php';
// use Graze\TelnetClient\TelnetClient;
use Graze\TelnetClient\TelnetResponse;

class Telnet extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$telnet = Graze\TelnetClient\TelnetClient::factory();
		$telnet->setLineEnding(null);
		$telnet->connect('192.168.220.22:23');
		$telnet->execute('zte', 'Username:', '%Error 20209: No username or bad password');
		sleep(1);
		$telnet->execute('zte', 'Password:', '%Error 20209: No username or bad password');

		// $resp = new TelnetResponse();
		// echo $resp($telnet);
		// print_r($telnet->execute('zte\n', 'Username:', '%Error 20209: No username or bad password'));
		// var_dump($telnet->execute('zte', 'Password:'));
	}

	// public function tes()
	// {
	// 	$isError = true;
	// 	$responseText = 'this is text';
	// 	$promptMatches = [1, 'two'];

	// 	$response = new TelnetResponse($isError, $responseText, $promptMatches);

	// 	$this->assertEquals($isError, $response->isError());
	// 	$this->assertEquals($responseText, $response->getResponseText());
	// 	$this->assertEquals($promptMatches, $response->getPromptMatches());
	// }
}
