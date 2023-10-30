<?php

require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once "../lib/db/DBConn.php";
// require_once("../../it_config.php");
// require_once "lib/db/DBConn.php";


//this ini is written by swapnil and applicable for Metro CNC new format.

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
        'value' => '2',
      ),
      1 => 
      array (
        'Name' => 'DealerName',
        'value' => 'Metro Cash & Carry',
      ),
      2 => 
      array (
        'Name' => 'DealerCode',
        'row' => 
        array (
          0 => 26,
        ),
        'Regex' => 
        array (
          0 => '/Order\\s+for\\s+Store\\s+no.\\s*\\:\\s*(\\S+)/',
        ),
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
          0 => 7,
        ),
        'Regex' => 
        array (
          0 => '/Order\\s+no.\\s*:\\s*(\\S+)/',
        ),
      ),
      6 => 
      array (
        'Name' => 'PO_Date',
        'row' => 
        array (
          0 => 1,
        ),
        'Regex' => 
        array (
          0 => '/(\\d+\\.\\d+\\.\\d+)/',
        ),
        'format' => 'd.m.Y',
      ),
      7 => 
      array (
        'Name' => 'Delivery_Date',
        'row' => 
        array (
          0 => 27,
        ),
        'Regex' => 
        array (
          0 => '/Delivery\\s+date\\s+(\\S+)/',
        ),
        'format' => 'd/m/Y',
      ),
      8 => 
      array (
        'Name' => 'Expiry_Date',
        'row' => 
        array (
          0 => 27,
        ),
        'Regex' => 
        array (
          0 => '/Delivery\\s+date\\s+(\\S+)/',
        ),
        'format' => 'd/m/Y',
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
          0 => 14,
        ),
        'Regex' => 
        array (
          0 => '/(.*)/',
        ),
      ),
      15 => 
      array (
        'Name' => 'VendorAddress',
        'row' => 
        array (
          0 => 16,
        ),
        'Regex' => 
        array (
          0 => '/(?\'addr\'^.{0,50})/',
        ),
        'stopIdentifierRegex' => '/Fiscal\\s+no.\\s+supplier/',
        'jumpUp' => 1,
        'regexPosition' => '1',
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
          0 => '/IN\\s*\\-\\s*\\d+\\s+(.*)/',
        ),
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
        'Name' => 'another_pdf_format',
        'value' => '1',
      ),
      20 => 
      array (
        'Name' => 'Vat_Tin',
        'row' => 
        array (
          0 => 5,
        ),
        'Regex' => 
        array (
          0 => '/Supplier\\s+no.\\s*:\\s*(\\d+)/',
        ),
      ),
    ),
  ),
  'Footer' => 
  array (
    'Identifier' => 
    array (
      'value' => 'Total order amount for store no.',
      'start' => 0,
      'length' => 32,
    ),
  ),
  'Items' => 
  array (
    'StartRow' => 29,
    'RowsPerItem' => -1,
    'Regex' => 
    array (
      0 => '/(?\'SrNo\'\\d+)\\s+(?\'ArticleNo\'\\S+)\\s+(?\'ign\'\\S+)\\s+(?\'ign6\'\\d+)?\\s+(?\'Itemname\'.*\\w+\\s)\\s+(?\'ign2\'\\S+)\\s+(?\'ign3\'\\d\\S*)\\s+(?\'ign4\'\\S+)\\s+(?\'ign5\'\\d+)\\s+(?\'Amount\'\\d\\S*)/',
      1 => '/(?\'Ignore\'\\S+)\\s+\\.\\s*(?\'ign\'\\S+)\\s+(?\'EAN\'\\S+)\\s+(?\'Itemname\'.*\\w+)?\\s+?(?\'ign2\'\\d+)\\s+(?\'CAR\'\\S+)\\s+(?\'Qty\'\\S+)\\s+(?\'Rate\'\\d\\S*)/',
      2 => '/(?\'ign\'\\S+)\\s+(?\'MRP\'\\d\\S*)\\s+(?\'VAT\'\\d\\S*)\\s*(?\'Ign\'\\%)\\s+(?\'ign1\'\\S+\\%)\\s+(?\'ign2\'\\d+)?\\s+(?\'ign3\'\\d\\S*%)/',
    ),
  ),
);


$ini_text=json_encode($uni_array);
$test=$db->safe($ini_text);
 echo $insertQry="insert into it_inis set ini_text= '".addslashes($ini_text)."', master_dealer_id = 14, createtime = now(), updatetime = now()";
 $no=$db->execInsert($insertQry);
 echo "\nResult:";
 echo $no;
 echo "\n\n";