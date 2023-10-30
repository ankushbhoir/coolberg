<?php

// require_once("/var/www/html/vlcc_dt/it_config.php");
// require_once "../lib/db/DBConn.php";
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";


$db = new DBConn();

$uni_array=array (
  'Header' => 
  array (
    'Rows' => 28,
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
        'value' => 'Marche Retail Pvt Ltd',
      ),
      2 => 
      array (
        'Name' => 'DealerCode',
        'value' => '',
      ),
      3 => 
      array (
        'Name' => 'DealerCity',
        'value' => '-',
      ),
      4 => 
      array (
        'Name' => 'Dealer_PhoneNo',
        'value' => '-',
      ),
      5 => 
      array (
        'Name' => 'PO_No',
        'row' => 
        array (
          0 => 10,
        ),
        'Regex' => 
        array (
          0 => '/PO\\sNumber\\s+(PO\\/\\d+\\/\\d+)/',
        ),
      ),
      6 => 
      array (
        'Name' => 'PO_Date',
        'row' => 
        array (
          0 => 12,
        ),
        'Regex' => 
        array (
          0 => '/PO\\sDate\\s+(\\S+)/',
        ),
        'format' => 'd-m-y',
      ),
      7 => 
      array (
        'Name' => 'Delivery_Date',
        'row' => 
        array (
          0 => 14,
        ),
        'Regex' => 
        array (
          0 => '/\\s+PO\\sExpiry\\s+(\\S+)/',
        ),
        'format' => 'd-m-y',
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
          0 => '/\\s+PO\\sExpiry\\s+(\\S+)/',
        ),
        'format' => 'd-m-y',
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
        'Name' => 'VendorName',
        'row' => 
        array (
          0 => 11,
        ),
        'Regex' => 
        array (
          0 => '/Vendor\\sName\\s+(.{28})/',
        ),
      ),
      15 => 
      array (
        'Name' => 'VendorAddress',
        'row' => 
        array (
          0 => 13,
          1 => 14,
          2 => 15,
        ),
        'Regex' => 
        array (
          0 => '/(?.{0,30})\\s+.*/',
          1 => '/(?.{0,40})\\s+.*/',
          2 => '/(?.{0,40})/',
        ),
      ),
      16 => 
      array (
        'Name' => 'VendorCity',
        'value' => '-',
      ),
      17 => 
      array (
        'Name' => 'VendorState',
        'value' => '-',
      ),
      18 => 
      array (
        'Name' => 'Vendor_PhoneNo',
        'value' => '-',
      ),
      19 => 
      array (
        'Name' => 'Vat_Tin',
        'row' => 
        array (
          0 => 10,
        ),
        'Regex' => 
        array (
          0 => '/Vendor\\s+No\\s+(\\d+)/',
        ),
      ),
    ),
  ),
  'Footer' => 
  array (
    'Identifier' => 
    array (
      'value' => 'Total',
      'start' => 0,
      'length' => 5,
    ),
  ),
  'Items' => 
  array (
    'StartRow' => 27,
    'RowsPerItem' => -1,
    'Regex' => 
    array (
      0 => '/(?\'ArticleNo\'\\d+)\\s+(?\'Itemname\'\\w.*)\\s+(?\'EAN\'\\S+)\\s+(?\'ign\'\\S+)\\s+(?\'MRP\'\\d\\S+)\\s+(?\'Qty\'\\S+)\\s+(?\'CAR\'\\S+)\\s+(?\'Rate\'\\S+)\\s+(?\'ign1\'\\S+)\\s+(?\'ign2\'\\S+)\\s+(?\'VAT\'\\S+)\\s+(?\'ign3\'\\S+)\\s+(?\'ign4\'\\S+)\\s+(?\'ign5\'\\S+)\\s+(?\'Amount\'\\S+)/',
    ),
  ),
);


$ini_text=json_encode($uni_array);
print_r($ini_text);
//$test=$db->safe($ini_text);
// echo $updateQ="update it_inis set ini_text= '".addslashes($ini_text)."' where id = 608";
//echo $insertQ ="insert into it_inis set name='Marche_Retail',ini_text='".addslashes($ini_text)."',master_dealer_id=50";
//$no=$db->execInsert($insertQ);
 //echo "\nResult:" ;
 //echo $no;