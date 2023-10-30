<?php

require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once "../lib/db/DBConn.php";
// require_once("../../it_config.php");
// require_once "lib/db/DBConn.php";


$db = new DBConn();

$uni_array=array (
  'Header' => 
  array (
    'Rows' => 27,
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
        'value' => 'Trent Hypermarket',
      ),
      2 => 
      array (
        'Name' => 'DealerCity',
        'row' => 
        array (
          0 => 18,
        ),
        'Regex' => 
        array (
          0 => '/(?\'addr\'^.{0,36})/',
        ),
        'stopIdentifierRegex' => '/(Sr \\s*.\\s* Article\\s*).*/',
        'jumpUp' => '1',
        'regexPosition' => '1',
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
          0 => 9,
        ),
        'Regex' => 
        array (
          0 => '/Purchase\\s*?Order\\s*?:\\s*?(.*)/',
        ),
      ),
      5 => 
      array (
        'Name' => 'PO_Date',
        'row' => 
        array (
          0 => 11,
        ),
        'Regex' => 
        array (
          0 => '/PO\\s*?Date\\s*?:\\s*?(.*)/',
        ),
        'format' => 'd.m.Y',
      ),
      6 => 
      array (
        'Name' => 'Delivery_Date',
        'row' => 
        array (
          0 => 12,
        ),
        'Regex' => 
        array (
          0 => '/Planned\\s*?Delivery\\s*?Date\\s*?:\\s*?(.*)/',
        ),
        'format' => 'd.m.Y',
      ),
      7 => 
      array (
        'Name' => 'Expiry_Date',
        'row' => 
        array (
          0 => 12,
        ),
        'Regex' => 
        array (
          0 => '/Planned\\s*?Delivery\\s*?Date\\s*?:\\s*?(.*)/',
        ),
        'format' => 'd.m.Y',
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
        'row' => 10,
        'start' => 0,
        'length' => 40,
      ),
      14 => 
      array (
        'Name' => 'VendorAddress',
        'row' => 
        array (
          0 => 12,
        ),
        'Regex' => 
        array (
          0 => '/(?\'addr\'^.{0,35})/',
        ),
        'stopIdentifierRegex' => '/(Consignee\\s*?Detail)/',
        'jumpUp' => '1',
        'regexPosition' => '1',
      ),
      15 => 
      array (
        'Name' => 'VendorCity',
        'row' => 14,
        'start' => 0,
        'length' => 20,
      ),
      16 => 
      array (
        'Name' => 'VendorState',
        'row' => 15,
        'start' => 0,
        'length' => 20,
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
          0 => 7,
        ),
        'Regex' => 
        array (
          0 => '/Vendor\\s*?No.\\s*?:\\s*?(.*)/',
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
    'RowsPerItem' => 1,
    'Regex' => '/(?\'SrNo\'\\d+)\\s+(?\'ArticleNo\'\\S+)\\s+(?\'Itemname\'.*\\w+)\\s+(?\'EAN\'\\d+)\\s+(?\'ign\'\\S+)\\s+(?\'ign1\'\\S+)\\s+(?\'ign2\'\\S+)\\s+(?\'ign3\'\\d+)\\s+(?\'CAR\'\\S+)\\s+(?\'Qty\'\\d\\S+)\\s+(?\'Rate\'\\d\\S*)\\s+(?\'Ignore\'\\d\\S*)\\s+(?\'VAT\'\\d\\S*)\\s+(?\'ign4\'\\d\\S*)\\s+(?\'ign5\'\\d\\S*)\\s+(?\'ign6\'\\d\\S*)\\s+(?\'Amount\'\\d\\S*)\\s+(?\'MRP\'\\d\\S*)/',
  ),
);


$ini_text=json_encode($uni_array);
$test=$db->safe($ini_text);
 echo $updateQ="update it_inis set ini_text= '".addslashes($ini_text)."' where id = 725";
//echo $insertQ ="insert into it_inis set name='Trent_Hyper_market',ini_text='".addslashes($ini_text)."',master_dealer_id=15";
$no=$db->execUpdate($updateQ);
 echo "\nResult:" ;
 echo $no;