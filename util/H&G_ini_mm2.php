<?php

require_once("/var/www/html/vlcc_dt/it_config.php");
require_once "../lib/db/DBConn.php";
// require_once("../../it_config.php");
// require_once "lib/db/DBConn.php";


$db = new DBConn();
// $handle = @fopen('php://output', 'w');
// header('Content-Type: text/csv; charset=utf-8');


//comment by mayur
//H&G 
// Add above inserted id for following resulted rows:

// SELECT * FROM `it_shipping_address` WHERE `shipping_address` LIKE 'SHOP NO 26,2ND FLOOR, ELEMENT MALL,NAGAVARA, THANISANDRA MAIN ROAD, PH : 8431246267.';

// SELECT * FROM `it_shipping_address` WHERE `shipping_address` LIKE 'NO.F-61,FIRST FLOOR, PHOENIX MARKET CITY MALL, DYAVASANDRA PHASE -II INDUST, AREA K R PURAM +91-9071759036.';

// SELECT * FROM `it_shipping_address` WHERE `shipping_address` LIKE 'NO 31 / 1, COLES ROAD, BANGALORE, PH : 8431246264.';

$uni_array=array (
  'Header' => 
  array (
    'Rows' => 16,
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
        'value' => 'H&G',
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
          0 => 15,
        ),
        'Regex' => 
        array (
          0 => '/.*PO\\s+No\\s*:\\s*(\\S+)/',
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
          0 => '/.*PO\\s+Date\\s*:\\s*(\\S+)/',
        ),
        'format' => 'd-M-y',
      ),
      6 => 
      array (
        'Name' => 'Delivery_Date',
        'row' => 
        array (
          0 => 15,
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
          0 => 19,
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
        'Name' => 'DealerCode',
        'row' => 
        array (
          0 => 7,
        ),
        'Regex' => 
        array (
          0 => '/Store\\s+Code\\s*:\\s*(\\S+)/',
        ),
      ),
      14 => 
      array (
        'Name' => 'VendorName',
        'row' => 
        array (
          0 => 17,
        ),
        'Regex' => 
        array (
          0 => '/Vendor\\s+Name\\s*:\\s*(.*)\\s+PO\\s+Date/',
        ),
      ),
      15 => 
      array (
        'Name' => 'VendorAddress',
        'row' => 
        array (
          0 => 17,
        ),
        'Regex' => 
        array (
          0 => '/Vendor\\s+Address\\s*:\\s*(.*)\\s+PO\\s+Status/',
        ),
      ),
      16 => 
      array (
        'Name' => 'VendorCity',
        'row' => 
        array (
          0 => 18,
        ),
        'Regex' => 
        array (
          0 => '/Vendor\\s+Address\\s*:\\s\\*.*\\,(.*),.*\\s+PO\\s+Status/',
        ),
      ),
      17 => 
      array (
        'Name' => 'VendorState',
        'row' => 
        array (
          0 => 18,
        ),
        'Regex' => 
        array (
          0 => '/Vendor\\s+Address\\s*:\\s*.*\\,.*,(.*)\\s+PO\\s+Status/',
        ),
      ),
      18 => 
      array (
        'Name' => 'Vendor_PhoneNo',
        'row' => 1,
        'start' => 0,
        'length' => 0,
      ),
      19 => 
      array (
        'Name' => 'Vat_Tin',
        'row' => 
        array (
          0 => 15,
        ),
        'Regex' => 
        array (
          0 => '/Vendor\\s+Code\\s*:\\s*(\\S+)/',
        ),
      ),
    ),
  ),
  'Footer' => 
  array (
    'Identifier' => 
    array (
      'value' => 'Delivery Date',
      'start' => 0,
      'length' => 13,
    ),
  ),
  'Items' => 
  array (
    'StartRow' => 23,
    'RowsPerItem' => -1,
    'Regex' => 
    array (
      0 => '/(?\'SrNo\'\\d+)\\s+(?\'ArticleNo\'\\d\\S*)\\s+(?\'EAN\'\\d\\S*)\\s+(?\'ign\'\\d\\S*)\\s+(?\'Qty\'\\d\\S*)\\s+(?\'Rate\'\\d\\S*)\\s+(?\'ign1\'\\S+)\\s+(?\'VAT\'\\d\\S*)\\s*(?\'ign2\'\\%)\\s+(?\'ign3\'\\d\\S*)\\s+(?\'ign4\'\\d\\S*)\\s+(?\'ign5\'\\d\\S*\\s*%)\\s+(?\'MRP\'\\d\\S*)\\s+(?\'Amount\'\\d\\S*)/',
      1 => '/(?\'Itemname\'.*)\\s+(?\'ign\'\\S+)\\s+(?\'ign1\'\\d\\S*\\s*%)\\s+(?\'ign2\'\\S+)\\s+(?\'ign3\'\\d\\S*\\s*%)\\s+(?\'ign4\'\\d\\S*)\\s+(?\'ign5\'\\S+)\\s+(?\'ign6\'\\d\\S*\\s*%)\\s+(?\'ign7\'\\d\\S*)/',
    ),
  ),
);


$ini_text=json_encode($uni_array);
$test=$db->safe($ini_text);
 echo $updateQ="insert into it_inis set ini_text= '".addslashes($ini_text)."', master_dealer_id = 22, name = 'H&G_661', shipping_address = ''";
 $no=$db->execUpdate($updateQ); 