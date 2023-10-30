<?php 

require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once "lib/db/DBConn.php";

$db = new DBConn();
$name = "itemname_prod_code_update_29082018.csv";
$handler = fopen($name,"r");

while($data = fgetcsv($handler)){
    //print_r($data);
    $old_prod_code = $db->safe($data[0]);
    $new_prod_code = $db->safe($data[1]);
    $new_desc = $db->safe($data[2]);
    
    $obj = $db->fetchObject("select * from it_master_items where product_code=$old_prod_code");
    
    if(isset($obj) && !empty($obj)){
        $db->execUpdate("update it_master_items set product_code=$new_prod_code,itemname=$new_desc,updatetime=now() where id=$obj->id");
    }else{
        echo "Prod code: $old_prod_code not found in database<br>";
    }
}
