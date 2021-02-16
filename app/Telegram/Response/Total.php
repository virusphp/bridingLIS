<?php

namespace App\Telegram\Response;

use Telegram\Bot\Laravel\Facades\Telegram;
use App\Repositories\Bot\Transaksi;

class Total
{
    protected $transaksi;

    public function __construct()
    {
        $this->transaksi = new Transaksi;
    }

    public function running($chat_id, $message_id, $parameters = []) : bool
    {  
        if(count($parameters) < 2) {
            return false;
        }

        if ($parameters[1] === "sukses" || $parameters[1] === "pending") {
            $text = $this->parsingMessage($this->transaksi->getTotalTransaksi($parameters[1]), $parameters[1]);
            // $text = $this->transaksi->getTotalTransaksi($parameters[1]);
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
                ."dengan Total ".rupiah($param)." Rupiah\n";
        return $text;
    }

    protected function parsingErrorMessage()
    {
        $text = "<b>Event GowesVirtual</b> by GowesBareng.id\n"
                ."parameter yang di pakai salah\n"
                ."Exp (!total `sukses` or !total `pending`)";
        return $text;
    }
}