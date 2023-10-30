<?php
ini_set('memory_limit', '-1'); //For allowing unlimited memory on server
ini_set('max_execution_time', 120);  
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once "lib/php/Classes/PHPExcel.php";
require_once 'lib/php/Classes/PHPExcel/Writer/Excel2007.php';

try {
    $sheetIndex = 0;
// Create new PHPExcel object
    $objPHPExcel = new PHPExcel();
// Create a first sheets
    $objPHPExcel->setActiveSheetIndex($sheetIndex);
    $objPHPExcel->getActiveSheet()->setTitle('StatusReport');
    $objPHPExcel->getActiveSheet()->setCellValue('A5', 'Sr.No');
    $objPHPExcel->getActiveSheet()->setCellValue('B5', 'Chain Name');
    $objPHPExcel->getActiveSheet()->setCellValue('C5', 'Total Files Received');
    $objPHPExcel->getActiveSheet()->setCellValue('D5', 'Sucessfully Processed');
    $objPHPExcel->getActiveSheet()->setCellValue('E5', 'Not Weikfield PO');
    $objPHPExcel->getActiveSheet()->setCellValue('F5', 'Issue At Processing');
    $objPHPExcel->getActiveSheet()->setCellValue('G5', 'UBU');
    $objPHPExcel->getActiveSheet()->setCellValue('H5', 'GR');
    $objPHPExcel->getActiveSheet()->setCellValue('I5', 'Missing EAN');
    $objPHPExcel->getActiveSheet()->setCellValue('J5', 'Missing Article');
    $objPHPExcel->getActiveSheet()->setCellValue('K5', 'Duplicate');
    
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(5);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(5);
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
   
    $styleArray = array(
        'font' => array(
            'Name'=> 'Times New Roman',
            'bold' => false,
       //'color' => array('rgb' => 'FF0000'),
            'size' => 10,           
    ));
    $headerstyleArray = array(
        'font' => array(
            //'Name'=> 'Arial',
            'bold' => true,
           //'color' => array('rgb' => 'FF0000'),
            'size' => 10,        
    ));   
    $borderarray= array('borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                            //'color' => array('rgb' => 'FF0000'),
                    ),
    ));   
    $alignmentstyle = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY,
        )
    );
    
    $header = 'A5:K5';
    $data= 'A6:K13';
        
    $objPHPExcel->getActiveSheet()->getStyle($header)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('00ffff00');
    $objPHPExcel->getActiveSheet()->getStyle($header)->applyFromArray($headerstyleArray);
    $objPHPExcel->getActiveSheet()->getStyle($header)->applyFromArray($borderarray);
    
    $objPHPExcel->getActiveSheet()->getStyle($data)->applyFromArray($borderarray);
   
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
   
    $rowCount = 6;
    $srno=1;
    $db = new DBConn();
    $today_dt = date('Y-m-d');
    $st_dt = $today_dt." 00:00:00 ";
    $st_dt_db = $db->safe(trim($st_dt));
    $ed_dt = $today_dt. " 23:59:59 ";
    $ed_dt_db = $db->safe(trim($ed_dt));
    
    $objPHPExcel->getActiveSheet()->mergeCells('A2:E3');
    $objPHPExcel->getActiveSheet()->getStyle('A2:E3')->applyFromArray($headerstyleArray);
    $msg='PO Processed Report On '.$today_dt;
    $objPHPExcel->getActiveSheet()->getStyle('A2:E3')->applyFromArray($borderarray);
    $objPHPExcel->getActiveSheet()->getStyle('A2:E3')->applyFromArray($alignmentstyle);
    $objPHPExcel->getActiveSheet()->setCellValue('A2', $msg);
    
    $query = "select m.name as name, count(*) as cnt from it_receivedpos r, it_master_dealers m  where r.createtime between $st_dt_db and $ed_dt_db and r.master_dealer_id= m.id group by master_dealer_id ";
    print"<br> qry-> $query<br>";
    $result = $db->getConnection()->query($query);

    while ($obj = $result->fetch_object()) {
          $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $srno);
          $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $obj->name);
          $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, $obj->cnt);
          $rowCount++;
          $srno++;
    }
    $rowCnt = 5;
    $query1 ="select p.master_dealer_id as mid, m.name as chain_name,p.status,p.is_Current_status, count(*) as cnt from it_process_status p, it_master_dealers m where p.master_dealer_id = m.id and p.createtime between $st_dt_db and $ed_dt_db and is_current_status=1 group by p.master_dealer_id, status"; 
    print"<br> qry-> $query1<br>";
    $result1 = $db->getConnection()->query($query1);

    $id_array= array();
    
    while ($obj1 = $result1->fetch_object()){
        if(!in_array($obj1->mid,$id_array) && $obj1->mid !=4){              
            array_push($id_array, $obj1->mid);
            $rowCnt++;                     
        }else if($obj1->mid == 4){
            if(!(in_array(3, $id_array))){
                array_push($id_array, 3);
              $rowCnt++;
            }            
        }
        
//        print"<br>array holds";
//        print_r($id_array);   
        if($obj1->mid==4){
            if($obj1->status==POStatus::STATUS_PROCESSED){
                $getcell=$objPHPExcel->getActiveSheet()->getCellByColumnAndRow(3,$rowCnt);
                $spcnt=$getcell->getValue();
                $updtcnt=$spcnt+$obj1->cnt;
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3,$rowCnt,$updtcnt);
            }
            if($obj1->status==POStatus::STATUS_NOT_WEIKFIELD){
                $getcell=$objPHPExcel->getActiveSheet()->getCellByColumnAndRow(4,$rowCnt);
                $spcnt=$getcell->getValue();
                $updtcnt=$spcnt+$obj1->cnt;
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4,$rowCnt,$updtcnt);
            }
            if($obj1->status==POStatus::STATUS_ISSUE_AT_PROCESSING){
                $getcell=$objPHPExcel->getActiveSheet()->getCellByColumnAndRow(5,$rowCnt);
                $spcnt=$getcell->getValue();
                $updtcnt=$spcnt+$obj1->cnt;
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5,$rowCnt,$updtcnt);
            }
            if($obj1->status==POStatus::STATUS_UNRECOGNIZED_BU){
                $getcell=$objPHPExcel->getActiveSheet()->getCellByColumnAndRow(6,$rowCnt);
                $spcnt=$getcell->getValue();
                $updtcnt=$spcnt+$obj1->cnt;
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6,$rowCnt,$updtcnt);
            }
            if($obj1->status==POStatus::STATUS_GR){
                $getcell=$objPHPExcel->getActiveSheet()->getCellByColumnAndRow(7,$rowCnt);
                $spcnt=$getcell->getValue();
                $updtcnt=$spcnt+$obj1->cnt;
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7,$rowCnt,$updtcnt);
            }
            if($obj1->status==POStatus::STATUS_MISSING_EAN){
                $getcell=$objPHPExcel->getActiveSheet()->getCellByColumnAndRow(8,$rowCnt);
                $spcnt=$getcell->getValue();
                $updtcnt=$spcnt+$obj1->cnt;
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8,$rowCnt,$updtcnt);
            }
            if($obj1->status==POStatus::STATUS_DUPLICATE_PO){
                $getcell=$objPHPExcel->getActiveSheet()->getCellByColumnAndRow(10,$rowCnt);
                $spcnt=$getcell->getValue();
                $updtcnt=$spcnt+$obj1->cnt;
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10,$rowCnt,$updtcnt);
            }   
        }else{
        if($obj1->status==POStatus::STATUS_PROCESSED){
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3,$rowCnt,$obj1->cnt);
        }
            if($obj1->status==POStatus::STATUS_NOT_WEIKFIELD){
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4,$rowCnt,$obj1->cnt);
        }
            if($obj1->status==POStatus::STATUS_ISSUE_AT_PROCESSING){
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5,$rowCnt,$obj1->cnt);
        } 
            if($obj1->status==POStatus::STATUS_UNRECOGNIZED_BU){
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6,$rowCnt,$obj1->cnt);
        }
            if($obj1->status==POStatus::STATUS_GR){
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7,$rowCnt,$obj1->cnt);
        }
            if($obj1->status==POStatus::STATUS_MISSING_EAN){
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8,$rowCnt,$obj1->cnt);
        }
            if($obj1->status==POStatus::STATUS_ARTICLE_NO_MISSING){
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9,$rowCnt,$obj1->cnt);
        }
            if($obj1->status==POStatus::STATUS_DUPLICATE_PO){
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10,$rowCnt,$obj1->cnt);
        }      
        //print"<br>rowCnt=$rowCnt<br>";
    }
    }
       

    $objPHPExcel->getActiveSheet()->mergeCells('A20:E23');
    $objPHPExcel->getActiveSheet()->getStyle('A20:E23')->applyFromArray($alignmentstyle);
    
    $msg='UBU = Unrecognized Business Units (we are working on it).
GR = Goods Receipt Notes (this files are not PO files so we do not process it).
Missing EAN = PO is marked as missing EAN when there is no record found in our database for the given EAN number in the PO.';
   
    $objPHPExcel->getActiveSheet()->getStyle('A20:E23')->applyFromArray($borderarray);
    $objPHPExcel->getActiveSheet()->setCellValue('A20', $msg);
    
    $nowtime = date('Y-m-d');
    $name = "statusReportDaily_" . $nowtime;
    $Ext = ".xls";
    $Filename = DEF_STATUS_REPORT_EXL_PATH . $name . $Ext;
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save($Filename);
    print"<br>excel created";
}catch (Exception $xcp) {
    print $xcp->getMessage();
}
?>

