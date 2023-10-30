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
    $site = $db->safe(trim($data[1]));
     $stp = $db->safe(trim($data[2]));
  // $dealer_id = $db->safe(trim($data[2]));
     //$new_ean = str_replace(' ', '', $ean);
    echo $q= "select * from it_ship_to_party where master_dealer_id=$mdid and site=$site and ship_to_party=$stp";
   $obj = $db->fetchObject("select * from it_ship_to_party where master_dealer_id=$mdid and site=$site and ship_to_party=$stp");
   
   if(isset($obj) && !empty($obj)){
//        $master_item_id = $obj->id;
//        $master_ean = $db->safe($obj->itemcode);
       
// //       $obs = $db->fetchAllObjects("select * from it_dealer_items where itemcode=$dealer_art_no and master_dealer_id=$master_dealer_id");
       
//        $obs1 = $db->fetchAllObjects("select * from it_dealer_items where itemcode=$dealer_art_no and master_dealer_id = $dealer_id");
       
//        if(isset($obs1) && !empty($obs1)){
//            foreach($obs1 as $obs){
//                  $db->execUpdate("update it_dealer_items set eancode=$master_ean,master_item_id=$master_item_id,is_master_item=1,is_vlcc=1,is_notFound=0 where id=$obs->id");      
    
//        $db->execUpdate("update it_po_items set master_item_id=$master_item_id where dealer_item_id=$obs->id");
    echo $mdid."::".$site."::".$stp;
    echo "\n";
           }
       
      else{
       
// INSERT INTO `it_ean_sku_mapping` (`id`, `ean`, `sku`, `createtime`, `updatetime`) VALUES (NULL, '8901365202370', '20237', NULL, CURRENT_TIMESTAMP);
        echo $i="INSERT INTO `it_ship_to_party` (`id`, `master_dealer_id`, `site`, `ship_to_party`, `createtime`, `updatetime`) VALUES (NULL, ".$mdid.", ".$site.", ".$stp.", NULL, CURRENT_TIMESTAMP);";
        echo "\n";

      $db->execUpdate("INSERT INTO `it_ship_to_party` (`id`, `master_dealer_id`, `site`, `ship_to_party`, `createtime`, `updatetime`) VALUES (NULL, ".$mdid.", ".$site.", ".$stp.", NULL, CURRENT_TIMESTAMP)");
   }
}

echo "Done";