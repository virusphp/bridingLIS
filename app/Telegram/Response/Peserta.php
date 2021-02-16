<?php

namespace App\Telegram\Response;

use Telegram\Bot\Laravel\Facades\Telegram;
use App\Repositories\Bot\Peserta as PesertaEvent;

class Peserta
{
    protected $peserta;

    public function __construct()
    {
        $this->peserta = new PesertaEvent;
    }

    public function running($chat_id, $message_id, $parameters = []) : bool
    { 
        if(count($parameters) < 2) {
            return false;
        }

        if ($parameters[1] === "v" || $parameters[1] === "n") {
            $text = $this->parsingMessage($this->peserta->getStatusPeserta($parameters[1]), $parameters[1]);
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
        $status = $status === "v" ? "Verified" : "Belum Verifikasi";
        $text = "<b>Event GowesVirtual</b> by GowesBareng.id\n"
                ."Peserta dengan status <b>$status</b> \n"
                ."Berjumlah $param Peserta\n";
        return $text;
    }

    protected function parsingErrorMessage()
    {
        $text = "<b>Event GowesVirtual</b> by GowesBareng.id\n"
                ."parameter yang di pakai salah\n"
                ."Exp (!peserta `v` or !peserta `n`)";
        return $text;
    }
}