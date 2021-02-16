<?php

namespace App\Telegram\Response;

use Telegram\Bot\Laravel\Facades\Telegram;
use App\Repositories\Bot\Misi as MisiEvent;

class Misi
{
    protected $misi;

    public function __construct()
    {
        $this->misi = new MisiEvent;
    }

    public function running($chat_id, $message_id, $parameters = []) : bool
    {
        if(count($parameters) < 2) {
                return false;
        }

        if ($parameters[1] === "kosong" || $parameters[1] === "proses" || $parameters[1] === "sukses"  || $parameters[1] === "gagal") {
            $text = $this->parsingMessage($this->misi->getStatusMisi($parameters[1]), $parameters[1]);
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
        // $status = $status === "sukses" ? "Sukses" : "Pending";
        $text = "<b>Event GowesVirtual</b> by GowesBareng.id\n"
                ."Misi dengan status <b>$status</b> \n"
                ."Berjumlah $param Peserta\n";
        return $text;
    }

    protected function parsingErrorMessage()
    {
        $text = "<b>Event GowesVirtual</b> by GowesBareng.id\n"
                ."parameter yang di pakai salah\n"
                ."Exp (!misi `suskes` or !misi `proses`)";
        return $text;
    }
}