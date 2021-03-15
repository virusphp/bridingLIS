<?php

namespace App\Repository\Api;
use Telegram\Bot\Laravel\Facades\Telegram;
use DB;

class ApiRepository
{
    protected $pathFile = "storage/lis";

    protected function sendMessage($params, $status)
    {
        $text = $this->parsingMessage($params, $status);
        dd($text, config('telegram.bots.mybot.group_id'));

        Telegram::sendMessage([
            'chat_id' => config('telegram.bots.mybot.group_id'),
            'parse_mode' => 'HTML',
            'text' => $text
        ]);
    }
  
    protected function parsingMessage($params, $status)
    {
        $text = "Data Lab :\n"
                ."🔖 : $params->no_reg\n"
                ."💳‍ : $params->no_rm\n"
                ."🙍🏻‍♂️ : $params->nama_pasien\n"
                ."🧾 : $params->no_lab\n"
                ."📰 : ".asset($this->pathFile. "/". $params->file_hasil)."\n"
                ."📆 : ".tanggal($params->tgl_pemeriksaan)."\n"
                ."⌚️ : ".formatJam($params->tgl_pemeriksaan)."\n"
                ."🏥 : ".jenisRawat($params->no_reg) ."\n"
                ."User $status : $params->nama_pegawai\n"
                ."Data berhasil Di uploads";
        return $text;
    }

}