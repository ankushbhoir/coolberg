<?php
//print_r($_POST);
ob_start();
if (!empty($_POST['storecode']) && !empty($_POST['password'])) {
//    print_r("Hello");exit();
    
    $anticsrf = filter_input(INPUT_POST, 'anticsrf', FILTER_SANITIZE_STRING);
    if (!$anticsrf || !isset($_SESSION['anticsrf']) || $anticsrf !== $_SESSION['anticsrf']) {
        // show an error message
//        echo '<p class="error">Error: invalid form submission</p>';
        $message = "<p class='error'>Error: invalid form submission, Please Try Again</p>";
        $type = "error";
        // return 405 http status code
//        header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
//        exit;
} else {
    require_once "lib/csrf-protection-using-php/SecurityService.php";
        $antiCSRF = new Phppot\SecurityService\securityService();
//        $csrfResponse = $antiCSRF->validate();
//        if (! empty($csrfResponse)) {
    
    
        require_once("../it_config.php");
        require_once("session_check.php");
        require_once("lib/codes/clsCodes.php");
        require_once ("lib/core/Constants.php");
        require_once "lib/db/DBConn.php";
        require_once("lib/db/DBLogic.php");
        require_once "lib/php/Classes/php-csrf.php";
        
        $csrf = new CSRF();


        $allowedMethods = ["POST"];
        $method = $_SERVER['REQUEST_METHOD'];
        if (!isset($allowedMethods)) {
            print "Unauthorized";
            exit;
        }
        if (!in_array($method, $allowedMethods)) {
            http_response_code(400); // Bad Request
            exit;
        }
        
        //Hash checkinng
//        if ($csrf->validate('login-form')) {
            $_POST['storecode'] = filter_var($_POST['storecode'], FILTER_SANITIZE_STRING);
            $_POST['storecode'] = htmlspecialchars($_POST['storecode']);

            if ($_POST['storecode'] == '') {

            } else {
                $errors['password'] = "Incorrect Username/Password";
            }

        extract($_POST);
        $errors = array();
        $db = new DBConn();
        $dbl = new DBLogic();
        $clsLogger = new clsLogger();
        try {
            $storecode = trim($storecode);
            $_SESSION['form_storecode'] = $storecode;
            $storecode = filter_var($storecode, FILTER_SANITIZE_STRING);
            $storecode = htmlspecialchars($storecode);
    //            echo $string;
            $password = urldecode($password);
            if (!$storecode) {
                $errors['storecode'] = 'Enter Username';
            }
            if (!$password) {
                $errors['password'] = 'Enter Password';
            }
            if (count($errors) == 0) {
                $clsCodes = new clsCodes();
                $codeInfo = $clsCodes->isAuthentic($storecode, $password);
                //echo $codeInfo;
                if (!$codeInfo) {
                    $errors['password'] = 'Incorrect Username or Password';
                }
                $_SESSION['currStore'] = $codeInfo;
                $clsLogger->logInfo("Login:$storecode");
            }
        } catch (Exception $xcp) {
            $clsLogger->logError("Failed to login $storecode:" . $xcp->getMessage());
            $errors['status'] = "There was a problem processing your request. Please try again later";
        }
        if (count($errors) > 0) {
            $_SESSION['form_errors'] = $errors;
        } else {
            unset($_SESSION['form_errors']);
        }
        $message = "Hi, we have received your message. Thank you.";
        $type = "success";
        session_write_close();
//        header("Location: " . DEF_SITEURL);
//        exit;
             echo "<script>location.href='".DEF_SITEURL."';</script>";
            // } else {
            //     //enter into log
            // }
//        } else {
//            http_response_code(400); // Bad Request
//            exit;
//        }
        
//    } else {
//            //do something
//            $message = "Security Alert: Unable to process your request.";
//            $type = "error";
//        }
    }

}

require_once "lib/php/Classes/php-csrf.php";

$csrf = new CSRF();
// $csrf = new CSRF(
//     'session-hashes',   // Save hashes on $_SESSION['session-hashes']
//     'hidden-key',       // Print the form input with the name 'hidden-key'
//     2*60,               // Hashes should expire after 2 minutes
//     256                 // Hashes should be 256 chars in size... TOO BIG!
// );

$form_errors = null;
if (isset($_SESSION['form_errors'])) { $form_errors = $_SESSION['form_errors']; }
if ($form_errors && count($form_errors) > 0) {
$form_errors = implode("<br />", $form_errors);
$disp="block";
} else {
$disp="none";
}
$clsLocation = new clsLocation();
$form_storecode = ""; if (isset($_SESSION['form_storecode'])) $form_storecode = $_SESSION['form_storecode'];

$_SESSION['anticsrf'] = md5(uniqid(mt_rand(), true));
//$password_hash = password_hash($vcode, PASSWORD_BCRYPT);
?>
<script>
    $(function(){
        
        $("#processing").hide();
    });
    
    function send_mail(){
                    
       var uname = $("#email").val();
       var anticsrf = $("#anticsrf").val();
       
       if(uname!=""){           
           var ajaxURL = "ajax/sendMailUser.php?uname="+uname+"&flag=1&anticsrf="+anticsrf;   
         
            $.ajax({
                url:ajaxURL,
                dataType: 'json',
                success:function(data){ 
                    if (data.error=="1") {
                        alert(data.message);
                        
                      
                    } else{           
         
           var r = confirm("Your password is reseted and temparory password has been sent to your registered Email.");
       if(r==true){
           $("#processing").show();
       var ajaxURL = "ajax/sendMailUser.php?uname="+uname+"&flag=2";
     
            $.ajax({
                url:ajaxURL,
                dataType: 'json',
                success:function(data){ 
                    if (data.error=="1") {
                        alert(data.message);
                    } else {
                   
                    $("#processing").hide();
                        $("#forgot_pwd_err").text(data.message);
                    
                    }
                }
            });
        }
                }
          }
            });
        }else{
            alert("Enter username");
        }
    
    }
</script>

<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
 <section id="page-content-wrapper" class="main-content">
                <div class="main-content-right-side sing-up">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="sign-up-listing-area">
                                    <h1>INTOUCH PARTNER NETWORK</h1>
                                    <p>Keeping Enterprises Connected</p>
                                    <ul class="sign-up-listing">
                                        <li class="supply-tracking">
                                            <a href="#">Supply <br>Tracking</a>
                                        </li>
                                        <li class="data-management">
                                            <a href="#">Channel Data <br>Management</a>
                                        </li>
                                        <li class="retail-solution">
                                            <a href="#">Retail Mall <br>Solutions</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="sing-up-block">
                                    <h2>Login </h2>
                                    <form method="post" name="storeloginform" action="">
                                        <div class="form-group">
                                            <input type="username" class="form-control" id="email" placeholder="Username" name="storecode" value="<?php echo $form_storecode; ?>" />
                                            
                                            <input type="hidden" size="15" id="anticsrf" name="anticsrf" value="<?php echo $_SESSION['anticsrf'];?>" />
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control" id="password" placeholder="Password" name="password">
                                        </div>
                                        <button type="submit" class="btn btn-default">Login </button> 
                                           
                                        <span class="error" id="slf_status" style="display:<?php echo $disp; ?>;"><?php echo $form_errors ?></span>    
                                        <?php unset($_SESSION['form_errors']); ?>
                                        
                                        <a href="#" onclick="javascript:send_mail();"><u>Forgot Password</u></a><br> 
                                            

                                         <div  class="grid_4" id="processing" style=" width:60%;">Sending Email. Please wait... <img src="images/loading.gif" />
                                         </div>
                                         <span class="error" id="forgot_pwd_err"></span>
                                    </form>
                                    <?php if(!empty($message)) { ?>
                                    <div id="phppot-message" class="<?php echo $type; ?>"><?php if (isset($message)) { ?>
                                                <?php echo $message;
                                            }
                                        } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                    
                </div>
            </section>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>           