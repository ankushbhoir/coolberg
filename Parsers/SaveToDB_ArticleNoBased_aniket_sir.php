<?php
require_once "/var/www/html/vlcc_dt/home/lib/db/DBConn.php";
require_once "/var/www/html/vlcc_dt/home/lib/php/Classes/PHPExcel.php";
require_once "/var/www/html/vlcc_dt/home/lib/core/Constants.php";
require_once '/var/www/html/vlcc_dt/home/lib/php/Classes/PHPExcel/Writer/Excel2007.php';
require_once "chkDuplicate.php";

//require_once "INF.php";
date_default_timezone_set('Asia/Kolkata');

   
function printItemsArticleNoBased($header,$items,$invtext,$iniid,$filename,$shipping_address,$shipping_id) {
    print"<br>IN SAVETODB_ARTICLENOBASED_copy.PHP<br>";
    //$parse_dttm = date('dd/mm/yyyy h:i:s a');
    $parse_dttm = date('d-M-Y h:i:s a');
    $resp = printItemsToConsoleArticleNoBased($header,$items,$invtext,$iniid,$filename,$shipping_address,$shipping_id);

    return $resp;
}

function printItemsToConsoleArticleNoBased($header,$items,$invtext,$iniid,$filename,$shipping_address,$shipping_id) {
    $db = new DBConn();                                                                                                                                                                                                                                                                                                                          
    $parse_dttm = date('d/M/Y h:i:s a');
    $docItems = array();
    $matches=array();
    $notification = "::-1";
    $PO_Date = trim($header->PO_Date);
    print"<br>PO_DasasATE- $PO_Date<br>";
    echo "Date format: $header->PO_DateFormat<br>";
    $Delivery_Date=trim($header->Delivery_Date);
    print"<br>delivery_date=$Delivery_Date<br>";
    if(isset($header->Expiry_Date)){
    $Expiry_Date = trim($header->Expiry_Date);
    }
    print"<br>Expiry_DATE- $Expiry_Date<br>";
    $invoicetext=$invtext;
    //print"$invoicetext";
    if((trim($header->PO_Date)!="")){//print"in PODate";
        if (isset($header->PO_DateFormat)) {
            if($header->PO_DateFormat=="Ymd"){
                $myDateTime = DateTime::createFromFormat('Ymd', $PO_Date);
                if($myDateTime != FALSE){
                    $PO_Date = $myDateTime->format('Y-m-d');
                }else{
                    //$ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification;//ststus::issue processd

                    $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_PODATE;//ststus::issue processd
                    return $ret;
                }
            }
            else if($header->PO_DateFormat=="d/m/Y"){
                $myDateTime = DateTime::createFromFormat('d/m/Y', $PO_Date);
                if($myDateTime != FALSE){
                    $PO_Date = $myDateTime->format('Y-m-d');
                }else{
                    //$ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification;//ststus::issue processdecho "date is wrong";

                    $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_PODATE;//ststus::issue processd
                    return $ret;
                }      
            }
            else if($header->PO_DateFormat=="d/m/y"){
                $myDateTime = DateTime::createFromFormat('d/m/y', $PO_Date);
                if($myDateTime != FALSE){
                    $PO_Date = $myDateTime->format('Y-m-d');
                }else{
                    //$ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification;//ststus::issue processd
                   
                    $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_PODATE;//ststus::issue processd
                    return $ret;
                }    
            }else if($header->PO_DateFormat=="d/M/Y"){
               // print"in matched date format d/M/Y <br>";
                 $myDateTime = DateTime::createFromFormat('d/M/Y', $PO_Date);
                if($myDateTime != FALSE){
                    $PO_Date = $myDateTime->format('Y-m-d');
                }else{
                   // print"format not matched<br>";
                    //$ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification;//ststus::issue processd

                    $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_PODATE;//ststus::issue processd
                    return $ret;
                }
            }
            else if($header->PO_DateFormat=="d-M-Y"){
               // print"in matched date format d/M/Y <br>";
                 $myDateTime = DateTime::createFromFormat('d-M-Y', $PO_Date);
                if($myDateTime != FALSE){
                    $PO_Date = $myDateTime->format('Y-m-d');
                }else{
                   // print"format not matched<br>";
                    //$ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification;//ststus::issue processd

                    $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_PODATE;//ststus::issue processd
                    return $ret;
                }
            }
            else if($header->PO_DateFormat=="d.m.Y"){// print"in PODateformat";
               print "<br> Here: ".$header->PO_DateFormat;
               echo "dsasdasd".$PO_Date;
               
                 echo $myDateTime = DateTime::createFromFormat('d.m.Y', $PO_Date);
                if($myDateTime != FALSE){
                    echo $PO_Date = $myDateTime->format('Y-m-d');
                }else{
                    //$ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification;//ststus::issue processd
                   
                    $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_PODATE;//ststus::issue processd
                    return $ret;
                }
            }


            else if($header->PO_DateFormat=="d-M-y"){

                 $myDateTime = DateTime::createFromFormat('d-M-y', $PO_Date);
                if($myDateTime != FALSE){
                    $PO_Date = $myDateTime->format('Y-m-d');
                }else{
                    //$ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification;//ststus::issue processd

                    $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_PODATE;//ststus::issue processd
                    return $ret;
                }
            }
            else if($header->PO_DateFormat=="d-M-Y"){
                 $myDateTime = DateTime::createFromFormat('d-M-Y', $PO_Date);
                if($myDateTime != FALSE){
                    $PO_Date = $myDateTime->format('Y-m-d');
                }else{
                    //$ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification;//ststus::issue processd
                    $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_PODATE;//ststus::issue processd
                    return $ret;
                }
            }
              else if($header->PO_DateFormat=="m/d/y"){
                 $myDateTime = DateTime::createFromFormat('m/d/y', $PO_Date);
                if($myDateTime != FALSE){
                    $PO_Date = $myDateTime->format('Y-m-d');
                }else{
                    //$ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification;//ststus::issue processd
                    $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_PODATE;//ststus::issue processd
                    return $ret;
                }
            }
               else if($header->PO_DateFormat=="m/d/Y"){
               //  echo "Inside date";
                 $myDateTime = DateTime::createFromFormat('m/d/Y', $PO_Date);
//                 print_r($myDateTime);
                if($myDateTime != FALSE){
                    $PO_Date = $myDateTime->format('Y-m-d');
                }else{
                    //$ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification;//ststus::issue processd
                    $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_PODATE;//ststus::issue processd
                    return $ret;
                }
            }
            else if($header->PO_DateFormat=="d-m-Y"){
                 "skdshdkjshdkjsdhkhsd";

                $myDateTime = DateTime::createFromFormat('d-m-Y', $PO_Date);
                if($myDateTime != FALSE){
                    $PO_Date = $myDateTime->format('Y-m-d');
                }else{
                    //$ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification;//ststus::issue processd
                    $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_PODATE;//ststus::issue processd
                    return $ret;
                }      
            }else if($header->PO_DateFormat=='*'){
            //    echo "Inside special case<br>";
                preg_match("/(\d\d)\S+(\d\d)\S+(\d{4})/",$PO_Date,$matches122);
              //  print_r($matches122);
                $myDateTime = DateTime::createFromFormat('d-m-Y', $matches122[1]."-".$matches122[2]."-".$matches122[3]);
                if($myDateTime != FALSE){
                    $PO_Date = $myDateTime->format('Y-m-d');
                 //   echo "$PO_Date";
                }else{                    
                    $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_PODATE;
                    return $ret;
                }
            }
        }
    }
    if((trim($header->Delivery_Date)!="")){
        if (isset($header->Delivery_DateFormat)) {
            if($header->Delivery_DateFormat=="Ymd"){
                $myDateTime = DateTime::createFromFormat('Ymd', $Delivery_Date);
                if($myDateTime != FALSE){
                    $Delivery_Date = $myDateTime->format('Y-m-d');
                }else{
                    //$ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification;//ststus::issue processd
                    $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_DELDATE;//ststus::issue processd
                    return $ret;
                }
            }
            else if($header->Delivery_DateFormat=="d/m/Y"){
                $myDateTime = DateTime::createFromFormat('d/m/Y', $Delivery_Date);
                if($myDateTime != FALSE){
                    $Delivery_Date = $myDateTime->format('Y-m-d');
                }else{
                    //$ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification;//ststus::issue processd
                    $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_DELDATE;//ststus::issue processd
                    return $ret;
                }
            }
            else if($header->Delivery_DateFormat=="d/m/y"){
                $myDateTime = DateTime::createFromFormat('d/m/y', $Delivery_Date);
                if($myDateTime != FALSE){
                    $Delivery_Date = $myDateTime->format('Y-m-d');
                }else{
                    //$ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification;//ststus::issue processd
                    $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_DELDATE;//ststus::issue processd
                    return $ret;
                }      
            }
            else if($header->Delivery_DateFormat=="d/M/Y"){
                $myDateTime = DateTime::createFromFormat('d/M/Y', $Delivery_Date);
                if($myDateTime != FALSE){
                    $Delivery_Date = $myDateTime->format('Y-m-d');
                }else{
                    //$ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification;//ststus::issue processd
                    $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_DELDATE;//ststus::issue processd
                    return $ret;
                }      
            }
             else if($header->Delivery_DateFormat=="d-M-Y"){
                $myDateTime = DateTime::createFromFormat('d-M-Y', $Delivery_Date);
                if($myDateTime != FALSE){
                    $Delivery_Date = $myDateTime->format('Y-m-d');
                }else{
                    //$ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification;//ststus::issue processd
                    $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_DELDATE;//ststus::issue processd
                    return $ret;
                }      
            }
            else if($header->Delivery_DateFormat=="d.m.Y"){
                $myDateTime = DateTime::createFromFormat('d.m.Y', $Delivery_Date);
                if($myDateTime != FALSE){
                    $Delivery_Date = $myDateTime->format('Y-m-d');
                }else{
                    //$ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification;//ststus::issue processd
                    $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_DELDATE;//ststus::issue processd
                    return $ret;
                }
            }
            else if($header->Delivery_DateFormat=="d-M-y"){
                $myDateTime = DateTime::createFromFormat('d-M-y', $Delivery_Date);
                if($myDateTime != FALSE){
                    $Delivery_Date = $myDateTime->format('Y-m-d');
                }else{
                    //$ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification;//ststus::issue processd
                    $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_DELDATE;//ststus::issue processd
                    return $ret;
                }
            }
             else if($header->Delivery_DateFormat=="d-M-Y"){
                $myDateTime = DateTime::createFromFormat('d-M-Y', $Delivery_Date);
                if($myDateTime != FALSE){
                    $Delivery_Date = $myDateTime->format('Y-m-d');
                }else{
                    $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification;//ststus::issue processd
                    return $ret;
                }
            }
            else if($header->Delivery_DateFormat=="m/d/y"){
                $myDateTime = DateTime::createFromFormat('m/d/y', $Delivery_Date);
                if($myDateTime != FALSE){
                    $Delivery_Date = $myDateTime->format('Y-m-d');
                }else{
                    //$ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification;//ststus::issue processd
                    $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_DELDATE;//ststus::issue processd
                    return $ret;
                }
            }
           
            else if($header->Delivery_DateFormat=="d-m-Y"){
                $myDateTime = DateTime::createFromFormat('d-m-Y', $Delivery_Date);
                if($myDateTime != FALSE){
                    $Delivery_Date = $myDateTime->format('Y-m-d');
                }else{
                    //$ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification;//ststus::issue processd
                    $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_DELDATE;//ststus::issue processd
                    return $ret;
                }    
            }else if($header->Delivery_DateFormat=='*'){
              //  echo "Inside special case<br>";
                preg_match("/(\d\d)\S+(\d\d)\S+(\d{4})/",$Delivery_Date,$matches122);
             //   print_r($matches122);
                $myDateTime = DateTime::createFromFormat('d-m-Y', $matches122[1]."-".$matches122[2]."-".$matches122[3]);
                if($myDateTime != FALSE){
                    $Delivery_Date = $myDateTime->format('Y-m-d');
                 //   echo "$PO_Date";
                }else{                    
                    $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_DELDATE;
                    return $ret;
                }
            }
        }
    }
    if((trim($header->Delivery_Date)=='-')){
        $Delivery_Date = "";
    }
    if(isset($header->Expiry_Date)){
    if((trim($header->Expiry_Date)!="")){
        if (isset($header->Expiry_DateFormat)) {
            if($header->Expiry_DateFormat=="Ymd"){
                $myDateTime = DateTime::createFromFormat('Ymd', $Expiry_Date);
                if($myDateTime != FALSE){
                    $Expiry_Date = $myDateTime->format('Y-m-d');
                }else{
                    //$ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification;//ststus::issue processd
                    $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_EXPDATE;//ststus::issue processd
                    return $ret;
                }
            }
            else if($header->Expiry_DateFormat=="d/m/Y"){
                $myDateTime = DateTime::createFromFormat('d/m/Y', $Expiry_Date);
                if($myDateTime != FALSE){
                    $Expiry_Date = $myDateTime->format('Y-m-d');
                }else{
                    //$ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification;//ststus::issue processd
                    $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_EXPDATE;//ststus::issue processd
                    return $ret;
                }      
            }
            else if($header->Expiry_DateFormat=="d/m/y"){
                $myDateTime = DateTime::createFromFormat('d/m/y', $Expiry_Date);
                if($myDateTime != FALSE){
                    $Expiry_Date = $myDateTime->format('Y-m-d');
                }else{
                    //$ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification;//ststus::issue processd
                    $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_EXPDATE;//ststus::issue processd
                    return $ret;
                }      
            }
            else if($header->Expiry_DateFormat=="d/M/Y"){
                $myDateTime = DateTime::createFromFormat('d/M/Y', $Expiry_Date);
                if($myDateTime != FALSE){
                    $Expiry_Date = $myDateTime->format('Y-m-d');
                }else{
                    //$ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification;//ststus::issue processd
                    $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_EXPDATE;//ststus::issue processd
                    return $ret;
                }      
            }
              else if($header->Expiry_DateFormat=="d-M-Y"){
                $myDateTime = DateTime::createFromFormat('d-M-Y', $Expiry_Date);
                if($myDateTime != FALSE){
                    $Expiry_Date = $myDateTime->format('Y-m-d');
                }else{
                    //$ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification;//ststus::issue processd
                    $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_EXPDATE;//ststus::issue processd
                    return $ret;
                }      
            }
            else if($header->Expiry_DateFormat=="d.m.Y"){
                 $myDateTime = DateTime::createFromFormat('d.m.Y', $Expiry_Date);
                if($myDateTime != FALSE){
                    $Expiry_Date = $myDateTime->format('Y-m-d');
                }else{
                    //$ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification;//ststus::issue processd
                    $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_EXPDATE;//ststus::issue processd
                    return $ret;
                }
            }
            else if($header->Expiry_DateFormat=="d-M-y"){
                 $myDateTime = DateTime::createFromFormat('d-M-y', $Expiry_Date);
                if($myDateTime != FALSE){
                    $Expiry_Date = $myDateTime->format('Y-m-d');
                }else{
                    //$ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification;//ststus::issue processd
                    $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_EXPDATE;//ststus::issue processd
                    return $ret;
                }
            }
             else if($header->Expiry_DateFormat=="d-M-Y"){
                 $myDateTime = DateTime::createFromFormat('d-M-Y', $Expiry_Date);
                if($myDateTime != FALSE){
                    $Expiry_Date = $myDateTime->format('Y-m-d');
                }else{
                    //$ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification;//ststus::issue processd
                    $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_EXPDATE;//ststus::issue processd
                    return $ret;
                }
            }
             else if($header->Expiry_DateFormat=="m/d/y"){
            //      echo "Inside date1";
                $myDateTime = DateTime::createFromFormat('m/d/y', $Expiry_Date);
                if($myDateTime != FALSE){
                    $Expiry_Date = $myDateTime->format('Y-m-d');
                }else{
                    //$ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification;//ststus::issue processd
                    $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_EXPDATE;//ststus::issue processd
                    return $ret;
                }      
            }          
            else if($header->Expiry_DateFormat=="d-m-Y"){
                $myDateTime = DateTime::createFromFormat('d-m-Y', $Expiry_Date);
                if($myDateTime != FALSE){
                    $Expiry_Date = $myDateTime->format('Y-m-d');
                }else{
                    //$ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification;//ststus::issue processd
                    $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_EXPDATE;//ststus::issue processd
                    return $ret;
                }      
            }
             else if($header->Expiry_DateFormat=="m/d/Y"){
            //     echo "Inside date";
                 $myDateTime = DateTime::createFromFormat('m/d/Y', $Expiry_Date);
//                 print_r($myDateTime);
                if($myDateTime != FALSE){
                    $Expiry_Date = $myDateTime->format('Y-m-d');
                }else{
                    //$ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification;//ststus::issue processd
                    $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_EXPDATE;//ststus::issue processd
                    return $ret;
                }
            }else if($header->Expiry_DateFormat=='*'){
              //  echo "Inside special case<br>";
                preg_match("/(\d\d)\S+(\d\d)\S+(\d{4})/",$Expiry_Date,$matches122);
             //   print_r($matches122);
                $myDateTime = DateTime::createFromFormat('d-m-Y', $matches122[1]."-".$matches122[2]."-".$matches122[3]);
                if($myDateTime != FALSE){
                    $Expiry_Date = $myDateTime->format('Y-m-d');
                 //   echo "$PO_Date";
                }else{                    
                    $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_EXPDATE;
                    return $ret;
                }
            }
        }
    }
    }
    if(trim($header->Expiry_Date)=='-'){
         $Expiry_Date="";
    }
    if(trim($header->PO_Date)==""){
            print"Missing PO Date <br> "; $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_PODATE;
            //return $ret;
           
    }else{
            print "<br>PO Date=>".$PO_Date."<br>";
    }

    if(trim($Delivery_Date)=="" || trim($Delivery_Date) == null){
            print"Missing Delivery Date <br> ";
    } else {
            print "Delivery Date=>".$Delivery_Date."<br>";
             $Delivery_Date_db = $db->safe(trim($Delivery_Date));
            $delv_dt_cls = ",delivery_date=$Delivery_Date_db";
    }      
   
   
    /*else if(isset($header->Delivery_Date)){
        $header->Expiry_Date = $Delivery_Date;
        $Delivery_Date_db = $db->safe(trim($Delivery_Date));
        $exp_dt_cls = ",expiry_date=$Delivery_Date_db";
    }*/
     $PONO=" ";
    if(trim($header->PO_No)==""){
            print" Missing PO No <br>  ";
            $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_PONO;
            return $ret;
    } else {
           // print $header->PO_No;
            $PONO= str_replace(":"," ",trim($header->PO_No));
            print "PO_No=>$PONO<br>";
    }
    if(trim($header->PO_Type)==""){
            //print" Missing PO_Type <br>  ";
    } else {
            print "PO_Type=>".$header->PO_Type."<br>";
    }
    if(trim($header->PO_Name)==""){
           // print" Missing PO_Name <br>  ";
    } else {
            print "PO_Name=>".$header->PO_Name."<br>";
    }
    if(trim($header->Purchase_Group)==""){
           // print" Missing Purchase_Group <br>  ";
    } else {
            print "Purchase_Group=>".$header->Purchase_Group."<br>";
    }
    if(trim($header->PO_Currency)==""){
            //print" Missing PO_Currency <br>  ";
    } else {
            print "PO_Currency=>".$header->PO_Currency."<br>";
    }
    if(trim($header->Type)==""){
           // print" Missing Type <br>  ";
    } else {
            print "Type=>".$header->Type."<br>";
    }
    // print"<br>tin1**********=$header->Vat_Tin1<br>";
    if(trim($header->Vat_Tin)==""){    
        if(isset($header->Vat_Tin1) && trim($header->Vat_Tin1 !="")){
             $Tin=trim($header->Vat_Tin1);
if(preg_match("/(\d\S*)\s+PO\s+No/",$Tin,$mtch)){
            $Tin = trim($mtch[1]);
        }    
             print "Vat_Tin=>".$Tin."<br>";
        }else{
        print" Missing Vat_Tin <br>  ";
        //$ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_UNIQUEID;
        //return $ret;
        }
    } else {
        $Tin=trim($header->Vat_Tin);
        if(preg_match("/(\d\S*)\s+PO\s+No/",$Tin,$mtch)){
            $Tin = trim($mtch[1]);
         }      
            print "Vat_Tin=>".$Tin."<br>";
    }
    $inv_qnt=count($items);
    if($inv_qnt==""){
            print" Missing Invoice Items <br>  ";
    } else {
            print "Invoice Quantity=>".$inv_qnt."<br>";
    }
    $DealerName=$header->DealerName;
    //print"$DealerName";
    if(trim($header->DealerName)==""){
            print" Missing Dealer Name <br> ";
    } else {
            print "Dealer Name=>".$header->DealerName."<br>";    
    }
   
    $dealer=$db->safe(trim($DealerName));
   
    $masterdealerid=0;
    $q="select * from it_master_dealers where name = $dealer";
  // print "<br>MDQRY: $q<br>";
    $dealerinfo = $db->fetchObject($q);//get dealer id from master_dealer table
    if($dealerinfo){
            $masterdealerid = $dealerinfo->id;
            print"<br>masterdealerid= $masterdealerid";
    }
   
      //Expiry date compulsary - Future retail,max,abrl
    $dealer_arr = array(2,3,4,11);
    if(in_array($masterdealerid,$dealer_arr)){
        if(trim($header->Expiry_Date)=="" || !isset($header->Expiry_Date)){
              print"Missing Expiry Date <br> "; $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_EXPDATE;
              return $ret;
        }else{
             print "Expiry Date=>".$Expiry_Date."<br>";
                $Expiry_Date_db = $db->safe(trim($Expiry_Date));
                $exp_dt_cls = ",expiry_date=$Expiry_Date_db";
        }
    }else{
         if(isset($header->Expiry_Date) && trim($header->Expiry_Date)!="" && trim($header->Expiry_Date)!="-"){        
                print "Expiry Date=>".$Expiry_Date."<br>";
                $Expiry_Date_db = $db->safe(trim($Expiry_Date));
                $exp_dt_cls = ",expiry_date=$Expiry_Date_db";  

    }else if(trim($header->Expiry_Date)=="" || !isset($header->Expiry_Date)){
            if(trim($header->Delivery_Date)==""){
                  print"Missing Expiry Date <br> "; $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_EXPDATE;
                    return $ret;
            }else{
               echo "Expiry date is not present so take delivery date as expiry date<br>";
                $Delivery_Date_db = $db->safe(trim($Delivery_Date));
                $exp_dt_cls = ",expiry_date=$Delivery_Date_db";
            }                    
    }
    }
   
   
   
   // if($masterdealerid==11 || $masterdealerid==3 || $masterdealerid==4 || $masterdealerid==7){
       
   
    if(trim($header->DealerCity)==""){
            print" Missing Dealer City <br> ";
    } else {
        $Dealer_address=trim($header->DealerCity);
        print "Dealer Address=>".$Dealer_address."<br>";
    }
    //Chnages done by Nivedita for Metro pdf case 06072018
    if(trim($header->DealerCode)==""){
            print" Missing Dealer code <br> ";
    } else {
        $Dealer_code=trim($header->DealerCode);
if(preg_match("/(\d\S*)\s+PO\s+No/",$Dealer_code,$mtch)){
            $Dealer_code = trim($mtch[1]);
        }
        print "Dealer Code=>".$Dealer_code."<br>";
    }
    if(!(trim($header->Dealer_PhoneNo)=="")){
         print "Dealer PhoneNo=>".$header->Dealer_PhoneNo."<br>";
    }
    $DistName=trim($header->VendorName);
    print  "Vendor Name: $DistName";
   
    if(trim($header->VendorName)==""){
            print" Missing Vendor Name <br> ";
    } else {
            print "Vendor Name=>".$header->VendorName."<br>";
    }

    if(trim($header->VendorAddress)==""){
            print" Missing Vendor Address <br> ";
    } else {
            print "Vendor Address=>".$header->VendorAddress."<br>";
    }
    if(trim($header->VendorCity)==""){
            print" Missing Vendor City <br> ";
    } else {
        //print"$header->VendorCity";
            preg_match("/(\w+\s?\w+)/",trim($header->VendorCity),$matches);
            $City=$matches[0];
            print "Vendor City=>".$City."<br>";
    }
    if(trim($header->VendorState)=="" || trim($header->VendorState)=="-"){
            print" Missing Vendor State <br> ";
    } else {  
        print"$header->VendorState";
            preg_match("/(\w+\s?\w+)/",trim($header->VendorState),$matches);
            print_r($matches);
            $State=$matches[0];
            print "Vendor State=>".$State."<br>";
    }
    if(!(trim($header->Vendor_PhoneNo)=="")){
            print "Vendor PhoneNo=>".$header->Vendor_PhoneNo."<br>";
    }

    $ini_type=0;
    if(trim($header->initype)==""){
            print" Missing ini type <br> ";
    } else {
            $ini_type=trim($header->initype);
            print"initype=$ini_type<br>";
    }

    foreach ($items as $item) { // just to print on screen
   //     if(! empty($item)){
//        print_r($item);
           $totQty = 0;
           $mrp = 0;
           if(! isset($item['MRP'])){
               if(isset($item['MRP2'])){
                $mrp = trim($item['MRP2']);
               }
           }else{
               $mrp = trim($item['MRP']);
           }

           if(isset($item['TQty']) && trim($item['TQty']) != ""){
               $totQty = trim($item['TQty']);
           }else{
                $totQty = trim($item['Qty']);  
           }
                     
         $docItems[] = array(
                   "ArticleNo" => trim(getFieldValue($item,"ArticleNo")),
                   "Itemname" => trim(getFieldValue($item,"Itemname")),
                   "EAN" =>  trim(getFieldValue($item,"EAN")),
                   "CAR" =>trim(getFieldValue($item,"CAR")),
                   "TAX" => trim(getFieldValue($item,"TAX")),
                   "Qty" => (trim(getFieldValue($item,"Qty"))),
                   "TQty" => ($totQty),
                   //"MRP" => trim(getFieldValue($item,"MRP")),
                   "MRP" => ( $mrp),
                   "VAT" =>  (trim(getFieldValue($item,"VAT"))),
                   "Rate" => (trim(getFieldValue($item,"Rate"))),
                   "Amount" => (trim(getFieldValue($item,"Amount"))));  
   // }
    }
               
//      echo "Savetodb_artclenobased items are: ";        
    echo'<pre>'; print_r($docItems); echo '</pre>';
//*******************

    $db = new DBConn();
    $createtime1=date('Y-m-d H:i:s');
    $createtime=$db->safe(trim($createtime1));
    $dealer=$db->safe(trim($DealerName));
    $invtext_db=$db->safe(trim($invtext));
    //echo "select * from it_master_dealers where name=$dealer";
    $masterdealerid=0;
   echo  $q="select * from it_master_dealers where name = $dealer";
   // print "<br>MDQRY: $q<br>";

    $dealerinfo = $db->fetchObject($q);//get dealer id from master_dealer table
    if($dealerinfo){
            $masterdealerid = $dealerinfo->id;
            print"<br>masterdealerid= $masterdealerid";
    }

 if($masterdealerid==14 && trim($Dealer_code)!=""){
       if(strpos($PONO,"/") || strpos($PONO,".")){          
       }else{
           $PONO = trim($Dealer_code).".".trim($PONO);
       }      
    }
    //echo "<br>";

    if(trim($PONO) != "" && trim($PO_Date) != "" ){//&& trim($Delivery_Date)!=""
        $shipping_db = $db->safe(trim($Dealer_address));
        $shipping_address_db = $db->safe(trim($shipping_address));
        $buid=0;
        $no_spaces = str_replace(" ", "", $shipping_address);
        $no_spaces_db = $db->safe(trim($no_spaces));
     //   print "<br> ***********NO SPACE: $no_spaces_db <br>";
        $check = " replace(bu_identifier ,' ','') = $no_spaces_db";
    //    print "<br> ************CHECK : $check <br>";
        $selbuQry="select * from  it_business_unit where $check ";
        //echo "<br>$selbuQry";

        $buinfo = $db->fetchObject($selbuQry);
       // print_r($buinfo);
       // exit;
        $Dealer_code_db="";
         if(isset($Dealer_code) && trim($Dealer_code)!=""){
                $Dealer_code_db = $db->safe($Dealer_code);                
            }else{
                $Dealer_code_db = "NULL";
            }
        if(isset($buinfo)){
                $buid = $buinfo->id;
               print"<br>if-get id= $buid";

                //Chnages done by Nivedita 06072018

                $db->execUpdate("update it_business_unit set code=$Dealer_code_db where id=$buid");
               // echo "update it_business_unit set code=$Dealer_code_db where id=$buid";
               
        }
        else{
            $clause_code = "";            
            if(isset($Dealer_code) && trim($Dealer_code)!=""){
                $Dealer_code_db = $db->safe($Dealer_code);
                $clause_code = ",code=$Dealer_code_db";
               // echo "something fuzzy....";
                //exit;
            }


           
            echo  $insbuQry = "insert into it_business_unit set bu_identifier = $no_spaces_db, bu_address=$shipping_address_db,master_dealer_id=$masterdealerid, createtime = $createtime $clause_code";
             // echo "<br>$insbuQry";
              //exit;
            $buid= $db->execInsert($insbuQry);
          //  print"<br>else inserted bunit id----$buid";
        }
    }
    else{
        print"<br>PO not Inserted to DB no po number and date<br>";
        //$ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification;//ststus::issue processd
        $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_PONODADTE;//ststus::issue processd
        return $ret;
    }
   
    $distid=0;

    $DistName;
   
    $dist=$db->safe(trim($DistName));

    $addClause="";
    $qClause = "";
    $idClause = "";
        if( isset($Tin) && trim($Tin)!=""){
            $tin_db = $db->safe(trim($Tin));
             $addClause="  and code = $tin_db";
             $qClause = " , code = $tin_db";
           //  if(is_numeric(trim($Tin))){
                  $idClause = ", supplier_id= $tin_db";
           //  }
        }
   
     echo  $qdist="select * from it_distributors where bu_id= $buid $addClause"  ;
   // echo "<br>$qdist";
   
    $distinfo = $db->fetchObject("$qdist");//get distributor id if not then insert distributor
    if(isset($distinfo)){
            $distid = $distinfo->id;
          // print"<br>if-get id= $distid";
           //exit;
    }
    else{
        //insert distributor in db
       

        $header->VendorAddress = trim($header->VendorAddress);
         
             $addr_db = $db->safe(trim($header->VendorAddress));
             $cClause = "";
             if(isset($City) && trim($City)!=""){
               $city_db = $db->safe(trim($City));  
               $cClause .= " ,city =  $city_db ";
             }
             if(isset($State) && trim($State)!=""){
                $state_db = $db->safe(trim($State));
                $cClause .= " ,state = $state_db ";
             }
             
             
            $shipping_db = $db->safe(trim($Dealer_address));
            $shipping_address_db = $db->safe(trim($shipping_address));
         
            echo  $q = "insert into it_distributors set name = $dist ,address = $addr_db ,ini_type = $ini_type ,shipping_address = $shipping_address_db, bu_id = $buid, createtime = $createtime $qClause $cClause $idClause";
           
           
            $distid= $db->execInsert($q);
        //    print"<br>else inserted dist id----$distid";
    }
 $distdealerid=0;
 $Dealer_address=$db->safe(trim($Dealer_address));

 print "<br>DIST ID: $distid :: MDID :: $masterdealerid :: DDID: $distdealerid <br>";

 if($distid>0 && $masterdealerid>0){// && $distdealerid>0){
      //  print "<br>IN IF p2<br>";
        $makeentry = 1;
        $PONO_db=$db->safe(trim($PONO));
        $PO_Date_db=$db->safe(trim($PO_Date));
        // $Delivery_Date_db=$db->safe(trim($Delivery_Date));
        // $Expiry_Date_db=$db->safe(trim($Expiry_Date));
        $po_amt=0;
        $po_qty=0;
        $poid=0;
        $insid = 0;      
        $norowupdate=0;
        $filenameparts=array();
        $filenamepartsrev=array();
      //   print"<br>Filename=$filename<br>";
        $filenameparts= explode("/",$filename);
        $filenamepartsrev= array_reverse($filenameparts);
        $id_fname=$filenamepartsrev[0];
        //$makeentry=1;
        $filename_db=$db->safe(trim($id_fname));
        $query = "select * from it_po where invoice_no= $PONO_db and dist_id= $distid and master_dealer_id = $masterdealerid and (status= ".POStatus::STATUS_PROCESSED ." || status= ".POStatus::STATUS_ARTICLE_NO_MISSING.")" ;
        print"<br> PO query :$query<br>";
       
         $poinfo = $db->fetchObject($query);
           if(isset($poinfo)){

                print"<br>PO already Exist<br>";
                $cd = new chkDuplicate();
                $makeentry= $cd->process($poinfo,$filename_db,$masterdealerid);
             //   print"<br>make entry= $makeentry<br>";
            }
            if($makeentry==1){
                print"<br>in make entry<br>";
               
               
                if(trim($PONO) != "" && trim($PO_Date) != "" ){ //&& trim($Delivery_Date)!=""
                //    print"<br>PO Inserted to DB<br>";

                    $query ="INSERT INTO it_po set filename=$filename_db, status= ".POStatus::STATUS_PROCESSED .",status_msg='" . POStatus::getStatusMsg(POStatus::STATUS_PROCESSED) ."',dist_id=$distid,master_dealer_id=$masterdealerid,ini_id=$iniid,shipping_id=$shipping_id ,invoice_text=$invtext_db,invoice_no=$PONO_db,invoice_date=$PO_Date_db,tqty=$po_qty,tamt=$po_amt,ctime=$createtime $exp_dt_cls $delv_dt_cls";
                  //print "<br>QRY: $query";
                  //exit;
                    $poid= $db->execInsert($query);
                   // print "<br>POID====$poid<br>";
                    //print"<br>PO Inserted to DB<br>";
                    //exit;
               //     if($poid > 0){print"<br>PO Inserted to DB<br>";}else{print"<br>PO Insertion to DB Query failed<br>";}
                }else{
                 //   print"<br>PO not Inserted to DB no po number and date<br>";
                    //$ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification;//ststus::issue processd
                    $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_PONODADTE;//ststus::issue processd
                    return $ret;
                }
            }else{
                 $ret = POStatus::STATUS_DUPLICATE_PO.$notification;//ststus::issue processd
                 return $ret;
            }
           // echo ">>>>>>>>>>".$poid;
            //exit;
            if($poid>0){
             //   print"<br>poid=$poid";
                $itemntfound= array();
                $filename= $header->DealerName."_".$header->VendorName."_".$createtime;
                //print"<br>$filename<br>";
                $index=0;
                $cntitm=0;
                $vat_amt = 0;
                $tvat_amt = 0;
                foreach ($items as $item) {
                  //  print_r($item);
                        $totQty = 0;
                        $mrp = 0;
                        $insid = 0;
                        if(! isset($item['MRP'])){
                            $mrp =  str_replace(",","",trim($item['MRP2']));
                        }else{
                            $mrp = str_replace(",","",trim($item['MRP']));
                        }

                        if(isset($item['TQty']) && trim($item['TQty']) != ""){
                            $totQty = trim($item['TQty']);
                        }else{
                             $totQty = trim($item['Qty']);  
                        }

                        $ArticleNo = trim(getFieldValue($item,"ArticleNo"));
                        if(isset($item["EAN"])){
                            $EAN = trim(getFieldValue($item,"EAN"));
                        }
                        //$Itemname=trim(substr(getFieldValue($item, "Itemname"),0,3));
                        $itemname_po = trim(getFieldValue($item, "Itemname"));
                        $Itemnamefull=trim(getFieldVal($item,"Itemname"));
                        $CAR = trim(getFieldValue($item,"CAR"));
                        $TAX  = trim(getFieldValue($item,"TAX"));
                        $Qty = doubleval(str_replace(",","",trim(getFieldValue($item,"Qty"))));
                        $totQty = doubleval(str_replace(",","",trim($totQty)));
                        $MRP = doubleval(str_replace(",","",trim($mrp)));
                       // $VAT = trim(getFieldValue($item,"VAT"));
                        $VAT = doubleval(str_replace(",","",trim(getFieldValue($item,"VAT"))));
                        $Rate = doubleval(str_replace(",","",trim(getFieldValue($item,"Rate"))));
                        $Amount = doubleval(str_replace(",","",trim(getFieldValue($item,"Amount"))));      
                         
                      //  print"<br>itemname=$itemname_po<br>";
                        $itemname_db = $db->safe($itemname_po);
                        $artno_db = $db->safe(trim($ArticleNo));
                    //    print"<br>ArticleNo=$artno_db";
                        $dealer_item_id=0;
                       
                        $vmart_clause = "";
                        if(trim($ArticleNo)=="-" && trim($EAN)=="-" && $masterdealerid==49){
                            $vmart_clause = " and itemname=$itemname_db ";
                        }
                       
                        $getitemQuery = "select * from it_dealer_items where itemcode= $artno_db  and master_dealer_id=$masterdealerid $vmart_clause";
                        print "<br>ITMQRY: $getitemQuery <br>";
                        $dealeriteminfo=$db->fetchObject($getitemQuery);// and is wkf=1
                                               
                        if(isset($dealeriteminfo)){
                            $dealer_item_id = $dealeriteminfo->id;                                                    
                            $itemname_db = $db->safe($itemname_po);//    
                            if(trim($dealeriteminfo->itemname)=="" || trim($dealeriteminfo->itemname)==NULL){
                                $db->execUpdate("update it_dealer_items set itemname=$itemname_db,updatetime=now() where id=$dealer_item_id");
                            }                              
                          //  $db->execUpdate("update it_dealer_items set itemname=$itemname_db,updatetime=now() where id=$dealer_item_id");
                         //   echo "<br><br>**********************update it_dealer_items set itemname=$itemname_db,updatetime=now() where id=$dealer_item_id****************<br><br>";                                  
                        }else{                                                                                    
                            $itemname_db = $db->safe($itemname_po);                              
                            $insitemQuery="insert into it_dealer_items set distid=$distid, master_dealer_id= $masterdealerid , itemcode= $artno_db,itemname=$itemname_db , is_vlcc = 0 ,is_NotFound=1, createtime = $createtime";
                            print "<br>INSQRY: $insitemQuery <br>";
                            $insid = $db->execInsert($insitemQuery);
                       //      print "<br>INISID 1: $insid<br>";
                           
                            $getitemQuery = "select * from it_dealer_items where id=$insid";
                     //   print "<br>ITMQRY after inserting into it_dealer_items: $getitemQuery <br>";
                       
                        $dealeriteminfo=$db->fetchObject($getitemQuery);
                            $dealer_item_id = $dealeriteminfo->id;
//                             $dealer_item_id = $insid;
                                                                                       
                        }
                       
                     //   echo "Amount: $Amount<br>";
                                               
                        $addval="";
                        $vatarr= array(0,5,12,18,28);   //IGST %
                                if($VAT!=""){
                                    if(! in_array($VAT, $vatarr)){
                                        $VAT=$VAT*2;        
                                    }
                                    $addval =",vat = $VAT";
                              //      print"addval=$addval";
                                }else{
                                    $addval =",vat = 0";
                                }
                                if($Rate!=""){
                                    $addval .=",cost_price = $Rate";
                            //        print"addval=$addval";
                                }
                        //       if($Amount!="" && $Amount!=0){  
                                 //   if($masterdealerid==5 || $masterdealerid==26){//New change only for Reliance and Vishal
                                      //  if(($masterdealerid==5 || $masterdealerid==26) || $masterdealerid==8 || $masterdealerid==24 || $masterdealerid==21){
/* if(($masterdealerid==26) || $masterdealerid==8 || $masterdealerid==24 || $masterdealerid==21){
                                          //  if($VAT > 18){
                                           // $VAT = $VAT/2;
                                            $val = round($Amount*$VAT/100,2);
                                            $Amount = $Amount+$val;
                                      //  }else{
                                         //   $Amount = round($Amount*(1+$VAT/100),2);  
                                       // }
                                     
                                        }*/
                                  //  }
                                  $vat_amt = $Amount*$VAT/100;
                                    $addval .=",amt = $Amount";  
                                     $addval .=",tvat_amt = $vat_amt";  
                          /*       //   print"addval=$addval";
                                }else{
                                    $updtquery="update it_po set status= ". POStatus::STATUS_ISSUE_AT_PROCESSING .",status_msg='" . POStatus::getStatusMsg( POStatus::STATUS_ISSUE_AT_PROCESSING) ."',issue_reason='".IssueReason::getIssueMsg(IssueReason::MISSING_AMT)."' where id=$poid";
                                    print $updtquery;
                                    $db->execQuery($updtquery);                                      
                                    $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_AMT;
                                    return $ret;
                                }*/
                       
                        if(isset($dealeriteminfo)){                                                        
                            $EANcode="";
                            $master_item_id=0;  
                            $EANcode=$dealeriteminfo->eancode;
                           
                            if(isset($EANcode) && trim($EANcode)!=""){
                                $EANcode_db = $db->safe($EANcode);
                               
                                    $getMasterItemInfoqry = "select * from it_master_items where itemcode=$EANcode_db";                                
                                    $getMasterItemInfoobj=$db->fetchObject($getMasterItemInfoqry);

                                    if(isset($getMasterItemInfoobj)){
                                    $master_item_id=$getMasterItemInfoobj->id;

                                    $itemname_db = $db->safe($itemname_po);
                               //     print"<br>MasterITEMID=$master_item_id<br>";      
                                    $uquery="update it_dealer_items set master_item_id= $master_item_id,is_vlcc=1,is_NotFound=0 where id= $dealer_item_id";//$insid
//                                    $uquery="update it_dealer_items set itemname= $itemname_db, master_item_id= $master_item_id,is_vlcc=1,is_NotFound=0 where id= $dealer_item_id";//$insid
                                    $db->execUpdate($uquery);
                                //    print"itemname update0:$uquery<br>";
                                }else{
                                    $itemname_db = $db->safe($itemname_po);
                                    $uquery="update it_dealer_items set is_vlcc=0,is_NotFound=1 where id= $dealer_item_id";//$insid
//                                   $uquery="update it_dealer_items set itemname= $itemname_db,is_vlcc=0,is_NotFound=1 where id= $dealer_item_id";//$insid
                                    $norowupdate = $db->execUpdate($uquery);
                                //    print"itemname update1:$uquery<br>";
                                    $cntitm++;
                                }
                            }else{    $itemname_db = $db->safe($itemname_po);
                                     $uquery="update it_dealer_items set is_vlcc=0,is_NotFound=1 where id= $dealer_item_id";//$insid
//                                   $uquery="update it_dealer_items set itemname= $itemname_db,is_vlcc=0,is_NotFound=1 where id= $dealer_item_id";//$insid
//                                $norowupdff = $db->execUpdate($uquery);
                                   $norowupdate = $db->execUpdate($uquery);
                              //  print"itemname update2:$uquery<br>";
                                $cntitm++;
                            }
                           
                            /*else{
                                 $updatequery="update it_po set status= ". POStatus::STATUS_ARTICLE_NO_MISSING.",status_msg='" . POStatus::getStatusMsg(POStatus::STATUS_ARTICLE_NO_MISSING) ."' where id=$poid";
                                       print $updatequery;
                                       $db->execQuery($updatequery);
                             }*/
                           
                              $mas_itm_clause = "";
                                if(trim($master_item_id)!="" && trim($master_item_id)>0){
                                    $mas_itm_clause = " , master_item_id = $master_item_id";
                                }
                         
                            if($poid>0 && $dealer_item_id>0){// && $master_item_id>0){                                
                                $car_db = $db->safe(trim($CAR));                                
                               //   echo "PO ean is: $EAN<br>";
                                $eanaddClause = "";
                                if(trim($EAN)!=""){
                                    $EAN_db = $db->safe(trim($EAN));
                                    $eanaddClause = " ,po_eancode=$EAN_db";
                                }
 $itemname_db = $db->safe($itemname_po);//                                                                                                                                  
                                $qpoitem="INSERT INTO it_po_items set po_id = $poid,po_itemname=$itemname_db,dealer_item_id =  $dealer_item_id $eanaddClause $mas_itm_clause ,mrp = $MRP,qty = $Qty, tot_qty = $totQty ,pack_type = $car_db,ctime = $createtime $addval ";
                            ///    echo"<br>insert to poitems=$qpoitem<br>";
                                $poitemid=$db->execInsert($qpoitem);
                                if(trim($poitemid)!=""){
                               //     print"inserted to po_items=====$poitemid";
                                    $po_amt=$po_amt+$Amount;
                                    $po_qty=$po_qty+$totQty;
                                    $tvat_amt = $tvat_amt + $vat_amt;
                                }                                                            
                            }else{                                
                                $updtquery="update it_po set status= ". POStatus::STATUS_ISSUE_AT_PROCESSING .",status_msg='" . POStatus::getStatusMsg( POStatus::STATUS_ISSUE_AT_PROCESSING) ."',issue_reason='".IssueReason::getIssueMsg(IssueReason::ITMNOTFND)."'  where id=$poid";
                          //      print $updtquery;
                                $db->execQuery($updtquery);
                               
                                $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::ITMNOTFND;
                                return $ret;
                            }
                        }else{        
                            $is_vlcc_Clause = "";
                            $mid = "";
                                $cntitm++;

                             if($poid>0){
                           
                               $car_db = $db->safe(trim($CAR));
                               
                             //    echo "PO ean is: $EAN<br>";
                                $eanaddClause = "";
                                if(trim($EAN)!=""){
                                    $EAN_db = $db->safe(trim($EAN));
                                    $eanaddClause = " ,po_eancode=$EAN_db";
                                }
                               
                               $itemname_db = $db->safe($itemname_po);
                               $qpoitem="INSERT INTO it_po_items set po_id = $poid,po_itemname=$itemname_db,dealer_item_id =  $insid $eanaddClause, mrp = $MRP,qty = $Qty, tot_qty = $totQty ,pack_type = $car_db,ctime = $createtime $addval ";
                          //     echo"<br>insert to poitems=$qpoitem<br>";
                               $poitemid=$db->execInsert($qpoitem);  
                               $po_amt=$po_amt+$Amount;
                               $po_qty=$po_qty+$totQty;
                               $tvat_amt = $tvat_amt + $vat_amt;
                            }
                             
                        } // not master item section ends here
                           // $dealer_item_id=" ";      
            }
         $noofitems= count($items);
            //  print"<br>total items=$noofitems <br> ";
           //   print"<br>No of rows updated=$norowupdate <br> ";
           //   print "No of updates: $cntitm<br>";
          //  if($norowupdate>0 && $noofitems >1){
           if($cntitm>0 && $noofitems>1){
                 $notification = "::2";
             //    print "<br>NOTIFICATION UPDATED: $notification <br>";
            }
             if($cntitm==$noofitems){    // all items are ean missing
                 $notification = "";
               //  print "<br>All items in Article number missing<br>";
            }
                    //Update (insert)PO_Amount and PO_quantity in it_po  
                         $po_updt="update it_po set tqty=$po_qty,tamt=$po_amt  ,tvat_amt = $tvat_amt where id = $poid";
                  //      print"<br>add amt & Qty if invoice=$po_updt<br>";
                        $db->execQuery($po_updt);

                        $cntquery="select count(*) as cnt from it_po_items p , it_dealer_items d where p.dealer_item_id = d.id and d.is_vlcc = 1 and po_id=$poid";
                     //   echo"$cntquery";
                        $cntobj=$db->fetchObject($cntquery);
                        if(isset($cntobj)){
                            $itmcnt=$cntobj->cnt;
                        //    print"<br>itemcnt=$itmcnt";
                            if(trim($cntobj->cnt) == 0){
                          //      print "<br>IN ) CNT <br>";
                                //chk if all the items are un defined weikfield item
                                  $uqry = "select count(*) as cnt from it_po_items p , it_dealer_items d where p.dealer_item_id = d.id and d.is_vlcc = 0 and po_id=$poid";
                            //      print "<br>QRY: $uqry<br>";
                                  $ciobj = $db->fetchObject($uqry);
                                  if(isset($ciobj) && trim($ciobj->cnt) > 0){
                                       //$notification="";
                                       $updatequery="update it_po set status= ". POStatus::STATUS_ARTICLE_NO_MISSING.",status_msg='" . POStatus::getStatusMsg(POStatus::STATUS_ARTICLE_NO_MISSING) ."' where id=$poid";
                                   //    print $updatequery;
                                       $db->execQuery($updatequery);
                                       //$ret =  POStatus::STATUS_MISSING_EAN.$notification;//status EAN Missing
                                       $ret=  POStatus::STATUS_ARTICLE_NO_MISSING.$notification;
                                       return $ret;
                                  }else{
                                      //means all items are not weikfield
                                    $updatequery="update it_po set status= ". POStatus::STATUS_NOT_WEIKFIELD .",status_msg='" . POStatus::getStatusMsg( POStatus::STATUS_NOT_WEIKFIELD) ."' where id=$poid";
                                   //  print $updatequery;
                                    $db->execQuery($updatequery);
                                    $ret =  POStatus::STATUS_NOT_WEIKFIELD.$notification;//ststus::not ewikfeild
                                    return $ret;
                                  }
                            }else{
                             //   print "<br><br>";
                                //means contains atlest i weikfield product n processed
                                $ret =  POStatus::STATUS_PROCESSED.$notification;//ststus::processd
                                return $ret;
                            }
                        }else{
                          //$ret =  POStatus::STATUS_PROCESSED.$notification;//ststus::processd
                            //$updtquery="update it_po set status= ". POStatus::STATUS_ISSUE_AT_PROCESSING .",status_msg='" . POStatus::getStatusMsg( POStatus::STATUS_ISSUE_AT_PROCESSING) ."' where id=$poid";
                             $updtquery="update it_po set status= ". POStatus::STATUS_ISSUE_AT_PROCESSING .",status_msg='" . POStatus::getStatusMsg( POStatus::STATUS_ISSUE_AT_PROCESSING) ."',issue_reason='".IssueReason::getIssueMsg(IssueReason::ISWKFQRYFAIL)."'  where id=$poid";
                          //  print $updtquery;
                            $db->execQuery($updtquery);
                            //$ret =  POStatus::STATUS_ISSUE_AT_PROCESSING.$notification;//ststus::processd
                            $ret =  POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::ISWKFQRYFAIL;
                            return $ret;
                        }
                     
            }else {
              //  print"<br>PO not Inserted to DB<br>";
                //$ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification;//ststus::issue processd
                $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_PONODADTE;
                return $ret;
            }        

        }else{          
            //$ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification;//ststus::issue processd
            $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::DBENTRYMISSING;
            return $ret;
        }
}
function getFieldVal($item, $fieldName, $cleanNumber=false) {
if (!isset($item[$fieldName])){
            return "";
        }else{
$val = $item[$fieldName];
        }
if ($cleanNumber) {
$val = preg_replace("/[^0-9.]/", "", $val);
}
return $val;
}