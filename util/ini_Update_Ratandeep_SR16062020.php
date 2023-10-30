<?php

require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once "../lib/db/DBConn.php";
// require_once("../../it_config.php");
// require_once "lib/db/DBConn.php";


$db = new DBConn();

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
        'Name' => 'DealerName',
        'value' => 'RATNADEEP',
      ),
      2 => 
      array (
        'Name' => 'DealerCity',
        'value' => '-',
      ),
      3 => 
      array (
        'Name' => 'Dealer_PhoneNo',
        'row' => 1,
        'start' => 0,
        'length' => 0,
      ),
      4 => 
      array (
        'Name' => 'PO_No',
        'row' => 
        array (
          0 => 16,
        ),
        'Regex' => 
        array (
          0 => '/PO\\s*Number\\s*:\\s*(\\S+)/',
        ),
      ),
      5 => 
      array (
        'Name' => 'PO_Date',
        'row' => 
        array (
          0 => 16,
        ),
        'Regex' => 
        array (
          0 => '/PO\\s+Date\\s*:\\s*(\\S+)/',
        ),
        'format' => 'd/m/Y',
      ),
      6 => 
      array (
        'Name' => 'Delivery_Date',
        'row' => 
        array (
          0 => 16,
        ),
        'Regex' => 
        array (
          0 => '/Deli\\s+Date\\s*:\\s*(\\S+)/',
        ),
        'format' => 'd/m/Y',
      ),
      7 => 
      array (
        'Name' => 'Expiry_Date',
        'row' => 
        array (
          0 => 16,
        ),
        'Regex' => 
        array (
          0 => '/Deli\\s+Date\\s*:\\s*(\\S+)/',
        ),
        'format' => 'd/m/Y',
      ),
      8 => 
      array (
        'Name' => 'PO_Type',
        'row' => 1,
        'start' => 0,
        'length' => 0,
      ),
      9 => 
      array (
        'Name' => 'PO_Name',
        'row' => 1,
        'start' => 0,
        'length' => 0,
      ),
      10 => 
      array (
        'Name' => 'Purchase_Group',
        'row' => 1,
        'start' => 0,
        'length' => 0,
      ),
      11 => 
      array (
        'Name' => 'PO_Currency',
        'row' => 1,
        'start' => 0,
        'length' => 0,
      ),
      12 => 
      array (
        'Name' => 'Type',
        'row' => 1,
        'start' => 0,
        'length' => 0,
      ),
      13 => 
      array (
        'Name' => 'VendorName',
        'row' => 
        array (
          0 => 8,
        ),
        'Regex' => 
        array (
          0 => '/Supplier\\s*:\\s*\\S+\\s*\\-\\s*(.*)/',
        ),
      ),
      14 => 
      array (
        'Name' => 'VendorAddress',
        'row' => 
        array (
          0 => 9,
          1 => 10,
          2 => 11,
        ),
        'Regex' => 
        array (
          0 => '/(?.{0,40}$)/',
          1 => '/(?.{0,40}$)/',
          2 => '/(?.{0,40}$)/',
        ),
      ),
      15 => 
      array (
        'Name' => 'VendorCity',
        'value' => '-',
      ),
      16 => 
      array (
        'Name' => 'VendorState',
        'value' => '-',
      ),
      17 => 
      array (
        'Name' => 'Vendor_PhoneNo',
        'row' => 1,
        'start' => 0,
        'length' => 0,
      ),
      18 => 
      array (
        'Name' => 'Vat_Tin',
        'row' => 
        array (
          0 => 8,
        ),
        'Regex' => 
        array (
          0 => '/Supplier\\s*:\\s*(\\S+)/',
        ),
      ),
    ),
  ),
  'Footer' => 
  array (
    'Identifier' => 
    array (
      'value' => 'Grand Total',
      'start' => 0,
      'length' => 12,
    ),
  ),
  'Items' => 
  array (
    'StartRow' => 20,
    'RowsPerItem' => -1,
    'Regex' => 
    array (
      0 => '/(?\'SrNo\'\\d+)\\s+(?\'ArticleNo\'\\d+)\\s+(?\'Itemname\'.*)\\s+(?\'EAN\'\\S+)\\s+(?\'Rate\'\\S+)\\s+(?\'MRP\'\\S+)\\s+(?\'Qty\'\\S+)\\s+(?\'ign\'\\S+)\\s+(?\'ign1\'\\S+)\\s+(?\'VAT\'\\S+)\\s+(?\'Amount\'\\S+)/',
    ),
  ),
);


$ini_text=json_encode($uni_array);
$test=$db->safe($ini_text);
 echo $updateQ="update it_inis set ini_text= '".addslashes($ini_text)."' where id = 668";

$no=$db->execUpdate($updateQ);
 echo "\nResult:" ;
 echo $no;