<?php

require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once "../lib/db/DBConn.php";

try{
    $db = new DBConn();
    $query= "select * from it_process_status where filename is  not null";
    print"<br>select from it_process_status: $query<br>";
    $processobjs= $db->fetchAllObjects($query);
    foreach ($processobjs as $processobj){
        if(isset($processobj)){
            $filename =   $processobj->filename;
            $masterdealerid = $processobj->master_dealer_id;
            print"<br>filename=$filename**********id=$masterdealerid<br>";
            $upqry="update it_receivedpos set master_dealer_id= $masterdealerid where filename='$filename' ";
            print"<br>update:$upqry<br>";
            $db->execUpdate($upqry);
        }
    }
} catch (Exception $ex) {
     print $ex->getMessage();
}
