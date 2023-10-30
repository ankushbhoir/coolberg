<?php

//require_once("../../it_config.php");
require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once "lib/db/DBConn.php";

$db = new DBConn();
$cnt = 0;
$new_category = 0;
$master_item_update = 0;
$master_item_insert = 0;
//$handle = fopen("vlcc_master_items.csv","r");
//$handle = fopen("master_item_15112018.csv","r");

$filename = $argv[1];
$handle = fopen($filename,"r");
$arr = array();

$commit = isset($argv[2]) ? $argv[2] : FALSE;
if(!isset($commit)){
    echo "Put 0=> to show no. of inserts / 1=> to commit changes\n";
    return;
}

while($data = fgetcsv($handle,100000,",")){
    $prodcode = trim($data[0]);
    $prod_code = $db->safe(trim($data[0]));
    $item_desc = trim($data[1]);
    $ecode = trim($data[2]);
    $itemcode = $db->safe(trim($data[2]));
    $sizee = trim($data[3]);
    $size = $db->safe(trim($data[3]));
    $mrp = trim($data[4]);
    $category = trim($data[5]);
    
    if(trim($data[2])=='EAN Code'){
        continue;
    }
    
    $itemname = preg_replace('/[^a-zA-Z0-9.,\+\-\*\/\:\@\$\(\)\[\]\!\#\%\&\= ]/',"",$item_desc);
    $itemname_db = $db->safe($itemname);
    
    $category = preg_replace('/[^a-zA-Z0-9.,\+\-\*\/\:\@\$\(\)\[\]\!\#\%\&\= ]/',"",$category);
    $category_db = $db->safe($category);
    
    $cnt++;
    $cat_exist = $db->fetchObject("select * from it_category where category=$category_db");
//    echo "select * from it_category where category=$category_db\n";
    $cat_id = "";
    if(isset($cat_exist) && !empty($cat_exist) && $cat_exist!=NULL){ //check category exist or not
        $cat_id = $cat_exist->id;
    }else{ //if not insert it
        $query = "insert into it_category set category=$category_db,createtime=now()";        
        if($commit==1){
            echo "New Category: ".$query."\n";
            $cat_id = $db->execInsert($query);
        }
        $new_category++;
    }        
   
    if(trim($ecode)!=""){
    $obj = $db->fetchObject("select * from it_master_items where itemcode=$itemcode");
    
    if(isset($obj) && !empty($obj) && $obj!=NULL){
        $clause = "";
          if(trim($cat_id)!=""){
            $clause .= " category_id=$cat_id";
        }
        if(trim($itemname)!=""){
            $clause .= " ,itemname=$itemname_db";
        }
       
         if(trim($sizee)!=""){
            $clause .= " ,sku=$size";
        }
         if(trim($prodcode)!=""){
            $clause .= " ,product_code=$prod_code";
        }
         if(trim($mrp)!=""){
            $clause .= " ,mrp=$mrp";
        }
//       $query1 = "update it_master_items set category_id=$cat_id,sku=$size,product_code=$prod_code,mrp=$mrp,updatetime=now() where id=$obj->id";       
        $query1 = "update it_master_items set $clause ,updatetime=now() where id=$obj->id";   
        echo $query1."\n";
        $master_item_update++;
       if($commit==1){
           echo "Update existing item: $query1\n";
            $db->execUpdate($query1);
       }
    }else{
         $clause1 = "";
          if(trim($cat_id)!=""){
            $clause1 .= " ,category_id=$cat_id";
        }
        if(trim($itemname)!=""){
            $clause1 .= " ,itemname=$itemname_db";
        }
       
         if(trim($sizee)!=""){
            $clause1 .= " ,sku=$size";
        }
         if(trim($prodcode)!=""){
            $clause1 .= " ,product_code=$prod_code";
        }
         if(trim($mrp)!=""){
            $clause1 .= " ,mrp=$mrp";
        }
        $query2 = "insert into it_master_items set itemcode=$itemcode $clause1 ,is_vlcc=1,createtime=now()";        
        $master_item_insert++;
        echo "Insert new item: $query2\n";
        if($commit==1){            
            $db->execInsert($query2);
        }
    }
}
}

echo "No. of items: $cnt\n";
echo "New categories inserted: $new_category\n";
echo "New items inserted: $master_item_insert\n";
echo "New items updated: $master_item_update\n";


