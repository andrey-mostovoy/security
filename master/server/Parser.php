<?php

class Parser {

    public function __construct() {

    }

    public static function Db_AfterAddRow($row) {

        // тревога!
        if ($row['ID_MESGRP'] == 1) {
            Event::Parser_Alert($row);
        }

        // восстановление
        if ($row['ID_MESGRP'] == 2) {
            Event::Parser_Restore($row);
        }

        // снятие/постановка с/на охрану
        if ($row['ID_MESGRP'] == 3) {
            if (strpos($row['MES'], 'Снятие с охраны') !== false) {
                Event::Parser_DisArm($row);
            } elseif (strpos($row['MES'], 'Взятие на охрану') !== false) {
                Event::Parser_SetArm($row);
            } else {
                Event::Parser_Arm($row);
            }
        }

    }
}
