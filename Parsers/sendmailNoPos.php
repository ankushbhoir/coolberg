<?php
require_once("../../it_config.php");
require_once "lib/email/EmailHelper.php";
  
    $body = "<br>Dear Sir,<br>";   
    $body .= "No POs received today.";

    $body .= "<br/>";
    $today_dt1 = date('d/m/Y');
    $subject = "VLCC : MTD and Daily report ($today_dt1)";

    $toArray = array("gsalunkhe@intouchrewards.com"                    
//                     "dsonar@intouchrewards.com",		  
//		     "atul.mishra@vlccpersonalcare.com",
//                     "ankit.bhandari@vlccpersonalcare.com",
//                    "igoyal@intouchrewards.com",
//                    "ashutosh.khattar@vlccpersonalcare.com",
//                    "rahul.gupta@vlccpersonalcare.com",
//                    "deepak.jain@vlccpersonalcare.com",
//                    "mdeodhar@intouchrewards.com"
    );
    $emailHelper = new EmailHelper();
    $errormsg = $emailHelper->send($toArray, $subject, $body);
    if ($errormsg != "0") {
        $errors['mail'] = " <br/> Error in sending mail, please try again later.";
        return -1;
    } else {
        return 1;
    }

