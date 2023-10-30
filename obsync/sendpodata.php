<?php 
header('Content-Type: application/json');
require_once('../../it_config.php');
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once 'jwt_helper.php';

$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? null;
$matches = [];

if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    http_response_code(401);
    exit();
}

$token = $matches[1];

$decodedData = JWTHelper::verifyToken($token);

if ($decodedData === null) {
    http_response_code(401);
    echo json_encode(array('message' => 'Unauthorized'));
    exit();
}

// Successfully authenticated user
//echo json_encode(array('message' => 'API endpoint accessed successfully'));




$db = new DBConn();

$today_dt = date('Y-m-d');
    $srt_dt = date('Y-m-d');
    $get_cuttent_time = date('Y-m-d H:i:s');
//    echo "Current time: ".$get_cuttent_time."<br>";
    $time = date('Y-m-d 00:00:00');
    if($get_cuttent_time < $time){
         $st_dt = $srt_dt . " 00:00:00 ";
    }else{
        $st_dt = $srt_dt . " 00:00:00 ";
    //$st_dt = $srt_dt . " 00:00:00 ";
    }
       $st_dt = "2023-10-06 00:00:00 ";
    $st_dt_db = $db->safe(trim($st_dt));
    $ed_dt = $today_dt . " 23:59:59 ";
    $ed_dt_db = $db->safe(trim($ed_dt));
  $main_query="select id,invoice_no from it_po where ctime between $st_dt_db and 
         $ed_dt_db and status not in (10,3,7,9,21,13)";
         $result_id = $db->getConnection()->query($main_query);
         $poids=array();
         $i=0;
          while ($obj_res = $result_id->fetch_object()) {
            
            $poids[$i]=$obj_res->id;
            $i++;
       }           
      // print_r($poids);
       $posid = implode(', ', $poids);
       //echo $posid;



 $query="select ip.invoice_no, ip.invoice_date, ip.delivery_date, ip.expiry_date, ip.tqty, ip.tamt,ip.ctime, ip.shipping_id as sid,ipt.mrp, ipt.qty,ipt.tot_qty, ipt.ttk_qty, ipt.ttk_uom,ipt.pack_type as CAR,ipt.cost_price,ipt.vat, ipt.amt, ipt.master_item_id ,imd.id as master_dealer_id,imd.displayname as dealername,imd.show_code as showflag, idt.itemcode as articleno,idt.itemname as description, d.code as distid, d.name as distname, sh.dc_address as address,sh.dc_state as state,sh.dc_city as city,sh.dc_name as name, bu.code as vendorcode,sh.customer_code,ipt.po_itemname as description_po,ipt.po_eancode, istp.ship_to_party, ivpm.plant,ivpm.storage_location_code,istp.sales_document_type, iesm.product_name,iesm.mrp,iesm.gst ,ipt.po_hsn,iesm.gst,iesm.inner_size,iesm.case_size,istp.margin,istp.customer_name,istp.site
    
    from  
    it_master_dealers imd, 
    it_dealer_items idt,
    it_shipping_address sh, 
    it_distributors d, 
    it_business_unit bu, 
    it_po ip, 
    it_po_items ipt, 
    it_ship_to_party istp, 
    it_ean_sku_mapping iesm, 
    it_vendor_plant_mapping ivpm  

    where  
    ip.id in ($posid) and
    ivpm.master_dealer_id = imd.id and 
    ivpm.vendor_number = d.code and 
    iesm.ean = ipt.po_eancode and 
    iesm.master_dealer_id=imd.id and
    istp.master_dealer_id = imd.id and 
    istp.site = bu.code and 
    ip.id=ipt.po_id  AND  
    ip.dist_id=d.id and 
    d.bu_id= bu.id and 
    sh.id = ip.shipping_id and 
    ip.master_dealer_id=imd.id AND 
    idt.id= ipt.dealer_item_id AND 
    ip.status  in (11) and 
    ip.is_active=1 

    order by dealername,ip.invoice_no";  
   
     $query."\n";
      
   $result = $db->getConnection()->query($query);
   $object='';
    $srno = 1;
   // $obj = $result->fetch_object();
    $invoiceJsonArray = [];
    if($result->num_rows>0){
    while ($object = $result->fetch_object()) {
    	 $invoiceNo = $object->invoice_no;
      $dateTime = new DateTime($object->delivery_date);
      $formattedDate = $dateTime->format("Ymd");

       $invdateTime = new DateTime($object->invoice_date);
      $invformattedDate = $invdateTime->format("Ymd");
    // Create an array for the current item
    $item = [
        'invoice_no' => $invoiceNo,
    	'Material' => $object->po_eancode,
        'qty' => $object->ttk_qty,
        'description' => $object->product_name,
        'plant' => $object->plant,
        'NetPrice' => $object->mrp,
        // Add more item fields if needed
    ];
    // Check if the invoice number exists in the array
    if (!isset($invoiceItems[$invoiceNo])) {
        // If not, create a new entry for the invoice number
        $invoiceItems[$invoiceNo] = [
            'header' => [
                'CustReferen' => $invoiceNo,
                'soldtoparty' => $object->ship_to_party,
                'invoice_date' => $invformattedDate,
                'PricingDate' => date('Ymd'),
                'ReqDelivDate' => $formattedDate,
                'TermsofPayment' => $object->sales_document_type,
                
                // Include other header fields here
            ],
            'items' => [$item], // Start with the first item
        ];
    } else {
        // If the invoice number already exists, add the item to its items array
        $invoiceItems[$invoiceNo]['items'][] = $item;
    }
}

// Convert the grouped array to JSON
if($invoiceItems){
$invoiceJson = json_encode(['data' => array_values($invoiceItems)], JSON_PRETTY_PRINT);
}

//$invoiceJson = trim($invoiceJson, '[]');
echo $invoiceJson;
}
else{
$invoiceJson = json_encode(['data' => "no data found"], JSON_PRETTY_PRINT);
echo $invoiceJson;
}

?>
