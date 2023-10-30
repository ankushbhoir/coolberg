<?php
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";

$db = new DBConn();
$cnt=0;
$filename = isset($argv[1]) ? $argv[1] : FALSE;
if(!isset($filename)){
    echo "Please add file\n";
    return;
}

$handle = fopen("$filename","r");
while($data = fgetcsv($handle,100000,",")){
    $invoice_no = $db->safe(trim($data[0]));   
    
   $objs =  $db->fetchAllObjects("select id,invoice_no from it_po where invoice_no=$invoice_no");
   
   if(isset($objs) && !empty($objs)){
   foreach($objs as $obj){
       $db->execUpdate("update it_po set is_active=0,utime=now() where id=$obj->id");
       $cnt++;
       
      $exist = $db->fetchAllObjects("select dealer_item_id from it_po_items where po_id=$obj->id");
      
      foreach($exist as $item){
          $db->execUpdate("update it_dealer_items set is_vlcc=2,updatetime=now() where id=$item->dealer_item_id");
      }
   }
   }else{
       echo "PO : $obj->invoice_no not found\n";
   }
}

echo "Total POs deactivated: $cnt\n";