<?php

abstract class Senders {
    protected $isActive = true;
    protected $type = null;

    public function __construct() {
    }

    public function getType() {
        return $this->type;
    }

    public function isActive() {
        return $this->isActive;
    }

    public function send($to, $msg){
        if(!$this->isActive) {
            return false;
        }

        $this->sendTo($to, $msg);
    }

    public function sendTo($to, $msg) {
        foreach($to as $recipientId => $contact) {
            if(empty($contact)) continue;
            try {
                $this->sendMessage($contact['name'], $contact[$this->type], $msg);
            } catch(Exception $e) {
                Log::error($e,$this->type);
            }
        }
    }

    protected abstract function sendMessage($recipient, $contact, $msg);
}