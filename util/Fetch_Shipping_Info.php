<?php
require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once "lib/db/DBConn.php";

try{
   $db= new DBConn();
   $qry = "select * from it_shipping_address where master_dealer_id = 2;";
   $psobj= $db->fetchAllObjects($qry);
   foreach($psobj as $row){
       $shipping_address = $row->shipping_address;
       $master_dealer_id = $row->master_dealer_id;
       $sid=$row->id;
        //$shipping_address ="BB-KANPUR-ANCHOR STORE ANCHOR STORE,RAVE@MOTIENTERTAINMENT PVT.LTD.,117/K/13 GUTAIYA,KANPUR. KANPUR-208005,Uttar Pradesh";
        //$master_dealer_id = 2;
   
           $name_arr=explode(" ",trim($shipping_address));
           //print"<br>Name_array<br>";
           //print_r($name_arr);
           $name=$name_arr[0]." ".$name_arr[1]." ".$name_arr[2];
           $name_db=$db->safe($name);
           print"<br>id= $sid";
           print"<br>dc_name=$name";
           print"<br>";   
           $query= "update it_shipping_address set dc_name= $name_db where id= $sid";
           print"$query<br>";
           $db->execUpdate($query);  
    }
} catch (Exception $ex) {
     print $ex->getMessage();
}