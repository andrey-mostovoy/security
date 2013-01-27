<?php

class SkypeAlert extends Senders {
    protected $isActive = true;
    protected $type = 'skype';
    private $url = 'http://109.234.154.13:3380/skypeBot/sendMessage';
    private $chats = array(
        'common'        => '#borodin777/$7718722fb659e0b6',
        'garden'        => '#sonetica-bot/$stalk.4.me;3e74cd758c7e5314',
    );

    public function send($to, $msg) {
        if(!$this->isActive) return;

        foreach ($this->chats as $chat) {
            $this->sendMessage(null, $chat, $msg);
        }
    }

    protected function sendMessage($recipient, $contact, $msg) {
        if (!@file_get_contents($this->url.'?chat='.urlencode($contact).'&message='.urlencode($msg))) {
            Log::error('cannot send message to skype ' . $contact . '. Msg: ' . $msg);
        }
    }
}
