<?php

include_once 'Senders.php';
include_once 'mail/MailAlert.php';
include_once 'skype/SkypeAlert.php';
include_once 'sms/SmsAlert.php';

class Alert {
    private $type = null;
    private $handlers = array();
    private $contacts = array();
    private static $instance = array();

    private function __construct($type) {
        $this->type = $type;
        $this->collectContacts();
        $this->handlers = array(
            'mail'  => new MailAlert(),
            'skype' => new SkypeAlert(),
            'sms'   => new SmsAlert(),
        );
    }

    private function collectContacts() {
        include_once 'contacts.php';
        if(isset($contacts)) {
            foreach($contacts as $id => $contactData) {
                if ($id == 'other') {
                    foreach ($contactData as $otherContactData) {
                        $this->contacts[] = $otherContactData;
                    }
                } else {
                    $this->contacts[$id] = $contactData;
                }
            }
        }
    }

    /**
     * @static
     * @return Alert
     */
    public static function getInstance($type) {
        if (!isset(self::$instance[$type]) || self::$instance[$type] === null) {
            self::$instance[$type] = new self($type);
        }
        return self::$instance[$type];
    }

    private function getContactDataByType($id, $sendType) {
        if (isset($this->contacts[$id][$sendType]) && $this->contacts[$id][$sendType]) {
            return array(
                'name' => $this->contacts[$id]['name'],
                $sendType => $this->contacts[$id][$sendType],
            );
        }
        return null;
    }

    public function send($msg, $replace = null, $restriction = null) {
        // подготовим сообщение
        if (is_array($replace)) {
            foreach ($replace as $target => $val) {
                switch ($target) {
                    case 'member':
                        if (isset($this->contacts[$val])) {
                            $replaceValue = $this->contacts[$val]['name'];
                        } else {
                            $replaceValue = $val;
                        }
                        break;
                    default:
                        $replaceValue = $val;
                }
                $msg = str_replace('{{ '.$target.' }}', $replaceValue, $msg);
            }
        }

        $excludeSendType = array();
        $excludeSendId = array();
        // отправим конкретно кому-то и исключим этот тип рассылки из массовой
        // в качестве значения должен быть ид того, кто в "основном" списке
        if (!empty($restriction)) {
            foreach ($restriction as $sendType => $toId) {
                if (!is_array($toId)) {
                    $toId = (array) $toId;
                }
                $sendTo = array();
                foreach ($toId as $id) {
                    $contactData = $this->getContactDataByType($id, $sendType);
                    if (!is_null($contactData)) {
                        $sendTo[$id] = $contactData;
                    }
                }
                // добавим тех кто "хочет"
                foreach ($this->contacts as $id => $data) {
                    if (!isset($data['notify'])) {
                        continue;
                    }
                    if (strpos($data['notify'], 'all') !== false || strpos($data['notify'], $this->type) !== false) {
                        $contactData = $this->getContactDataByType($id, $sendType);
                        if (!is_null($contactData)) {
                            $sendTo[$id] = $contactData;
                        }
                    }
                }
                Log::msg('Try send private alert by '.$sendType.' to '.implode(',',array_keys($sendTo)));
                $this->handlers[$sendType]->sendTo($sendTo, $msg);
                $excludeSendType[] = $sendType;
                $excludeSendId[$sendType] = $sendTo;
            }
        }

        foreach($this->handlers as $sendType => $Handler) {
            /**
             * @var Senders $Handler
             */

            if (!$Handler->isActive()) {
                Log::msg($sendType . ' type is inactive.');
                continue;
            }

            // сформируем список тех кому шлем
            $sendTo = array();
            foreach ($this->contacts as $id => $data) {
                if (!isset($data['notify'])) {
                    continue;
                }

                if (strpos($data['notify'], 'all') !== false || strpos($data['notify'], $this->type) !== false) {
                    $contactData = $this->getContactDataByType($id, $sendType);
                    if (!is_null($contactData)) {
                        $sendTo[$id] = $contactData;
                    }
                }
            }
            // если отсылали кому-то по ограничению то уберем отсюда
            if (in_array($sendType, $excludeSendType)) {
                $sendTo = array_diff_assoc($sendTo, $excludeSendId[$sendType]);
            }
            Log::msg('Try send alert by '.$sendType.' to '.implode(',',array_keys($sendTo)));
            // отошлем ))
            $Handler->send($sendTo, $msg);
        }
    }

    public static function Parser_Alert($row) {
        $Alert = Alert::getInstance('alert');
        $Alert->send('Внимание! Тревога! ' .$row['MES'] . ' ' . $row['ZONA']);
    }

    public static function Parser_Restore($row) {
        $Alert = Alert::getInstance('restore');
        $Alert->send('Восстановление! '.$row['MES']);
    }

    public static function Parser_Arm($row) {
        $Alert = Alert::getInstance('arm');
        $Alert->send($row['MES'] . '. Сотрудник ' . $row['ZONA'] . ' {{ member }}. Время ' . $row['SDATE'], array(
            'member' => $row['ZONA'],
        ),
        array(
            'sms' => $row['ZONA'],
        ));
    }

    /**
     * установка
     * @param $row
     */
    public static function Parser_SetArm($row) {
        $Alert = Alert::getInstance('setarm');
        $date = date('d.m H:i', strtotime(trim($row['SDATE'], '"')));
        $Alert->send(trim($row['MES'], '"') . '. ' . $row['ZONA'] . ' {{ member }}. ' . $date, array(
                'member' => $row['ZONA'],
            ),
            array(
                'sms' => $row['ZONA'],
            )
        );
    }

    /**
     * снятие
     * @param $row
     */
    public static function Parser_DisArm($row) {
        $Alert = Alert::getInstance('disarm');
        $date = date('d.m H:i', strtotime(trim($row['SDATE'], '"')));
        $Alert->send(trim($row['MES'], '"') . '. ' . $row['ZONA'] . ' {{ member }}. ' . $date, array(
                'member' => $row['ZONA'],
            )
        );
    }
}