<?php

class SmsAlert extends Senders {
    protected $isActive = false;
    protected $type = 'sms';

    private $info = array(
        'login' => '3757_kalinka',
        'pwd' => 'BzAu6Jd8',
        'originator' => 'truegle.ru',
    );

    protected function sendMessage($recipient, $contact, $msg) {
        include_once 'StreamSms.php';

        $originator = (string)$this->info['originator'];
        $user = $this->info['login'];
        $password = $this->info['pwd'];

        $StreamSms = new STREAMSMS();

        $status = 0;
        $flash = 0;
        $time = 10;

        $msg = htmlspecialchars($msg); // Для преобразования символов (например ' <> & ) к XML формату

        $result = $StreamSms->SendTextMessage($user,$password,$contact, $msg, $originator, $status,$flash,$time);
        Log::save('send sms to ' . $contact.' ('.$recipient.') result: '.var_export($result,true), 'sms');
//        print_r($result);
//        echo "\n";
//        //echo "<br />"; flush();
//
//        # Ждём 5 секунд пока сообщение отсылается
//        sleep(10);
//
//        # Проверяем статус отосланого сообщения
//        print_r($StreamSms->GetMessageState($user,$password,$result['ID сообщения']));
//        echo "\n";
    }
}
