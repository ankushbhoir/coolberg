<?php

require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once "../lib/db/DBConn.php";


$db = new DBConn();


$ini = $db->fetchObject("select ini_text from it_inis where id=490");

$initext = $ini->ini_text;

echo "JSON Encoded data: ";
print_r($initext);
echo "\n\n\n";

$decoded_data = json_decode($initext);

echo "JSON Decoded data: ";
print_r($decoded_data);
echo "\n\n\n";