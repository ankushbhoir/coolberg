<?php 
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once 'lib/core/strutil.php';
require_once "lib/email/EmailHelper.php";
 $db = new DBConn();
 $ubuarr =array();
 $fpatharr=array();
 $subject = "Unrecognized Chain Found";
        $body = "<br>Hi<br>";
        $body .= "<p>Delivery Address for Purchase Order Not found </p>";
        $body .= '

  <p>Attachment of  Missing BU details</p>';

 $body .= "<p>Kindly Take Action </p>";
 $body .= "<p> <br>Regards,</p>";
 $body .= "<p>Intouch Consumer Care Solutions Pvt Ltd</p>";
$body.='</body>
</html>';

 echo $get_file_name="select id,po_filenames,fullpath from it_po_details where status=0 and datetime LIKE '".date('Y-m-d')."%' and sent=0";
   $issuesobj= $db->fetchAllObjects($get_file_name);
  
    if(isset($issuesobj) && !empty($issuesobj)){
      foreach ($issuesobj as $value) {

      array_push($ubuarr, $value->fullpath);
       $db->execUpdate("update it_po_details set sent=1 where id=$value->id");
    }
    //$dirPPOnm='/home/ttk/public_html/Data/DataFixingXls/DataFixing_2020-09-06.xls';
//  array_push($fpatharr, $dirPPOnm);
// print_r($ubuarr);
  //select * from it_po_details where status=4 and datetime like '2020-09-06%';
       // exit;
//        if(file_exists($zipfilepath)){
//        $body .= "<b><br><br></b>";
//        }
       // $body .= "<b><br><br>Please Find The Attachments</b>";
       // $body .= "<br/>";
         echo $body;
        // exit;
         $toArray = array(
                   "aashtekar@intouchrewards.com",
                   "sarjkunthwar@intouchrewards.com",
                
                "igoyal@intouchrewards.com"
             
                      
        );
             
                      
        //);
//             $toArray = array(
//                    "aashtekar@intouchrewards.com",
                 
//                 "ashtekaraniket@gmail.com"
// //                "ykirad@intouchrewards.com//",
                      
      //  );
       //echo $body;
         $emailHelper = new EmailHelper();
        // $emailHelper->isHTML(TRUE);
        $errormsg = $emailHelper->send($toArray, $subject, $body,$ubuarr);
        if ($errormsg != "0") {
            $errors['mail'] = " <br/> Error in sending mail, please try again later.";
            return -1;
        } 
     
        else{
            print"<br>Mail send successfully";
            return 1;
        } 
    }