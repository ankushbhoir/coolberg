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
    
    $city = isset($_GET['city']) ? ($_GET['city']) : false;
    $stateId = isset($_GET['state']) ? ($_GET['state']) : false;
    // if(!$region){ $error['region'] = "Not able to get region"; }

    if(count($error) == 0 && $stateId){
        $stateObj = $dbl->getStateNameByStateId($stateId);
        $objCities = $dbl->getCityListByState($stateObj->name);
        // print_r($objCities);
        // return;
        if($objCities != NULL){
            ?>
            <option value="-1"><?php echo "All" ?></option>
            <?php
            foreach($objCities as $cityObj){
                $selected = "";
                if($city == $cityObj->dc_city){ $selected = "selected"; }
              ?>
              <option value="<?php echo $cityObj->dc_city ?>" <?php echo $selected ?> ><?php echo $cityObj->dc_city ?></option>
              <?php
          }
      }
  }
}catch(Exception $xcp){
    print($xcp->getMessage());
}
