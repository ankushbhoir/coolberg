<?php

//require_once("../../it_config.php");
require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once "lib/db/DBConn.php";

$db = new DBConn();

$filename = isset($argv[1]) ? $argv[1] : FALSE;
if(!isset($filename)){
    echo "Please add file\n";
    return;
}

$master_dealer_id = isset($argv[2]) ? $argv[2] : FALSE;
if(!isset($master_dealer_id) || $master_dealer_id==''){
    echo "Please put master dealer id\n";
    return;
}

$handle = fopen("$filename","r");
while($data = fgetcsv($handle,100000,",")){
   $dealer_art_no = $db->safe(trim($data[0]));
   $fg_codes = $db->safe(trim($data[1]));
   
   $obj = $db->fetchObject("select * from it_master_items where product_code=$fg_codes");
   
   if(isset($obj) && !empty($obj)){
       $master_item_id = $obj->id;
       $master_ean = $db->safe($obj->itemcode);
       
       $obs = $db->fetchObject("select * from it_dealer_items where itemcode=$dealer_art_no and master_dealer_id=$master_dealer_id");
       
       if(isset($obs) && !empty($obs)){
            $db->execUpdate("update it_dealer_items set eancode=$master_ean,master_item_id=$master_item_id,is_master_item=1,is_vlcc=1,is_notFound=0 where id=$obs->id");
      // echo "update it_dealer_items set eancode=$master_ean,master_item_id=$master_item_id,is_master_item=1,is_vlcc=1,is_notFound=0 where itemcode=$dealer_art_no and master_dealer_id=$master_dealer_id\n";
    
       $db->execUpdate("update it_po_items set master_item_id=$master_item_id where dealer_item_id=$obs->id");
       }
      
   }
}

echo "Done";
