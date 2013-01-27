<?php

class MailAlert extends Senders {
    protected $isActive = true;
    protected $type = 'mail';

    protected function sendMessage($recipient, $contact, $msg) {
        $subject = 'Message';
        $headers = 'From: ' . $_SERVER['HTTP_HOST'] . "\r\n" .
            'X-Mailer: PHP/' . phpversion();
        if (!mail($contact, $subject, $msg, $headers)) {
            throw new Exception('cannot send message by mail');
        }
    }
}
