<?php
// ini_set('memory_limit', '-1'); //For allowing unlimited memory on server
// ini_set('max_execution_time', 180);  
// ini_set('memory_limit', '-1'); //For allowing unlimited memory on server
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once 'lib/core/strutil.php';
require_once "lib/email/EmailHelper.php";


try {
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
      // $st_dt = "2020-05-26 00:00:00 ";
    $st_dt_db = $db->safe(trim($st_dt));
    $ed_dt = $today_dt . " 23:59:59 ";
    $ed_dt_db = $db->safe(trim($ed_dt));


    //original query mayur
//$st_dt_db=$db->safe('2020-08-30 15:00:00');
//$ed_dt_db=$db->safe('2020-08-30 23:59:59');

       $main_query="select id,invoice_no from it_po where ctime between $st_dt_db and 
         $ed_dt_db and status not in (10,3,7,5,4,8,9,21,6)";
        // exit;
    //$CurPageURL = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];  
//echo $remoteFile="/".SFTP_FOLDER."/processed/";

       $main_query="select id,invoice_no from it_po where status not in (10,3,7,5,4,8,9,21,6)";

         $result_id = $db->getConnection()->query($main_query);
          while ($obj_res = $result_id->fetch_object()) {

            $query="select ip.invoice_no,ip.filename, ip.invoice_date, ip.delivery_date, ip.expiry_date, ip.tqty, ip.tamt,ip.ctime, ip.shipping_id as sid,ipt.mrp, ipt.qty,ipt.tot_qty, ipt.ttk_qty, ipt.ttk_uom,ipt.pack_type as CAR,ipt.cost_price,ipt.vat, ipt.amt, ipt.master_item_id ,imd.id as master_dealer_id,imd.displayname as dealername,imd.show_code as showflag, idt.itemcode as articleno,idt.itemname as description, d.code as distid, d.name as distname, sh.dc_address as address,sh.dc_state as state,sh.dc_city as city,sh.dc_name as name, bu.code as vendorcode,sh.customer_code,ipt.po_itemname as description_po,ipt.po_eancode,idt.is_vlcc, istp.ship_to_party, ivpm.plant, iesm.sku,iesm.category, ipt.po_hsn,istp.emails,imd.abbr,ipt.sgst,ipt.cgst,ipt.igst
    
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
    ip.id = $obj_res->id and
    ivpm.master_dealer_id = imd.id and 
    ivpm.vendor_number = d.code and 
    iesm.ean = ipt.po_eancode and 
    istp.master_dealer_id = imd.id and 
    istp.site = bu.code and 
    istp.category=iesm.category AND
    ip.id=ipt.po_id  AND  
    ip.dist_id=d.id and 
    d.bu_id= bu.id and 
    sh.id = ip.shipping_id and 
    ip.master_dealer_id=imd.id AND 
    idt.id= ipt.dealer_item_id AND 
     
   
    ip.status not in (10,3,7,5,4,8,21,6) and 
    ip.is_active=1 

    order by dealername,ip.invoice_no";
 
    $result = $db->getConnection()->query($query);
     if( $result->num_rows==0)
       
       return 0;

    $srno = 1;
$cdata = '<br>';

                header('Content-Type: application/xml; charset=utf-8');
                $ENVELOPE = new SimpleXMLElement('<?xml version="1.0"?><ENVELOPE></ENVELOPE>');


                
                // $header = $envelope->addChild("HEADER");
                // $header->addChild("TALLYREQUEST", "Import Data");
                // $body = $envelope->addChild("BODY");
                // $importdata = $body->addChild("IMPORTDATA");
                // $reqdesc = $importdata->addChild("REQUESTDESC"); //"REPORTNAME","Vouchers"
                // $reqdesc->addChild("REPORTNAME", "Sales Voucher");
                // $staticvariable = $reqdesc->addChild("STATICVARIABLES");
                // $staticvariable->addChild("SVCURRENTCOMPANY", "Sarotam 2018-19");
                // //echo "gererrere";
                // $reqdata = $importdata->addChild("REQUESTDATA");


    while ($obj = $result->fetch_object()) {


        $filename_arr=array();
        $filename_arr[0]=$obj->plant;
        $filename_arr[1]=$obj->abbr;
        $filename_arr[2]=$obj->invoice_no;
       $emails=$obj->emails;
        // $emails
        if(empty($emails)){
          echo  $query_get_sku_emails = "select emails from it_email_action where id=1";
        $obj_em_sku =   $db->fetchObject($query_get_sku_emails);
       $emails=$obj_em_sku->emails;
        print_r($toArray);
       
         }
       
       $po_fname=$obj->filename;
       $chain_display_name=$obj->dealername;
        $EAN="";
        $sku="";  
        $productgrp=""; 
        $category= "";
        $address = $obj->address;//str_replace("                                          ", " ", $obj->address);
        $itemname_po = $obj->description;
     //   $itemname_po = $obj->description_po;
        $inv_no= (string)$obj->invoice_no;
        $PO_date = explode(" ", $obj->invoice_date);
        if($obj->master_dealer_id==7){

          $obj->delivery_date=$obj->invoice_date;
        }
        $Del_date = explode(" ", $obj->delivery_date);
        $newDate = date("d/m/Y", strtotime($obj->delivery_date));  
        $Exp_date = explode(" ", $obj->expiry_date);
        $newexpDate= date("d/m/Y", strtotime($obj->expiry_date));  
        $email_date = explode(" ", $obj->ctime);
        $new_email_date= date("d/m/Y", strtotime($obj->ctime));
        $po_date123 = date("d/m/Y", strtotime($obj->invoice_date));

           if($obj->master_dealer_id==7){

          $obj->delivery_date=$po_date123;
        }
        $City = strtoupper($obj->city);  // providing city name in capital
        $State = strtoupper($obj->state); //providing state name in capital
        $State_str = str_replace(" ","",$State);
        $is_vlcc = $obj->is_vlcc;
        $flag = " ";
        if($is_vlcc==1){
            $flag = "Y";
        }else if($is_vlcc==2){
            $flag = "N";
        }
        $zone = $db->fetchObject("select r1.name from it_regions r1,it_regions r2 where r1.id=r2.zone_id and replace(UPPER(r2.name),' ','') like '%$State_str%'");
       if(isset($zone) && !empty($zone)){
           $zone_name = $zone->name;
       }else{
           $zone_name = " ";
       }
        
        $mid=$obj->master_item_id;
       //echo "mid------$mid \n";
        if($mid != NULL){
            $mquery="select imt.*, c.category from it_master_items imt, it_category c where imt.id= $mid and imt.category_id= c.id";
//            echo "\n".$mquery."\n";
            $mobj= $db->fetchObject($mquery);
            
            if(isset($mobj)){
                $EAN=$mobj->itemcode;                
                $itemname= $mobj->itemname;               
                $sku=$mobj->sku;  
                $productgrp=$mobj->product_code; 
                $category=$mobj->category;
                $master_mrp=$mobj->mrp;
            }else{
            $EAN = "";
            $itemname = "";
            $sku = "";
            $productgrp="";
            $category="";
            $master_mrp="";
        }
        }else{
            $EAN = "";
            $itemname = "";
            $sku = "";
            $productgrp="";
            $category="";
            $master_mrp="";
        }
        
//        if(trim($EAN)=="" || trim($EAN)==NULL){
//            $EAN = $obj->eancode;
//        }
        
        $sup_id= trim($obj->distid);
        if(trim($obj->showflag == 0)){
            $sup_id=" ";
        }        
        
        //Check for PO Type: Direct or Distributor
//        if(preg_match('/vlcc\s+personal\s+care\s+ltd/i',$obj->invoice_text)==1){
//            $po_type = "Direct";
//          //array_push($arr,$obj->master_dealer_id."<>".$obj->invoice_no);
//        }else{
//            $po_type = "Distributor";
//        }
        
        if(isset($obj->customer_code)){
            $cust_code = $obj->customer_code;
        }else{
            $cust_code = " ";
        }
        $cust_name="";
         if($obj->dealername=='ABRL Super' || $obj->dealername=='ABRL Hyper'){
            $cust_name = "Aditya Birla Retail Ltd.";
        }else if($obj->dealername=='FIORA Hypermarket' || $obj->dealername=='Trent Hypermarket'){
            $cust_name = "Trent Hypermarket Private Limited";
        }else{
            $cust_name = $obj->dealername;
        }
        
         $str = substr($obj->distid,0,1);
      // echo "Vendor code 1st 3 letters: ".$str."of inv no: $inv_no \n";
        if($obj->master_dealer_id == 5){
           if($str == '1'){
               $cust_name = "Reliance Retail";
           }else if($str == '5' || $str == '8'){
               $cust_name = "Reliance CNC";
           }else{
               $cust_name = $obj->dealername;
           }
       }
        
        
          $vendor_name = "";
//        if(stripos($obj->distname,"vlcc")==TRUE){
        if(preg_match('/vlcc\s+personal\s+care/i',$obj->distname)){
            $vendor_name = "VLCC Personal Care Ltd.";
        }else if(preg_match('/Tanya\s+Enterprise/i',$obj->distname)){
            $vendor_name = "Tanya Enterprises";
        }else{
            $vendor_name = $obj->distname;
        }  
        
        //New change 
        if(!isset($Exp_date[0]) || trim($Exp_date[0])==""){
            $Exp_date[0] = $Del_date[0];
        }

    if(trim($Exp_date[0])=='0000-00-00' && $obj->master_dealer_id==8){
            $Exp_date[0] = $Del_date[0];
        }
        
       if(trim($obj->vat)<=0){
            $obj->vat = 18;
        }
        
        if($obj->master_dealer_id==3 || $obj->master_dealer_id==4 || $obj->master_dealer_id==5 || $obj->master_dealer_id==41 || $obj->master_dealer_id==49 || $obj->master_dealer_id==24 || $obj->master_dealer_id==21){
            $amt = $obj->amt;
        }else{
            $amt = round($obj->amt/1.18,2);
        }
        
         if($obj->master_dealer_id==3 || $obj->master_dealer_id==4 || $obj->master_dealer_id==5 || $obj->master_dealer_id==41 || $obj->master_dealer_id==49 || $obj->master_dealer_id==24 || $obj->master_dealer_id==21){
            $inc_amt = round(1.18*$obj->amt,2);
        }else{
            $inc_amt = $obj->amt;
        }

     $City_db = $db->safe($City);
        $cf_loc = $db->fetchObject("select * from it_cfa_location where city=$City_db and active=1");
        
        $cf_location = "";
        $zone_name = "";
        if(isset($cf_loc) && !empty($cf_loc)){
            $cf_location = $cf_loc->cfa_location;
            $zone_name = $cf_loc->zone;
        }
        
        if($obj->master_dealer_id==47){
            $obj->distid = "";
        }
        
        if($obj->master_dealer_id==2){
            if(preg_match('/sf/i',$address)==1){
                $obj->dealername = "Bharti Retail Limited";
            }
        }
                if($obj->master_dealer_id==26){
                $old_price=$obj->cost_price;
                $obj->name='VISHAL MEGA MART PVT LTD';
                if($obj->vat==18)
                {
                   $cal_value=1.18; 
                }
                  if($obj->vat==28)
                {
                   $cal_value=1.28; 
                }
                    if($obj->vat==5)
                {
                   $cal_value=1.05; 
                }
                       if($obj->vat==9)
                {
                   $cal_value=1.18; 
                }
                         if($obj->vat==12)
                {
                   $cal_value=1.12; 
                }

                $obj->cost_price=$obj->cost_price/$cal_value;
               $obj->cost_price=round( $obj->cost_price, 2); 
            }
             
                     if($obj->master_dealer_id==11){
                $old_price=$obj->cost_price;
                $obj->ttk_qty=$obj->qty;
                $obj->name='MAX HYPERMART INDIA PVT.LTD';
                if($obj->vat==18)
                {
                   $cal_value=1.18; 
                }
                  if($obj->vat==28)
                {
                   $cal_value=1.28; 
                }
                    if($obj->vat==5)
                {
                   $cal_value=1.05; 
                }
                       if($obj->vat==9)
                {
                   $cal_value=1.18; 
                }
                         if($obj->vat==12)
                {
                   $cal_value=1.12; 
                }

               

              
            }
  if($obj->master_dealer_id==3){
                $old_price=$obj->cost_price;
                 $obj->ttk_qty=$obj->qty;
                $obj->name='More Retail Private Limited';
                if($obj->vat==18)
                {
                   $cal_value=1.18; 
                }
                  if($obj->vat==28)
                {
                   $cal_value=1.28; 
                }
                    if($obj->vat==5)
                {
                   $cal_value=1.05; 
                }
                       if($obj->vat==9)
                {
                   $cal_value=1.18; 
                }
                         if($obj->vat==12)
                {
                   $cal_value=1.12; 
                }

               $cost_price=$obj->cost_price*$cal_value;
               $cost_price=round( $cost_price, 2); 

              
            }

         //if($obj->master_dealer_id==2){
            if(preg_match('/whsm/i',$address)==1){
                $obj->dealername = "WH Smith";
                $cust_name = ": Travel News Services (India) Pvt Ltd";
            }

            if($obj->master_dealer_id==5){
                $cal_quantity=$obj->qty;
            }
            else{
                
                $cal_quantity=$obj->ttk_qty;
            }

                    if($obj->master_dealer_id==56){
                $old_price=$obj->cost_price;
                
                

              $obj->cost_price=$obj->cost_price/$obj->ttk_qty;
              $obj->cost_price=round( $obj->cost_price, 2); 

              
            }

                 if($obj->master_dealer_id==58){
                $old_price=$obj->cost_price;
                
                
                $obj->ttk_qty=$obj->tot_qty;
              $obj->cost_price=$obj->cost_price*$obj->ttk_qty;
              $obj->cost_price=round( $obj->cost_price, 2); 
              $newDate=$newexpDate;

              
            }
                   if($obj->master_dealer_id==55){
                $old_price=$obj->cost_price;
                
                

              $obj->cost_price=$obj->cost_price/$obj->ttk_qty;
              $obj->cost_price=round( $obj->cost_price, 2); 

              
            }
        // print_r($obj->name);
            $test="\n";
        $obj->ttk_uom='';
        $ENVELOPE->addChild('Sr.No.',$srno);
        $ENVELOPE->addChild('Chain_Name',htmlentities($obj->dealername));
        $ENVELOPE->addChild('Document_Type',"");
        $ENVELOPE->addChild('Site',$obj->vendorcode);
        $ENVELOPE->addChild('TTK_ShipTo_Party',$obj->ship_to_party);
        $ENVELOPE->addChild('EAN_Number',$obj->po_eancode);
        $ENVELOPE->addChild('TTK_SKU',$obj->sku);
        $ENVELOPE->addChild('Delivery_Date',$newDate);
        $ENVELOPE->addChild('Email_Date',$new_email_date);
        $ENVELOPE->addChild('PO_Number',$inv_no);
        $ENVELOPE->addChild('PO_Date',$po_date123);
       
        $ENVELOPE->addChild('Total_Base_Cost',$obj->cost_price);
        if($obj->master_dealer_id==26 ){
        $ENVELOPE->addChild('Net_Price',$old_price);  
        }
        if($obj->master_dealer_id==3){
         $ENVELOPE->addChild('Net_Price', $cost_price);
    }
          if($obj->master_dealer_id==58){
         $ENVELOPE->addChild('Net_Price', $obj->amt);
    }
        else{
        $ENVELOPE->addChild('Net_Price',$obj->cost_price*$cal_quantity);
      }
        $ENVELOPE->addChild('Vendor_Code',$obj->distid);
        $ENVELOPE->addChild('TTK_Plant_Code',$obj->plant);
        $ENVELOPE->addChild('Cust_Article_Number',$obj->articleno);
        $ENVELOPE->addChild('Cust_HSN_Code',$obj->po_hsn);
        if($obj->master_dealer_id==7){
          $ENVELOPE->addChild('Cust_Quantity',$obj->qty*$obj->ttk_qty);

        }else {
        $ENVELOPE->addChild('Cust_Quantity',$obj->qty);
      }
        $ENVELOPE->addChild('Cust_UOM',$obj->CAR);
         if($obj->master_dealer_id==7){
        $ENVELOPE->addChild('TTK_Quantity',$obj->qty*$obj->ttk_qty);
      }
      else
      {
       $ENVELOPE->addChild('TTK_Quantity',$obj->ttk_qty); 
      }
        $ENVELOPE->addChild('TTK_UOM',$obj->ttk_uom);
        $ENVELOPE->addChild('MRP',$obj->mrp);
        $ENVELOPE->addChild('CGST',$obj->cgst);
        $ENVELOPE->addChild('SGST',$obj->sgst);
        $ENVELOPE->addChild('IGST',$obj->igst);
        $ENVELOPE->addChild('UGST',"");
        $ENVELOPE->addChild('CESS',"");


        $srno++;

    }


     $query_get_emails = "select from_email,id from it_po_details where  new_filename='".$po_fname."'";
        $obj_em =   $db->fetchObject($query_get_emails);
$toAackarray=array();
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
        // array_push($toArray, $obj_em->from_email);
       $toAackarray = explode (",", $emails);
       array_push($toAackarray, $obj_em->from_email);
//             $toArray = array(
//                    $matches[0],
                 
//                 "igoyal@intouchrewards.com"
// //                "ykirad@intouchrewards.com//",
                      
//         );
       //echo $body;
       
         $emailHelper = new EmailHelper();
        // $emailHelper->isHTML(TRUE);
        $errormsg = $emailHelper->send($toAackarray, $subject, $body);
        if ($errormsg != "0") {
            $errors['mail'] = " <br/> Error in sending mail, please try again later.";
            //return -1;
        } 

        else{
            print"<br>Mail send successfully";
            $db->execUpdate("update it_po_details set ack=1 where id=$obj_em->id");
            
        } 



    $dom = new DOMDocument('1.0');
$dom->preserveWhiteSpace = false;
$dom->formatOutput = true;
$dom_xml = dom_import_simplexml($ENVELOPE);
$dom_xml = $dom->importNode($dom_xml, true);
$dom_xml = $dom->appendChild($dom_xml);
     $dom->saveXML();
     

    $ndate = date('Ymd');
    $ntime  = date('his');
    $Ext='.xml';
    $name = $filename_arr[0]."_". $filename_arr[1]."_".$filename_arr[2]."_".$ndate."_".$ntime;
    $Email_name=$name . $Ext;
     $Filename = DEF_PARSED_DAILY_EXL_PATH . $name . $Ext;
     
    $dom->save($Filename);


        $subject = "$Email_name";
        $body = "<br>Hi<br>";
        $body .= "<p>The file is created for below mentioned Purchase Orders </p>";
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
  
  <table >
    <tr>
      <th>PO No.</th><th>Chain Name</th>
    </tr>';
    // $get_info="select ip.id,ip.invoice_no,ip.master_dealer_id,m.displayname from it_po ip ,it_master_dealers m where ip.ctime between $st_dt_db and 
    // $ed_dt_db and 
    // ip.status not in (10,3,7,5,4,8,21) and 
    // ip.master_dealer_id=m.id and
    // ip.is_active=1";
      // $result = $db->getConnection()->query($get_info);
      //  while ($obj = $result->fetch_object()) {
   $body.= "<tr>
      <td>$inv_no</td><td>$chain_display_name</td>
    </tr>";
    
  // }
 $body .= '</table>';
 $body .= "<p>Kindly Take Action </p>";
 $body .= "<p> <br>Regards,</p>";
 $body .= "<p>Intouch Consumer Care Solutions Pvt Ltd</p>";
$body.='</body>
</html>';
        
//        if(file_exists($zipfilepath)){
//        $body .= "<b><br><br></b>";
//        }
       // $body .= "<b><br><br>Please Find The Attachments</b>";
       // $body .= "<br/>";
         echo $body;
        // exit;
        // $toArray = array("melvin.sebastian@ttkprestige.com",
        //           "Bhushan@ttkprestige.com",
        //            "aashtekar@intouchrewards.com",
        //            "surya.teja@ttkprestige.com",
        //         "siva@ttkprestige.com",
        //         "igoyal@intouchrewards.com"
             
                      
        //  );
         // echo $obj->emails;
         // exit;
         $toArray = explode (",", $emails);
       //  $toArray = array($emails);
       //echo $body;
   
         $emailHelper = new EmailHelper();
        // $emailHelper->isHTML(TRUE);

        $errormsg = $emailHelper->send($toArray, $subject, $body);
        if ($errormsg != "0") {
            $errors['mail'] = " <br/> Error in sending mail, please try again later.";
            //return -1;
        } 
        else{
         
            print"<br>Mail send successfully";
            $emails='';
            
           //exit;
            } 
 //             $server="arddms.intouchrewards.com";
 // $serverUser="arddms";
 // $serverPassword="smd@dra@2020";
// $file = "LFS_PO_20200831_034813.xml";//tobe uploaded
             $db->execUpdate("update it_po set status=21 where id=$obj_res->id");
             $chain_display_name='';
 $remote_file = "/upload/";

 // set up basic connection
//  echo $conn_id = ftp_connect($ftp_server);
// exit;
//  // login with username and password
//  $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

//  // upload a file
//  if (ftp_put($conn_id, $remote_file, $file, FTP_ASCII)) {
//     echo "successfully uploaded $file\n";
//     exit;
//  } else {
//     echo "There was a problem while uploading $file\n";
//     exit;
   // }
 // close the connection
 
 //ftp_close($conn_id);



$localFile=$Filename;
$remoteFile="/".SFTP_FOLDER."/processed/$Email_name";
$host = "sftp.onintouch.com";
$port = 22;
$user = "arddms";
$pass = "arddms";

$connection = ssh2_connect($host, $port);
ssh2_auth_password($connection, $user, $pass);
$sftp = ssh2_sftp($connection);
//exit;
$stream = fopen("ssh2.sftp://$sftp$remoteFile", 'w');
$file = file_get_contents($localFile);
fwrite($stream, $file);
fclose($stream);
            // exit();
            // return 1;
        
}
   
} catch (Exception $xcp) {
    print($xcp->getMessage());
}
?>



