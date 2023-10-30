<?php

require_once("../../it_config.php");
//require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once "lib/db/DBConn.php";

$db = new DBConn();

$filename = isset($argv[1]) ? $argv[1] : FALSE;
if(!isset($filename)){
    echo "Please add file\n";
    return;
}

//$master_dealer_id = isset($argv[2]) ? $argv[2] : FALSE;
//if(!isset($master_dealer_id) || $master_dealer_id==''){
//    echo "Please put master dealer id\n";
//    return;
//}

$handle = fopen("$filename","r");
while($data = fgetcsv($handle,100000,",")){
    $mdid = $db->safe(trim($data[0]));
    $vendor = $db->safe(trim($data[1]));
     $plant = $db->safe(trim($data[2]));
  // $dealer_id = $db->safe(trim($data[2]));
     //$new_ean = str_replace(' ', '', $ean);
    echo $q= "select * from it_vendor_plant_mapping where master_dealer_id=$mdid and vendor_number=$vendor and plant=$plant";
   $obj = $db->fetchObject("select * from it_vendor_plant_mapping where master_dealer_id=$mdid and vendor_number=$vendor and plant=$plant");
   
  
// INSERT INTO `it_ean_sku_mapping` (`id`, `ean`, `sku`, `createtime`, `updatetime`) VALUES (NULL, '8901365202370', '20237', NULL, CURRENT_TIMESTAMP);
        echo $i="INSERT INTO `it_vendor_plant_mapping` (`id`, `master_dealer_id`, `vendor_number`, `plant`, `createtime`, `updatetime`) VALUES (NULL, $mdid, ".$vendor.", ".$plant.", NULL, CURRENT_TIMESTAMP);";
        echo "\n";

      $db->execUpdate("INSERT INTO `it_vendor_plant_mapping` (`id`, `master_dealer_id`, `vendor_number`, `plant`, `createtime`, `updatetime`) VALUES (NULL, $mdid, ".$vendor.", ".$plant.", NULL, CURRENT_TIMESTAMP)");
   
}

echo "Done";