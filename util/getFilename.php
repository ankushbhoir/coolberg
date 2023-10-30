<?php
require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once "lib/db/DBConn.php";
try{
   $db = new DBConn();
   $filenameparts=array();
   $qry = "select * from it_process_status";
   $psobj= $db->fetchAllObjects($qry);
   foreach($psobj as $row){
       $id = $row->id;
       $pdfname=$row->pdfname;
       $filenameparts= explode("/",$pdfname);
       $filenamepartsrev= array_reverse($filenameparts);
       //print_r($filenamepartsrev);
       $fname=$filenamepartsrev[0];
       $filename=$db->safe($fname);
       $upQry= "update it_process_status set filename=$filename where id= $id";
       print"id=$id<br>";
       print"<br>filename=$filename<br>";
       print"<br>$upQry<br>";
       $db->execUpdate($upQry);
   }
   print"updated";
}
catch (Exception $ex) {
    print $ex->getMessage();
}

   