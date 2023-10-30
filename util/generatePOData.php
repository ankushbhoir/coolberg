<?php
ini_set('memory_limit', '-1'); //For allowing unlimited memory on server
ini_set('max_execution_time', 180);  
require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once "lib/php/Classes/PHPExcel.php";
require_once 'lib/php/Classes/PHPExcel/Writer/Excel2007.php';

try {
    //Dealer wise sell
    $sheetIndex = 0;
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->setActiveSheetIndex($sheetIndex);
    $objPHPExcel->getActiveSheet()->setTitle('Dealer Wise Sell');
    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Chain Name');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Total Quantity');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Total amount');
    
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);

    
    $styleArray = array(
        'font' => array(
            'bold' => false,
            'size' => 10,
    ));
    $headerstyleArray = array(
        'font' => array(
            'bold' => true,
            'size' => 10,
    ));
  
    $objPHPExcel->getActiveSheet()->getStyle('A')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('B')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('C')->applyFromArray($styleArray);
    
    $colCount = 0;
    $rowCount = 2;
    $db = new DBConn();

    $query = "select round(sum(pi.tot_qty),2) as tot_qty,round(sum(pi.amt),2) as tot_amt,md.name from it_master_dealers md,it_po p,it_po_items pi where p.id=pi.po_id and p.status=1 and p.master_dealer_id=md.id and md.id not in (7,11) group by md.id order by md.name";  
   $result = $db->getConnection()->query($query);

    while ($obj = $result->fetch_object()) {        
       $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $obj->name);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $obj->tot_qty);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, $obj->tot_amt);
        
        $colCount = 0;
        $rowCount++;
    }
    
    //Item wise sell
    $sheetIndex++;
    $objPHPExcel->createSheet();
    $objPHPExcel->setActiveSheetIndex($sheetIndex);
    $objPHPExcel->getActiveSheet()->setTitle('Chain Wise Item Sell');
    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Chain Name');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Item Name');
  //  $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Quantity');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Amount');
    
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
 //   $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);

    
    $styleArray = array(
        'font' => array(
            'bold' => false,
            'size' => 10,
    ));
    $headerstyleArray = array(
        'font' => array(
            'bold' => true,
            'size' => 10,
    ));
  
    $objPHPExcel->getActiveSheet()->getStyle('A')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('B')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('C')->applyFromArray($styleArray);
   // $objPHPExcel->getActiveSheet()->getStyle('D')->applyFromArray($styleArray);
    
    $colCount = 0;
    $rowCount = 2;
    $db = new DBConn();

    $qry = "select id,name from it_master_dealers where id not in (7,11) order by name";
    $objj = $db->fetchAllObjects($qry);
    
    foreach($objj as $ob){
        $dealer_name = $ob->name;
        $qry1 = "select round(sum(pi.tot_qty),2) as tot_qty,round(sum(pi.amt),2) as tot_amt,mi.itemname from it_po p,it_po_items pi,it_master_items mi where p.id=pi.po_id and p.status=1 and pi.master_item_id=mi.id and p.master_dealer_id=$ob->id and mi.itemname is not null group by mi.id order by tot_amt desc limit 1";
    
   $result = $db->getConnection()->query($qry1);

    while ($obj = $result->fetch_object()) {        
       $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $dealer_name);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $obj->itemname);
        //$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, $obj->tot_qty);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, $obj->tot_amt);
        
        $colCount = 0;
        $rowCount++;
    }
}

    //Location wise sell
        $sheetIndex++;
    $objPHPExcel->createSheet();
    $objPHPExcel->setActiveSheetIndex($sheetIndex);
    $objPHPExcel->getActiveSheet()->setTitle('Location Wise Sell');
    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Chain Name');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', 'City');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', 'State');
   
    
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
   

    
    $styleArray = array(
        'font' => array(
            'bold' => false,
            'size' => 10,
    ));
    $headerstyleArray = array(
        'font' => array(
            'bold' => true,
            'size' => 10,
    ));
  
    $objPHPExcel->getActiveSheet()->getStyle('A')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('B')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('C')->applyFromArray($styleArray);
   
    
    $colCount = 0;
    $rowCount = 2;
    $db = new DBConn();

$qry = "select id,name from it_master_dealers where id not in (7,11) order by name";
    $objj = $db->fetchAllObjects($qry);
    
    foreach($objj as $ob){
        $dealer_name = $ob->name;
        $qry1 = "select sh.dc_city,sh.dc_state,round(sum(pi.amt),2) as tot_amt from it_po p,it_shipping_address sh,it_po_items pi where p.status=1 and p.id=pi.po_id and p.shipping_id=sh.id and p.master_dealer_id=$ob->id group by p.shipping_id order by tot_amt desc limit 1";
    
   $result = $db->getConnection()->query($qry1);

    while ($obj = $result->fetch_object()) {        
       $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $dealer_name);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $obj->dc_city);
        //$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, $obj->tot_qty);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, $obj->dc_state);
        
        $colCount = 0;
        $rowCount++;       
    }
}
    
    //Quantity wise sell
    $sheetIndex++;
    $objPHPExcel->createSheet();
    $objPHPExcel->setActiveSheetIndex($sheetIndex);
    $objPHPExcel->getActiveSheet()->setTitle('Quantity Wise Sell');
    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'EAN Code');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Item Name');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Quantity');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Amount');
    
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);

    
    $styleArray = array(
        'font' => array(
            'bold' => false,
            'size' => 10,
    ));
    $headerstyleArray = array(
        'font' => array(
            'bold' => true,
            'size' => 10,
    ));
  
    $objPHPExcel->getActiveSheet()->getStyle('A')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('B')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('C')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('D')->applyFromArray($styleArray);
    
    $colCount = 0;
    $rowCount = 2;
    $db = new DBConn();

$query = "select mi.itemcode,mi.itemname,round(sum(pi.tot_qty),2) as tot_qty,round(sum(pi.amt),2) as tot_amt from it_po p,it_po_items pi,it_master_items mi where p.id=pi.po_id and p.status=1 and pi.master_item_id=mi.id and mi.itemname is not null and p.master_dealer_id not in (7,11)  group by mi.id order by tot_qty desc;";  
   $result = $db->getConnection()->query($query);

    while ($obj = $result->fetch_object()) {        
       $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $obj->itemcode);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $obj->itemname);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, $obj->tot_qty);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $rowCount, $obj->tot_amt);
        
        $colCount = 0;
        $rowCount++;        
    }                
    
    
    //Amount wise sell
    $sheetIndex++;
    $objPHPExcel->createSheet();
    $objPHPExcel->setActiveSheetIndex($sheetIndex);
    $objPHPExcel->getActiveSheet()->setTitle('Amount Wise Sell');
    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'EAN Code');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Item Name');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Quantity');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Amount');
    
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);

    
    $styleArray = array(
        'font' => array(
            'bold' => false,
            'size' => 10,
    ));
    $headerstyleArray = array(
        'font' => array(
            'bold' => true,
            'size' => 10,
    ));
  
    $objPHPExcel->getActiveSheet()->getStyle('A')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('B')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('C')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('D')->applyFromArray($styleArray);
    
    $colCount = 0;
    $rowCount = 2;
    $db = new DBConn();

$query = "select mi.itemcode,mi.itemname,round(sum(pi.tot_qty),2) as tot_qty,round(sum(pi.amt),2) as tot_amt from it_po p,it_po_items pi,it_master_items mi where p.id=pi.po_id and p.status=1 and pi.master_item_id=mi.id and mi.itemname is not null and p.master_dealer_id not in (7,11)  group by mi.id order by tot_amt desc;";  
   $result = $db->getConnection()->query($query);

    while ($obj = $result->fetch_object()) {        
       $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $obj->itemcode);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $obj->itemname);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, $obj->tot_qty);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $rowCount, $obj->tot_amt);
        
        $colCount = 0;
        $rowCount++;        
    }
    
   
    $name = "VLCC_Report";
    $Ext = ".xls";
    $Filename =  $name . $Ext;
   
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save($Filename);
    print"<br>excel created";
} catch (Exception $xcp) {
    print $xcp->getMessage();
}
?>
