<?php

/**
 * ключи
 * notify - означает что по этим событиям будет присылаться рассылка на указанные контакты. Если не указан этот параметр
 *          то в массовой рассылке не учавствует.
 * sms    - Эти поля как контакты выступают. если их нет - личной рассылки не будет
 * mail
 */
$contacts = array (
    1  => array (
        'name' => 'Бородин Дмитрий Владимирович',
    ),
    2  => array (
        'name' => 'Савчук Александр',
    ),
    3  => array (
        'name' => 'Есин Илья Васильевич',
        'sms' => '79312567887'
    ),
    4  => array (
        'name' => 'Сергиенко Олег Викторович',
    ),
    5  => array (
        'name' => 'Маслов Алексей Юрьевич',
    ),
    6  => array (
        'name' => 'Тодоров Максим',
    ),
    7  => array (
        'name' => 'Никитин Василий',
    ),
    8  => array (
        'name' => 'Петров Алексей Николаевич',
    ),
    9  => array (
        'name' => 'Филатов Дмитрий Владимирович',
    ),
    10 => array (
        'name' => 'Ахмедьянов Ренат Фидаилевич',
    ),
    11 => array (
        'name' => 'Курочкин Владимир Юрьевич',
    ),
    12 => array (
        'name' => 'Шихарев Семён Валерьевич',
    ),
    13 => array (
        'name' => 'Дементьев Евгений Викторович',
    ),
    14 => array (
        'name' => 'Маслов Сергей Владимирович',
    ),
    15 => array (
        'name' => 'Бобров Иван Павлович',
    ),
    16 => array (
        'name' => 'Лужковский Виктор Андреевич',
    ),
    17 => array (
        'name' => 'Мищенко Андрей Игоревич',
    ),
    18 => array (
        'name' => 'Дудин Роман Владимирович',
    ),
    19 => array (
        'name' => 'Копелевич Денис Александрович',
    ),
    20 => array (
        'name' => 'Попов Юрий Владимирович',
    ),
    21 => array (
        'name' => 'Фугин Сергей Владимирович',
    ),
    22 => array (
        'name' => 'Киселёв Павлик Васильевич',
        'sms' => '79522610696',
    ),
    23 => array (
        'name' => 'Промыслова Наталья',
    ),
    24 => array (
        'name' => 'Куликов Евгений Юрьевич',
    ),
    25 => array (
        'name' => 'Фомичев Александр',
    ),
    26 => array (
        'name' => 'пусто',
    ),
    27 => array (
        'name' => 'Радославов Саша',
    ),
    28 => array (
        'name' => 'Иванов Евгений',
    ),
    29 => array (
        'name' => 'Ильин Лев',
    ),
    30 => array (
        'name' => 'Куликов Евгений',
    ),
    31 => array (
        'name' => 'Васильев Виталий',
    ),
    32 => array (
        'name' => 'Никитин Андрей',
    ),
    33 => array (
        'name' => 'Савинцев Евгений',
    ),
    34 => array (
        'name' => 'Левченко Виталий Андреевич',
    ),
    35 => array (
        'name' => 'Миронов Максим',
    ),

    // остальные желающие получать уведомления
    'other' => array(
        array(
            'name'  => 'Андрей Мостовой',
            'mail'  => 'stalk.4.me@gmail.com',
            'sms'   => '79215980559',
            'notify' => 'all restore arm setarm disarm alert',
        )
    ),
);