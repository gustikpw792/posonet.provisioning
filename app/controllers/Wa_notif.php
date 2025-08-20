<?php defined('BASEPATH') or exit('No direct script access allowed');
class Wa_notif extends CI_Controller
{


	function __construct()
	{
		parent::__construct();
		if (!is_logged_in()) {
			redirect('login?_rdr=' . urlencode(current_url()));
		}
		$this->load->model('pelanggan_model', 'pelanggan');
		$this->load->model('wa_notif_model', 'wa_notif');
		$this->load->helper(array('MY_ribuan', 'MY_bulan'));
		$this->wa = $this->config->item('kirimwa_id');
	}

	public function index()
	{
		set_status_header(401);
	}

	public function tes()
	{
		$db = $this->wa_notif->get_pelanggan();
		$data = array();
		foreach ($db as $d) {
			$r = array();
			$r[] = "Pelanggan Yth,\nTagihan POSONET Anda nomor $d->no_pelanggan a/n *$d->nama_pelanggan*, bulan tagihan Maret masa aktif s/d 20-04-2022 sebesar *Rp. " . ribuan($d->total) . "* batas akhir pembayaran pada 20-03-2022.\n\nPembayaran dapat dilakukan melalui ATM, Mobile Banking, Internet Banking dan SMS Banking.\n\nUntuk pembayaran melewati tgl 20, silahkan konfirmasi dengan menyertakan bukti pembayaran.\n\n*Abaikan informasi ini jika sdh melakukan pembayaran.*\nTerima kasih";
			$data[] = $r;
		}
		echo json_encode($data);
	}

	public function apiKirimWaRequest(array $params)
	{
		$httpStreamOptions = [
			'method' => $params['method'] ?? 'GET',
			'header' => [
				'Content-Type: application/json',
				'Authorization: Bearer ' . ($params['token'] ?? '')
			],
			'timeout' => 15,
			'ignore_errors' => true
		];

		if ($httpStreamOptions['method'] === 'POST') {
			$httpStreamOptions['header'][] = sprintf('Content-Length: %d', strlen($params['payload'] ?? ''));
			$httpStreamOptions['content'] = $params['payload'];
		}

		// Join the headers using CRLF
		$httpStreamOptions['header'] = implode("\r\n", $httpStreamOptions['header']) . "\r\n";

		$stream = stream_context_create(['http' => $httpStreamOptions]);
		$response = file_get_contents($params['url'], false, $stream);

		// Headers response are created magically and injected into
		// variable named $http_response_header
		$httpStatus = $http_response_header[0];

		preg_match('#HTTP/[\d\.]+\s(\d{3})#i', $httpStatus, $matches);

		if (!isset($matches[1])) {
			throw new Exception('Can not fetch HTTP response header.');
		}

		$statusCode = (int)$matches[1];
		if ($statusCode >= 200 && $statusCode < 300) {
			return ['body' => $response, 'statusCode' => $statusCode, 'headers' => $http_response_header];
		}

		throw new Exception($response, $statusCode);
	}

	public function post_device()
	{
		try {
			$reqParams = [
				'token' => $this->wa['API_TOKEN'],
				'url' => 'https://api.kirimwa.id/v1/devices',
				'method' => 'POST',
				'payload' => json_encode([
					'device_id' => $this->wa['DEVICE_ID']
				])
			];

			$response = $this->apiKirimWaRequest($reqParams);
			echo $response['body'];
		} catch (Exception $e) {
			print_r($e);
		}
	}

	public function get_device()
	{
		try {
			$reqParams = [
				'token' => $this->wa['API_TOKEN'],
				'url' => 'https://api.kirimwa.id/v1/devices'
			];

			$response = $this->apiKirimWaRequest($reqParams);
			echo $response['body'];
		} catch (Exception $e) {
			print_r($e);
		}
	}

	public function get_device_byid()
	{
		try {
			$reqParams = [
				'token' => $this->wa['API_TOKEN'],
				'url' => sprint('https://api.kirimwa.id/v1/devices/%s', $this->wa['DEVICE_ID'])
			];

			$response = $this->apiKirimWaRequest($reqParams);
			echo $response['body'];
		} catch (Exception $e) {
			print_r($e);
		}
	}

	public function delete_device()
	{
		try {
			$reqParams = [
				'token' => $this->wa['API_TOKEN'],
				'url' => sprintf('https://api.kirimwa.id/v1/devices/%s', $this->wa['DEVICE_ID']),
				'method' => 'DELETE'
			];

			$response = $this->apiKirimWaRequest($reqParams);
			echo $response['body'];
		} catch (Exception $e) {
			print_r($e);
		}
	}

	public function get_qr()
	{
		try {
			$query = http_build_query(['device_id' => $this->wa['DEVICE_ID']]);
			$reqParams = [
				'token' => $this->wa['API_TOKEN'],
				'url' => sprintf('https://api.kirimwa.id/v1/qr?%s', $query)
			];

			$response = $this->apiKirimWaRequest($reqParams);
			echo $response['body'];
		} catch (Exception $e) {
			print_r($e);
		}
	}

	public function post_messages()
	{
		try {
			$reqParams = [
				'token' => $this->wa['API_TOKEN'],
				'url' => 'https://api.kirimwa.id/v1/messages',
				'method' => 'POST',
				'payload' => json_encode([
					'message' => 'Halo ini adalah pesan dari api.kirimwa.id.',
					'phone_number' => '6281340310250',
					'message_type' => 'text',
					'device_id' => $this->wa['DEVICE_ID']
				])
			];

			$response = $this->apiKirimWaRequest($reqParams);
			echo $response['body'];
		} catch (Exception $e) {
			print_r($e);
		}
	}

	public function batch_messages()
	{
		try {
			$reqParams = [
				'token' => $this->wa['API_TOKEN'],
				'url' => 'https://api.kirimwa.id/v1/batch-messages',
				'method' => 'POST',
				'payload' => json_encode([
					'messages' => [
						[
							// Scheduled image message
							'message' => 'https://rioastamal.net/portfolio/img/rioastamal.jpg?build=202001140325',
							'caption' => 'Hallo',
							'phone_number' => '6285298470228',
							'send_at' => '2022-02-25T00:06:25+08:00'
						],
						[
							// Scheduled image message
							'message' => 'https://rioastamal.net/portfolio/img/rioastamal.jpg?build=202001140325',
							'caption' => 'Hallos',
							'phone_number' => '6281340310250',
							'send_at' => '2022-02-25T00:06:25+08:00'
						]
					],
					'device_id' => $this->wa['DEVICE_ID']
				])
			];

			$response = $this->apiKirimWaRequest($reqParams);
			echo $response['body'];
		} catch (Exception $e) {
			print_r($e);
		}
	}

	public function coba()
	{
		$d = strtotime("+30 Seconds");
		echo date("c", $d) . "<br>";

		// echo date("Y-m-d H:i:s");

		// $date = date_create_from_format(date("c"));
		// // $date = date_create("2013-03-15");
		// date_add($date, date_interval_create_from_date_string("40 seconds"));
		// echo date_format($date, "c");
	}
}
