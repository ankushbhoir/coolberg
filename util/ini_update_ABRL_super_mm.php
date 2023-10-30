<?php

require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once "../lib/db/DBConn.php";
// require_once("../../it_config.php");
// require_once "lib/db/DBConn.php";


$db = new DBConn();

$uni_array=array (
  'Header' => 
  array (
    'Rows' => 19,
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
        'value' => 'ABRL Super',
      ),
      2 => 
      array (
        'Name' => 'DealerCity',
        'row' => 
        array (
          0 => 6,
          1 => 7,
          2 => 8,
          3 => 9,
          4 => 10,
          5 => 11,
        ),
        'Regex' => 
        array (
          0 => '/^(?.{0,58})/',
          1 => '/^(?.{0,58})/',
          2 => '/^(?.{0,58})/',
          3 => '/^(?.{0,58})/',
          4 => '/^(?.{0,58})/',
          5 => '/^(?.{0,58})/',
        ),
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
          0 => 4,
        ),
        'Regex' => 
        array (
          0 => '/.*PO\\s+Number\\s*:\\s*(\\S+)/',
        ),
      ),
      5 => 
      array (
        'Name' => 'PO_Date',
        'row' => 
        array (
          0 => 15,
        ),
        'Regex' => 
        array (
          0 => '/.*PO\\s+Date\\s*:\\s*(\\S+)/',
        ),
        'format' => 'd-M-y',
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
          0 => '/.*Delivery\\s+Date\\s*:\\s*(\\S+)/',
        ),
        'format' => 'd-M-y',
      ),
      7 => 
      array (
        'Name' => 'Expiry_Date',
        'row' => 
        array (
          0 => 17,
        ),
        'Regex' => 
        array (
          0 => '/.*Expiry\\s+Date:\\s*(\\S+)/',
        ),
        'format' => 'd-M-y',
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
          0 => 7,
        ),
        'Regex' => 
        array (
          0 => '/(?\'addr\'^.{130}(.*)$)/',
        ),
        'startIdentifierRegex' => '/.*(Supplier).*/',
        'stopIdentifierRegex' => '/(Unique\\s+?Vendor\\s+Id:).*/',
        'jumpUp' => 3,
        'sameline' => '',
      ),
      14 => 
      array (
        'Name' => 'VendorAddress',
        'row' => 
        array (
          0 => 8,
        ),
        'Regex' => 
        array (
          0 => '/(?\'addr\'^.{130}(.*)$)/',
        ),
        'stopIdentifierRegex' => '/(Unique\\s+?Vendor\\s+Id:).*/',
        'jumpUp' => '0',
      ),
      15 => 
      array (
        'Name' => 'VendorCity',
        'row' => 
        array (
          0 => 10,
        ),
        'Regex' => 
        array (
          0 => '/.*,(.*),/',
        ),
      ),
      16 => 
      array (
        'Name' => 'VendorState',
        'row' => 
        array (
          0 => 10,
        ),
        'Regex' => 
        array (
          0 => '/.*,(.*)/',
        ),
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
          0 => 11,
        ),
        'Regex' => 
        array (
          0 => '/Unique\\s*Vendor\\s*Id:\\s*(\\d*)/',
        ),
      ),
    ),
  ),
  'Footer' => 
  array (
    'Identifier' => 
    array (
      'value' => 'Total Qty:',
      'start' => 0,
      'length' => 11,
    ),
  ),
  'Items' => 
  array (
    'StartRow' => 20,
    'RowsPerItem' => -1,
    'Regex' => 
    array (
      0 => '/(?\'Srno\'\\S+)\\s+(?\'HSNcode\'\\d+\\S+)\\s+(?\'ArticleNo\'\\d+\\S+)\\s*(?\'Itemname\'\\w+.*)?\\s+(?\'EAN\'\\d+\\S+)\\s+(?\'MRP\'\\d\\S+)\\s+(?\'Rate\'\\d\\S+)\\s+(?\'CAR\'\\S+)\\s+(?\'Qty\'\\d\\S*)\\s*(?\'ign\'\\S+\\s*-)?\\s*(?\'VAT\'\\d+)?\\s*(?\'ign2\'\\%)?\\s*(?\'VAT1\'\\S+)?\\s+(?\'Amount\'\\S+)/',
      1 => '/(?\'Itemname\'\\w+.*)\\s+(?\'ing\'\\S+\\s-)\\s*(?\'VAT\'\\d+\\S+?|\\d+\\S?)\\%\\s+(?\'VAT1\'\\d+\\S+)/',
      2 => '/(?\'VAT\'\\S+\\s*)%/',
      3 => '',
    ),
  ),
);


$ini_text=json_encode($uni_array);
$test=$db->safe($ini_text);
 echo $updateQ="update it_inis set ini_text= '".addslashes($ini_text)."' where id = 608";
 $no=$db->execInsert($updateQ);
 echo "\nResult:";
 echo $no;