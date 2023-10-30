<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
<head>
</head>

<body>
	      
	<form name="import" method="post" enctype="multipart/form-data">
            <input type="file" name="file" /><br />
            <input type="submit" name="submit" value="Submit" />
        </form>
<?php
require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once "lib/db/DBConn.php";
try{
   if(isset($_POST["submit"]))
	{
            $file = $_FILES['file']['tmp_name'];
            $handle = fopen($file, "r");
            $db=new DBConn();
             while(($filesop = fgetcsv($handle, 1000, ",")) !== false){    
                $dc_code=isset($filesop[0])?trim($filesop[0]):"";
                $dc_name = isset($filesop[1])?trim($filesop[1]):"";
                $dc_address= isset($filesop[2])?trim($filesop[2]):"";
                $dc_city = isset($filesop[3])?trim($filesop[3]):"";
                $dc_state= isset($filesop[4])?trim($filesop[4]):"";
               
                if(trim($dc_code)!=" " && trim($dc_name)!=" " && trim($dc_address)!=" " && trim($dc_city) !="" && trim($dc_state)!=" " ){ 
                    $dc_code_db = $db->safe(trim($dc_code));
                    $dc_name_db = $db->safe(trim($dc_name));
                    $dc_address_db = $db->safe(trim($dc_address));
                    $dc_city_db = $db->safe(trim($dc_city));
                    $dc_state_db = $db->safe(trim($dc_state));
                    
                   $query= "update it_shipping_address set dc_name =$dc_name_db, dc_address =$dc_address_db, dc_city =$dc_city_db,  dc_state=$dc_state_db where shipping_address = $dc_code_db";
                   $noofrowupdated=$db->execUpdate($query);
                }
             }
            if($noofrowupdated>0){
		    echo "Your database has imported successfully. You have inserted/updated ". $noofrowupdated ." recoreds";
		}else{
		    echo "Sorry! There is some problem.";
		}
	} 
    
} catch (Exception $ex) {

}