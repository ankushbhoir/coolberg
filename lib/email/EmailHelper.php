<?php

// example on using PHPMailer with GMAIL

include("class.phpmailer.php");
include("class.smtp.php"); // note, this is optional - gets called from main class if not already loaded

class EmailHelper {

public function send($toArray, $subject, $body,$attachments=false) {
    $mail             = new PHPMailer();

    $mail->IsSMTP();
    $mail->SMTPAuth   = true;                  // enable SMTP authentication
    $mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
    $mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
    $mail->Port       = 465;                   // set the SMTP port

    $mail->Username   = "testintouch25@gmail.com";  // GMAIL username
    $mail->Password   = "paritxnuifonavjb";            // GMAIL password wrong one for testing so email should not go
    
    $mail->From       = "intouchemailtester@gmail.com";
    $mail->FromName   = "InTouch TTK Portal";
    $mail->Subject    = $subject;
    $mail->WordWrap   = 70; // set word wrap

    $mail->MsgHTML($body);
    
    $mail->AddReplyTo("intouchemailtester@gmail.com", "Intouch Admin");

    
    $mail->IsHTML(true);
     // send as HTML
    if($attachments){

   foreach($attachments as $attachment){
              $mail->AddAttachment($attachment);
            }
        }


    foreach($toArray as $to) {
      $mail->AddAddress($to);
    }
   
    //optional
//    if($ccArray){
//        foreach($ccArray as $cc){
//         $mail->AddCC($cc);
//        }        
//    }
    
   // $mail->IsHTML(true); // send as HTML

    if(!$mail->Send()) {
      return $mail->ErrorInfo;
    } else {
      return 0;
    }
}

}
