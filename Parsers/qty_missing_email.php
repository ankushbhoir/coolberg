<?php 
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
  
require_once 'lib/core/strutil.php';
require_once "lib/email/EmailHelper.php";
$db = new DBConn();
$missean=array();


      
        echo $main_query="select id,invoice_no from it_po where ctime like '".date('Y-m-d')."%'  and status =13";
         $result_id = $db->getConnection()->query($main_query);
         if( $result_id->num_rows==0){
        echo "no missing EAN";

       }else{

          while ($obj_res = $result_id->fetch_object()) {

               $po_array=array();
 $subject = "Missing Qantity";
        $body = "<br>Hi<br>";
        $body .= "<p>Qantity is Wront for Purchase Order  </p>";
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
</head>
<body>
  <p>Following Missing Qantity details</p>
  <table >
    <tr>
      <th>PO No.</th><th>EAN</th><th>PO Qantity</th><th>Master Qantity</th><th>Chain Name</th>
    </tr>';
        
            echo $get_from_email="select pd.id,pd.from_email,pd.new_filename,pd.fullpath,p.invoice_no,p.filename from it_po p,it_po_details pd where p.invoice_no='".$obj_res->invoice_no."' and p.filename=pd.new_filename and p.ctime like '".date('Y-m-d')."%' and pd.sent=0;";
               $result_email = $db->getConnection()->query($get_from_email);

                 while ($obj_email = $result_email->fetch_object()) {
                  $missean=array();
                   array_push($missean, $obj_email->fullpath);
                  $toArray=array();
                  $db->execUpdate("update it_po_details set sent=1 where id=$obj_email->id");
                  $get_info="select * from it_missing_qty where created_at like '".date('d-m-Y')."%' and sent=0 and po_no='".$obj_res->invoice_no."'";
    
       $result = $db->getConnection()->query($get_info);
     

     

       while ($obj = $result->fetch_object()) {
      $db->execUpdate("update it_missing_qty set sent=1 where id=$obj->id");
         
   $body.= "<tr>
      <td>$obj->po_no</td><td>$obj->ean</td><td>$obj->poqty</td><td>$obj->master_qty</td><td>$obj->chain_name</td>
    </tr>";



                }
                 $body .= '</table>';
 $body .= "<p>Kindly Take Action </p>";
 $body .= "<p> <br>Regards,</p>";
 $body .= "<p>Intouch Consumer Care Solutions Pvt Ltd</p>";
$body.='</body>
</html>';
echo "\n---------------------------------------------------";
print_r($body);
echo "\n---------------------------------------------------";
$emailHelper = new EmailHelper();
        // $emailHelper->isHTML(TRUE);
        $query_get_sku_emails = "select emails from it_email_action where id=3";
        $obj_em_sku =   $db->fetchObject($query_get_sku_emails);
        $toArray = explode (",", $obj_em_sku->emails);
        array_push($toArray, $obj_email->from_email);
       $errormsg = $emailHelper->send($toArray, $subject, $body,$missean);
        if ($errormsg != "0") {
            $errors['mail'] = " <br/> Error in sending mail, please try again later.";
            //return -1;
        } 

        else{
            print"<br>Mail send successfully";
          // return 1;
        } 
                 }
          }
        }
     