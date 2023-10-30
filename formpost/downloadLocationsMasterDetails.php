<?php

require_once("../../it_config.php");
require_once("session_check.php");
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once "lib/core/strutil.php";
require_once "lib/php/Classes/PHPExcel.php";
require_once 'lib/php/Classes/PHPExcel/Writer/Excel2007.php';
extract($_GET);
$chain_id = ($_GET['chain_id']);
//print_r($_GET);
//extract($_POST);
//print_r($_POST);
//return;
$errors = array();
$chkbxarr = array();
try {
    $addedWhere = "";
    if ($chain_id != -1) {
        $addedWhere .= "and m.id = $chain_id";
    }
    $db = new DBConn();
//print_r($addClause);
//return;
//$query = "select o.order_no,o.customer_name,o.city, s.title as description, (select name from it_users where id in (hd.by_user)) as by_user, (select name from it_users where id in (hd.to_user)) as to_user, hd.ctime, (select license from it_android_instances where id in (hd.android_instance_id)) as license from handover_diary hd,status s,it_users iu,orders o where o.id = hd.order_id and hd.status = s.id and hd.by_user = iu.id $addClause and o.status != 28 and hd.type = 1 ORDER BY o.id ASC, hd.ctime ASC";
//$query = "select hd.type, o.order_no,o.customer_name,o.city, s.title as description, iu.name as by_user, tu.name as to_user, hd.ctime, iai.license as license from handover_diary hd left outer join it_users tu on hd.to_user = tu.id left outer join it_android_instances iai on iai.id = hd.android_instance_id ,status s,it_users iu, orders o where o.id = hd.order_id and hd.status = s.id and hd.by_user = iu.id and o.status != 28 and hd.type = 1 $addClause ORDER BY o.id DESC, hd.ctime ASC";
    $query = "select SQL_CALC_FOUND_ROWS m.name, sh.customer_code, sh.dc_name, sh.dc_address,sh.dc_city, sh.dc_state, 
    (select r1.name from it_regions r1,it_regions r2 where r1.id=r2.zone_id and replace(UPPER(r2.name),' ','') = replace(UPPER(sh.dc_state),' ','')) as Zone, 
    d.code, bu.code as 'store_code' from it_master_dealers m ,it_po p, it_distributors d, it_shipping_address sh, it_business_unit bu 
    where p.shipping_id = sh.id and p.dist_id = d.id and bu.id = d.bu_id and p.master_dealer_id = m.id $addedWhere 
    group by m.name, sh.customer_code, sh.dc_name, sh.dc_address, sh.dc_city, sh.dc_state, Zone,d.code, bu.code ";

    $oobjs = $db->fetchAllObjects($query);
    $sheetIndex = 0;
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->setActiveSheetIndex($sheetIndex);
    $objPHPExcel->getActiveSheet()->setTitle('Customers list');
    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Chain Name');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Customer Code');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Store Code');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Vendor Code');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', 'DC Name');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', 'DC Address');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', 'DC City');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', 'DC State');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', 'Zone');


    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(50);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);

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
    $objPHPExcel->getActiveSheet()->getStyle('A1:I1')->applyFromArray($headerStyle);
    $objPHPExcel->getActiveSheet()->getStyle('A')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('B')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('C')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('D')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('E')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('F')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('G')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('H')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('I')->applyFromArray($styleArray);
    $colCount = 0;
    $rowCount = 2;
    $srno = 1;
    if (isset($oobjs)) {
        foreach ($oobjs as $obj) {
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $obj->name);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $obj->customer_code);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, $obj->store_code);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $rowCount, $obj->code);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $rowCount, $obj->dc_name);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $rowCount, $obj->dc_address);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $rowCount, $obj->dc_city);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $rowCount, $obj->dc_state);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $rowCount, $obj->Zone);

            $colCount = 0;
            $rowCount++;
            $srno++;
        }
        $filename = 'customer_locations_details.xls';
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