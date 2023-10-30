<?php
require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once "Parsers/wordWrapItemsCase.php";
require_once "Parsers/mixcaseABRL.php";
require_once "eachRowRegex.php";
require_once "Max_Hypermarket_Case.php";
require_once "mixcaseABRL.php";
require_once "mixcase2ABRL.php";
print "<br>HERE IN PDB <br>";

//extract($_POST);
//
//print_r($_POST);

class itemsINI{
    
    public function __construct() {
        
    }
    
    public function iprocess($filename,$iniFile){

        try {
            $dataFile=$filename;
            $invtext=file_get_contents($filename);
             

            $iniStr = $iniFile;
            $ini = json_decode($iniStr);
            $rows = file($dataFile);
            print"<br>Rows in itemsINI==<br>";
            print_r($rows);
            print"<br>";
            print"<br>ini in itemsINI==<br>";
            print_r($ini);
            print"<br>";
            //$header = getIHeader($rows, $ini);  // call to getHeader()
            //$header->fileName=$dataFile;


            $items = getIItems($rows, $ini);// call to getItems()
            
            return $items;
            /***** END CLASS CALL HERE *********/
           //////////////////////////////////////// 
            $verifySum = getIVerifySum($rows, $ini);// call to getVerifySum()


            $fileOK = 1;  

            if (isset($verifySum) && isset($verifySum->value) && isset($verifySum->field)) {
                    $total = (float)0.0;
                    foreach ($items as $item) {
                            $value= $item[$verifySum->field];
                            $value= str_replace(",","", $value);		
                            $total = $total + $value ;
                    }
            //    print "Total=$total\n";
            //       print "vsum=".$verifySum->value."<br>";
                    $diff = abs($verifySum->value - $total);
                    //print"<br>diff==$diff<br>";
            //	if ((int)$verifySum->value != (int)$total) {
                    if ($diff > 1.0) {
                            $fileOK = false;
                            print "[".$verifySum->value."] != [$total] [$diff]\n";
                    }
            }
            //var_dump($items);
            if ($fileOK) {
                $resp = printItems($header,$items,$invtext);  //call to printItems() in printItem.php
                return $resp;
                exit;

            }
} catch (Exception $xcp) {
	print "Error:".$xcp->getMessage();
}
    }
}

function getIHeader($rows, $ini) {
  // print_r($rows);
  //  print"<br><br>";
    //print_r($ini);
 ///   print"<br><br>";
    
if (!isset($ini->Header->Fields) || !is_array($ini->Header->Fields) || count($ini->Header->Fields) == 0) 
{print"JSON not read";return;}
//print"present<br>";
$fields = $ini->Header->Fields;
//print_r($fields);
$header = array();
 $matches=array();
foreach ($fields as $field) {
  	if (isset($field->value)) {
		$header[$field->Name] = trim($field->value);
                 //print_r($field->value);
	} else if (isset($field->fromFilename)) {
             // print_r($field->value);
		$value = $ini->fileName;
                    if (isset($field->start)) {
                    	    if (isset($field->length)) {
				$value = trim(substr($value,$field->start,$field->length));  //print"val---$value";			
			    } else {
				$value = trim(substr($value,$field->start));   //print"val---$value";
			    }
		        }
                   $header[$field->Name] = $value;                	  
	}
      
        else if(is_array($field->row)){
            $vaddrlastrow=0;
            if(isset($field->stopIdentifierRegex) && trim($field->stopIdentifierRegex) != ""){
                print "<br>LRI: ".$field->stopIdentifierRegex."<br>";
                //$st_row = $field->row[0] - 1;
                $st_row = $field->row[0];
                if(isset($field->jumpUp)&& trim($field->jumpUp)!=""){
                    $jumpUp = $field->jumpUp;
                }else{
                    $jumpUp = 0;
                }
                $vaddrlastrow = fetchIAddressLastRow($st_row,$field->stopIdentifierRegex,$rows,$field->Regex[0],$jumpUp);
                $st_row = $st_row - 1;
                $vaddr="";
                for($i=$st_row;$i<$vaddrlastrow;$i++){
                    if(preg_match($field->Regex[0],$rows[$i],$matches)){
//                    print"<br>addr<br>"; print_r($matches); print"<br>";
                    //$vaddr = $vaddr." ".trim($matches[2]); //store address
                         if(isset($field->regexPosition) && trim($field->regexPosition) !="" && trim($field->regexPosition) > 0){
                          // print"<br> in regexPosition<br>";
                            $vaddr = $vaddr." ".trim($matches[$field->regexPosition]);   
                        }else{
                           // print"<br> in regexPosition else ABRL case<br>";
                          $vaddr = $vaddr." ".trim($matches[2]); //store address for ABRL case
                        }
                    }
                }
                $header[$field->Name] =trim($vaddr);
            }else if(isset($field->Regex))
            {// print"in if<br>";
            //print "<br> Regex: ".$field->Regex." <br>";
                 $str="";
                 $totlines=count($field->row);
                 for($i=0;$i<$totlines;$i++)
                 {                    
                    $rowIndex=$field->row[$i]-1;
//                    print"$rowIndex<br>";
//                    print "<br> Regex: ".$field->Regex[$i]."<br>";
                    if(preg_match($field->Regex[$i],$rows[$rowIndex],$matches)){
//                    print"<br>addr<br>"; print_r($matches); print"<br>";
                    $str = $str." ".trim($matches[1]); //store address
                    }
                 }
                //print"address===$str<br>";
                 //$address=preg_replace('/\d{2}\/\d{2}\/\d{4}/','',$str);
                  //print"address===$address<br>";
                 $header[$field->Name] =trim($str);
            }
           
            else
            {// print"in else<br>";
                 $str="";
                 $totlines=count($field->row);
                 for($i=0;$i<$totlines;$i++)
                 {                    
                    $rowIndex=$field->row[$i]-1;
                    $str = $str.$rows[$rowIndex];  
                 }
                 //print"address2===$str<br>";
                 //$address=preg_replace('/\d{2}\/\d{2}\/\d{4}/','',$str);
                  //print"address===$address<br>";
                 $header[$field->Name] = $str;
                
            }
        }
         
        else{
                $rowIndex = (int)$field->row - 1;
                $value = trim($rows[$rowIndex]);
             
               if (isset($field->regex)) {
                   //print"val---$value<br><br>";	
			if (preg_match($field->regex, $value, $matches)) $value = $matches[1];
                    } else if (isset($field->start)) {
			if (isset($field->length)) {
				$value = trim(substr($value,$field->start,$field->length));	//print"val---$value<br><br>";			
			} else {
				$value = trim(substr($value,$field->start));     //  print"val---$value<br><br>";		
			}
		}
		$header[$field->Name] = $value;
	}
	if (isset($field->format)) {
		$header[$field->Name."Format"] = $field->format;
	}
}
return (object)$header;
}

function getIItems($rows, $ini) {
$startRow = (int)$ini->Items->StartRow-1;
//print"sr=$startRow<br>";
$footerIdentifier=false;
$lastItemRow=0;
if (isset($ini->Footer->Rows)) { // print"in1<br>";
	$footerRows = (int)$ini->Footer->Rows;
	$lastItemRow = count($rows) - $footerRows;
       
} else if (isset($ini->Footer->Identifier)) { //print"in2<br>";
	$footerIdentifier = trim($ini->Footer->Identifier->value);
       // print"$footerIdentifier";
	$lastRow = count($rows);
        //print count($rows);
        //print "<br>START ROW = $startRow , END ROW: $lastRow";
        $foundFooter=false;
        for ($i=$startRow; $i<$lastRow; $i++) { 
       	if ($foundFooter == false) {
		$rowIdentifier = trim($rows[$i]);
                      	if (isset($ini->Footer->Identifier->start)) {
			$start = (int) $ini->Footer->Identifier->start;
                        if (isset($ini->Footer->Identifier->length)) {
				$length = (int) $ini->Footer->Identifier->length;
				$rowIdentifier = substr($rowIdentifier,$start,$length);
			} else {
				$rowIdentifier = substr($rowIdentifier,$start);
			}
		}
              // print "<br> RI: $rowIdentifier <br>FI: $footerIdentifier  ";
               $sval=strcmp(trim($rowIdentifier),trim($footerIdentifier));
               //print"<br>sval=$sval<br>";
		//if ((strcmp($rowIdentifier,$footerIdentifier)==0) ){
                 if($sval==0)
                 {
                    //print"<br>rowno=$i";
                    $r=count($rows);
                   // print"<br>no of rows=$r";
                   	$ini->Footer->Rows = count($rows) - $i;
                      //  print"<br>footer row<br>";print $ini->Footer->Rows;
			$foundFooter=true;
                        $lastItemRow=$i;
                        //print"lr=$lastItemRow";
			break;
		}
	}
        }
} else {//print"in3<br>";
	return false;
}
//print"lr=$lastItemRow";
$items = array();
$foundFooter=false;
print"strt=$startRow<br>end=$lastItemRow";
//print "<br>START ROW = $startRow , END ROW: $lastItemRow";
//print"<br>RowsPerItem=";print $ini->Items->RowsPerItem;
//print"<br>";
//$lastItemRow=$lastItemRow-1;
 //$rmatch = 0;
$matched_regex_position = -1;
$item_parts = array();
//$complete_item = array();
$all_items = array();
//$start_of_items = 1;
//$first_item_pushed = 0;
if(trim($ini->Items->RowsPerItem)==-1){ // appay all regerx to all rows if itemline
        print"<br>in -1 case --------------if<br>";
        $PER = new eachRowRegex();
        $items = $PER->fetchItems($startRow,$lastItemRow,$rows,$ini,$master_dealer_id);
        print"<br>return array items=<br> ";
    }
if(trim($ini->Items->RowsPerItem)==-2){ // word wrap items case
    $ww = new wordWrapItemsCase();
    $items = $ww->fetchItems($startRow,$lastItemRow,$rows,$ini);
}
if(trim($ini->Items->RowsPerItem)==-3){ // Max_Hypermarket_Case Item in 3/4 lines with * in item line or above itemline
    $MH = new Max_Hypermarket_Case();
    $items = $MH->fetchItems($startRow,$lastItemRow,$rows,$ini);
}
 if(trim($ini->Items->RowsPerItem)==-4){ // appay all regerx to all rows if itemline
        print"<br>in mixABRL case --------------if<br>";
        $MCA = new mixcaseABRL();
        $items = $MCA->fetchItems($startRow,$lastItemRow,$rows,$ini);
        print"<br>return array items=<br> ";
//        print_r($items);
//        print"<br>";
    }
    if(trim($ini->Items->RowsPerItem)==-5){ // itemname n tax positions changes line wise optionally
        print"<br>in mixABRL case2 itemname n tax comes in upper or same line --------------if<br>";
        $MCA2 = new mixcase2ABRL();
        $items = $MCA2->fetchItemsAB($startRow,$lastItemRow,$rows,$ini);
//        print"<br>return array items=<br> ";
//        print_r($items);
        print"<br>";
    }
else {
    for ($i=$startRow; $i<$lastItemRow; $i++) { 

        if(trim($ini->Items->RowsPerItem)==1){    
         $keywords=array();
         $iarray = array();
      	if (preg_match($ini->Items->Regex, $rows[$i], $fields)) {
//           print"in if";
            $fields=array_map('trim',$fields);          
            if (isset($ini->Items->DerivedFields)) { //not used
                    $dFields = $ini->Items->DerivedFields;
                        foreach ($dFields as $dField) {
                               	$fieldName = $dField->fieldName;
                                $operation = $dField->operation;
                                $useFields = $dField->useFields;
                               if ($operation == "multiply") {
					$value = 1;
					foreach ($useFields as $useFieldName) {
                                               $value = $value * (float)$fields[$useFieldName];
					}
					$fields[$fieldName] = $value;
                                       
				}
			}
		}
                 
//                print "<br><br><br><br>BEFORE IF CONDN FIELDS ARR: <br>";
//                print_r($fields);
//                print "<br><br>";
                
               if(!empty($fields)){
                // print "<br>in not empty fields";
                  $pushFlag=0;
                  $pregFlag=0;
                   $artno=(trim(getFieldValue($fields,"ArticleNo")));
                   $EAN=trim(getFieldValue($fields,"EAN"));
                   $Itemname=trim(getFieldValue($fields,"Itemname"));
                   $CAR=trim(getFieldValue($fields,"CAR"));
                   $mrp=doubleval(trim(getFieldValue($fields,"MRP")));
                  // $VAT=doubleval(trim(getFieldValue($fields,"VAT")));
                   $VAT=trim(getFieldValue($fields,"VAT"));
                   $Qty=doubleval(trim(getFieldValue($fields,"Qty")));
                   $Rate=doubleval(trim(getFieldValue($fields,"Rate")));
                   $Amount=doubleval(trim(getFieldValue($fields,"Amount")));
                  
//                   print"<br>an=$artno<br>ean=$EAN<br>car=$CAR<br>mrp=$mrp<br>vat=$VAT<br>qty=$Qty<br>rate=$Rate<br>amt=$Amount<br>";
//                   print"<br>itemname=$itemname<br>";
                   if($artno!="" && $EAN!="" && $CAR!="" && $mrp!="" && $VAT!="" && $Qty!="" && $Rate!="" && $Amount!="")
                   { 
//                       print "<br> PF 1st case: $pushFlag ";
//                       print"in if";
                       if(preg_match("/[0-9]/",$artno)){
                           $pregFlag=1;
                          //print"<br> $artno true case---$pregFlag";
                       }else{
                           $pregFlag=0;
                          // print"<br> $artno false case---$pregFlag";
                       }
                       
                       if(preg_match("/[0-9a-zA-Z]/",$EAN)){
                           $pregFlag=1;
                          //print"<br> $EAN true case---$pregFlag";
                       }else{
                           $pregFlag=0;
                          // print"<br> $EAN false case---$pregFlag";
                       }
                       
                       if(preg_match("/[0-9a-zA-Z]/",$CAR)){
                           $pregFlag=1;
                           //print"<br> $CAR true case---$pregFlag";
                       }else{
                           $pregFlag=0;
                          // print"<br> $CAR false case---$pregFlag";
                       }
                       
                       if((is_double($mrp))&&(is_double($Qty))&&(is_double($Rate))&&(is_double($Amount))){ //&&(is_double($VAT))
                           $pushFlag=1;
                         //  print"<br> Push Flag true case---$pushFlag";
                       }else{
                           
                          $pushFlag=0;
                         // print"<br> Push Flag false case---$pushFlag";
                        }
                       
                   }
                  //print"<br>pushflag=$pushFlag<br>pregflag=$pregFlag<br>"; 
                  //print"<br>lineno=$lineno && prod=="; 
                   
//                  print "<br>fileds arr: <br>"; 
//                   print_r($fields); print"<br>"; 
                  if($pushFlag==1)
                  {
//                   print "<br> in flag ITEM ARRAY true case: <br>";
                   $items[] = $fields; 
//                   print_r($items);
//                   print "<br><br>";
                  }
               }
	       //print"<br> prod=="; print_r($items);print"<br>";
              // $itemname=trim(getFieldValue($fields,"itemname"));
              // print"<br>itemname=$itemname<br>";
            }
} else if(trim($ini->Items->RowsPerItem)==-1){ // every item line check with each regex
     
   
    $regex_size = sizeof($ini->Items->Regex);
    $linetext = $rows[$i];
    $continue_main_loop = 0;
    for($j = 0 ; $j < $regex_size ; $j++){ // regex iteration loop
        $regex = $ini->Items->Regex[$j]; 
        $matches=array();
        print"<br>Line no: $i <br>Regex at: $j <br> Regex is : $regex <br> Line text is: $linetext <br>";
        preg_match($regex,trim($linetext),$matches);
         print_r($matches); 
         print"<br>";       
         if(!empty($matches)){ // means regex matched
             $matched_regex_position = $j;
             print "<br>IN ESLE CASE Matched regex position : $matched_regex_position ";
             //****************************************
             if($matched_regex_position==0){
                 if(! empty($item_parts)){
                     array_push($all_items, $item_parts);
                        print "<br>All Items array <br>";
                       //print_r($all_items);
                       unset($item_parts);
                       $item_parts = array();
                 }
             }
             $item_parts = array_merge($item_parts,$matches);
             //*****************************************            
                           
             //global $item_parts;
             
             
             print "<br>ITEM PARTS ARR IN ELSE CASE: <br>";
             print_r($item_parts);
             print "<br>";
             $continue_main_loop = 1;
             break;
         }   
         
         if(trim($linetext)==""){ //blank line             
//             $continue_main_loop = 1;
//             $matched_regex_position++;
//             break;
         }
    }
    
    if($continue_main_loop == 1){ // go start of min item loop to fetch next item
        print "<br> In continue main loop : $continue_main_loop ";
        continue;
    }
     
}
  else if(trim($ini->Items->RowsPerItem) > 1){ // means rows per item > 1
           $fields = array();
           $arrs = array();
           $range =  $ini->Items->RowsPerItem;
           $l=$i;                   //l for line index
           //for($k=1;$k<=$range;$k++){
           $pageFlag = 0;
           for($k=0;$k<$range;$k++){
               
               if($pageFlag == 1){$k=0;}
              // print "<br>"
                //$r=$k-1;                // r for regex index
               $r=$k;
                
                $regex = $ini->Items->Regex[$r];
//                print "<br> REGEX: $regex <br>";
//                print "<br> ROW DATA ---$l----: ".$rows[$l]."<br><br>";
                if(isset($rows[$l])){
                    $linetext = $rows[$l];
                }else{
                    $linetext = "";
                }
//                print "<br>LINE TEXT abv blank chk: ".$linetext."<br>";
                if(trim($linetext)==""){ //skip blank lines
                    $l++;
                    //$k++;
                    continue;
                }
             
                if(preg_match("/Page\s+\d+/",$linetext)){
//                    print "<br>IN PAGE";
                    $l++;
                    $pageFlag=1;
                    continue;
                }
              
                      $pageFlag=0;
               
                
                print "<br> R: $r";
                print "<br> L: $l";
                print "<br> I REGEX[$r]:$l".$ini->Items->Regex[$r];
                print "<br> ROW DATA ---$l----: ".$rows[$l]."<br><br>";
//                
                print "<br>LINE TEXT: ".$linetext."<br>";
                $matches=array();
                print"<br>matches in > 1 condn:<br>";
                preg_match($regex,trim($linetext),$matches);
                 print_r($matches); 
                 print"<br>";
               //if(preg_match($regex,$linetext))
                 if(!empty($matches))
               {
//                        print"<br>In if(linetxt)<br>";
                     //preg_match($regex,$rows[$l],$arrs);
//                     print "I REGEX[$r]:$l".$ini->Items->Regex[$r]."<br>";
//                     print "<br> ROW DATA: ".$rows[$l]."<br>";
                    //print "<br>ARRAY:".print_r($arrs)."<br>";
                     $fields = array_merge($fields,$matches);
                     $l=$l+1;
                //print"nextline=$l<br>";
                }
                 else
                {
//                            print"<br>In else (linetxt)<br>";
                      $l=$l+1;
                      preg_match($regex,$rows[$l],$arrs);
//                      print "I REGEX[$r]:$l".$ini->Items->Regex[$r]."<br>";
//                      print "<br> ROW DATA: ".$rows[$l]."<br>";
//                      print "<br>ARRS: ".print_r($arrs)."<br>";
                      $fields = array_merge($fields,$arrs);
                      $l=$l+1;
                }
           }
           //print "<br>i=$i";
           //print "<br>l=$l";
            $i=$l-1;
           //print"<br>line=$i<br>";
                  
            if (isset($ini->Items->DerivedFields)) { //not used
                    $dFields = $ini->Items->DerivedFields;
                        foreach ($dFields as $dField) {
                               	$fieldName = $dField->fieldName;
                                $operation = $dField->operation;
                                $useFields = $dField->useFields;
                                if ($operation == "multiply") {
					$value = 1;
					foreach ($useFields as $useFieldName) {
                                               $value = $value * (float)$fields[$useFieldName];
					}
					$fields[$fieldName] = $value;
                                }
			}
		}
                
               
//                print "<br><br><br><br>BEFORE IF CONDN FIELDS ARR: <br>";
//                print_r($fields);
//                print "<br><br>";
                
               if(!empty($fields)){
                 //print "<br>in not empty fields";
                  $pushFlag=0;
                  $pregFlag=0;
                   $artno=(trim(getFieldValue($fields,"ArticleNo")));
                   $EAN=trim(getFieldValue($fields,"EAN"));
                   $Itemname=trim(getFieldValue($fields,"Itemname"));
                   $CAR=trim(getFieldValue($fields,"CAR"));
                   $mrp=doubleval(trim(getFieldValue($fields,"MRP")));
                   //$VAT=doubleval(trim(getFieldValue($fields,"VAT")));
                   $VAT=trim(getFieldValue($fields,"VAT"));
                   $Qty=doubleval(trim(getFieldValue($fields,"Qty")));
                   $Rate=doubleval(trim(getFieldValue($fields,"Rate")));
                   $Amount=doubleval(trim(getFieldValue($fields,"Amount")));
                  
//                   print"<br>an=$artno<br>ean=$EAN<br>ord=$Order<br>mrp=$mrp<br>vat=$VAT<br>qty=$Qty<br>rate=$Rate<br>amt=$Amount<br>";
                   //print"<br>itemname=$itemname<br>";
                   if($artno!="" && $EAN!="" && $CAR!="" && $mrp!="" && $VAT!="" && $Qty!="" && $Rate!="" && $Amount!="")
                   { 
//                       print "<br> PF 1st case: $pushFlag ";
//                       print"in if";
                       if(preg_match("/[0-9]/",$artno)){
                           $pregFlag=1;
                         // print"<br> $artno true case---$pregFlag";
                       }else{
                           $pregFlag=0;
                         //  print"<br> $artno false case---$pregFlag";
                       }
                       
                       if(preg_match("/[0-9a-zA-Z]/",$EAN)){
                           $pregFlag=1;
                           //print"<br> $EAN true case---$pregFlag";
                       }else{
                           $pregFlag=0;
                           //print"<br> $EAN false case---$pregFlag";
                       }
                       
                       if(preg_match("/[0-9a-zA-Z]/",$CAR)){
                           $pregFlag=1;
                           //print"<br> $CAR true case---$pregFlag";
                       }else{
                           $pregFlag=0;
                           //print"<br> $CAR false case---$pregFlag";
                       }
                       
                       if((is_double($mrp))&&(is_double($Qty))&&(is_double($Rate))&&(is_double($Amount) && trim($pregFlag)==1)){ //&&(is_double($VAT))
                           $pushFlag=1;
                           //print"<br> Push Flag true case---$pushFlag";
                       }else{
                           
                          $pushFlag=0;
                          //print"<br> Push Flag false case---$pushFlag";
                        }
                       
                   }
                  //print"<br>pushflag=$pushFlag<br>pregflag=$pregFlag<br>"; 
                  //print"<br>lineno=$lineno && prod=="; 
                   
//                  print "<br>fileds arr: <br>"; 
//                   print_r($fields); print"<br>"; 
                  if($pushFlag==1)
                  {
//                   print "<br> in flag ITEM ARRAY true case: <br>";
                   $items[] = $fields; 
//                   print_r($items);
//                   print "<br><br>";
                  }
               }
        
}
}

}

if(! empty($item_parts)){
array_push($all_items, $item_parts);
$items = $all_items;
}
//print "<br>ALL ITEMS: ";
//print_r($all_items);
//print"<br> prod=="; print_r($items);print"<br>";     
//if ($footerIdentifier && !$foundFooter) { print "Footer start not found using identifier [$footerIdentifier]\n"; return; }
return $items;
}

function getIVerifySum($rows, $ini) {
if (!isset($ini->Footer->VerifySum))    return;

$footerRows = (int)$ini->Footer->Rows;
//print"fr==$footerRows<br>";
$numRows = count($rows);
//print"nr==$numRows<br>";
$rowIndex = $numRows - $footerRows + (int)$ini->Footer->VerifySum->row-1;
//print"ri==$rowIndex<br>";
$value = $rows[$rowIndex];
$value= str_replace(",","", $value);
//print"val==$value<br>";
$start = (int)$ini->Footer->VerifySum->start-1 ;
//print"srt==$start<br>";
$length=false;
if (isset($ini->Footer->VerifySum->length)) {
	$length = (int)$ini->Footer->VerifySum->length;
        //print"len--$length<br>";
	$value = (float)trim(substr($value, $start, $length)); print"val==$value<br>";
} else {
	$value = (float)trim(substr($value, $start));  print"val==$value<br>";
}
return (object) array("value" => $value, "field" => $ini->Footer->VerifySum->field);
}


function fetchIAddressLastRow($startRow,$stopIdentifierRegex,$rows,$regex,$jumpUp){
    print "<br>START ROW : $startRow :: stopIdentifierRegex : $stopIdentifierRegex :: Regex : $regex<br>";
    $eof = count($rows);
    print "<br>EOF: $eof <br>";
   // $no_spaces_identifier = str_replace(" ", "", $identifier);
    //print "<br>NO SPACES IDENTIFIER: $no_spaces_identifier <br>";
    $last_row = 0;
    for($i=$startRow;$i<=$eof;$i++){
       //$linedata = $rows[$i];
        $linedata = "";
        if(preg_match($regex,$rows[$i],$matches)){
//                    print"<br>addr<br>"; print_r($matches); print"<br>";
            $linedata = trim($matches[1]); //store address
         }
        print "<br>LINE $i:LINE DATA: $linedata <br>";
        $no_spaces_line_data = str_replace(" ", "", $linedata);
        print "<br>NO SPCS LINE DATA: $no_spaces_line_data <br>";
       // if(strcmp(trim($no_spaces_identifier),trim($no_spaces_line_data))==0){
        $arr = array();
        if(preg_match($stopIdentifierRegex,$linedata,$arr)){
            print "<br>Matched<br>";
            $last_row = $i;
            $last_row = $last_row-$jumpUp;
            break;
        }else{
            print "<br>Not Matched<br>";
        }
    }
    print "<br>LR: $last_row <br>";
    return $last_row;
}
?>