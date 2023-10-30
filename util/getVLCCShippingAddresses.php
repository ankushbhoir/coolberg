<?php
ini_set('memory_limit', '-1'); //For allowing unlimited memory on server
ini_set('max_execution_time', 180);  
require_once("../../it_config.php");
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
    $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Vendor Name');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Chain Name');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Shipping Address');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Customer Code');
    
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(45);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
    
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
    
    $get_cuttent_time = date('Y-m-d H:i:s');
//    echo "Current time: ".$get_cuttent_time."<br>";
    $time = date('Y-m-d 14:00:00');
    if($get_cuttent_time < $time){
         $date = date('Y-m-d 00:00:00');
    }else{
        $date = date('Y-m-d 14:00:00');
    }
//    $date = date('Y-m-d 00:00:00');
    $date_db = $db->safe($date);

    $query = "select p.dist_id,p.shipping_id,md.name from it_po p,it_master_dealers md where md.id=p.master_dealer_id and p.ctime >= $date_db group by p.shipping_id order by md.name";
//    $query = "select p.dist_id,p.shipping_id,md.name from it_po p,it_master_dealers md where md.id=p.master_dealer_id and md.id in (2,3,4,5,8,11,14,15,16,26,7,27,33) group by p.shipping_id order by md.name";
    echo $query;
    $result = $db->getConnection()->query($query);

     
        $srno = 1;
        while ($obj = $result->fetch_object()) {
         $dist = $db->fetchObject("select id,name from it_distributors where id=$obj->dist_id");
        
        if(isset($dist) && !empty($dist)){
            $dist_name = $dist->name;
        }else{
            $dist_name = "";
        }
        
        if(preg_match('/(vlcc|VLCC|Vlcc)/',$dist_name)){
            $vendor_name = "VLCC Personal Care Ltd.";
        //}//else{
          //  $vendor_name = $dist_name;
        //}
        
        $shipping = $db->fetchObject("select id,master_dealer_id,shipping_address,dc_address,customer_code from it_shipping_address where id=$obj->shipping_id and customer_code is NULL");
        
        if(isset($shipping) && !empty($shipping)){
            $shipping_addr = $shipping->dc_address;
            $shipping_id = $shipping->id;
            $master_dealer = $db->fetchObject("select id,name from it_master_dealers where id=$shipping->master_dealer_id");
            $dealerName = $master_dealer->name;
            $cust_code = $shipping->customer_code;
             $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $shipping_id);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $vendor_name);
       $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, $dealerName);
       $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $rowCount, $shipping_addr);      
       $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $rowCount, $cust_code);  
       
       $colCount = 0;
        $rowCount++;
        $srno++;   
        }/*else{
            $shipping_addr = "";
            $shipping_id = "";
            $dealerName="";
            $cust_code = "";
        }*/
           
        
        
        }
}

     $nowtime = date('Y-m-d');
    $name = "Missing_customer_code_".$nowtime;
    $Ext = ".xls";
   // $Filename = $name . $Ext;
   $Filename = DEF_MISSING_CUST_XLS . $name . $Ext;
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save($Filename);
    print"<br>excel created";
} catch (Exception $xcp) {
    print $xcp->getMessage();
}
?>
