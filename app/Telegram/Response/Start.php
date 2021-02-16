<?php

namespace App\Telegram\Response;

use Telegram\Bot\Laravel\Facades\Telegram;

class Start
{
    public function running($chat_id, $message_id, $parameters = []) : bool
    {
        $response = $this->parsingMessage();

        Telegram::sendMessage([
            'chat_id' => $chat_id,
            'parse_mode' => 'HTML',
            'text' => $response,
            'reply_to_message_id' => $message_id
        ]);

        return true;
    }

    protected function parsingMessage()
    {
        $text = "Selamat Datang di Group PPC\n"
                ."Untuk Perintah Bantuan tekan <b>!Help</b>\n";
        return $text;
    }
}