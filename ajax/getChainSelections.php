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
    $chain = isset($_GET['chain']) ? ($_GET['chain']) : false;
    // if(!$region){ $error['region'] = "Not able to get region"; }

    if(count($error) == 0 && $city){

        $objChains = $dbl->getChainListByCity($city);
        // print_r($objCities);
        // return;
        if($objChains != NULL){
            ?>
            <option value="-1"><?php echo "All" ?></option>
            <?php
            foreach($objChains as $chainObj){
                $selected = "";
                if($chain == $chainObj->id){ $selected = "selected"; }
              ?>
              <option value="<?php echo $chainObj->id ?>" <?php echo $selected ?> ><?php echo $chainObj->name ?></option>
              <?php
          }
      }
  }
}catch(Exception $xcp){
    print($xcp->getMessage());
}
