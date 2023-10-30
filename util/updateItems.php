<?php
require_once("../../it_config.php");
//require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
//require_once("/home/ykirad/dev/subversion/onlinePOS/vlcc_dt/it_config.php");
require_once "lib/db/DBConn.php";

updateItemsAsNonVLCC();

function updateItemsAsNonVLCC(){
    $db = new DBConn();
//$db->execUpdate("update it_dealer_items set is_vlcc=2  where itemname like 'Himalaya%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2  where itemname like 'BIO%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2  where itemname like 'DR BATRAS%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2  where itemname like 'Streax%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2  where itemname like 'GARNIER%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2  where itemname like 'VCARE%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2  where itemname like 'BELLA%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2  where itemname like 'EVERYUTH%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2  where itemname like 'LP%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2  where itemname like 'Tastilo%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2  where itemname like 'Chiko%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2  where itemname like 'NUTRALITE%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2  where itemname like 'LOREAL%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2  where itemname like 'Pitambari%'");	
$db->execUpdate("update it_dealer_items set is_vlcc=2  where itemname like 'Yoga%'");	
$db->execUpdate("update it_dealer_items set is_vlcc=2  where itemname like 'kara%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2  where itemname like 'Layer%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2  where itemname like 'Godrej%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2  where itemname like 'Lotus%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2  where itemname like 'aer pocket%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2  where itemname like 'BIKANO%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2  where itemname like 'GOODKNIGHT%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2  where itemname like 'CINTHOL%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2  where itemname like 'Catch%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2  where itemname like 'SUGARFREE%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2  where itemname like 'BBLUNT%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2  where itemname like 'GOOD KNIGHT%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2  where itemname like 'PROTEKT MASTER%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2  where itemname like 'GDRJ%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2  where itemname like 'HIT%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2  where itemname like 'ezee%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2  where itemname like 'aer spray%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2  where itemname like 'choki choki%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2  where itemname like 'bhpc%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2  where itemname like 'banjaras%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2  where itemname like 'seni perfecta%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2  where itemname like 'v wash%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2  where itemname like 'secret temptation%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2 where itemname like 'CIN DEO%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2 where itemname like 'GENTEEL%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2 where itemname like 'GK ACTIV+%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2 where itemname like 'WILD STONE%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2 where itemname like 'DR.BATRAS%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2 where itemname like 'CANDID%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2 where itemname like 'BIRYANEEZ%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2 where itemname like 'American Garden%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2 where itemname like 'American Garden%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2 where itemname like 'Woodwards%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2 where itemname like 'cntl%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2 where itemname like 'aer%'");
$db->execUpdate("update it_dealer_items set is_vlcc=2 where itemcode IN (1339825,1229785,1111203,1009695,1277856,1249136,1234951,1208161,1185082,1185077,1021200,1021143,1010010,1309129,1308303,1307337,1300295,1300278,1290529,1289009,1289003,1289002,1287766,1287765,1287764,1283744,1280683,1279896,1277440,1277436,1274856,1274855,1267169,1259193,1249801,1249799,1228536,1228534,1219783,1216641,1216619,1174390,1129017,1010410,1010408,1010406,1317883,1319288,1319286,1317883,128640488,122727806,105011574,102486507,100044659,100043201,100043197
");


}

