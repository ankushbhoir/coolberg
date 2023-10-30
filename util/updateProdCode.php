<?php
require_once("/home/ykirad/dev/subversion/onlinePOS/vlcc_dt/it_config.php");

require_once "lib/db/DBConn.php";

$db = new DBConn();

$file = $argv[1];
$updated=0;
$cnt=0;
$handle = fopen($file,"r");
while (($data = fgetcsv($handle, 1048576, ",")) !== FALSE) {    
    $prod_code = $db->safe($data[0]);   
    $art_no = $db->safe($data[1]);
    $po_itemname = $db->safe($data[2]);
  
    $cnt++;
    $objs = $db->fetchAllObjects("select * from it_dealer_items where itemname=$po_itemname and itemcode=$art_no");
    echo "select * from it_dealer_items where itemname=$po_itemname and itemcode=$art_no\n";
    
   foreach($objs as $obj){
       $mdata = $db->fetchObject("select * from it_master_items where product_code=$prod_code");
       echo "select * from it_master_items where product_code=$prod_code\n";
       if(isset($mdata) && !empty($mdata)){
           $db->execUpdate("update it_dealer_items set is_master_item=1,master_item_id=$mdata->id,is_vlcc=1,is_notFound=0,updatetime=now() where id=$obj->id");
     $updated++;
            }
   }

}

echo "No. of rows: $cnt\n";
echo "No. of updates: $updated\n";