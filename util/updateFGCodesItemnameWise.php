<?php

//require_once("../../it_config.php");
require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once "lib/db/DBConn.php";

$db = new DBConn();
$cnt = 0;
$cnt_insert = 0;
$cnt_update = 0;
$filename = $argv[1];
$handle = fopen($filename,"r");


while($data = fgetcsv($handle,10000,",")){    
    $product_code = $db->safe(trim($data[0]));        
    $itemname = $db->safe(trim($data[1]));        
    
    if(trim($data[1])=='Itemname'){
        continue;
    }
    
    $obj = $db->fetchObject("select * from it_dealer_items where itemname=$itemname");
//    echo "select * from it_dealer_items where itemname=$itemname\n";
    
    $cnt++;
    if(isset($obj) && !empty($obj)){
        $obj1 = $db->fetchObject("select * from it_master_items where product_code=$product_code");
        if(isset($obj1) && !empty($obj1)){
              $db->execUpdate("update it_dealer_items set eancode=$obj1->itemcode,is_master_item=1,master_item_id=$obj1->id,is_vlcc=1,is_notFound=0,updatetime=now() where id=$obj->id");
            $cnt_update++;
        }
      
    }else{
        $cnt_insert++;
        echo "Not Exist itemname : ".$itemname."\n";
    }
}

echo "No. of entries: $cnt\n";
echo "New FG-codes to be inserted: $cnt_insert\n";
echo "New FG-codes updated: $cnt_update\n";


