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
    $new_product_code = $db->safe(trim($data[0]));        
    $old_product_code = $db->safe(trim($data[1]));        
    
    if(trim($data[1])=='Old code'){
        continue;
    }
    
    $obj = $db->fetchObject("select * from it_master_items where product_code=$old_product_code");
    $cnt++;
    if(isset($obj) && !empty($obj)){
        $db->execUpdate("update it_master_items set product_code=$new_product_code,old_product_code=$old_product_code,updatetime=now() where id=$obj->id");
        $cnt_update++;
    }else{
        $cnt_insert++;
        echo "Not Exist codes : ".$old_product_code."\n";
    }
}

echo "No. of entries: $cnt\n";
echo "New FG-codes to be inserted: $cnt_insert\n";
echo "New FG-codes updated: $cnt_update\n";


