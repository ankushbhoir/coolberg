<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once "lib/core/Constants.php";
require_once "lib/email/EmailHelper.php";

$error = array();
try{
    $db = new DBConn();
    $dbl = new DBLogic();
    
    $region = isset($_GET['region']) ? ($_GET['region']) : false;
    $state = isset($_GET['state']) ? ($_GET['state']) : false;

    if(count($error) == 0 && $region){
        $objStates = $dbl->getStateListByRegion($region);
        // print_r($objStates);
        // return;
        if($objStates != NULL){
            ?>
            <option value="-1"><?php echo "All" ?></option>
            <?php
            foreach($objStates as $stateObj){
                $selected = "";
                if($state == $stateObj->id){ $selected = "selected"; }
              ?>
              <option value="<?php echo $stateObj->id ?>" <?php echo $selected ?> ><?php echo $stateObj->name ?></option>
              <?php
          }
      }
  }
}catch(Exception $xcp){
    print($xcp->getMessage());
}
