<?php
defined('BASEPATH') or exit('No direct script access allowed');

require 'vendor/autoload.php';

use Telegram\Bot\Api as TGBot;
use Telegram\Bot\Keyboard\Keyboard;

class Bot extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		date_default_timezone_set('Asia/Makassar');

		$this->mybot = $this->config->item('telegram_bot');

        $this->telegram = new TGBot($this->mybot['TOKENTEST']);
	}

	public function index(){
        $token = $this->mybot['TOKENTEST'];
        $apiLink = $this->mybot['BASE_URL']."bot$token/";
            
        echo "<h1>Dilarang masuk</>\n$apiLink";

    }

    public function sendKeyboard($chat_id, $text=null){
        
        $reply_markup2 = Keyboard::make()
                ->setResizeKeyboard(true)
                ->setOneTimeKeyboard(true)
                ->row(
                    Keyboard::button(['text' => 'LOS']), 
                    Keyboard::button(['text' => 'UNCFG']), 
                    Keyboard::button(['text' => 'EXPIRED']),)
                ->row(
                    Keyboard::button(['text' => 'OFFLINE']), 
                    Keyboard::button(['text' => 'btn5']),
                    Keyboard::button(['text' => 'btn6']),);

        $response = $this->telegram->sendMessage([
            'chat_id' => $chat_id,
            'text' => 'Hai, Pilih informasi yang kamu inginkan:',
            'reply_markup' => $reply_markup2
        ]);

        $messageId = $response->getMessageId();
    }

	public function myhook($token)
	{
        $content = file_get_contents("php://input");

        // untuk validasi bahwa hanya yang mempunyai token ini boleh akses url ini
        $localToken = "VUIHA0LG8DsQGKZgnvCWWrqSL09G";

        if ($token != $localToken) {
            // http://localhost/posonet/bot/VUIHA0LG8DsQGKZgnvCWWrqSL09G/hooks
            echo json_encode(['text' => "Token Salah!", 'status' => 401]);
            exit();
        }

        if ($content) {
            $token = $this->mybot['TOKENTEST'];
            $apiLink = $this->mybot['BASE_URL']."bot$token/";         

            /**
             * Set Webhook
             * https://api.telegram.org/bot7497901283:AAHWWrqS-L09GKZgnvCCNHkVUIHA0LG8DsQ/setwebhook?url=https://0687-36-74-112-201.ngrok-free.app/posonet/bot/VUIHA0LG8DsQGKZgnvCWWrqSL09G/hooks
             * 
             * Delete Webhook
             * https://api.telegram.org/bot7497901283:AAHWWrqS-L09GKZgnvCCNHkVUIHA0LG8DsQ/deletewebhook
             * 
             * 
             */

            $update = json_decode($content, true);

            $chat_id = $update['message']['chat']['id'];
            $text = $update['message']['text'];
            $chatName = $update['message']['chat']['first_name'].$update['message']['chat']['username'];

            if ($text == "/status") {
                $reply = urlencode("Name: Sopo\nWilayah: Pendolo\nIP:172.0.100.23\nLaser:-19.78\nDurasi Online: 00:34:23\nPaket: 10M-IPTV");
            } 
            if ($text == "/menu") {
                $this->sendKeyboard($chat_id,'');
            } 
            if ($text == "LOS") {
                $reply="Hai $chatName, Berikut nama ONT LOS:";
            } 
            // else{
            //     $reply="Hai $chatName, Kamu ketik ".$text;
            // }

            file_get_contents($apiLink."sendmessage?chat_id=$chat_id&text=$reply");
        } else {
            
            echo "<h1>Dilarang masuk</>";
        }
	}

}