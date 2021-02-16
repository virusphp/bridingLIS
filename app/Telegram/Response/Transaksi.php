<?php

namespace App\Telegram\Response;

use Telegram\Bot\Laravel\Facades\Telegram;
use Exception;
use App\Repositories\Bot\Transaksi as TransaksiEvent;

class Transaksi
{
    protected $transaksi;

    public function __construct()
    {
        $this->transaksi = new TransaksiEvent;
    }

    public function running($chat_id, $message_id, $parameters = []) : bool
    {   
        if(count($parameters) < 2) {
            return false;
        }

        if ($parameters[1] === "sukses" || $parameters[1] === "pending") {
            $text = $this->parsingMessage($this->transaksi->getStatusTransaksi($parameters[1]), $parameters[1]);

        } else {
            $text = $this->parsingErrorMessage();
        }

    
        Telegram::sendMessage([
            'chat_id' => $chat_id,
            'parse_mode' => 'HTML',
            'text' => $text,
            'reply_to_message_id' => $message_id
        ]);
      

        return true;

    }

    protected function parsingMessage($param, $status)
    {
        $status = $status === "sukses" ? "Sukses" : "Pending";
        $text = "<b>Event GowesVirtual</b> by GowesBareng.id\n"
                ."Transaksi dengan status <b>$status</b> \n"
                ."Berjumlah $param Peserta\n";
        return $text;
    }

    protected function parsingErrorMessage()
    {
        $text = "<b>Event GowesVirtual</b> by GowesBareng.id\n"
                ."parameter yang di pakai salah\n"
                ."Exp (!transaksi `sukses` or !transaksi `pending`)";
        return $text;
    }
}