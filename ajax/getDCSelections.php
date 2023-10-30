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
    
    $dc = isset($_GET['dc']) ? ($_GET['dc']) : false;
    $chain = isset($_GET['chain']) ? ($_GET['chain']) : false;
    $city = isset($_GET['city']) ? ($_GET['city']) : false;
    // if(!$region){ $error['region'] = "Not able to get region"; }

    if(count($error) == 0 && $chain){

        $objDC = $dbl->getDCListByChain($chain,$city);
        // print_r($city);
        // return;
        if($objDC != NULL){
            ?>
            <option value="-1"><?php echo "All" ?></option>
            <?php
            foreach($objDC as $dcObj){
                $selected = "";
                if($dc == $dcObj->id){ $selected = "selected"; }
              ?>
              <option value="<?php echo $dcObj->id ?>" <?php echo $selected ?> ><?php echo $dcObj->customer_code."-".$dcObj->dc_address; ?></option>
              <?php
          }
      }
  }
}catch(Exception $xcp){
    print($xcp->getMessage());
}
