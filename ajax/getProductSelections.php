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
    
    $category = isset($_GET['category']) ? ($_GET['category']) : false;
    $product = isset($_GET['product']) ? ($_GET['product']) : false;
    // if(!$region){ $error['region'] = "Not able to get region"; }

    if(count($error) == 0 && $category){

        $objProducts = $dbl->getProductsListByChain($category);
        // print_r($city);
        // return;
        if($objProducts != NULL){
            ?>
            <option value="-1"><?php echo "All" ?></option>
            <?php
            foreach($objProducts as $prodObj){
                $selected = "";
                if($product == $prodObj->id){ $selected = "selected"; }
              ?>
              <option value="<?php echo $prodObj->id ?>" <?php echo $selected ?> ><?php echo $prodObj->itemname; ?></option>
              <?php
          }
      }
  }
}catch(Exception $xcp){
    print($xcp->getMessage());
}
