<?php

namespace App\Telegram\Response;

use Telegram\Bot\Laravel\Facades\Telegram;

class Baru
{
    public function running($chat_id, $nama_user, $parameters = []) : bool
    {
        $response = $this->parsingMessage($nama_user);

        Telegram::sendMessage([
            'chat_id' => $chat_id,
            'parse_mode' => 'HTML',
            'text' => $response
            // 'reply_to_message_id' => $message_id
        ]);

        return true;
    }

    protected function parsingMessage($nama_user)
    {
        $text = "Selamat Datang $nama_user di Group PPC\n"
                ."Untuk mengikuti group silahkan perkenalkan diri terlebih dahulu\n";
        return $text;
    }
}