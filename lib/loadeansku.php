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
   echo $sku = $db->safe(trim($data[0]));
   echo $ean = $db->safe(trim($data[1]));
  // $dealer_id = $db->safe(trim($data[2]));
   
   $obj = $db->fetchObject("select * from it_ean_sku_mapping where ean=$ean");
   
   if(isset($obj) && !empty($obj)){
//        $master_item_id = $obj->id;
//        $master_ean = $db->safe($obj->itemcode);
       
// //       $obs = $db->fetchAllObjects("select * from it_dealer_items where itemcode=$dealer_art_no and master_dealer_id=$master_dealer_id");
       
//        $obs1 = $db->fetchAllObjects("select * from it_dealer_items where itemcode=$dealer_art_no and master_dealer_id = $dealer_id");
       
//        if(isset($obs1) && !empty($obs1)){
//            foreach($obs1 as $obs){
//                  $db->execUpdate("update it_dealer_items set eancode=$master_ean,master_item_id=$master_item_id,is_master_item=1,is_vlcc=1,is_notFound=0 where id=$obs->id");      
    
//        $db->execUpdate("update it_po_items set master_item_id=$master_item_id where dealer_item_id=$obs->id");
    echo $ean."::".$sku;
           }
       
      else{
       
// INSERT INTO `it_ean_sku_mapping` (`id`, `ean`, `sku`, `createtime`, `updatetime`) VALUES (NULL, '8901365202370', '20237', NULL, CURRENT_TIMESTAMP);
      $db->execUpdate("insert into it_ean_sku_mapping s(`id`, `ean`, `sku`, `createtime`, `updatetime`) VALUES (NULL, '".$ean."', '".$sku."', NULL, CURRENT_TIMESTAMP)");
   }
}

echo "Done";