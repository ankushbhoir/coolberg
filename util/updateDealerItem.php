<?php
require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once "lib/db/DBConn.php";

$db = new DBConn();

$file = $argv[1];
$updated=0;
$cnt=0;
$handle = fopen($file,"r");
while (($data = fgetcsv($handle, 1048576, ",")) !== FALSE) {    
    $po_itemname = $db->safe($data[0]);       
  
    $cnt++;
    $obj = $db->fetchObject("select * from it_dealer_items where itemname=$po_itemname");
    
    if(isset($obj) && !empty($obj)){          
        $db->execUpdate("update it_dealer_items set is_vlcc=2,updatetime=now() where id=$obj->id");
        $updated++;
    }else{
        echo "Dealer Itemname is: $po_itemname\n";
    }

}

echo "No. of rows: $cnt\n";
echo "No. of updates: $updated\n";