<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require 'vendor/autoload.php';

use Telegram\Bot\Api as Bot;

class Api_telegrambot_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
		$this->load->helper(array('MY_ribuan', 'MY_bulan'));
        
        // START telegram api configuration
        $this->tgrow = array();
        $this->tgbot = $this->db->query("SELECT * FROM settings where option_name LIKE 'tg_%' OR option_name LIKE 'bri_%'")->result();
        foreach ($this->tgbot as $key) {
			$this->tgrow["$key->option_name"] = $key->option_value;
		}
        // END load telegram api configuration


        // $this->bot = $this->config->item('telegram_bot');

        // $this->url = $this->bot['BASE_URL']. $this->bot['TOKEN'];
        

        // $this->telegram = new Bot($this->bot['TOKEN']);
        $this->telegram = new Bot($this->tgrow['tg_token_bot']);

    }

    public function getTgSettings(){
        return $this->tgrow;
    }

    public function getUpdates(){
        $response = $this->telegram->getUpdate();

        // $botId = $response->getId();
        // $firstName = $response->getFirstName();
        // $username = $response->getUsername();

        return $response;
    }


    public function sendMessages(){
        $keyboard = [
            ['7', '8', '9'],
            ['4', '5', '6'],
            ['1', '2', '3'],
                ['0']
        ];

        $reply_markup = $this->telegram->replyKeyboardMarkup([
            'keyboard' => $keyboard, 
            'resize_keyboard' => true, 
            'one_time_keyboard' => true
        ]);

        $response = $this->telegram->sendMessage([
            // 'chat_id' => $this->bot['CHAT_ID_ADMIN'], 
            'chat_id' => $this->tgrow['tg_chat_id_admin'], 
            'text' => 'Hello World', 
            'reply_markup' => $reply_markup
        ]);

        $messageId = $response->getMessageId();
    }
    
    public function sendMessage(){

        $message = "\xF0\x9F\x9A\xA8 *LOS*\nName : %s\nLocation : %s\n\nHP : %s\nONT Phase : LOS/DyingGasp";
        $dt = sprintf($message,'Agus', 'https://maps.app.goo.gl/bYdqxJmzGSJzz12h6', '085320435480');
        $data = [
            'chat_id'       => $this->tgrow['tg_chat_id_group'],
            'text'          => $dt,
            'parse_mode'    => 'markdown'
        ];

        return $this->telegram->sendMessage($data);
    }


    public function sendToGroup($ticket){
        $data = [
            'chat_id'       => $this->tgrow['tg_chat_id_group'],
            'text'          => $ticket,
            'parse_mode'    => 'markdown'
        ];

        return $this->telegram->sendMessage($data);
    }

    public function sendToAdmin($msg){
        try {
            $data = [
                'chat_id'       => $this->tgrow['tg_chat_id_admin'],
                'text'          => $msg,
                'parse_mode'    => 'markdown'
            ];
    
            return $this->telegram->sendMessage($data);
        } catch (\Exception $th) {
            return $th;
        }
    }

    public function sendNewClientToAdmin($data){

        $tmp = "*Terima kasih telah berlangganan POSONET*.\n
Berikut data registrasi Anda.

Nomor Pelanggan : *%s*
Nama Pelanggan : *%s*
HP/WA : *%s*
Tgl Instalasi : %s
Paket Aktif : %s
Harga Paket : %s/bulan
Masa aktif berakhir : %s

Pembayaran berikutnya jika melalui transfer
Bank BRI:
An. %s:
Rek: %s

Jumlah Transfer = %s
(Transfer pas hingga digit terakhir)

input by %s";

        $query = "SELECT nama_paket,tarif FROM paket WHERE id_paket=".$data['id_paket'];
        $paket = $this->db->query($query)->row();

        $msg = sprintf($tmp, 
            $data['no_pelanggan'], 
            $data['nama_pelanggan'], 
            $data['telp'],
            $data['tgl_instalasi'],
            $paket->nama_paket,
            ribuan($paket->tarif),
            tgl_lokal($data['expired']),
            $this->tgrow['bri_nama_pemilik_rekening'],
            $this->tgrow['bri_no_rekening'],
            ribuan($paket->tarif + $data['no_pelanggan']),
            $data['input_by'],
        );

        $contactName = "WIFI %s. %s";
        $firstName = sprintf($contactName, $data['no_pelanggan'], $data['nama_pelanggan']);
        $lastName = $paket->nama_paket;

        try {
            $dataPesan = [
                'chat_id'       => $this->tgrow['tg_chat_id_admin'],
                'text'          => $msg,
                'parse_mode'    => 'markdown'
            ];

            $dataKontak = [
                'chat_id'       => $this->tgrow['tg_chat_id_admin'],
                'phone_number'  => $this->formatNomor($data['telp']),
                'first_name'    => $firstName,
                'last_name'     => $lastName
            ];
            $feedback = $this->telegram->sendMessage($dataPesan);
            $this->telegram->sendContact($dataKontak);

            return $feedback;
        } catch (\Exception $e) {
            return $e;
        }


    }

    public function templateMessages($mode){
        $extend = "\xF0\x9F\x95\x93 *Extend Paket*\nName : {}\nExpired at: {}\nProfile : {}";
        $los = "\xF0\x9F\x94\xB4 \xF0\x9F\x86\x98 *LOS*\nName : {}\nLocation : {}\nHP : {}";
    }

    public function getUp(){
        $telegram = new Bot('5657520282:AAEM8VglypDXYgx6FN5wkijmgl7zVfbpbnM');
        $response = $telegram->getMe();
        return $response;
    }

    public function formatNomor($phoneNumber){
        // Check if the phone number starts with '0'
        if (substr($phoneNumber, 0, 1) === '0') {
            // Replace the leading '0' with '+62'
            $formattedPhoneNumber = '+62' . substr($phoneNumber, 1);
        } else {
            // If it does not start with '0', leave it unchanged
            $formattedPhoneNumber = $phoneNumber;
        }
        return $formattedPhoneNumber;
    }

    public function sendKontak(){

        try {
            $data = [
                'chat_id'       => $this->tgrow['tg_chat_id_admin'],
                'phone_number'  => $this->formatNomor('085326612643'),
                'first_name'    => 'WIFI 10M 304. EVI TORUNDE'
            ];
            return $this->telegram->sendContact($data);
        } catch (\Exception $e) {
            return $e;
        }
    }
    

}