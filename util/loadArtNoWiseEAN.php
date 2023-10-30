<?php

//require_once("../../it_config.php");
//require_once "lib/db/DBConn.php";
require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once "lib/db/DBConn.php";

$db = new DBConn();
$cnt = 0;
$arr = array();
$arr1 = array();
$filename = isset($argv[1]) ? $argv[1] : FALSE;
if(!isset($filename)){
    echo "Please add file\n";
    return;
}
$flag=0;
$dealer_item_not_found=0;
$master_item_not_found=0;
$upt_dealer_item=0;
$handle = fopen("$filename","r");
while($data = fgetcsv($handle,100000,",")){
    $mean = trim($data[0]);
    $master_ean = $db->safe(trim($data[0]));
    $dealer_item_desc = $db->safe(trim($data[1]));
    $dealer_art_no = $db->safe(trim($data[2]));        
   
    if(trim($flag)==0){
        $flag=1;        
    }
         
    if(trim($data[0])=='EAN'){
        continue;
    }          
   
    if(trim($mean)!=""){
            $obj = $db->fetchObject("select * from it_master_items where itemcode=$master_ean");
            echo "select * from it_master_items where itemcode=$master_ean\n\n";

            if(isset($obj) && !empty($obj) && $obj!=NULL){                     
                   $obj1 = $db->fetchAllObjects("select * from it_dealer_items where itemcode=$dealer_art_no");           

                   if(isset($obj1) && !empty($obj1)){
                       foreach($obj1 as $obgh){
                           $db->execUpdate("update it_dealer_items set eancode=$master_ean,updatetime=now(),is_master_item=1,master_item_id=$obj->id,is_vlcc=1,is_notFound=0 where id=$obgh->id");               
                        $db->execUpdate("update it_po_items set master_item_id=$obj->id,utime=now() where dealer_item_id=$obgh->id");
                       }
                        
                        $upt_dealer_item++;                        
               }else{
                   array_push($arr,$chain_name);
                   $dealer_item_not_found++;
               }       
        }else{
            array_push($arr1,$master_ean);
            $master_item_not_found++;
        }
        }
}

echo "Dealer items updated: $upt_dealer_item\n";
echo "Dealer item not found: $dealer_item_not_found\n";
echo "Item not found: $master_item_not_found\n";

print_r($arr);
print_r($arr1);


