<?php

require_once ("../../it_config.php");
require_once ("lib/core/Constants.php");
require_once "lib/db/DBLogic.php";

class po_data_validation_class{
    function __construct($params=null) {
        echo "object";
    }

    function test(){
        print_r("test");
    }

}

$val = new po_data_validation_class();


?>


