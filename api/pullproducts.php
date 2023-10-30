<?php 
$curl = curl_init();
//2022-11-03
$sdate=date('Y-m-d');
$previousDate = date('Y-m-d', strtotime('-1 day', strtotime($sdate))); // 
curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://service.alignbooks.com/ABDataService.svc/ShortList_Item',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{
"new_id":"00000000-0000-0000-0000-000000000000"
}',
  CURLOPT_HTTPHEADER => array(
    'username:  Yashika@coolberg.in',
    'apikey: d7bb8ac4-c295-11ea-9a09-0050569823ac',
    'company_id: d16ab7d6-ba66-4086-9ec0-744e903d7cd6',
    'enterprise_id: 34e809ca-fb88-4a59-a5c8-a036000a444a',
    'user_id: 0380defa-341b-4dc2-ac13-25731c9bd558',
    'Content-Type: application/json'
  ),
));


require_once "/home/mamaearth/it_config.php"; // For Live
// require_once "../../it_config.php"; // For Local
 
require_once "lib/db/DBConn.php";
require_once "lib/logger/clsLogger.php";
require_once "lib/core/Constants.php";
require_once "lib/core/strutil.php";
//require_once "lib/serverChanges/clsServerChanges.php";
//require_once "lib/securereq/clsSecureReq.php"; //to check requested method is allowed or not
require_once "lib/db/DBLogic.php";
require_once "lib/email/EmailHelper.php";

$response = curl_exec($curl);
// print_r($response);
// exit;
 $dbLogic = new DBLogic(); 
 $db = new DBConn();
    //$data= json_decode($response,true);
    $responseArray = json_decode($response, true); // Decode the JSON response to an associative array

$jsonDataTable = json_decode($responseArray['JsonDataTable'], true);
print_r($jsonDataTable);

curl_close($curl);
//echo $response;
$newdata=array();
  if(isset($jsonDataTable)) {
    echo "<<<<<<<<<<<<<<<<<<<<<<<< START >>>>>>>>>>>>>>>>>>>>>>>>";
    if($jsonDataTable){
        //print_r($data->message);
  $newdata=$jsonDataTable;
        foreach($newdata as $item){
            //print_r($data['d']['results'][$i]);

            $name = trim($item['name']);
            $id = trim($item['id']);
            $code = trim($item['code']);
            $group_name = trim($item['group_name']);
            $group_id = trim($item['group_id']);
            $stock_unit_id = trim($item['stock_unit_id']);
            $barcode = trim($item['barcode']);
            $rack_box = trim($item['rack_box']);
            $hsn_code = ltrim($item['hsn_code']);
           
            
            // if (empty($dist_code) || $dist_code == null || empty($dealer_code) || $dealer_code == null) {
            //     continue;
            // }

            $updated_row = 0;
            
            echo $query = "select id from it_cool_products where code = '$code'";
            $recobj = $db->fetchObject("select id from it_cool_products where code = '$code'");
            if (isset($recobj) && !empty($recobj)) {
//                        echo $query = " update it_rate_master set rate = '$rate' where id = $recobj->id";
//                    echo $query = "update it_cool_products set creationdate='$creation_date', change_date='$change_date', code='$dist_code', name='$dist_name', sales_office_code='$sales_office_code',  branch_code='$branch_code', address='$address', city='$city', pincode='$pincode', state='$state', panno='$panno', email='$email', phone='$phone', gstno='$gstno', tax_category='$tax_category', is_distributor='$is_distributor',  source='$source', lat_long='$lat_long', beat_id='$beat_id', ref_dealer_id='$ref_dealer_id', flag='$flag',  updatetime=now() where id = $recobj->id";
                    echo $query = "update it_cool_products set product_name='$name', product_id='$id', code='$code', group_name='$group_name', group_id='$group_id',  stock_unit_id='$stock_unit_id', barcode='$barcode', rack_box ='$rack_box', hsn_code='$hsn_code',  updatetime=now() where id = $recobj->id";
                    $updated_row = $db->execUpdate($query);
                    if ($updated_row > 0) {
                        $updated_row = $recobj->id;
                    }
            } else {
                //Insert into sfa  db add billing_type_desc
//                echo $query = "insert into it_clients set creationdate='$creation_date', change_date='$change_date', code='$dist_code', name='$dist_name', sales_office_code='$sales_office_code', branch_code='$branch_code', address='$address', city='$city', pincode='$pincode', state='$state', panno='$panno', email='$email', phone='$phone', gstno='$gstno', tax_category='$tax_category', is_distributor='$is_distributor',  source='$source', lat_long='$lat_long', beat_id='$beat_id', ref_dealer_id='$ref_dealer_id', flag='$flag',  createtime=now()";
//                echo $query = "insert into it_clients set creationdate='$creation_date', change_date='$change_date', code='$dist_code', name='$dist_name', sales_office_code='$sales_office_code', branch_code='$branch_code', address='$address', city='$city', pincode='$pincode', state='$state', panno='$panno', email='$email', phone='$phone', gstno='$gstno', tax_category='$tax_category', is_distributor='$is_distributor',  source='$source', lat_long='$lat_long', beat_id='$beat_id', ref_dealer_id='$ref_dealer_id', flag='$flag',  createtime=now()";
                echo $query = "insert into it_cool_products set product_name='$name', product_id='$id', code='$code', group_name='$group_name', group_id='$group_id',  stock_unit_id='$stock_unit_id', barcode='$barcode', rack_box ='$rack_box', hsn_code='$hsn_code',  createtime=now()";
                $updated_row = $db->execInsert($query);
                if($updated_row <= 0){
                    $error[] = array("Db Insertion failed" => "product_code=".$code);
                }
            }

            //insert into it_server_changes
//            if ($updated_row > 0) {
//                $cnt++;
//                $obj = $db->fetchObject("select * from it_rate_master where id = $updated_row");
//                if ($obj) {
//                    $server_ch = json_encode($obj);
//                    $ser_type = DataType::rates;
//                    $serverCh->insert($ser_type, $server_ch, $updated_row);
//                }
//            }
        }
        }
    }else{
        $error['res'] = $jsonDataTable;
        
    }

if (empty($error)) {
    print "<br/>Records added successfully"; 
    $datasent = "Records added successfully";
} else {
    print json_encode($error);
    $datasent = json_encode($error);
    sendMail($emailHelper,json_encode($error),"");
}

$msg = "";
if(isset($response)){
    $msg = $db->safe(trim($msg)) . $db->safe(trim($response));
}
$clsLogger = new clsLogger();
//$apicalled = DEF_SITEURL . "api/cz/pullRateMasterDms.php";
$apicalled = DEF_SITEURL . "api/pullproducts.php";
$datasent = $db->safe(trim($datasent));
$clsLogger->logInfo($msg, $incomingid = false, $apicalled, "", $datasent);
date_default_timezone_set("Asia/Kolkata");
echo "API End Time - ".date("Y-m-d h:i:sa")."<br>";

//function sendMail($emailHelper, $errormsg, $errorJsonString){
//
//    $toArray = array('aashtekar@intouchrewards.com','rghule@intouchrewards.com','schaudhari@intouchrewards.com');
//
//    $subject = "Employee konnect PullClientsDms API Error ".date('Y-m-d');
//    $body = "<div>
//                Dear Team,
//                <p>
//                Please rectify the below errors in Emp connect SAP API for client.
//                </p>
//
//                <b>Error Details</b>
//                <p>".$errormsg."
//                </p>
//
//                <b>Error Json</b>
//                <p>".$errorJsonString."
//                </p>
//
//                <p>
//                <b>NOTE:</b> This is a system generated e-mail and please do not reply.
//                </p>
//                <p>
//                Regards,<br>
//                Intouch consumer Care solutions Pvt. Ltd.<br>
//                http://www.onintouch.com/
//                </p>
//            </div>";
//    $emailHelper->send($toArray, $subject, $body);
//}

?>