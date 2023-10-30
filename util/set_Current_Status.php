<?php
require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
try{
   $db = new DBConn();
   $statusupdated = array();
 //id, master_dealer_id, filename, createtime
   $qry = "select * from it_process_status order by id desc";//where status not in(".POStatus::STATUS_JUNK_FILES.",".POStatus::STATUS_NEW_PO.",".POStatus::STATUS_UNRECOGNIZED_CHAIN.")
   print"\n $qry";
   $psobj= $db->fetchAllObjects($qry);
   foreach($psobj as $row){
        $id = $row->id;
        $filename=$row->filename;
        //$mdid=$row->master_dealer_id;
        if(in_array($filename,$statusupdated)){
            print"file $filename already Updated<br> ";
            continue;
        }else{
        $upQry= "update it_process_status set is_current_status = 1 where id = $id"; 
        print"<br>$upQry<br>";
        $db->execUpdate($upQry);
        array_push($statusupdated,$filename);
        }
   }
print_r($statusupdated);  
print"no of files updated".count($statusupdated);
}
catch (Exception $ex) {
    print $ex->getMessage();
}
