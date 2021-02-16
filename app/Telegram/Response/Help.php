<?php

namespace App\Telegram\Response;

use Telegram\Bot\Laravel\Facades\Telegram;

class Help
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
        $text = "Selamat Datang di Group Internal\n"
                ."Untuk Perintah Bantuan tekan !Help \n"
                ."Perintah yang tersedia adalah: \n\n"
                ."<b>!peserta [v, n]</b> Untuk cek status peserta\n"
                ."<b>!transaksi [sukses, pending]</b> Untuk cek status transaksi\n"
                ."<b>!total [sukses, pending]</b> Untuk cek total transaksi\n"
                ."<b>!misi [kosong, proses, sukses, gagal]</b> Untuk cek status mission\n"
                ."Aplikasi dan System di buat \noleh @ferifahrul13, @dimas, @setsuga\n";
        return $text;
    }
}