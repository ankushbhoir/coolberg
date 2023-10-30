<?php
ini_set('memory_limit', '-1'); //For allowing unlimited memory on server
ini_set('max_execution_time', 180);  
require_once('../../it_config.php');
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once 'lib/core/strutil.php';
require_once "lib/php/Classes/PHPExcel.php";
require_once 'lib/php/Classes/PHPExcel/Writer/Excel2007.php';
require_once "lib/email/EmailHelper.php";
//require_once("../../it_config.php");
//require_once("/home/camlin/it_config.php");
//require_once "/home/camlin/public_html/home/lib/db/DBConn.php";
//require_once "/home/camlin/public_html/home/lib/core/Constants.php";
//require_once "/home/camlin/public_html/home/lib/php/Classes/PHPExcel.php";
//require_once '/home/camlin/public_html/home/lib/php/Classes/PHPExcel/Writer/Excel2007.php';





try {
    $sheetIndex=0;
//$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory_serialized;
//$cacheSettings = array( 'memoryCacheSize' => '1500MB');
//PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
// Create new PHPExcel object
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
// Create a first sheet
 $objPHPExcel = new PHPExcel();
                $rowCount = 2;

    //$st_dt_db=$db->safe('2020-10-12 00:00:00');
    //$ed_dt_db=$db->safe('2020-10-12 23:59:59');

    
    
    $emailHelper = new EmailHelper();

    
    $colCount = 0;
    
    $db = new DBConn();
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

    echo $main_query="select id,invoice_no from it_po where ctime between $st_dt_db and 
         $ed_dt_db and status not in (10,3,7,9,21,13)";
         $result_id = $db->getConnection()->query($main_query);
         $poids=array();
         $i=0;
          while ($obj_res = $result_id->fetch_object()) {
            
            $poids[$i]=$obj_res->id;
            $i++;
       }           
       print_r($poids);
       $posid = implode(', ', $poids);
       echo $posid;
    
      
    $query="select ip.invoice_no, ip.invoice_date, ip.delivery_date, ip.expiry_date, ip.tqty, ip.tamt,ip.ctime, ip.shipping_id as sid,ipt.mrp, ipt.qty,ipt.tot_qty, ipt.ttk_qty, ipt.ttk_uom,ipt.pack_type as CAR,ipt.cost_price,ipt.vat, ipt.amt, ipt.master_item_id ,imd.id as master_dealer_id,imd.displayname as dealername,imd.show_code as showflag, idt.itemcode as articleno,idt.itemname as description, d.code as distid, d.name as distname, sh.dc_address as address,sh.dc_state as state,sh.dc_city as city,sh.dc_name as name, bu.code as vendorcode,ip.invoice_text,sh.customer_code,ipt.po_itemname as description_po,ipt.po_eancode, istp.ship_to_party, ivpm.plant, ivpm.storage_location_code,iesm.product_name,iesm.mrp,iesm.gst ,ipt.po_hsn,iesm.gst,iesm.inner_size,iesm.case_size,istp.margin,istp.customer_name,istp.site
    
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
    ip.id in (".$posid.") and
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
    ip.status not in (10,3,7,9,21,13) and 
    ip.is_active=1 

    order by dealername,ip.invoice_no";  
   
    echo $query."\n";
      
   $result = $db->getConnection()->query($query);
   $obj='';
    $srno = 1;
    while ($obj = $result->fetch_object()) {




      
        if(!empty($obj->expiry_date)){
        $datePartes = explode(" ",$obj->expiry_date) ;
        $change_date=$datePartes[0];

        $change_time=$datePartes[1];
        }
        else
        {
            $change_date='';
        $change_time='';
        }
        $ctime=explode(" ",$obj->ctime);
        $change_date=$ctime[0];
        $change_time=$ctime[1];
        $newDate = date("d.m.Y", strtotime($change_date));
        $new_invoice_date = date("d.m.Y",strtotime($obj->invoice_date));
        //$Exp_date = explode(" ", $obj->expiry_date);
         $Exp_date = date("d.m.Y",strtotime($obj->expiry_date));
      if($obj->ttk_qty==0){
        $obj->ttk_qty=$obj->qty;
        }

        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $srno);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $obj->plant);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, $obj->invoice_no);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $rowCount, $new_invoice_date);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $rowCount, $Exp_date);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $rowCount, 'E001');
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
         
    
    
 $nowtime = date('Y-m-d');
    echo $name = "DailyPOReport_" . $nowtime;
    $Ext = ".xls";
   echo  $Filename = DEF_PARSED_DAILY_EXL_PATH . $name . $Ext;
        
   $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save($Filename);
   
    $db->execUpdate("update it_po set status=21 where id in (".$posid.")");
    sleep(4);
}
     print"<br>excel created";

        $subject = "CSV Created for PO:$obj_res->invoice_no";
        $body = "<br>Hi<br>";
        $body .= "<p>CSV file is created for the purchase order $obj_res->invoice_no </p>";
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
  <p>Please Download the attachment</p>';
  $body .= '</table>';
 $body .= "<p>Kindly Take Action </p>";
 $body .= "<p> <br>Regards,</p>";
 $body .= "<p>Intouch Consumer Care Solutions Pvt Ltd</p>";
$body.='</body>
</html>';
$toArray = array(
                   "aashtekar@intouchrewards.com",
                   "igoyal@intouchrewards.com",
                   "akanksha.s@mamaearth.in",
                   "deepak.j@mamaearth.in"
                  
            
                
                      
        );
//$toArray=array('aashtekar@intouchrewards.com','srajkunthwar@intouchrewards.com');

      $errormsg = $emailHelper->send($toArray, $subject, $body,$Filename);
        if ($errormsg != "0") {
            $errors['mail'] = " <br/> Error in sending mail, please try again later.";
            return -1;
        } 

        else{
            print"<br>Mail send successfully";
          //  return 1;
        }

   //adding mrp column

} catch (Exception $xcp) {
    print $xcp->getMessage();
}
?>
