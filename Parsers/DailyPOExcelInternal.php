<?php
ini_set('memory_limit', '-1'); //For allowing unlimited memory on server
ini_set('max_execution_time', 180);  
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once "lib/php/Classes/PHPExcel.php";
require_once 'lib/php/Classes/PHPExcel/Writer/Excel2007.php';

try {
    $sheetIndex=0;
//$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory_serialized;
//$cacheSettings = array( 'memoryCacheSize' => '1500MB');
//PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
// Create new PHPExcel object
    $objPHPExcel = new PHPExcel();
// Create a first sheet
    $objPHPExcel->setActiveSheetIndex($sheetIndex);
    $objPHPExcel->getActiveSheet()->setTitle('Daily Complete Data');
    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Sr.No');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', 'PO Number');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', 'PO Date');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', 'PO Expiry Date');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Vendor Code');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Vendor Name');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Chain Name');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', 'Customer Name');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', 'Customer code');
    $objPHPExcel->getActiveSheet()->setCellValue('J1', 'Store code');
    $objPHPExcel->getActiveSheet()->setCellValue('K1', 'Store Name');
    $objPHPExcel->getActiveSheet()->setCellValue('L1', 'DC Location / Store Location');
    $objPHPExcel->getActiveSheet()->setCellValue('M1', 'City');
    $objPHPExcel->getActiveSheet()->setCellValue('N1', 'State');
    $objPHPExcel->getActiveSheet()->setCellValue('O1', 'CFA Location');
    $objPHPExcel->getActiveSheet()->setCellValue('P1', 'Zone');
    $objPHPExcel->getActiveSheet()->setCellValue('Q1', 'FG Code');
    $objPHPExcel->getActiveSheet()->setCellValue('R1', 'Item Description as VLCC Master');
    $objPHPExcel->getActiveSheet()->setCellValue('S1', 'PO Article No');
    $objPHPExcel->getActiveSheet()->setCellValue('T1', 'PO Item Description');
    $objPHPExcel->getActiveSheet()->setCellValue('U1', 'SKU Units');
    $objPHPExcel->getActiveSheet()->setCellValue('V1', 'Product Category');
    $objPHPExcel->getActiveSheet()->setCellValue('W1', 'Master EAN');
    $objPHPExcel->getActiveSheet()->setCellValue('X1', 'PO EAN');    
    $objPHPExcel->getActiveSheet()->setCellValue('Y1', 'Master Item MRP');
    $objPHPExcel->getActiveSheet()->setCellValue('Z1', 'MRP');    
    $objPHPExcel->getActiveSheet()->setCellValue('AA1', 'Qty');
    $objPHPExcel->getActiveSheet()->setCellValue('AB1', 'CAR');
        $objPHPExcel->getActiveSheet()->setCellValue('AC1', 'Invoice');
    $objPHPExcel->getActiveSheet()->setCellValue('AD1', 'BASIC Rate');
    $objPHPExcel->getActiveSheet()->setCellValue('AE1', 'Tax');
    $objPHPExcel->getActiveSheet()->setCellValue('AF1', 'Total Amount (Excluding GST)');    
    $objPHPExcel->getActiveSheet()->setCellValue('AG1', 'Total Amount (Including GST)'); 
    $objPHPExcel->getActiveSheet()->setCellValue('AH1', 'IS VLCC');     
    
    
//    $objPHPExcel->getActiveSheet()->setCellValue('AA1', 'Total QTY');
//    $objPHPExcel->getActiveSheet()->setCellValue('AB1', 'Invoice');
//    $objPHPExcel->getActiveSheet()->setCellValue('AC1', 'BASIC Rate');
//    $objPHPExcel->getActiveSheet()->setCellValue('AD1', 'Tax');
//    $objPHPExcel->getActiveSheet()->setCellValue('AE1', 'Total Amount');
    
    
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(40);
    $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(40);
    $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(40);
    $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('AD')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('AE')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('AF')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('AG')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('AH')->setWidth(30);
    
    
    $styleArray = array(
        'font' => array(
            'bold' => false,
//        'color' => array('rgb' => 'FF0000'),
            'size' => 10,
    ));
    $headerstyleArray = array(
        'font' => array(
            'bold' => true,
//        'color' => array('rgb' => 'FF0000'),
            'size' => 10,
    ));
  
    $objPHPExcel->getActiveSheet()->getStyle('A')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('B')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('C')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('D')->applyFromArray($styleArray);
    //$objPHPExcel->getActiveSheet()->getStyle('E')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('E')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('F')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('G')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('H')->applyFromArray($styleArray);//H
    $objPHPExcel->getActiveSheet()->getStyle('I')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('J')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('K')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('L')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('M')->applyFromArray($styleArray);//M
    $objPHPExcel->getActiveSheet()->getStyle('N')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('O')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('P')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('Q')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('R')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('S')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('T')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('U')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('V')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('W')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('X')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('Y')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('Z')->applyFromArray($styleArray);
  //  $objPHPExcel->getActiveSheet()->getStyle('AA')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('AB')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('AC')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('AD')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('AE')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('AF')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('AG')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('AH')->applyFromArray($styleArray);
    
    $colCount = 0;
    $rowCount = 2;
    $db = new DBConn();

    $today_dt = date('Y-m-d');
    $srt_dt = date('Y-m-d');
    $get_cuttent_time = date('Y-m-d H:i:s');
//    echo "Current time: ".$get_cuttent_time."<br>";
    $time = date('Y-m-d 14:00:00');
    if($get_cuttent_time < $time){
         $st_dt = $srt_dt . " 00:00:00 ";
    }else{
        $st_dt = $srt_dt . " 14:00:00 ";
	//$st_dt = $srt_dt . " 00:00:00 ";
    }
//       $st_dt = "2018-10-01 00:00:00 ";
    $st_dt_db = $db->safe(trim($st_dt));
    $ed_dt = $today_dt . " 23:59:59 ";
    $ed_dt_db = $db->safe(trim($ed_dt));

//$query="select ip.invoice_no, ip.invoice_date, ip.delivery_date, ip.expiry_date, ip.tqty, ip.tamt,ip.ctime as Intouch, ip.shipping_id as sid,ipt.mrp, ipt.qty,ipt.tot_qty,ipt.pack_type as CAR,ipt.cost_price,ipt.vat, ipt.amt, ipt.master_item_id ,imd.name as dealername,imd.show_code as showflag, idt.itemcode as articleno,idt.itemname as description,  d.code as distid, d.name as distname, sh.dc_address as address,sh.dc_state as state,sh.dc_city as city,sh.dc_name as name, bu.code as vendorcode,ip.invoice_text,sh.customer_code from  it_master_dealers imd, it_dealer_items idt,it_shipping_address sh, it_distributors d, it_business_unit bu, it_po ip, it_po_items ipt  where ip.id=ipt.po_id  AND  ip.dist_id=d.id and d.bu_id= bu.id and sh.id = ip.shipping_id  and  ip.master_dealer_id=imd.id AND idt.id= ipt.dealer_item_id and idt.is_vlcc = 1  AND ip.ctime between $st_dt_db and $ed_dt_db and ip.status =1 and imd.id not in (7,11) order by dealername,ip.invoice_no";
//$query="select ip.invoice_no, ip.invoice_date, ip.delivery_date, ip.expiry_date, ip.tqty, ip.tamt,ip.ctime as Intouch, ip.shipping_id as sid,ipt.mrp, ipt.qty,ipt.tot_qty,ipt.pack_type as CAR,ipt.cost_price,ipt.vat, ipt.amt, ipt.master_item_id ,imd.id as master_dealer_id,imd.name as dealername,imd.show_code as showflag, idt.itemcode as articleno,idt.itemname as description, idt.eancode, d.code as distid, d.name as distname, sh.dc_address as address,sh.dc_state as state,sh.dc_city as city,sh.dc_name as name, bu.code as vendorcode,ip.invoice_text,sh.customer_code from  it_master_dealers imd, it_dealer_items idt,it_shipping_address sh, it_distributors d, it_business_unit bu, it_po ip, it_po_items ipt  where ip.id=ipt.po_id  AND  ip.dist_id=d.id and d.bu_id= bu.id and sh.id = ip.shipping_id and ip.master_dealer_id=imd.id AND idt.id= ipt.dealer_item_id AND ip.ctime between $st_dt_db and $ed_dt_db and ip.status =1 and ip.is_active=1 order by dealername,ip.invoice_no"; 
    $query="select ip.invoice_no, ip.invoice_date, ip.delivery_date, ip.expiry_date, ip.tqty, ip.tamt,ip.ctime as Intouch, ip.shipping_id as sid,ipt.mrp, ipt.qty,ipt.tot_qty,ipt.pack_type as CAR,ipt.cost_price,ipt.vat, ipt.amt, ipt.master_item_id ,imd.id as master_dealer_id,imd.name as dealername,imd.show_code as showflag, idt.itemcode as articleno,idt.itemname as description, d.code as distid, d.name as distname, sh.dc_address as address,sh.dc_state as state,sh.dc_city as city,sh.dc_name as name, bu.code as vendorcode,ip.invoice_text,sh.customer_code,ipt.po_itemname as description_po,ipt.po_eancode,idt.is_vlcc from  it_master_dealers imd, it_dealer_items idt,it_shipping_address sh, it_distributors d, it_business_unit bu, it_po ip, it_po_items ipt  where ip.id=ipt.po_id  AND  ip.dist_id=d.id and d.bu_id= bu.id and sh.id = ip.shipping_id and ip.master_dealer_id=imd.id AND idt.id= ipt.dealer_item_id AND ip.ctime between $st_dt_db and $ed_dt_db and ip.status not in (10,3) and ip.is_active=1 order by dealername,ip.invoice_no";  
    echo $query."<br/>";
   // exit();
   $result = $db->getConnection()->query($query);

    $srno = 1;
    while ($obj = $result->fetch_object()) {
        
        print_r($obj);
        //exit();
        $EAN="";
        $sku="";  
        $productgrp=""; 
        $category= "";
        $address = $obj->address;//str_replace("                                          ", " ", $obj->address);
//        $itemname_po = $obj->description;
        $itemname_po = $obj->description_po;
        $inv_no= (string)$obj->invoice_no;
        $PO_date = explode(" ", $obj->invoice_date);
        $Del_date = explode(" ", $obj->delivery_date);
        $Exp_date = explode(" ", $obj->expiry_date);
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
        
        if(preg_match('/vlcc\s+personal\s+care\s+Ltd./i',$obj->distname)){   
            
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
        
        if($obj->master_dealer_id==3 || $obj->master_dealer_id==4 || $obj->master_dealer_id==5 || $obj->master_dealer_id==41 || $obj->master_dealer_id==49 || $obj->master_dealer_id==24){
            $amt = $obj->amt;
        }else{
            $amt = round($obj->amt/1.18,2);
        }
        
         if($obj->master_dealer_id==3 || $obj->master_dealer_id==4 || $obj->master_dealer_id==5 || $obj->master_dealer_id==41 || $obj->master_dealer_id==49 || $obj->master_dealer_id==24){
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
        
         //if($obj->master_dealer_id==2){
            if(preg_match('/whsm/i',$address)==1){
                $obj->dealername = "WH Smith";
                $cust_name = ": Travel News Services (India) Pvt Ltd";
            }
        //}
    
              $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $srno);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $inv_no);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, $PO_date[0]);        
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $rowCount, $Exp_date[0]);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $rowCount, $obj->distid);         
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $rowCount, $vendor_name);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $rowCount, $obj->dealername);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $rowCount, $cust_name);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $rowCount, $cust_code);
       $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $rowCount, $obj->vendorcode);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10, $rowCount, $obj->name);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(11, $rowCount, $address);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(12, $rowCount, $City);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(13, $rowCount, $State);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(14, $rowCount, $cf_location);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(15, $rowCount, $zone_name);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(16, $rowCount, $productgrp);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(17, $rowCount, $itemname);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(18, $rowCount, $obj->articleno);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(19, $rowCount, $itemname_po);        
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(20, $rowCount, $sku);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(21, $rowCount, $category);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(22, $rowCount, $EAN);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(23, $rowCount, $obj->po_eancode);        
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(24, $rowCount, $master_mrp);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(25, $rowCount, $obj->mrp);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(26, $rowCount, $obj->qty);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(27, $rowCount, $obj->CAR);
      //  $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow826, $rowCount, $obj->qty);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(28, $rowCount, " ");
         $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(29, $rowCount, $obj->cost_price);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(30, $rowCount, $obj->vat);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(31, $rowCount, $amt);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(32, $rowCount, $inc_amt);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(33, $rowCount, $flag);
        
        $colCount = 0;
        $rowCount++;
        $srno++;
    }
    
    // Only VLCC data
  /*  
    $sheetIndex++;
    $objPHPExcel->createSheet();
    $objPHPExcel->setActiveSheetIndex($sheetIndex);
    $objPHPExcel->getActiveSheet()->setTitle('Daily VLCC Data');
    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Sr.No');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', 'PO Number');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', 'PO Date');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', 'PO Expiry Date');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Vendor Code');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Vendor Name');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Chain Name');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', 'Customer Name');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', 'Customer code');
    $objPHPExcel->getActiveSheet()->setCellValue('J1', 'Store code');
    $objPHPExcel->getActiveSheet()->setCellValue('K1', 'Store Name');
    $objPHPExcel->getActiveSheet()->setCellValue('L1', 'DC Location / Store Location');
    $objPHPExcel->getActiveSheet()->setCellValue('M1', 'City');
    $objPHPExcel->getActiveSheet()->setCellValue('N1', 'State');
    $objPHPExcel->getActiveSheet()->setCellValue('O1', 'CFA Location');
    $objPHPExcel->getActiveSheet()->setCellValue('P1', 'Zone');
    $objPHPExcel->getActiveSheet()->setCellValue('Q1', 'FG Code');
    $objPHPExcel->getActiveSheet()->setCellValue('R1', 'Item Description as VLCC Master');
    $objPHPExcel->getActiveSheet()->setCellValue('S1', 'PO Article No');
    $objPHPExcel->getActiveSheet()->setCellValue('T1', 'PO Item Description');
    $objPHPExcel->getActiveSheet()->setCellValue('U1', 'SKU Units');
    $objPHPExcel->getActiveSheet()->setCellValue('V1', 'Product Category');
    $objPHPExcel->getActiveSheet()->setCellValue('W1', 'EAN');
    $objPHPExcel->getActiveSheet()->setCellValue('X1', 'Master Item MRP');
    $objPHPExcel->getActiveSheet()->setCellValue('Y1', 'MRP');    
    $objPHPExcel->getActiveSheet()->setCellValue('Z1', 'Qty');
    $objPHPExcel->getActiveSheet()->setCellValue('AA1', 'CAR');
        $objPHPExcel->getActiveSheet()->setCellValue('AB1', 'Invoice');
    $objPHPExcel->getActiveSheet()->setCellValue('AC1', 'BASIC Rate');
    $objPHPExcel->getActiveSheet()->setCellValue('AD1', 'Tax');
    $objPHPExcel->getActiveSheet()->setCellValue('AE1', 'Total Amount (Excluding GST)');    
    $objPHPExcel->getActiveSheet()->setCellValue('AF1', 'Total Amount (Including GST)');    
    
    
//    $objPHPExcel->getActiveSheet()->setCellValue('AA1', 'Total QTY');
//    $objPHPExcel->getActiveSheet()->setCellValue('AB1', 'Invoice');
//    $objPHPExcel->getActiveSheet()->setCellValue('AC1', 'BASIC Rate');
//    $objPHPExcel->getActiveSheet()->setCellValue('AD1', 'Tax');
//    $objPHPExcel->getActiveSheet()->setCellValue('AE1', 'Total Amount');
    
    
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(40);
    $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(40);
    $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(40);
    $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('AD')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('AE')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('AF')->setWidth(30);
    
    
    $styleArray = array(
        'font' => array(
            'bold' => false,
//        'color' => array('rgb' => 'FF0000'),
            'size' => 10,
    ));
    $headerstyleArray = array(
        'font' => array(
            'bold' => true,
//        'color' => array('rgb' => 'FF0000'),
            'size' => 10,
    ));
  
    $objPHPExcel->getActiveSheet()->getStyle('A')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('B')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('C')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('D')->applyFromArray($styleArray);
    //$objPHPExcel->getActiveSheet()->getStyle('E')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('E')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('F')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('G')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('H')->applyFromArray($styleArray);//H
    $objPHPExcel->getActiveSheet()->getStyle('I')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('J')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('K')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('L')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('M')->applyFromArray($styleArray);//M
    $objPHPExcel->getActiveSheet()->getStyle('N')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('O')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('P')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('Q')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('R')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('S')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('T')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('U')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('V')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('W')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('X')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('Y')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('Z')->applyFromArray($styleArray);
  //  $objPHPExcel->getActiveSheet()->getStyle('AA')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('AB')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('AC')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('AD')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('AE')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('AF')->applyFromArray($styleArray);
    
    $colCount = 0;
    $rowCount = 2;
    $db = new DBConn();

    $today_dt = date('Y-m-d');
    $srt_dt = date('Y-m-d');
    $get_cuttent_time = date('Y-m-d H:i:s');
//    echo "Current time: ".$get_cuttent_time."<br>";
    $time = date('Y-m-d 14:00:00');
    if($get_cuttent_time < $time){
         $st_dt = $srt_dt . " 00:00:00 ";
    }else{
        $st_dt = $srt_dt . " 14:00:00 ";
	//$st_dt = $srt_dt . " 00:00:00 ";
    }
//       $st_dt = "2018-10-01 00:00:00 ";
    $st_dt_db = $db->safe(trim($st_dt));
    $ed_dt = $today_dt . " 23:59:59 ";
    $ed_dt_db = $db->safe(trim($ed_dt));

//$query="select ip.invoice_no, ip.invoice_date, ip.delivery_date, ip.expiry_date, ip.tqty, ip.tamt,ip.ctime as Intouch, ip.shipping_id as sid,ipt.mrp, ipt.qty,ipt.tot_qty,ipt.pack_type as CAR,ipt.cost_price,ipt.vat, ipt.amt, ipt.master_item_id ,imd.name as dealername,imd.show_code as showflag, idt.itemcode as articleno,idt.itemname as description,  d.code as distid, d.name as distname, sh.dc_address as address,sh.dc_state as state,sh.dc_city as city,sh.dc_name as name, bu.code as vendorcode,ip.invoice_text,sh.customer_code from  it_master_dealers imd, it_dealer_items idt,it_shipping_address sh, it_distributors d, it_business_unit bu, it_po ip, it_po_items ipt  where ip.id=ipt.po_id  AND  ip.dist_id=d.id and d.bu_id= bu.id and sh.id = ip.shipping_id  and  ip.master_dealer_id=imd.id AND idt.id= ipt.dealer_item_id and idt.is_vlcc = 1  AND ip.ctime between $st_dt_db and $ed_dt_db and ip.status =1 and imd.id not in (7,11) order by dealername,ip.invoice_no";
//$query="select ip.invoice_no, ip.invoice_date, ip.delivery_date, ip.expiry_date, ip.tqty, ip.tamt,ip.ctime as Intouch, ip.shipping_id as sid,ipt.mrp, ipt.qty,ipt.tot_qty,ipt.pack_type as CAR,ipt.cost_price,ipt.vat, ipt.amt, ipt.master_item_id ,imd.id as master_dealer_id,imd.name as dealername,imd.show_code as showflag, idt.itemcode as articleno,idt.itemname as description, idt.eancode, d.code as distid, d.name as distname, sh.dc_address as address,sh.dc_state as state,sh.dc_city as city,sh.dc_name as name, bu.code as vendorcode,ip.invoice_text,sh.customer_code from  it_master_dealers imd, it_dealer_items idt,it_shipping_address sh, it_distributors d, it_business_unit bu, it_po ip, it_po_items ipt  where ip.id=ipt.po_id  AND  ip.dist_id=d.id and d.bu_id= bu.id and sh.id = ip.shipping_id and ip.master_dealer_id=imd.id AND idt.id= ipt.dealer_item_id AND ip.ctime between $st_dt_db and $ed_dt_db and ip.status =1 and ip.is_active=1 order by dealername,ip.invoice_no"; 
    $query="select ip.invoice_no, ip.invoice_date, ip.delivery_date, ip.expiry_date, ip.tqty, ip.tamt,ip.ctime as Intouch, ip.shipping_id as sid,ipt.mrp, ipt.qty,ipt.tot_qty,ipt.pack_type as CAR,ipt.cost_price,ipt.vat, ipt.amt, ipt.master_item_id ,imd.id as master_dealer_id,imd.name as dealername,imd.show_code as showflag, idt.itemcode as articleno,idt.itemname as description, idt.eancode, d.code as distid, d.name as distname, sh.dc_address as address,sh.dc_state as state,sh.dc_city as city,sh.dc_name as name, bu.code as vendorcode,ip.invoice_text,sh.customer_code from  it_master_dealers imd, it_dealer_items idt,it_shipping_address sh, it_distributors d, it_business_unit bu, it_po ip, it_po_items ipt  where ip.id=ipt.po_id  AND  ip.dist_id=d.id and d.bu_id= bu.id and sh.id = ip.shipping_id and ip.master_dealer_id=imd.id AND idt.id= ipt.dealer_item_id AND ip.ctime between $st_dt_db and $ed_dt_db and ip.status not in (10) and ip.is_active=1 and idt.is_vlcc not in (2) order by dealername,ip.invoice_no";  
    echo $query."<br/>";
   $result = $db->getConnection()->query($query);

    $srno = 1;
    while ($obj = $result->fetch_object()) {
        $EAN="";
        $sku="";  
        $productgrp=""; 
        $category= "";
        $address = $obj->address;//str_replace("                                          ", " ", $obj->address);
        $itemname_po = $obj->description;
       // $brand = explode(" ", $itemname_po);
        $inv_no= (string)$obj->invoice_no;
        $PO_date = explode(" ", $obj->invoice_date);
        $Del_date = explode(" ", $obj->delivery_date);
        $Exp_date = explode(" ", $obj->expiry_date);
        $City = strtoupper($obj->city);  // providing city name in capital
        $State = strtoupper($obj->state); //providing state name in capital
        $State_str = str_replace(" ","",$State);
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
        
        if(trim($EAN)=="" || trim($EAN)==NULL){
            $EAN = $obj->eancode;
        }
        
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
        
        if($obj->master_dealer_id==3 || $obj->master_dealer_id==4 || $obj->master_dealer_id==5 || $obj->master_dealer_id==41 || $obj->master_dealer_id==49){
            $amt = $obj->amt;
        }else{
            $amt = round($obj->amt/1.18,2);
        }
        
         if($obj->master_dealer_id==3 || $obj->master_dealer_id==4 || $obj->master_dealer_id==5 || $obj->master_dealer_id==41 || $obj->master_dealer_id==49){
            $inc_amt = round(1.18*$obj->amt,2);
        }else{
            $inc_amt = $obj->amt;
        }

	//if(trim($obj->CAR)==''){
	//	$obj->CAR = "EA";
	//}

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
        
         //if($obj->master_dealer_id==2){
            if(preg_match('/whsm/i',$address)==1){
                $obj->dealername = "WH Smith";
                $cust_name = ": Travel News Services (India) Pvt Ltd";
            }
        //}
    
              $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $srno);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $inv_no);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, $PO_date[0]);        
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $rowCount, $Exp_date[0]);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $rowCount, $obj->distid);         
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $rowCount, $vendor_name);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $rowCount, $obj->dealername);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $rowCount, $cust_name);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $rowCount, $cust_code);
       $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $rowCount, $obj->vendorcode);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10, $rowCount, $obj->name);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(11, $rowCount, $address);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(12, $rowCount, $City);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(13, $rowCount, $State);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(14, $rowCount, $cf_location);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(15, $rowCount, $zone_name);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(16, $rowCount, $productgrp);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(17, $rowCount, $itemname);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(18, $rowCount, $obj->articleno);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(19, $rowCount, $itemname_po);        
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(20, $rowCount, $sku);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(21, $rowCount, $category);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(22, $rowCount, $EAN);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(23, $rowCount, $master_mrp);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(24, $rowCount, $obj->mrp);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(25, $rowCount, $obj->qty);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(26, $rowCount, $obj->CAR);
      //  $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(26, $rowCount, $obj->qty);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(27, $rowCount, " ");
         $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(28, $rowCount, $obj->cost_price);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(29, $rowCount, $obj->vat);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(30, $rowCount, $amt);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(31, $rowCount, $inc_amt);
        
        $colCount = 0;
        $rowCount++;
        $srno++;
    }
    */
    //$nowtime=date('Y-m-d H:i:s');
     $nowtime = date('Y-m-d');
    $name = "DailyPOReportInternal_" . $nowtime;
    $Ext = ".xls";
    $Filename = DEF_PARSED_DAILY_EXL_PATH . $name . $Ext;
   
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save($Filename);
    print"<br>excel created";
} catch (Exception $xcp) {
    print $xcp->getMessage();
}
?>
