<?php
// ini_set('memory_limit', '-1'); //For allowing unlimited memory on server
// ini_set('max_execution_time', 180);  
// ini_set('memory_limit', '-1'); //For allowing unlimited memory on server
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once 'lib/core/strutil.php';
require_once "lib/php/Classes/PHPExcel.php";
require_once 'lib/php/Classes/PHPExcel/Writer/Excel2007.php';
extract($_GET);
$po_ids = ($_GET['ids']);
// print_r($_GET);
//extract($_POST);
//print_r($_POST);
// return;
//$po_ids=122;
$errors = array();
$chkbxarr = array();
try {

    $sheetIndex=0;

//$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory_serialized;
//$cacheSettings = array( 'memoryCacheSize' => '1500MB');
//PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
// Create new PHPExcel object
    $objPHPExcel = new PHPExcel();
// Create a first sheet
    $objPHPExcel->setActiveSheetIndex($sheetIndex);
    $objPHPExcel->setActiveSheetIndex($sheetIndex);
    $objPHPExcel->getActiveSheet()->setTitle('Daily Complete Data');
    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Sr.No.');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Plant');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Customer Ref');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Po Date');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Po Expiry Date');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Storage Location');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Customer');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', 'Customer name');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', 'EAN/UPC');
    $objPHPExcel->getActiveSheet()->setCellValue('J1', 'Product Name');
    $objPHPExcel->getActiveSheet()->setCellValue('K1', 'Case Size');
    $objPHPExcel->getActiveSheet()->setCellValue('L1', 'Inner size');
    $objPHPExcel->getActiveSheet()->setCellValue('M1', 'GST');
    $objPHPExcel->getActiveSheet()->setCellValue('N1', 'MRP');
    $objPHPExcel->getActiveSheet()->setCellValue('O1', 'Margin');
    $objPHPExcel->getActiveSheet()->setCellValue('P1', 'Rate');
    $objPHPExcel->getActiveSheet()->setCellValue('Q1', 'Order Quantity');
    $objPHPExcel->getActiveSheet()->setCellValue('R1', 'NSV Value');
    
    
    
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(40);
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(18);
    $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(13);
    $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(11);
    $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(13);
    $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(13);
 
    
    
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
    $objPHPExcel->getActiveSheet()->getStyle('E')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('F')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('G')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('H')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('I')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('J')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('K')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('L')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('M')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('N')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('O')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('P')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('Q')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('R')->applyFromArray($styleArray);
    
    $colCount = 0;
    $rowCount = 2;
    $db = new DBConn();

    $fileNames = "";

    $idArray = explode(",", $po_ids);
    // foreach ($idArray as $poId) {
    //     $qry = "select case when new_filename is null then po_filenames else new_filename end as filename from it_po_details where id = $poId";
    //     $objPO = $db->fetchObject($qry);
        
    //     if($fileNames == ""){
    //         $fileNames .= "'$objPO->filename'";
    //     }else{
    //         $fileNames .= ",'$objPO->filename'";
    //     }
    // }

   $query="select ip.invoice_no, ip.invoice_date, ip.delivery_date, ip.expiry_date, ip.tqty, ip.tamt,ip.ctime, ip.shipping_id as sid,ipt.mrp, ipt.qty,ipt.tot_qty, ipt.ttk_qty, ipt.ttk_uom,ipt.pack_type as CAR,ipt.cost_price,ipt.vat, ipt.amt, ipt.master_item_id ,imd.id as master_dealer_id,imd.displayname as dealername,imd.show_code as showflag, idt.itemcode as articleno,idt.itemname as description, d.code as distid, d.name as distname, sh.dc_address as address,sh.dc_state as state,sh.dc_city as city,sh.dc_name as name, bu.code as vendorcode,ip.invoice_text,sh.customer_code,ipt.po_itemname as description_po,ipt.po_eancode, istp.ship_to_party, ivpm.plant,ivpm.storage_location_code, iesm.product_name,iesm.mrp,iesm.gst ,ipt.po_hsn,iesm.gst,iesm.inner_size,iesm.case_size,istp.margin,istp.customer_name,istp.site
    
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
    ip.id in (".$po_ids.") and
    ivpm.master_dealer_id = imd.id and 
    ivpm.vendor_number = d.code and 
    iesm.ean = ipt.po_eancode and 
    istp.master_dealer_id = imd.id and 
    iesm.master_dealer_id=imd.id and
    istp.site = bu.code and 
    ip.id=ipt.po_id  AND  
    ip.dist_id=d.id and 
    d.bu_id= bu.id and 
    sh.id = ip.shipping_id and 
    ip.master_dealer_id=imd.id AND 
    idt.id= ipt.dealer_item_id AND 
    ip.status not in (10,3) and 
    ip.is_active=1 

    order by dealername,ip.invoice_no";
 
       //echo $query."<br/>";
     
     
   $result = $db->getConnection()->query($query);

    $srno = 1;
    while ($obj = $result->fetch_object()) {
        $filename_arr=array();
        $filename_arr[0]=$obj->plant;
        $filename_arr[1]=$obj->abbr;
        $filename_arr[2]=$obj->invoice_no;
        $EAN="";
        $sku="";  
        $productgrp=""; 
        $category= "";
        $address = $obj->address;//str_replace("                                          ", " ", $obj->address);
        $itemname_po = $obj->description;
     //   $itemname_po = $obj->description_po;
        $inv_no= (string)$obj->invoice_no;
        $PO_date = explode(" ", $obj->invoice_date);
        $Del_date = explode(" ", $obj->delivery_date);
        $newDate = date("d/m/Y", strtotime($obj->delivery_date));  
        $Exp_date = explode(" ", $obj->expiry_date);

        $email_date = explode(" ", $obj->ctime);
        $new_email_date= date("d/m/Y", strtotime($obj->ctime));
        $po_date123 = date("d/m/Y", strtotime($obj->invoice_date));
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
        
        
        $sup_id= trim($obj->distid);
        if(trim($obj->showflag == 0)){
            $sup_id=" ";
        }        
        
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

         //if($obj->master_dealer_id==2){
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

               //$obj->cost_price=$obj->cost_price/$cal_value;
               //$obj->cost_price=round( $obj->cost_price, 2); 

              
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
            if($obj->master_dealer_id==56){
                $old_price=$obj->cost_price;
                
                

              $obj->cost_price=$obj->cost_price/$obj->ttk_qty;
              $obj->cost_price=round( $obj->cost_price, 2); 

              
            }
                   if($obj->master_dealer_id==55){
                $old_price=$obj->cost_price;
                
                

              $obj->cost_price=$obj->cost_price/$obj->ttk_qty;
              $obj->cost_price=round( $obj->cost_price, 2); 

              
            }

               if(preg_match('/whsm/i',$address)==1){
                $obj->dealername = "WH Smith";
                $cust_name = ": Travel News Services (India) Pvt Ltd";
            }
        //}
     if($obj->ttk_qty==0){
        $obj->ttk_qty=$obj->qty;
        }
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $srno);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $obj->plant);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, $obj->invoice_no);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $rowCount, $PO_date[0]);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $rowCount, $Exp_date[0]);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $rowCount,$obj->storage_location_code);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $rowCount, $obj->ship_to_party);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $rowCount, $obj->customer_name."-".$obj->site);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $rowCount, $obj->po_eancode);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $rowCount, $obj->product_name);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10, $rowCount, $obj->case_size);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(11, $rowCount, $obj->inner_size);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(12, $rowCount, $obj->gst);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(13, $rowCount, $obj->mrp);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(14, $rowCount, $obj->margin);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(15, $rowCount, $obj->cost_price);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(16, $rowCount, $obj->ttk_qty);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(17, $rowCount, $obj->amt);
        
        $colCount = 0;
        $rowCount++;
        $srno++;
    }
// print_r($filename_arr);
// exit;
    $ndate = date('Ymd');
    $ntime  = date('his');
    $name = $filename_arr[0]."_". $filename_arr[1]."_".$filename_arr[2]."_".$ndate."_".$ntime;
    $Ext = ".xls";
    $filename = $name . $Ext;
    header('Content-Disposition: attachment;filename=' . $filename);
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    ob_end_clean();
    ob_start();
    $objWriter->save('php://output');
    $db->closeConnection();
} catch (Exception $xcp) {
    print $xcp->getMessage();
}