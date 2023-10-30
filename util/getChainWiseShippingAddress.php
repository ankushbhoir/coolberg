<?php
ini_set('memory_limit', '-1'); //For allowing unlimited memory on server
ini_set('max_execution_time', 180);  
require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once "lib/php/Classes/PHPExcel.php";
require_once 'lib/php/Classes/PHPExcel/Writer/Excel2007.php';

try {
    $sheetIndex = 0;
// Create new PHPExcel object
    $objPHPExcel = new PHPExcel();
// Create a first sheet
    $objPHPExcel->setActiveSheetIndex($sheetIndex);
    $objPHPExcel->getActiveSheet()->setTitle('Addresses');
    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'ID');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Vendor Code');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Vendor Name');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Chain Name');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Shipping Address');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Customer Code');
    
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(50);
    
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
    
    $colCount = 0;
    $rowCount = 2;
    $db = new DBConn();  

    $query = "select p.dist_id,p.shipping_id,md.name from it_po p,it_master_dealers md where md.id=p.master_dealer_id and md.id=5 group by p.shipping_id order by md.name";
    $result = $db->getConnection()->query($query);

     
        $srno = 1;
        while ($obj = $result->fetch_object()) {
         $dist = $db->fetchObject("select id,name,code from it_distributors where id=$obj->dist_id");
        
        if(isset($dist) && !empty($dist)){
            $dist_name = $dist->name;
            $vendor_code = $dist->code;
        }else{
            $dist_name = "";
            $vendor_code = "";
        }
        
        $shipping = $db->fetchObject("select id,master_dealer_id,shipping_address,dc_address,customer_code from it_shipping_address where id=$obj->shipping_id");
        
        if(isset($shipping) && !empty($shipping)){
            $shipping_addr = $shipping->dc_address;
            $shipping_id = $shipping->id;
            $master_dealer = $db->fetchObject("select id,name from it_master_dealers where id=$shipping->master_dealer_id");
            $dealerName = $master_dealer->name;
            $cust_code = $shipping->customer_code;
        }else{
            $shipping_addr = "";
            $shipping_id = "";
            $dealerName="";
            $cust_code = "";
        }
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $shipping_id);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $vendor_code);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, $dist_name);
       $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $rowCount, $dealerName);
       $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $rowCount, $shipping_addr);      
       $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $rowCount, $cust_code);      
        
        $colCount = 0;
        $rowCount++;
        $srno++;    
}

     $nowtime = date('Y-m-d');
    $name = "Reliance_addresses";
    $Ext = ".xls";
    $Filename = $name . $Ext;
   
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save($Filename);
    print"<br>excel created";
} catch (Exception $xcp) {
    print $xcp->getMessage();
}
?>
