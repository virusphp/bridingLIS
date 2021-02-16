<?php

namespace App\Telegram;

final class Response
{
    public $data;
    public $chat;

    public function __construct(array $data)
    {
        $this->data = $data;

        if (isset($data["update_id"], $data["message"])) {
            $msg = $data["message"];

            if (isset($msg["text"])) {
                $this->chat["msg_type"] = "text";
                $this->chat["text"] = $msg["text"];
                $this->chat["text_entities"] = $msg["entities"] ?? null;
              } else
              if (isset($msg["photo"])) {
                $this->chat["msg_type"] = "photo";
                $this->chat["text"] = $msg["caption"] ?? null;
                $this->chat["text_entities"] = $msg["caption_entities"] ?? null;
              } else
              if (isset($msg["sticker"])) {
                $this->chat["msg_type"] = "sticker";
                $this->chat["text"] = $msg["sticker"]["emoji"] ?? null;
              } else
              if (isset($msg["animation"])) {
                $this->chat["msg_type"] = "animation";
                $this->chat["text"] = $msg["caption"] ?? null;
                $this->chat["text_entities"] = $msg["caption_entities"] ?? null;
              } else
              if (isset($msg["voice"])) {
                $this->chat["msg_type"] = "voice";
                $this->chat["text"] = $msg["caption"] ?? null;
                $this->chat["text_entities"] = $msg["caption_entities"] ?? null;
              } else
              if (isset($msg["video"])) {
                $this->chat["msg_type"] = "video";
                $this->chat["text"] = $msg["caption"] ?? null;
                $this->chat["text_entities"] = $msg["caption_entities"] ?? null;
              } else 
              if(isset($msg["new_chat_member"])){
                $this->chat["msg_type"] = "new_chat_memeber";
                $this->chat["text"] = "new_chat_members";
                $this->chat["text_entities"] = $msg["caption_entities"] ?? null;
              } else {
                // di kembangin nanti
                $this->chat["msg_type"] = "unknown";
                $this->chat["text"] = "unknown";
                $this->chat["text_entities"] = "unknown";
              }
        } else {
          // bisa di kembangin nanti
          // $msg = $data["edited_message"]
          $this->chat["msg_type"] = "unknown";
          $this->chat["text"] = "unknown";
          $this->chat["text_entities"] = "unknown";

        }    
    }

    public function running($class, $chat_id, $message_id, $parameters = []) : bool
    {
      // dd($class, $chat_id, $message_id, $parameters);
        if(is_string($class)) {
            $class = "\\App\\Telegram\\Response\\{$class}";

            $b = class_exists($class);
            
            $b = new $class; 
            return (bool)$b->running($chat_id, $message_id, $parameters);
            
        return true;

        }

        return false;
    }
}