<?php

//require_once("../../it_config.php");
//require_once "lib/db/DBConn.php";
require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once "lib/db/DBConn.php";


$db = new DBConn();
$cnt = 0;
$new_category = 0;
$master_item_update = 0;
$master_item_insert = 0;
$arr = array();
$arr1 = array();
$filename = isset($argv[1]) ? $argv[1] : FALSE;
if(!isset($filename)){
    echo "Please add file\n";
    return;
}
$flag=0;
$new_dealer_item=0;
$chain_name=FALSE;
$chain_not_found=0;
$master_item_not_found=0;
$upt_dealer_item=0;
$handle = fopen("$filename","r");
while($data = fgetcsv($handle,100000,",")){
    $master_ean = $db->safe(trim($data[0]));
    $item_desc = trim($data[1]);
    $dealerEAN = trim($data[2]);    
    $dealer_ean = $db->safe(trim($data[2]));    
  //  echo "Before: ".$dealerEAN."\n";
    $dealerEAN = ltrim($dealerEAN,'0');
   //  echo "After: ".$dealerEAN."\n";
     $dealer_ean = $db->safe($dealerEAN);
    if(trim($flag)==0){
        $flag=1;
        $chain_name = $db->safe(trim($data[2]));
    }
         
    if(trim($data[0])=='EAN'){
        continue;
    }
    
    $itemname = preg_replace('/[^a-zA-Z0-9.,\+\-\*\/\:\@\$\(\)\[\]\!\#\%\&\= ]/',"",$item_desc);
    $itemname_db = $db->safe($itemname);
        
    $cnt++;        
   
    $obj = $db->fetchObject("select * from it_master_items where itemcode=$master_ean");
    
    if(isset($obj) && !empty($obj) && $obj!=NULL){
       $chainData = $db->fetchObject("select * from it_master_dealers where displayname=$chain_name");  
      // echo "select * from it_master_dealers where displayname=$chain_name\n";
       if(isset($chainData) && !empty($chainData) && $chainData!=NULL){
           $obj1 = $db->fetchObject("select * from it_dealer_items where itemcode=$dealer_ean and master_dealer_id=$chainData->id");           
       
           if(!isset($obj1) && empty($obj1) && $obj1==NULL){
               $clause = "";
               if(trim($dealerEAN)!=''){
                    $db->execInsert("insert into it_dealer_items set distid=NULL,master_dealer_id=$chainData->id,eancode=$master_ean ,itemcode=$dealer_ean,itemname=$itemname_db,is_master_item=1,master_item_id=$obj->id,is_vlcc=1,createtime=now()");
                  //  echo "insert into it_dealer_items set distid=NULL,master_dealer_id=$chainData->id,eancode=$master_ean,itemcode=$dealer_ean,itemname=$itemname_db,is_master_item=1,master_item_id=$obj->id,is_vlcc=1,createtime=now()\n";
                    $new_dealer_item++;
               }
          }else{
              if(trim($dealerEAN)!=''){
                $db->execUpdate("update it_dealer_items set eancode=$master_ean,updatetime=now(),is_master_item=1,master_item_id=$obj->id,is_vlcc=1,is_notFound=0 where id=$obj1->id");
                $db->execUpdate("update it_po_items set master_item_id=$obj->id,utime=now() where dealer_item_id=$obj1->id");
// echo "update it_dealer_items set itemcode=$dealer_ean,updatetime=now() where id=$obj1->id\n";
                $upt_dealer_item++;
              }
          }
       }else{
        //   echo "$chain_name chain does not found in database.\n";
           array_push($arr,$chain_name);
           $chain_not_found++;
       }   
    }else{
      // echo "$master_ean does not found in database.\n";
       array_push($arr1,$master_ean);
       $master_item_not_found++;
    }
}

echo "New dealer items inserted: $new_dealer_item\n";
echo "Dealer items updated: $upt_dealer_item\n";
echo "Chain not found: $chain_not_found\n";
echo "Item not found: $master_item_not_found\n";

//print_r($arr);
print_r($arr1);


