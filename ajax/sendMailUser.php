<?php
require_once "../../it_config.php";
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once ("session_check.php");
require_once "lib/email/EmailHelper.php";

extract($_GET);
//print_r($_GET);
$errors = array();
$success = array();
try {
    $db = new DBConn();
   
$uname = $_GET['uname'];
$flag = $_GET['flag'];
$uname_db = $db->safe($uname);
//echo $flag;
if(trim($flag)==1){
    $result = $db->fetchObject("select id,email from it_users where username=$uname_db and inactive=0");
    if(isset($result) && !empty($result)){ 
        echo json_encode(array("error" => "0", "message" => "Ok"));
    }else{
        echo json_encode(array("error" => "1", "message" => "User does not exist"));
    }
}else if(trim($flag)==2){
    $result = $db->fetchObject("select id,email,name from it_users where username=$uname_db and inactive=0");
    session_destroy();
    if(isset($result) && !empty($result)){  
    $emailHelper = new EmailHelper();
    $userid = $result->id;
    $vcode = generateRandomString(10);    
    $dbvcode = $db->safe($vcode); 
    $password_hash = password_hash($vcode, PASSWORD_BCRYPT);
   // $md_pwd = md5($dbvcode);
    if(isset($result->email) && $result->email != null){
        $toArray = array($result->email);
    }else{
//        $toArray = array('sambit@ttkprestige.com','geethanjali@ttkprestige.com');
    }
    
  //  echo DEF_SITEURL."resetpass/auth=".$userid.":".$vcode;
  //  print_r($toArray);
    $subject = "Email OTP to Reset Password";
    $body = "<div>    
    Dear $result->name,
    <p>    
        You have requested a password reset, Your Email OTP is $vcode
    </p>
    <p>
    Please use this OTP for your login. You can change password by <b>My settings</b> menu from your login. 
    Please ignore this email if you did not request a password change.
    </p>

    <p>
    NOTE: This is a system generated e-mail and please do not reply.
    </p>
    
<p>
Regards,<br>
Intouch consumer Care solutions Pvt Ltd<br>
http://www.onintouch.com/
</p>
</div>";
    
    $errormsg = $emailHelper->send($toArray, $subject, $body);
    if ($errormsg != "0") {
        $errors['mail'] = " <br/> Error in sending mail, please try again later.";
    }
//    $db->execUpdate("update it_users set password=md5($dbvcode),password_updated_at=now() where id=$userid");
    $db->execUpdate("update it_users set password='$password_hash',password_updated_at=now() where id=$userid");
    if(isset($result->email) && $result->email != null){
        echo json_encode(array("error" => "0", "message" => "Email has been sent to reset password."));
    }else{
//        echo json_encode(array("error" => "0", "message" => "Email has been sent to TTK HO. As your id does not have Email Id"));
    }
//    $to = array('sambit@ttkprestige.com','geethanjali@ttkprestige.com');
//    $emailHelper->send($to, $subject, $body);

    
}else{
    echo json_encode(array("error" => "1", "message" => "User does not exist"));
}
}
  
} catch (Exception $xcp) {
    echo json_encode(array("error" => "1", "message" => "Something went wrong. Try again."));
}

function generateRandomString($length) {
//    $characters = 'bcBCT5VWEvwxyzNXYopqrsDRSdefAO34QtuZ67F0IJKL12ijklmn89aGHMghPU';
//    $randomString = '';
//    for ($i = 0; $i < $length; $i++) {
//        $randomString .= $characters[rand(0, strlen($characters) - 1)];
//    }
//    return $randomString;
    
    $lowercase = 'abcdefghijklmnopqrstuvwxyz';
    $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $digits = '0123456789';
    $special_characters = '!@#$%^&*()-_=+[]{}|;:,.<>?';

    $randomString = '';

    // Generate at least 1 lowercase character
    $randomString .= $lowercase[rand(0, strlen($lowercase) - 1)];

    // Generate at least 1 uppercase character
    $randomString .= $uppercase[rand(0, strlen($uppercase) - 1)];

    // Generate at least 1 digit
    $randomString .= $digits[rand(0, strlen($digits) - 1)];

    // Generate at least 1 special character
    $randomString .= $special_characters[rand(0, strlen($special_characters) - 1)];

    // Generate the remaining characters to reach a minimum length of 8
    $remaining_length = $length - 4; // 4 characters are added, need 4 more
    for ($i = 0; $i < $remaining_length; $i++) {
        $characters = $lowercase . $uppercase . $digits . $special_characters;
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }

    // Shuffle the generated randomString to randomize character positions
    $randomString = str_shuffle($randomString);

    return $randomString;
}
?>
