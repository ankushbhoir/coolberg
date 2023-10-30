<?php
require_once "lib/db/DBConn.php";
require_once "lib/php/Classes/PHPExcel.php";
require_once "lib/core/Constants.php";
require_once 'lib/php/Classes/PHPExcel/Writer/Excel2007.php';
require_once "chkDuplicate.php";

//require_once "INF.php";
date_default_timezone_set('Asia/Kolkata');

   
function printItemsArticleNoBased($header,$items,$invtext,$iniid,$filename,$shipping_address,$shipping_id) {
    print"<br>IN SAVETODB_ARTICLENOBASED.PHP<br>";
    //$parse_dttm = date('dd/mm/yyyy h:i:s a');
    $parse_dttm = date('d-M-Y h:i:s a');
    $resp = printItemsToConsoleArticleNoBased($header,$items,$invtext,$iniid,$filename,$shipping_address,$shipping_id);
    return $resp;
}

function printItemsToConsoleArticleNoBased($header,$items,$invtext,$iniid,$filename,$shipping_address,$shipping_id) {
    $parse_dttm = date('d/M/Y h:i:s a');
    $docItems = array();
    $matches=array();
    $notification = "::-1";
    $PO_Date = trim($header->PO_Date);
    print"<br>PO_DATE- $PO_Date<br>";
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
                    //$ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification;//ststus::issue processd
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
            }
            else if($header->PO_DateFormat=="d.m.Y"){// print"in PODateformat";
                print "<br> Here: ".$header->PO_DateFormat;
                 $myDateTime = DateTime::createFromFormat('d.m.Y', $PO_Date);
                if($myDateTime != FALSE){
                    $PO_Date = $myDateTime->format('Y-m-d');
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
            else if($header->PO_DateFormat=="d-m-Y"){
                $myDateTime = DateTime::createFromFormat('d-m-Y', $PO_Date);
                if($myDateTime != FALSE){
                    $PO_Date = $myDateTime->format('Y-m-d');
                }else{
                    //$ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification;//ststus::issue processd
                    $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_PODATE;//ststus::issue processd
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
            }
        }
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
        }
    }
    }
    if(trim($header->PO_Date)==""){
            print"Missing PO Date <br> "; $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_PODATE;
            return $ret;
            
    }else{
            print "<br>PO Date=>".$PO_Date."<br>";
    }

    if(trim($header->Delivery_Date)==""){
            print"Missing Delivery Date <br> ";
    } else {
            print "Delivery Date=>".$Delivery_Date."<br>";
    }
    if(isset($header->Expiry_Date)){
    if(trim($header->Expiry_Date)==""){
            print"Missing Expiry Date <br> ";
    } else {
            print "Expiry Date=>".$Expiry_Date."<br>";
    }
    }
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
    if(trim($header->Vat_Tin)==""){
       // print"<br>tin=$header->Vat_Tin<br>"; 
        print" Missing Vat_Tin <br>  ";
         $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_UNIQUEID;
        return $ret;
    } else { 
        $Tin=trim($header->Vat_Tin);
           
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
    if(trim($header->DealerCity)==""){
            print" Missing Dealer City <br> ";
    } else {
        $Dealer_address=trim($header->DealerCity);
        print "Dealer Address=>".$Dealer_address."<br>";
    }
    if(!(trim($header->Dealer_PhoneNo)=="")){
         print "Dealer PhoneNo=>".$header->Dealer_PhoneNo."<br>";
    }
    $DistName=trim($header->VendorName);
    //print"$DistName";
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
        //print_r($item);
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
                
               
    echo'<pre>'; print_r($docItems); echo '</pre>';
//*******************

    $db = new DBConn();
    $createtime1=date('Y-m-d H:i:s');
    $createtime=$db->safe(trim($createtime1));
    $dealer=$db->safe(trim($DealerName));
    $invtext_db=$db->safe(trim($invtext));
    //echo "select * from it_master_dealers where name=$dealer";
    $masterdealerid=0;
    $q="select * from it_master_dealers where name = $dealer";
    print "<br>MDQRY: $q<br>";
    $dealerinfo = $db->fetchObject($q);//get dealer id from master_dealer table
    if($dealerinfo){
            $masterdealerid = $dealerinfo->id;
            print"<br>masterdealerid= $masterdealerid";
    }
    /*else {
            //print"insert master dealer";
    }*/

//    //fetch business unit id
//    //if found while select where bu_identifier = shipping_address of it_shipping_address table
//    //else do insert in it_business_unit
    
    if(trim($PONO) != "" && trim($PO_Date) != "" && trim($Delivery_Date)!=""){
        $shipping_db = $db->safe(trim($Dealer_address));
        $buid=0;
        $no_spaces = str_replace(" ", "", $shipping_address);
        $no_spaces_db = $db->safe(trim($no_spaces));
        print "<br> ***********NO SPACE: $no_spaces_db <br>";
        $check = " replace(bu_identifier ,' ','') = $no_spaces_db";
        print "<br> ************CHECK : $check <br>";
        $selbuQry="select * from  it_business_unit where $check "; 
        echo "<br>$selbuQry";
        $buinfo = $db->fetchObject($selbuQry);
        if(isset($buinfo)){
                $buid = $buinfo->id;
                print"<br>if-get id= $buid";
        }
        else{ 
            //insert        
            $insbuQry = "insert into it_business_unit set bu_identifier = $shipping_db, bu_address=$shipping_db,master_dealer_id=$masterdealerid, createtime = $createtime ";
            echo "<br>$insbuQry";
            $buid= $db->execInsert($insbuQry);
            print"<br>else inserted bunit id----$buid"; 
        }
    }
    else{
        print"<br>PO not Inserted to DB no po number and date<br>";
        //$ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification;//ststus::issue processd
        $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_PONODADTE;//ststus::issue processd
        return $ret; 
    }
    
    $distid=0; 
    $dist=$db->safe(trim($DistName));
    $addClause="";
    $qClause = "";
        if( isset($Tin) && trim($Tin)!=""){
            $tin_db = $db->safe(trim($Tin));
             $addClause="  and code = $tin_db";

             $qClause = " , code = $tin_db";
        }
    //$qdist="select * from it_distributors where name=$dist $addClause ";
      $qdist="select * from it_distributors where bu_id= $buid $addClause";
    echo "<br>$qdist";
    $distinfo = $db->fetchObject("$qdist");//get distributor id if not then insert distributor
    if(isset($distinfo)){
            $distid = $distinfo->id;
            print"<br>if-get id= $distid";
    }
    else{ 
        //insert distributor in db
        $header->VendorAddress = trim($header->VendorAddress);
            //$q = "INSERT INTO it_distributors(code,name,address,city,state,ini_type,shipping_address, createtime)"."VALUES('$Tin','$DistName','$header->VendorAddress','$City','$header->VendorState','$ini_type','$Dealer_address','$createtime')";
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
            //$q = "insert into it_distributors set name = $dist ,address = $addr_db ,ini_type = $ini_type ,shipping_address = $shipping_db, createtime = $createtime $qClause $cClause ";
            $q = "insert into it_distributors set name = $dist ,address = $addr_db ,ini_type = $ini_type ,shipping_address = $shipping_db, bu_id = $buid, createtime = $createtime $qClause $cClause ";
            echo "<br>$q";
            $distid= $db->execInsert($q);
            print"<br>else inserted dist id----$distid"; 
    }
 $distdealerid=0;
 $Dealer_address=$db->safe(trim($Dealer_address));
 if($distid>0 && $masterdealerid>0){  
     print "<br>IN IF <br>";
        $distdealerinfo = $db->fetchObject("select * from it_dist_dealers where name=$dealer and distid=$distid");
            if(isset($distdealerinfo)){
                 print "<br>IN IF 1 <br>";
                 $distdealerid=$distdealerinfo->id;
                print"<br>select distdelrid=$distdealerid<br>";
            }
            else {
                 print "<br>IN IF 2<br>";
                   $qitdisdel="INSERT INTO it_dist_dealers set distid=$distid,name=$dealer,address=$Dealer_address,master_dealer_id=$masterdealerid,createtime=$createtime";
                   echo "<br>$qitdisdel";
                   $distdealerid= $db->execInsert($qitdisdel);
                   print"<br>inserted distdelrid=$distdealerid<br>";
            }
 }
 print "<br>DIST ID: $distid :: MDID :: $masterdealerid :: DDID: $distdealerid <br>";
 if($distid>0 && $masterdealerid>0 && $distdealerid>0){
        print "<br>IN IF p2<br>";
        $makeentry = 1;
        $PONO_db=$db->safe($PONO);
        $PO_Date_db=$db->safe($PO_Date);
        $Delivery_Date_db=$db->safe($Delivery_Date);
        $Expiry_Date_db=$db->safe($Expiry_Date);
        $po_amt=0;
        $po_qty=0;
        $poid=0;
        $insid = 0;      
        $norowupdate=0;
        $filenameparts=array();
        $filenamepartsrev=array();
         print"<br>Filename=$filename<br>";
        $filenameparts= explode("/",$filename);
        $filenamepartsrev= array_reverse($filenameparts);
        $id_fname=$filenamepartsrev[0];
        //$makeentry=1;
        $filename_db=$db->safe($id_fname);
        $query = "select * from it_po where invoice_no= $PONO_db and dist_id= $distid and master_dealer_id = $masterdealerid and status= ".POStatus::STATUS_PROCESSED ;
         print"<br> PO query :$query<br>";
         $poinfo = $db->fetchObject($query);
           if(isset($poinfo)){
//            $poid=$poinfo->id;
//                print"<br>PO already Exist<br>";
//               $ret = POStatus::STATUS_DUPLICATE_PO.$notification;
//                return $ret;
                print"<br>PO already Exist<br>";
                $cd = new chkDuplicate();
                $makeentry= $cd->process($poinfo,$filename_db,$masterdealerid);
                print"<br>make entry= $makeentry<br>";
            }
            if($makeentry==1){
                print"<br>in make entry<br>";
                if(trim($PONO) != "" && trim($PO_Date) != "" && trim($Delivery_Date)!=""){ 
                    print"<br>PO Inserted to DB<br>";
                    $query ="INSERT INTO it_po set filename=$filename_db, status= ".POStatus::STATUS_PROCESSED .",status_msg='" . POStatus::getStatusMsg(POStatus::STATUS_PROCESSED) ."',dist_id=$distid,master_dealer_id=$masterdealerid,ini_id=$iniid,shipping_id=$shipping_id ,invoice_text=$invtext_db,invoice_no=$PONO_db,invoice_date=$PO_Date_db,delivery_date=$Delivery_Date_db,expiry_date=$Expiry_Date_db,tqty=$po_qty,tamt=$po_amt,ctime=$createtime";
                    print "<br>QRY: $query";
                    $poid= $db->execInsert($query);
                    print"<br>PO Inserted to DB<br>";
                }
                else{
                    print"<br>PO not Inserted to DB no po number and date<br>";
                    //$ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification;//ststus::issue processd
                    $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_PONODADTE;//ststus::issue processd
                    return $ret; 
                }
            }
            else{
                 $ret = POStatus::STATUS_DUPLICATE_PO.$notification;//ststus::issue processd
                 return $ret;
            }
//            //if(!($poinfo)){
//          else{
//            if(trim($PONO) != "" && trim($PO_Date) != "" && trim($Delivery_Date)!=""){
//                    // print"<br>PO Inserted to DB<br>";
//                    //$query ="INSERT INTO it_po set status= ".POStatus::STATUS_PROCESSED .",status_msg='" . POStatus::getStatusMsg(POStatus::STATUS_PROCESSED) ."',dist_id=$distid,master_dealer_id=$masterdealerid,ini_id=$iniid,invoice_text=$invtext_db,invoice_no=$PONO_db,invoice_date=$PO_Date_db,delivery_date=$Delivery_Date_db,expiry_date=$Expiry_Date_db,tqty=$po_qty,tamt=$po_amt,ctime=$createtime";
//                    $query ="INSERT INTO it_po set filename=$filename_db, status= ".POStatus::STATUS_PROCESSED .",status_msg='" . POStatus::getStatusMsg(POStatus::STATUS_PROCESSED) ."',dist_id=$distid,master_dealer_id=$masterdealerid,ini_id=$iniid,invoice_text=$invtext_db,invoice_no=$PONO_db,invoice_date=$PO_Date_db,delivery_date=$Delivery_Date_db,expiry_date=$Expiry_Date_db,tqty=$po_qty,tamt=$po_amt,ctime=$createtime";
//                    print "<br>QRY: $query";
//                    $poid= $db->execInsert($query);
//                    if($poid>0){
//                    print"<br>PO Inserted to DB FROM aRTICLE <br>";
//                    }
//                    else{
//                     print"<br>PO not Inserted to DB no po number and date<br>";
//                     $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification;//ststus::issue processd
//                     return $ret; 
//                    }          
//            }
//             }
            if($poid>0){
                print"<br>poid=$poid";
                $itemntfound= array();
                $filename= $header->DealerName."_".$header->VendorName."_".$createtime;
                //print"<br>$filename<br>";
                $index=0;
                foreach ($items as $item) {
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
//                        $EAN = trim(getFieldValue($item,"EAN"));
                        $Itemname=trim(substr(getFieldValue($item, "Itemname"),0,3));
                        $CAR = trim(getFieldValue($item,"CAR"));
                        $TAX  = trim(getFieldValue($item,"TAX"));
                        $Qty = doubleval(str_replace(",","",trim(getFieldValue($item,"Qty"))));
                        $totQty = doubleval(str_replace(",","",trim($totQty)));
                        $MRP = doubleval(str_replace(",","",trim($mrp)));
                       // $VAT = trim(getFieldValue($item,"VAT"));
                        $VAT = doubleval(str_replace(",","",trim(getFieldValue($item,"VAT"))));
                        $Rate = doubleval(str_replace(",","",trim(getFieldValue($item,"Rate"))));
                        $Amount = doubleval(str_replace(",","",trim(getFieldValue($item,"Amount"))));      
           // add is_double check for item values here                          
                        print"<br>itemname=$Itemname<br>";
                        $artno_db = $db->safe(trim($ArticleNo));
                        print"<br>ArticleNo=$artno_db";
                        $dealer_item_id=0;
                       // $getitemQuery="select * from it_master_items where itemcode= $ean_db AND is_weikfield= 1";
                        $getitemQuery="select * from it_dealer_items where itemcode= $artno_db  and master_dealer_id=$masterdealerid and is_weikfield= 1";//add distid?
                        print "<br>ITMQRY: $getitemQuery <br>";
                        $dealeriteminfo=$db->fetchObject($getitemQuery);// and is wkf=1
                        if(isset($dealeriteminfo)){
                                $dealer_item_id = $dealeriteminfo->id;
                                print"<br>dealeritemid===$dealer_item_id<br>";
                        }
                        else{
                           // $getitemQuery="select * from it_dealer_items where itemcode= $artno_db ";//AND is_weikfield = 0";
                            $getitemQuery="select * from it_dealer_items where itemcode= $artno_db  and master_dealer_id=$masterdealerid ";//add distid?
                            print "<br>MS Query: $getitemQuery <br>";
                            $iteminfo=$db->fetchObject($getitemQuery);// and is wkf=0
                            if(!(isset($iteminfo))){
                                $insitemQuery="insert into it_dealer_items set distid=$distid, master_dealer_id= $masterdealerid , itemcode= $artno_db , is_weikfield = 0 , createtime = $createtime";
                                print "<br>INSQRY: $insitemQuery <br>";
                                $insid = $db->execInsert($insitemQuery);
                                print "<br>INISID 1: $insid<br>";
                            }else{
                               // $insid = 0;
                                $insid = $iteminfo->id;  //already present item id when $iteminfo is set                            
                            }
                            print "<br>INISID 2: $insid  <br>";
//                            print_r($iteminfo);
                        }
                        //}
                        print "<br>INISID 3: $insid <br>";
//                        print_r($iteminfo);
                        $addval="";
                                if($VAT!=""){
                                    $addval =",vat = $VAT";
                                    print"addval=$addval";
                                }
                                if($Rate!=""){
                                    $addval .=",cost_price = $Rate";
                                    print"addval=$addval";
                                }
                                if($Amount!="" && $Amount!=0){
                                    $addval .=",amt = $Amount";
                                    print"addval=$addval";
                                }else{
                                        //$updtquery="update it_po set status= ". POStatus::STATUS_ISSUE_AT_PROCESSING .",status_msg='" . POStatus::getStatusMsg( POStatus::STATUS_ISSUE_AT_PROCESSING) ."' where id=$poid";
                                        $updtquery="update it_po set status= ". POStatus::STATUS_ISSUE_AT_PROCESSING .",status_msg='" . POStatus::getStatusMsg( POStatus::STATUS_ISSUE_AT_PROCESSING) ."',issue_reason='".IssueReason::getIssueMsg(IssueReason::MISSING_AMT)."' where id=$poid";
                                        print $updtquery;
                                        $db->execQuery($updtquery);
                                        //$ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification;//status::issue  at processing
                                        $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_AMT;
                                        return $ret;
                                }
                        //$dealer_item_id=0;
                        if(isset($dealeriteminfo)){
                            print"<br>Item present in dealer_items with weikfeild =1";
                            //find master_item_id using artno and eancode
                            $EANcode="";
                            $master_item_id=0;  //?????????????? ask before uncommenting
                            $EANcode=$dealeriteminfo->eancode; 
                            print"<br>EAN code from Dealeriteminfo=$EANcode<br>";
                            if(isset($EANcode) && trim($EANcode)!=""){
                                $EANcode_db=$db->safe($EANcode);
                                print"<br>EANcode_db=$EANcode_db<br>";
                                $getMasterItemInfoqry="select * from it_master_items where itemcode=$EANcode_db";
                                print"<br>getMasterItemInfoqry=$getMasterItemInfoqry<br>";
                                $getMasterItemInfoobj=$db->fetchObject($getMasterItemInfoqry);
                                if(isset($getMasterItemInfoobj)){
                                $master_item_id=$getMasterItemInfoobj->id;
                                print"<br>MasterITEMID=$master_item_id<br>";
                                }
                                else{
                                    print"<br>getMasterItemInfoqry failed<br>";
                                }
                            }else{
                                //$updtquery="update it_po set status= ". POStatus::STATUS_ISSUE_AT_PROCESSING .",status_msg='" . POStatus::getStatusMsg( POStatus::STATUS_ISSUE_AT_PROCESSING) ."' where id=$poid";
                                $updtquery="update it_po set status= ". POStatus::STATUS_ISSUE_AT_PROCESSING .",status_msg='" . POStatus::getStatusMsg( POStatus::STATUS_ISSUE_AT_PROCESSING) ."',issue_reason='".IssueReason::getIssueMsg(IssueReason::MISSING_EAN)."' where id=$poid";
                                print $updtquery;
                                $db->execQuery($updtquery);
                                //$ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification;//status::issue  at processing
                                $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::MISSING_EAN;
                                return $ret;
                             }
                            print"<br>poid=$poid>0 && DIID=$dealer_item_id>0 && MIID=$master_item_id>0<br>";
                            if($poid>0 && $dealer_item_id>0 && $master_item_id>0){  
                                print"<br>insert to it_po_items whem wkfld=1<br>";
                                $car_db = $db->safe(trim($CAR));
                                //print"<br>dealer item=$dealer_item_id<br>";
                                //$qpoitem="INSERT INTO it_po_items set po_id = $poid,dealer_item_id =  $dealer_item_id, master_item_id = $master_item_id ,mrp = $MRP,vat = $VAT,cost_price = $Rate,qty = $Qty, tot_qty = $totQty ,pack_type = $car_db,amt = $Amount,ctime = $createtime ";
                                //$qpoitem="INSERT INTO it_po_items set po_id = $poid,dealer_item_id =  $dealer_item_id, mrp = $MRP,vat = $VAT,cost_price = $Rate,qty = $Qty, tot_qty = $totQty ,pack_type = $car_db,amt = $Amount,ctime = $createtime "; // master_item_id removed from query
                                $qpoitem="INSERT INTO it_po_items set po_id = $poid,dealer_item_id =  $dealer_item_id, master_item_id = $master_item_id ,mrp = $MRP,qty = $Qty, tot_qty = $totQty ,pack_type = $car_db,ctime = $createtime $addval ";
                                echo"<br>insert to poitems=$qpoitem<br>"; 
                                $poitemid=$db->execInsert($qpoitem);
                                if(trim($poitemid)!=""){
                                    print"inserted to po_items=====$poitemid";
                                    $po_amt=$po_amt+$Amount;
                                    $po_qty=$po_qty+$totQty;
                                }                                                             
                            }else{
                                //$updtquery="update it_po set status= ". POStatus::STATUS_ISSUE_AT_PROCESSING .",status_msg='" . POStatus::getStatusMsg( POStatus::STATUS_ISSUE_AT_PROCESSING) ."' where id=$poid";
                                $updtquery="update it_po set status= ". POStatus::STATUS_ISSUE_AT_PROCESSING .",status_msg='" . POStatus::getStatusMsg( POStatus::STATUS_ISSUE_AT_PROCESSING) ."',issue_reason='".IssueReason::getIssueMsg(IssueReason::ITMNOTFND)."'  where id=$poid";
                                print $updtquery;
                                $db->execQuery($updtquery);
                                //$ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification;//status::issue  at processing
                                $ret = POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::ITMNOTFND;
                                return $ret;
                            }

                        }else{
                             print"<br>Item in dealer_items with weikfeild = 0/2";
                           //not master item section
                            $is_weikfield_Clause = "";
                            $mid = "";
                            $getblankitemQuery="select * from it_dealer_items where itemcode= $artno_db and master_dealer_id=$masterdealerid and is_weikfield= 2";
                            print "<br>BLANK QRY: $getblankitemQuery <br>";
                            $blankiteminfo=$db->fetchObject($getblankitemQuery);// and is wkf=2 
                            if(isset($blankiteminfo)){
                                //means not a weikfield product
                                $mid = $blankiteminfo->id;
                               // $is_weikfield_Clause = ", is_weikfield=2 ";
                            }else{
                                // undefined weikfield product / undefined product
                                print "<br>IN STR CMP: <br>";
                                //if((strcasecmp($Itemname,"WEI")==0) ||(strcasecmp($Itemname,"ECO")==0) ||(strcasecmp($Itemname, "ST.")==0)||(strcasecmp($Itemname, "ST")==0)||(strcasecmp($Itemname, "St")==0)){
                                $WKFDarr= array('WF','WK','WEI','ECO','ST\.','ST ','EV ');
//                                $no=count($WKFDarr);
//                                print"<br>no=$no";
                                
                                foreach($WKFDarr as $brand){
                                    print"itemname=$Itemname<br> brand=$brand <br>";
                                     //if((stripos($Itemname,$brand)!==false)){
                                     $regex="/$brand/i";
                                    //print"<br>check:===$str<br>$Itemname";
                                    if(preg_match($regex,$Itemname)){
                                   // if(preg_match('/$brand/',$Itemname)){
                                        print"<br>is wkfd $Itemname<br>";  
                                        print "<br>STR CMP SUCCESS <br>";
                                        if($insid>0){
                                            $updateQ="update it_dealer_items set is_notfound=1 , updatetime = now() where id=$insid";
                                            print "update QRY: $updateQ ";
                                            $norowupdate=$db->execUpdate($updateQ);
                                            print "<br>NO ROWS UPDATED: $norowupdate <br>";
                                        }  
                                        $is_weikfield_Clause = ", is_weikfield = 0 "; 
                                        break;
                                    }else{
                                        $is_weikfield_Clause = ", is_weikfield = 2 ";
                                        $upquery="update it_dealer_items set is_weikfield = 2 where id= $insid";
                                        print "update QRY is_wekfd=2 : $upquery ";
                                        $db->execUpdate($upquery);
                                    } 
                                }
                            }                            
                            //insert into po_items
                             if($poid>0){
                               print"<br>insert to it_po_items when wkfld=0/2<br>";
                               $car_db = $db->safe(trim($CAR));
                               //print"<br>dealer item=$dealer_item_id<br>";
                               //$qpoitem="INSERT INTO it_po_items(po_id,dealer_item_id,master_item_id,mrp,vat,cost_price,qty,pack_type,amt,ctime)"."VALUES('$poid','$dealer_item_id','$master_item_id','$MRP','$VAT','$Rate','$Qty','$CAR','$Amount','$createtime')";
                               //$qpoitem="INSERT INTO it_po_items set po_id = $poid,dealer_item_id =  $insid, mrp = $MRP,vat = $VAT,cost_price = $Rate,qty = $Qty, tot_qty = $totQty ,pack_type = $car_db,amt = $Amount,ctime = $createtime ";//removed master_item_id = $mid ,
                               $qpoitem="INSERT INTO it_po_items set po_id = $poid,dealer_item_id =  $insid, mrp = $MRP,qty = $Qty, tot_qty = $totQty ,pack_type = $car_db,ctime = $createtime $addval ";
                               echo"<br>insert to poitems=$qpoitem<br>"; 
                               $poitemid=$db->execInsert($qpoitem);                                                                                         
                            }
                             
                        } // not master item section ends here
                           // $dealer_item_id=" ";       
            }
         
            if($norowupdate>0){    
                 $notification = "::2";
                 print "<br>NOTIFICATION UPDATED: $notification <br>";
            }
                    //Update (insert)PO_Amount and PO_quantity in it_po  
                        $po_updt="update it_po set tqty=$po_qty,tamt=$po_amt where id = $poid";
                        print"<br>add amt & Qty if invoice=$po_updt<br>";
                        $db->execQuery($po_updt); 

                    //chk if po_items is empty
                       // $cntquery="select count(*) as cnt from it_po_items where po_id=$poid";
                        $cntquery="select count(*) as cnt from it_po_items p , it_dealer_items d where p.dealer_item_id = d.id and d.is_weikfield = 1 and po_id=$poid";
                        echo"$cntquery";
                        $cntobj=$db->fetchObject($cntquery);
                        if(isset($cntobj)){
                            $itmcnt=$cntobj->cnt;
                            print"<br>itemcnt=$itmcnt";
                            if(trim($cntobj->cnt) == 0){
                                print "<br>IN ) CNT <br>";
                                
                                //chk if all the items are un defined weikfield item
                                  $uqry = "select count(*) as cnt from it_po_items p , it_dealer_items d where p.dealer_item_id = d.id and d.is_weikfield = 0 and po_id=$poid";
                                  print "<br>QRY: $uqry<br>";
                                  $ciobj = $db->fetchObject($uqry);
                                  if(isset($ciobj) && trim($ciobj->cnt) > 0){
                                       $updatequery="update it_po set status= ". POStatus::STATUS_ARTICLE_NO_MISSING.",status_msg='" . POStatus::getStatusMsg(POStatus::STATUS_ARTICLE_NO_MISSING) ."' where id=$poid";
                                       print $updatequery;
                                       $db->execQuery($updatequery);
                                       //$ret =  POStatus::STATUS_MISSING_EAN.$notification;//status EAN Missing
                                       $ret=  POStatus::STATUS_ARTICLE_NO_MISSING.$notification;
                                       return $ret;
                                  }else{
                                      //means all items are not weikfield 
                                    $updatequery="update it_po set status= ". POStatus::STATUS_NOT_WEIKFIELD .",status_msg='" . POStatus::getStatusMsg( POStatus::STATUS_NOT_WEIKFIELD) ."' where id=$poid";
                                     print $updatequery;
                                    $db->execQuery($updatequery);
                                    $ret =  POStatus::STATUS_NOT_WEIKFIELD.$notification;//ststus::not ewikfeild
                                    return $ret;
                                  }
                            }else{
                                print "<br><br>";
                                //means contains atlest i weikfield product n processed
                                $ret =  POStatus::STATUS_PROCESSED.$notification;//ststus::processd
                                return $ret;
                            }
                        }else{
                          //$ret =  POStatus::STATUS_PROCESSED.$notification;//ststus::processd
                            //$updtquery="update it_po set status= ". POStatus::STATUS_ISSUE_AT_PROCESSING .",status_msg='" . POStatus::getStatusMsg( POStatus::STATUS_ISSUE_AT_PROCESSING) ."' where id=$poid";
                             $updtquery="update it_po set status= ". POStatus::STATUS_ISSUE_AT_PROCESSING .",status_msg='" . POStatus::getStatusMsg( POStatus::STATUS_ISSUE_AT_PROCESSING) ."',issue_reason='".IssueReason::getIssueMsg(IssueReason::ISWKFQRYFAIL)."'  where id=$poid";
                            print $updtquery;
                            $db->execQuery($updtquery);
                            //$ret =  POStatus::STATUS_ISSUE_AT_PROCESSING.$notification;//ststus::processd
                            $ret =  POStatus::STATUS_ISSUE_AT_PROCESSING.$notification."::".IssueReason::ISWKFQRYFAIL;
                            return $ret;
                        }
                     
            }else {
                print"<br>PO not Inserted to DB<br>";
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
