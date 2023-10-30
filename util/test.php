<?php 

require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once "lib/db/DBConn.php";

$db = new DBConn();
$handle = @fopen('php://output', 'w');
header('Content-Type: text/csv; charset=utf-8');


//To get shipping address
$name = "addresses.csv";
header('Content-Disposition: attachment; filename='.$name);
$headers = array('ID','Master Dealer Name','Address');
 fputcsv($handle, $headers);
$md = $db->fetchAllObjects("select * from it_master_dealers order by name");
foreach($md as $master_dealer){
$objs = $db->fetchAllObjects("select sh.id as shipping_id,sh.dc_address from it_shipping_address sh where sh.master_dealer_id=$master_dealer->id group by sh.dc_address");

foreach($objs as $obj){
    $shipping_id = $obj->shipping_id;
    $master_dealername = $master_dealer->name;
    $address = $obj->dc_address;
    
    $data = array();
    $data[] = $shipping_id;
    $data[] = $master_dealername;
    $data[] = $address;
    fputcsv($handle,  array_values($data));
}
}

/*
//To get MT data
$name = "MT_data.csv";
header('Content-Disposition: attachment; filename='.$name);
 $headers = array('Master Dealer Name','Location','Address','Ini id','Assigned ini ids');
 fputcsv($handle, $headers);
$md = $db->fetchAllObjects("select * from it_master_dealers where id in (2,3,4,5,8,14,15,16) order by name");
foreach($md as $master_dealer){
    $objs = $db->fetchAllObjects("select sh.id,sh.dc_city,sh.shipping_address,p.ini_id,sh.ini_id as shipping_ini_set from it_shipping_address sh,it_po p where sh.id=p.shipping_id and p.status=1 and p.master_dealer_id=sh.master_dealer_id and p.master_dealer_id=$master_dealer->id and p.ini_id is not null group by p.shipping_id,p.ini_id");

    foreach($objs as $obj){
        $master_dealername = $master_dealer->name;
        $location = $obj->dc_city;
        $address = $obj->shipping_address;
        $ini_id = $obj->ini_id;
        $shipping_ini_set = $db->safe($obj->shipping_ini_set);

        $data = array();
       // $data[] = $obj->id;
        $data[] = $master_dealername;
        $data[] = $location;
        $data[] = $address;
        $data[] = $ini_id;
        $data[] = $shipping_ini_set;
        fputcsv($handle,  array_values($data));
}
}
*/