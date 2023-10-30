<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$hostname = "localhost";
$username = "intouch";
$password = "intouch25";
$database = "weikfield_dt_db";
 
 
$conn = mysql_connect("$hostname","$username","$password") or die(mysql_error());
mysql_select_db("$database", $conn);
?>
