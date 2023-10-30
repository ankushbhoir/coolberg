<?php
//require_once("../../it_config.php");
require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once("lib/db/DBConn.php");
require_once "lib/core/Constants.php";

$db = new DBConn();

$objs = $db->fetchAllObjects("select id,invoice_no,ctime,filename from it_po where status not in (10) and is_active=1");

foreach($objs as $obj){
    $non_itms_cnt = $db->fetchObject("select count(*) as cnt from it_dealer_items di,it_po_items pi where pi.dealer_item_id=di.id and pi.po_id=$obj->id and di.is_vlcc=2");
    $po_itm_cnt = $db->fetchObject("select count(*) as cnt from it_dealer_items di,it_po_items pi where pi.dealer_item_id=di.id and pi.po_id=$obj->id");
    
   if(trim($non_itms_cnt->cnt)==trim($po_itm_cnt->cnt)){
       echo "PO Id: $obj->id   PO Number: $obj->invoice_no   Ctime: $obj->ctime   Filename: $obj->filename\n";
       $db->execUpdate("update it_po set is_active=0,updatetime=now() where id=$obj->id");
   }
}

echo "<br><br>Updation completed......<br><br>";
