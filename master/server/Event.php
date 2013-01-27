<?php

class Event {

    private static function fire($class, $method,$a=null,$b=null,$c=null,$d=null,$e=null) {
        try {
            call_user_func(array($class,$method),$a,$b,$c,$d,$e);
        } catch(Exception $e) {
            Log::error($e,'event');
        }
    }

    public static function Index_Request() {
        self::fire('Environment','Index_Request');
    }

    public static function Db_AfterAddRow($r) {

        self::fire('Parser','Db_AfterAddRow',$r);
    }

    public static function Parser_Alert($r) {

        self::fire('Alert','Parser_Alert',$r);
    }

    public static function Parser_Restore($r) {

        self::fire('Alert','Parser_Restore',$r);
    }

    public static function Parser_Arm($r) {

        self::fire('Alert','Parser_Arm',$r);
    }

    public static function Parser_SetArm($r) {

        self::fire('Alert','Parser_SetArm',$r);
    }

    public static function Parser_DisArm($r) {

        self::fire('Alert','Parser_DisArm',$r);
    }


}

?>