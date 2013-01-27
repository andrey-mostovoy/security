<?php

include_once 'Log.php';
include_once 'Alert/Alert.php';
include_once 'Event.php';
include_once 'Parser.php';
include_once 'Environment.php';
include_once 'db/Db.php';

Event::Index_Request();

include_once 'runtimeInfo/last_ids.php';

$Db = Db::getInstance();

include_once 'runtimeInfo/last_ping.php';

require_once 'tpl/page.php';