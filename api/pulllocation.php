<?php 
$curl = curl_init();
//2022-11-03
$sdate=date('Y-m-d');
$previousDate = date('Y-m-d', strtotime('-1 day', strtotime($sdate))); // 
curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://service.alignbooks.com/ABDataService.svc/ShortList_Location',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => '{"new_id":"00000000-0000-0000-0000-000000000000"}',
    CURLOPT_HTTPHEADER => array(
        'apikey: d7bb8ac4-c295-11ea-9a09-0050569823ac',
        'username: Yashika@coolberg.in',
        'company_id: d16ab7d6-ba66-4086-9ec0-744e903d7cd6',
        'enterprise_id: 34e809ca-fb88-4a59-a5c8-a036000a444a',
        'user_id: 0380defa-341b-4dc2-ac13-25731c9bd558',
        'Content-Type: application/json'
    ),
));


require_once "/home/mamaearth/it_config.php"; // For Live
//require_once "../../it_config.php"; // For Local
 
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
            $id = trim($item['id']);
            $name = trim($item['name']);
            $code = trim($item['code']);
            $gst_no = trim($item['gst_no']);
            $state_id = trim($item['state_id']);
            
            $updated_row = 0;
            
            echo $query = "select id from it_cool_locations where code = '$code'";
            $recobj = $db->fetchObject("select id from it_cool_locations where code = '$code'");
            if (isset($recobj) && !empty($recobj)) {
                    echo $query = "update it_cool_locations set loc_id='$id', name='$name', code='$code', gst_no='$gst_no', state_id='$state_id', updatetime=now() where id = $recobj->id";
                    $updated_row = $db->execUpdate($query);
                    if ($updated_row > 0) {
                        $updated_row = $recobj->id;
                    }
            } else {
                //Insert into db add coolberg_locations
                echo $query = "insert into it_cool_locations set loc_id='$id', name='$name', code='$code', gst_no='$gst_no', state_id='$state_id',  createtime=now()";
                $updated_row = $db->execInsert($query);
                if($updated_row <= 0){
                    $error[] = array("Db Insertion failed" => "location_code=".$code);
                }
            }
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
$apicalled = DEF_SITEURL . "api/pulllocation.php";
$datasent = $db->safe(trim($datasent));
$clsLogger->logInfo($msg, $incomingid = false, $apicalled, "", $datasent);
date_default_timezone_set("Asia/Kolkata");
echo "API End Time - ".date("Y-m-d h:i:sa")."<br>";

?>