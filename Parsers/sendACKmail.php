<?php 
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once 'lib/core/strutil.php';
require_once "lib/email/EmailHelper.php";
 $db = new DBConn();
 $misssite=array();
   echo $get_file_name="select id,po_filenames,fullpath,from_email from it_po_details where ack=0 and datetime LIKE '".date('Y-m-d')."%' and sent=0";
   $issuesobj= $db->fetchAllObjects($get_file_name);
  
    if(isset($issuesobj) && !empty($issuesobj)){
      foreach ($issuesobj as $value) {

            //  echo $body;
        // exit;

              preg_match('/([a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z]{2,4})/',  $value->from_email,$matches);
        
            //  $toArray = array("");
             
                      //print_r($matches);
                      // exit;
         
 $subject = "Thanks for Submitting the file";
        $body = "<br>Hi<br>";
        $body .= "<p>Please Check File after 15 Minutes </p>";
        $body .= '
<html>
<head>
  <style>
table {
  border-collapse: collapse;
  width:400px;
}

table, td, th {
  border: 1px solid black;  
}
</style>
</head>';


   
 $body .= '</table>';
 $body .= "<p> <br>Regards,</p>";
 $body .= "<p>Intouch Consumer Care Solutions Pvt Ltd</p>";
$body.='</body>
</html>';
        
     
            $toArray = array(
                   $matches[0],
                 
                "aashtekar@intouchrewards.com"
//                
                      
        );
       //echo $body;
         $emailHelper = new EmailHelper();
        // $emailHelper->isHTML(TRUE);
        $errormsg = $emailHelper->send($toArray, $subject, $body,$misssite);
        if ($errormsg != "0") {
            $errors['mail'] = " <br/> Error in sending mail, please try again later.";
            return -1;
        } 

        else{
            print"<br>Mail send successfully";
            $db->execUpdate("update it_po_details set ack=1 where id=$value->id");
            return 1;
        } 
      
    }
  }      
