<?php

namespace App\Http\Controllers\Bot;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Telegram\Response;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramBotController extends Controller
{

    public function updateActivity()
    {
        $activities = Telegram::deleteWebhook();
        $activities = Telegram::getUpdates();
        dd($activities);
    }

    public function webhook()
    {
        $update = Telegram::setWebhook([
            'url' => str_replace('http', 'https', url(route('webhook', config('telegram.bots.mybot.token'))))
        ]);
        // $update = Telegram::setWebhook([
        //     'url' => url(route('webhook', config('telegram.bots.mybot.token')))
        // ]);
        dd($update);
    }

    public function index()
    {
        $response = json_decode(file_get_contents("php://input"), true);
        // dd($response);
        $response = new Response($response);
        // dd(response);
        $message = $response->chat;

        if ($message["msg_type"] !== "unknown") {

            $data = $response->data["message"];

            $text = $message["text"];
            $message_id = $data["message_id"];
            $chat_id = $data["chat"]["id"];
            $responseRoute = explode(' ', $text); 

            if ("!start" === $responseRoute[0]){
                if($response->running("Start", $chat_id, $message_id, $responseRoute)) {
                    return;
                }            
            }

            if ("!help" === $responseRoute[0]){
                if($response->running("Help", $chat_id, $message_id, $responseRoute)) {
                    return;
                }            
            }

            if ("!peserta" === $responseRoute[0]){
                if($response->running("Peserta", $chat_id, $message_id, $responseRoute)) {
                    return;
                }            
            }

            if ("!transaksi" === $responseRoute[0]) {
                if($response->running("Transaksi", $chat_id, $message_id, $responseRoute)) {
                    return;
                }            
            }

            if ("!misi" === $responseRoute[0]) {
                if($response->running("Misi", $chat_id, $message_id, $responseRoute)) {
                    return;
                }            
            }

            if ("!total" === $responseRoute[0]) {
                if($response->running("Total", $chat_id, $message_id, $responseRoute)) {
                    return;
                }            
            }
            // User new 
            if ("new_chat_members" === $responseRoute[0]) {
                $nama_user = $data["new_chat_participant"]["first_name"];
                if($response->running("Baru", $chat_id, $nama_user, $responseRoute)) {
                    return;
                }            

            }
        }
        return;
    }
}
