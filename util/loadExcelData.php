<?php
require_once("../../it_config.php");
//require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/php/Classes/PHPExcel.php";

$db = new DBConn();

//$file = $argv[1];
$file = "/home/ykirad/dev/subversion/onlinePOS/vlcc_dt/home/util/VMM_Nov_2nd_Lot_PO.xlsx";
$handle = fopen($file,"r");

insertToDB($file);

function insertToDB($newfile) {
    $resp = "";
    $objPHPExcel = PHPExcel_IOFactory::load($newfile);
    $objWorksheet = $objPHPExcel->getActiveSheet();
    $rowno = 0;
    $line1 = false;
    $resp = "";
    $cnt = 0;
    $db = new DBConn();

    foreach ($objWorksheet->getRowIterator() as $row) {
        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false);
        $colno = 0;

        //Fetch actual data
        if ($rowno > 1) {
            foreach ($cellIterator as $cell) {
               if ($colno == 1 && trim($cell->getValue()) != "") {
                    $invoice_no = strval(trim($cell->getValue()));
               }else if ($colno == 2 && trim($cell->getValue()) != "") {                    
                    $invoice_date = strval(trim($cell->getValue()));                    
               }else if ($colno == 3 && trim($cell->getValue()) != "") {                    
                    $vendor_code = strval(trim($cell->getValue()));                    
                }else if ($colno == 4 && trim($cell->getValue()) != "") {                    
                    $article_no = strval(trim($cell->getValue()));                    
                }else if ($colno == 5 && trim($cell->getValue()) != "") {                    
                    $po_itemname = strval(trim($cell->getValue()));                    
                }else if ($colno == 6 && trim($cell->getValue()) != "") {                    
                    $vendor_name = strval(trim($cell->getValue()));                    
                }else if ($colno == 9 && trim($cell->getValue()) != "") {                    
                    $dealer_name = strval(trim($cell->getValue()));                    
                }else if ($colno == 10 && trim($cell->getValue()) != "") {                    
                    $qty = strval(trim($cell->getValue()));                    
                }else if ($colno == 11 && trim($cell->getValue()) != "") {                    
                    $unitprice = strval(trim($cell->getValue()));                    
                }else if ($colno == 12 && trim($cell->getValue()) != "") {                    
                    $po_item_linetotal = strval(trim($cell->getValue()));                    
                }            
                $colno++;
            }
        }
        $rowno++;
    }
    return $resp . "<>" . $cnt;
}
