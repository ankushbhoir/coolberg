<?php

// require_once("/var/www/html/vlcc_dt/it_config.php");
// require_once "../lib/db/DBConn.php";
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";


$db = new DBConn();
// $handle = @fopen('php://output', 'w');
// header('Content-Type: text/csv; charset=utf-8');



//Walmart shipping_address = 'Cash N Carry - Vijayawada OPP. DPS, NIDAMANURU RS No. 125/1, NH-5, ELURU RD.VIJAYAWADA-521104,India'
$uni_array=array (
  'Header' => 
  array (
    'Rows' => 70,
    'Fields' => 
    array (
      0 => 
      array (
        'Name' => 'initype',
        'value' => '1',
      ),
      1 => 
      array (
        'Name' => 'new_frt',
        'value' => '1',
      ),
      2 => 
      array (
        'Name' => 'DealerName',
        'value' => 'Walmart',
      ),
      3 => 
      array (
        'Name' => 'DealerCity',
        'value' => '-',
      ),
      4 => 
      array (
        'Name' => 'DealerCode',
        'row' => 
        array (
          0 => 3,
        ),
        'Regex' => 
        array (
          0 => '/Wal\\s*\\-\\s*Mart\\s+India\\s+Pvt.\\s+Ltd.\\s+\\(\\s*(\\S+)\\s*\\)/',
        ),
      ),
      5 => 
      array (
        'Name' => 'Dealer_PhoneNo',
        'value' => '-',
      ),
      6 => 
      array (
        'Name' => 'PO_No',
        'row' => 
        array (
          0 => 12,
        ),
        'Regex' => 
        array (
          0 => '/PURCHASE\\s+ORDER\\s+NO.\\s*:\\s*(\\S+)/',
        ),
      ),
      7 => 
      array (
        'Name' => 'PO_Date',
        'row' => 
        array (
          0 => 13,
        ),
        'Regex' => 
        array (
          0 => '/ORDER\\s+DATE\\s*:\\s*(\\S+)/',
        ),
        'format' => 'd.m.Y',
      ),
      8 => 
      array (
        'Name' => 'Expiry_Date',
        'row' => 
        array (
          0 => 14,
        ),
        'Regex' => 
        array (
          0 => '/PO\\s+CANCEL\\s+DATE\\s*:\\s*(\\S+)/',
        ),
        'format' => 'd.m.Y',
      ),
      9 => 
      array (
        'Name' => 'PO_Type',
        'value' => '-',
      ),
      10 => 
      array (
        'Name' => 'PO_Name',
        'value' => '-',
      ),
      11 => 
      array (
        'Name' => 'Purchase_Group',
        'value' => '-',
      ),
      12 => 
      array (
        'Name' => 'PO_Currency',
        'value' => '-',
      ),
      13 => 
      array (
        'Name' => 'Type',
        'value' => '-',
      ),
      14 => 
      array (
        'Name' => 'Vendor_Code',
        'row' => 
        array (
          0 => 10,
        ),
        'Regex' => 
        array (
          0 => '/Supplier\\s+No.\\s*:\\s*(\\S+)/',
        ),
      ),
      15 => 
      array (
        'Name' => 'VendorName',
        'row' => 
        array (
          0 => 4,
        ),
        'Regex' => 
        array (
          0 => '/(?.{0,30})\\s+.*/',
        ),
      ),
      16 => 
      array (
        'Name' => 'VendorAddress',
        'row' => 
        array (
          0 => 5,
          1 => 6,
          2 => 7,
        ),
        'Regex' => 
        array (
          0 => '/(?.{0,30})\\s+.*/',
          1 => '/(?.{0,40})\\s+.*/',
          2 => '/(?.{0,40})/',
        ),
      ),
      17 => 
      array (
        'Name' => 'VendorCity',
        'row' => 
        array (
          0 => 7,
        ),
        'Regex' => 
        array (
          0 => '/(.*)\\s*\\,\\d+\\,/',
        ),
      ),
      18 => 
      array (
        'Name' => 'VendorState',
        'value' => '-',
      ),
      19 => 
      array (
        'Name' => 'Vendor_PhoneNo',
        'value' => '-',
      ),
      20 => 
      array (
        'Name' => 'Vat_Tin',
        'row' => 
        array (
          0 => 8,
        ),
        'Regex' => 
        array (
          0 => '/Supplier\\s+No.\\s*:\\s*(\\S+)/',
        ),
      ),
    ),
  ),
  'Footer' => 
  array (
    'Identifier' => 
    array (
      'value' => 'Total cost without tax',
      'start' => 0,
      'length' => 22,
    ),
  ),
  'Items' => 
  array (
    'StartRow' => 25,
    'RowsPerItem' => -1,
    'Regex' => 
    array (
      0 => '/(?\'SrNo\'\\d+)\\s+(?\'Itemname\'.*)\\s+(?\'Qty1\'\\d+)\\s+(?\'Ign\'\\S+)\\s+(?\'Qty\'\\d+)\\s*(?\'CAR\'\\S+)\\/(?\'Ign1\'\\d+\\S+)\\s+(?\'MRP\'\\d\\S*)\\/(?\'ign3\'\\S+)\\s+(?\'Rate\'\\d\\S*)\\s+(?\'ign\'\\d\\S*)\\s+(?\'ign1\'IN:)\\s+(?\'ign2\'\\S+)\\s*(?\'ign4\'\\()\\s*(?\'VAT\'\\d\\S*)\\s*(?\'ign5\'%\\)\\s*\\-)\\s+(?\'ign6\'\\d\\S*)\\s+(?\'Amount\'\\d\\S*)/',
    ),
  ),
);


$ini_text=json_encode($uni_array);
$test=$db->safe($ini_text);
 echo $updateQ="insert into it_inis set ini_text= '".addslashes($ini_text)."', master_dealer_id = 7, name = 'walmart', shipping_address = ''";
 $no=$db->execInsert($updateQ); 