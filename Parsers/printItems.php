<?php
date_default_timezone_set('Asia/Kolkata');

function printItems($header,$items) {
//$parse_dttm = date('dd/mm/yyyy h:i:s a');
$parse_dttm = date('d-M-Y h:i:s a');
//$docDate = $header->StatementDate;
   
/*if (isset($header->StatementDateFormat)) {
	$myDateTime = DateTime::createFromFormat($header->StatementDateFormat, $docDate);
	$docDate = $myDateTime->format('d-M-Y');
}*/
printItemsToConsole($header,$items);
}

function savetoDB($header, $items) {
$document = array(
"Brand" => array(
	"Name" => "Sanofi",
	"Code" => "SST-Sanofi"
	),
"Stockist" => array(
	"Name" => $header->StockistName,
	"City" => $header->StockistCity
	),
"DocDate" => $docDate,
"ProcessDate" => $parse_dttm,
"Filename" => $header->fileName
);

$docItems = array();
foreach ($items as $item) {
$docItems[] = array(
"ProductName" => getFieldValue($item,"ProductName"),
"PackSize" => getFieldValue($item,"PackSize"),
"Opening" => getFieldValue($item,"Opening"),
"Receipt" => getFieldValue($item,"Receipt",true),
"Sales" => getFieldValue($item,"Sales",true),
"Closing" => getFieldValue($item,"Closing",true),
"SalesVal" => getFieldValue($item,"SalesVal",true),
"ClosingVal" => getFieldValue($item,"ClosingVal",true)
);
}

$document["items"] = $docItems;

$m = new MongoClient();

// select a database
$db = $m->ventasys_db;

// select a collection (analogous to a relational database's table)
$collection = $db->documents;

$collection->insert($document);

}

function printItemsToConsole($header,$items) {
//print"result";
//print_r($items);   
//print "Rec Type~Parse Date Time~File Name~SST Date~Cust Name~Cust Name2~Product~Product2~Opening Units~Receipts Units~Sales Units~Closing Units~Sales Val~Closing Val\n";
$parse_dttm = date('d/M/Y h:i:s a');
$docItems = array();
//foreach ($items as $item) {
//print "SST-Sanofi"."~";
//print $parse_dttm."~";
//print $header->fileName."~";
$PO_Date = $header->PO_Date;
//print"<br> $PO_Date";
if(!($header->PO_Date=="")){
if (isset($header->PO_DateFormat)) {
    if($header->PO_DateFormat=="Ymd")
    {
	$myDateTime = DateTime::createFromFormat('Ymd', $PO_Date);
        //print $myDateTime ;
        $PO_Date = $myDateTime->format('Y-m-d');
        //print "ST DT: $PO_Date";
    }
 else if($header->PO_DateFormat=="d/m/Y")
     {
         $myDateTime = DateTime::createFromFormat('d/m/Y', $PO_Date);
         //print $myDateTime_______________ ;
         $PO_Date = $myDateTime->format('Y-m-d');
         //print "ST DT: $PO_Date-";
        
    }
    else if($header->PO_DateFormat=="d.m.Y")
     { 
         $myDateTime = DateTime::createFromFormat('d.m.Y', $PO_Date);
         //print $myDateTime_______________ ;
         $PO_Date = $myDateTime->format('Y-m-d');
         //print "ST DT: $PO_Date-";
        
    }
}

     }
if($header->PO_Date==""){
        print"Missing PO Date <br> ";
}
 else {
        print "<br>PO Date=>".$PO_Date."<br>";
}
if($header->PO_No==""){
        print" Missing PO No <br>  ";
}
 else {
        print "PO_No=>".$header->PO_No."<br>";
}
if($header->PO_Type==""){
        print" Missing PO_Type <br>  ";
}
 else {
        print "PO_Type=>".$header->PO_Type."<br>";
}
if($header->PO_Name==""){
        print" Missing PO_Name <br>  ";
}
 else {
        print "PO_Name=>".$header->PO_Name."<br>";
}
if($header->Purchase_Group==""){
        print" Missing Purchase_Group <br>  ";
}
 else {
        print "Purchase_Group=>".$header->Purchase_Group."<br>";
}
if($header->PO_Currency==""){
        print" Missing PO_Currency <br>  ";
}
 else {
        print "PO_Currency=>".$header->PO_Currency."<br>";
}
if($header->Type==""){
        print" Missing Type <br>  ";
}
 else {
        print "Type=>".$header->Type."<br>";
}
if($header->Vat_Tin==""){
        print" Missing Vat_Tin <br>  ";
}
 else {
        print "Vat_Tin=>".$header->Vat_Tin."<br>";
}
$inv_qnt=count($items);
if($inv_qnt==""){
        print" Missing Invoice Items <br>  ";
}
 else {
        print "Invoice Quantity=>".$inv_qnt."<br>";
}


if($header->DealerName==""){
        print" Missing Dealer Name <br> ";
}
 else {
        print "Dealer Name=>".$header->DealerName."<br>";
}

if($header->DealerCity==""){
        print" Missing Dealer City <br> ";
}
 else {
        print "Dealer City=>".$header->DealerCity."<br>";
}
if(!($header->Dealer_PhoneNo=="")){
     print "Dealer PhoneNo=>".$header->Dealer_PhoneNo."<br>";
}
 
if($header->VendorName==""){
        print" Missing Vendor Name <br> ";
}
 else {
        print "Vendor Name=>".$header->VendorName."<br>";
}

if($header->VendorCity==""){
        print" Missing Vendor City <br> ";
}
 else {
        print "Vendor City=>".$header->VendorCity."<br>";
}
if(!($header->Vendor_PhoneNo=="")){
     print "Vendor PhoneNo=>".$header->Vendor_PhoneNo."<br>";
}

 $PO_amount=0;
 foreach ($items as $item)
{
     $Amount=  getFieldValue($item,"Amount");
    // print"<br>LT@@$linetotal<br>";
     $Amount= str_replace(",","", $Amount);
     // print"<br>LT@@$linetotal<br>";
     $PO_amount += $Amount;
     // print"<br>$PO_amount<br>";
}
//print"<br>ia@@$invoice_amount<br>";
if($PO_amount==0){
        print" Missing PO Amount  <br>  ";
}
 else {
        print "PO Amount=>".$PO_amount."<br>";
}
//*******************
foreach ($items as $item) {
      $docItems[] = array(
                "ArticleNo" => trim(getFieldValue($item,"ArticleNo")),
                "EAN" =>  trim(getFieldValue($item,"EAN")),
                "Order" =>trim(getFieldValue($item,"Order")),
                "TAX" => trim(getFieldValue($item,"TAX")),
                "Qty" => trim(getFieldValue($item,"Qty")),
                "MRP" => trim(getFieldValue($item,"MRP")),
                "VAT" =>  trim(getFieldValue($item,"VAT")),
                "Rate" => trim(getFieldValue($item,"Rate")),
                "Amount" => trim(getFieldValue($item,"Amount")));            
}

    echo '<pre>'; print_r($docItems); echo '</pre>';
   /* if(count($items)==0)
    {
        print"Items are missing";
    }*/
}

function getFieldValue($item, $fieldName, $cleanNumber=false) {
	if (!isset($item[$fieldName])) return "";
	$val = $item[$fieldName];
	if ($cleanNumber) {
		$val = preg_replace("/[^0-9.]/", "", $val);
	}
	return $val;
}
