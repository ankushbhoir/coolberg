<?php

require_once("../../it_config.php");
require_once("session_check.php");
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once "lib/core/strutil.php";
require_once "lib/php/Classes/PHPExcel.php";
require_once 'lib/php/Classes/PHPExcel/Writer/Excel2007.php';
extract($_GET);
$cat_id = ($_GET['cat_id']);
//print_r($_GET);
//extract($_POST);
//print_r($_POST);
//return;
$errors = array();
$chkbxarr = array();
try {
    $addedWhere = "";
    if ($cat_id != -1) {
        $addedWhere .= "and c.id = $cat_id";
    }
    $db = new DBConn();
//print_r($addClause);
//return;
//$query = "select o.order_no,o.customer_name,o.city, s.title as description, (select name from it_users where id in (hd.by_user)) as by_user, (select name from it_users where id in (hd.to_user)) as to_user, hd.ctime, (select license from it_android_instances where id in (hd.android_instance_id)) as license from handover_diary hd,status s,it_users iu,orders o where o.id = hd.order_id and hd.status = s.id and hd.by_user = iu.id $addClause and o.status != 28 and hd.type = 1 ORDER BY o.id ASC, hd.ctime ASC";
//$query = "select hd.type, o.order_no,o.customer_name,o.city, s.title as description, iu.name as by_user, tu.name as to_user, hd.ctime, iai.license as license from handover_diary hd left outer join it_users tu on hd.to_user = tu.id left outer join it_android_instances iai on iai.id = hd.android_instance_id ,status s,it_users iu, orders o where o.id = hd.order_id and hd.status = s.id and hd.by_user = iu.id and o.status != 28 and hd.type = 1 $addClause ORDER BY o.id DESC, hd.ctime ASC";
    $query = "select mi.id, mi.itemcode as 'EAN', mi.itemname, c.category, mi.num_units,mi.sku, mi.product_code as 'FG_code', mi.mrp "
            . "from it_master_items mi, it_category c where mi.category_id = c.id $addedWhere order by mi.id";

    $oobjs = $db->fetchAllObjects($query);
    $sheetIndex = 0;
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->setActiveSheetIndex($sheetIndex);
    $objPHPExcel->getActiveSheet()->setTitle('Customers list');
    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Id');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', 'EAN');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Item Name');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Category');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', 'NO.Units');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', 'SKU');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', 'FG Code');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', 'MRP');


    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(50);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
    
    

    $styleArray = array(
        'font' => array(
            'bold' => false,
            'size' => 10,
        )
    );
    $headerStyle = array(
        'font' => array(
            'bold' => true,
            'size' => '10',
            'align' => 'center',
        )
    );
    $objPHPExcel->getActiveSheet()->getStyle('A1:H1')->applyFromArray($headerStyle);
    $objPHPExcel->getActiveSheet()->getStyle('A')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('B')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('C')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('D')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('E')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('F')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('G')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('H')->applyFromArray($styleArray);
    
    $colCount = 0;
    $rowCount = 2;
    $srno = 1;
    if (isset($oobjs)) {
        foreach ($oobjs as $obj) {
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $obj->id);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $obj->EAN);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, $obj->itemname);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $rowCount, $obj->category);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $rowCount, $obj->num_units);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $rowCount, $obj->sku);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $rowCount, $obj->FG_code);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $rowCount, $obj->mrp);
            

            $colCount = 0;
            $rowCount++;
            $srno++;
        }
        $filename = 'products_details.xls';
        header('Content-Disposition: attachment;filename=' . $filename);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }else{
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, "Data not found for this chain.");
    }
    $db->closeConnection();
} catch (Exception $xcp) {
    print $xcp->getMessage();
}