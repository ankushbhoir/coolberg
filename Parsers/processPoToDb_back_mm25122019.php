<?php
require_once("../../it_config.php");
require_once "SaveToDB.php";
//require_once "SaveToDB_ArticleNoBased.php";
require_once "SaveToDB_ArticleNoBased_copy.php";
require_once "wordWrapItemsCase.php";
require_once "Max_Hypermarket_Case.php";
require_once "eachRowRegex.php";
require_once "mixcaseABRL.php";
require_once "mixcase2ABRL.php";
require_once "overlappingCase.php";
require_once "lib/core/Constants.php";
//print "<br>HERE IN PDB <br>";

//extract($_POST);
//
//print_r($_POST);

class processPOToDB {
    
    public function __construct() {
        
    }
    
    public function process($filename,$iniFile,$master_dealer_id, $iniid,$newPOpdf,$shipping_address,$shipping_id){//ini_id  
      print "<br>************IN PR with INI_ID=$iniid<br>";
        try {
           
            $dataFile=$filename;
            $invtext=file_get_contents($filename);


            $iniStr = $iniFile;
            $ini = json_decode($iniStr);
            $rows = file($dataFile);
   
//To count total no of PO as 1po = 100lines
//count rows then divide it by 100 to get pocount save it to some table (it_po)in database with po no            
//            $filelen= count($rows);
//            print" file length: $filelen \n\n";
//            if($filelen > 100){
//                $noofpos= ceil($filelen/100);
//                print"\n1:$noofpos \n";
//            }else{
//               $noofpos=1; 
//               print"\n2:$noofpos \n";
//            }
// print"<br>mayur<br>";
            $header = getHeader($rows, $ini);  // call to getHeader()
            $header->fileName=$dataFile;
            // print"<br>mayur<br>";
            // print_r($header);
            $noofitems=0; 
            
            $items = getItems($rows, $ini,$master_dealer_id);// call to getItems()
            
            /////
            //$response= itemchk($items);
            //$responsearr= explode($response);
            //if($responsearr[0]= POStatus::STATUS_ISSUE_AT_PROCESSING){
            //     $ret =  POStatus::STATUS_ISSUE_AT_PROCESSING."::-1";
            //     return $ret;
            //}
            /////
            
            $vat_skip_chainid=array(14);
           
            $noofitems=count($items);
            print"<br>no of items in PO=$noofitems<br>";
            
          //  print"<br>ITEMS FOUND:<br>";echo'<pre>';print_r($items);echo'<pre>';
            
            if($noofitems==0){
                 //$ret =  POStatus::STATUS_ISSUE_AT_PROCESSING."::-1";
                 $ret =  POStatus::STATUS_ISSUE_AT_PROCESSING."::-1::".IssueReason::ITEMARREMPTY;
                 return $ret;
            }else{
      //          print"<br>Check items in array";
//                print_r($items);
//                print"<br>";
                $cnt=1;
                foreach($items as $item){
                    $Qty = str_replace(",","",trim(getFieldValue($item,"Qty")));
                    $MRP = str_replace(",","",trim(getFieldValue($item,"MRP")));
                    //$VAT = str_replace(",","",trim(getFieldValue($item,"VAT")));
                    $Rate = str_replace(",","",trim(getFieldValue($item,"Rate")));
                    $Amount = str_replace(",","",trim(getFieldValue($item,"Amount")));     
                    if($master_dealer_id == 7 ){    //only for walmart
        //                print"<br> set vat & rate =0 for wallmart<br>";
                         $item['VAT']= 0;
                         $item['Rate']= 0;
                         $Rate=0;
                    }
                                        
                    if(is_numeric("$Qty")  && is_numeric("$Rate") && is_numeric("$MRP") && is_numeric("$Amount")){    //&& is_numeric("$VAT")                   
                        if($master_dealer_id != DEF_METRO){
                            $VAT = str_replace(",","",trim(getFieldValue($item,"VAT")));
                                 print"<br>$cnt. TAX percent".$VAT."<br>";
                                 $cnt++;
                                 print_r($item);
                            if($VAT==""){
                                $VAT=0;
                                $item['VAT']=0;
                               print"VAT is null set to 0";
                            }
                            //if($item['VAT']<= DEF_TAX && $item['VAT'] >= 0 ){
                             if($VAT<= DEF_TAX && $VAT >= 0 && is_numeric("$VAT")){
                                print"<br>TAX :".$VAT."<=".DEF_TAX;
                            }
                            else{
                                print"<br>$cnt. invalid TAX percent".$VAT."<br>";
                                print"values in item->";
                             print_r($item);
                                $ret =  POStatus::STATUS_ISSUE_AT_PROCESSING."::-1::".IssueReason::INVALID_TAX;
                                //$ret =  POStatus::STATUS_ISSUE_AT_PROCESSING."::-1";
                                return $ret;
                            }
                        }
                    
                        $poAmount= str_replace(",", "", $item['Amount']);
			 $car_arr = array('PC','PCS','pc','Pc','Pcs','pcs','EA');
                        if($master_dealer_id==27 && !in_array($item['CAR1'],$car_arr)){
                            $item['Rate'] = $item['Rate']/$item['Qty'];
                        }
                        $cal_amt=$item['Qty']*$item['Rate'];

                        if($master_dealer_id == 14){                            // only for metro 
                            if(!( trim($item['Qty']) > 0 && trim($item['MRP']) > 0 && trim($item['MRP']) <= 2000 && trim($item['Rate']) > 0 && trim($item['Rate']) <= 2000)){
                    //            print" in Qty,MRP or Rate out of range check<br>";
                                //$ret =  POStatus::STATUS_ISSUE_AT_PROCESSING."::-1";
                                $ret =  POStatus::STATUS_ISSUE_AT_PROCESSING."::-1::".IssueReason::INVALID_MRPQTY;
                                return $ret;
                            }
                        } 
                      //  print"<br>calculations on processpotodb <br>Qty:".$item['Qty']."<br>Rate:".$item['Rate']."<br>calamt:".$cal_amt."<br>poAmount:".$poAmount."<br>";
                        //print"<br>MD_id=$master_dealer_id<br>";
//                        if(trim($master_dealer_id) != 13){    
                            //print"<br>chk po amt on processpotodb page";
                            if($poAmount >= $cal_amt){
                          //      print"<br>valid amount<br>";
                                continue;
                            }
                            else{
                                $diff_amt = $cal_amt - $poAmount;
                                $diff_per = ($diff_amt / $cal_amt) * 100 ;
                              //  print"<br>diff_per:".$diff_per."<br>diff_amt:".$diff_amt."<br>";
                                if($diff_per <= DEF_TAX){
                                    continue;
                                }
                                else{
                                //    print"<br>invalid Amount<br>";
                                    //$ret =  POStatus::STATUS_ISSUE_AT_PROCESSING."::-1";
                                    $ret =  POStatus::STATUS_ISSUE_AT_PROCESSING."::-1::".IssueReason::INVALID_AMT;
                                    return $ret;
                                }
                            }
//                        }
                    }
                    else{
                           // print_r($item);
                            //print"<br>";
                            //print"<br>invalid Item Values<br>";
                            //$ret =  POStatus::STATUS_ISSUE_AT_PROCESSING."::-1";
                            $ret =  POStatus::STATUS_ISSUE_AT_PROCESSING."::-1::".IssueReason::INVALID_ITEM;
                            return $ret;
                    }
                }
            }
            
            //$verifySum = getVerifySum($rows, $ini);// call to getVerifySum()


            $fileOK = 1;  

//            if (isset($verifySum) && isset($verifySum->value) && isset($verifySum->field)) {
//                    $total = (float)0.0;
//                    foreach ($items as $item) {
//                            $value= $item[$verifySum->field];
//                            $value= str_replace(",","", $value);		
//                            $total = $total + $value ;
//                    }
//            //    print "Total=$total\n";
//            //       print "vsum=".$verifySum->value."<br>";
//                    $diff = abs($verifySum->value - $total);
//                    //print"<br>diff==$diff<br>";
//            //	if ((int)$verifySum->value != (int)$total) {
//                    if ($diff > 1.0) {
//                            $fileOK = false;
//                            print "[".$verifySum->value."] != [$total] [$diff]\n";
//                    }
//            }
            //var_dump($items);
            if ($fileOK) {
                $ini_type=0;
                    if(isset($header->initype)){
                        $ini_type=$header->initype;
                        //print"initype=$ini_type<br>";
                    
                        if($ini_type==1){
//                           print"<br>call to printItems() in SaveToDB.php<br>"; 
//                           $resp = printItems($header,$items,$invtext,$iniid,$newPOpdf,$shipping_address,$shipping_id,$master_dealer_id); //ini_id //call to printItems() in SaveToDB.php
//                           return $resp;
                            // print"<br>call to printItems() in SaveToDB_ArticleNoBased.php<br>"; 
                           $resp = printItemsArticleNoBased($header,$items,$invtext,$iniid,$newPOpdf,$shipping_address,$shipping_id); //ini_id //call to printItems() in SaveToDB_ArticleNoBased.php
                           return $resp;
                        }else if($ini_type==2){
                          //  print"<br>call to printItems() in SaveToDB_ArticleNoBased.php<br>"; 
                           $resp = printItemsArticleNoBased($header,$items,$invtext,$iniid,$newPOpdf,$shipping_address,$shipping_id); //ini_id //call to printItems() in SaveToDB_ArticleNoBased.php
                           return $resp;
                        }else{
                            //print" Missing ini type <br> ";
                        }
                    }
//                      $resp = printItems($header,$items,$invtext);  //call to printItems() in printItem.php
//                      return $resp;
                    exit; 
            }
        } catch (Exception $xcp) {
                print "Error:".$xcp->getMessage();
        }
    }
}

function getHeader($rows, $ini) {
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
               // print "<br>LRI: ".$field->stopIdentifierRegex."<br>";
                //$st_row = $field->row[0] - 1;
                $st_row = $field->row[0];
                if(isset($field->jumpUp)&& trim($field->jumpUp)!=""){
                    $jumpUp = $field->jumpUp;
                }else{
                    $jumpUp = 0;
                }
                $vaddrlastrow = fetchAddressLastRow($st_row,$field->stopIdentifierRegex,$rows,$field->Regex[0],$jumpUp);
                $st_row = $st_row - 1;
                $vaddr="";
                //print"<br>strt=$st_row<br>end=$vaddrlastrow<br>";
                for($i=$st_row;$i<$vaddrlastrow;$i++){
                  //  print"<br>regex[0]=<br>";
                    //print_r($field->Regex[0]);
                    //print"<br>";
                    if(preg_match($field->Regex[0],$rows[$i],$matches)){
                    //print"<br>addr<br>"; print_r($matches); print"<br>";
                    //$vaddr = $vaddr." ".trim($matches[2]); //store address
                        if(isset($field->regexPosition) && trim($field->regexPosition) !="" && trim($field->regexPosition) > 0){
                           //print"<br> in regexPosition<br>";
                            $vaddr = $vaddr." ".trim($matches[$field->regexPosition]);   
                        }else{
                           // print"<br> in regexPosition else ABRL case<br>";
                          $vaddr = $vaddr." ".trim($matches[2]); //store address for ABRL case
                        }
                    }
                }
               // print"<br>address=$vaddr<br>";
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
        } else if(is_array($field->PregMatch)){
          //  print_r($field->PregMatch);
          //  echo "Line: ".$rows."<br>";
             for($i=0;$i<count($rows);$i++){
            //     echo "$i Line: $rows[$i]<br>";
                 if(preg_match($field->PregMatch[0],$rows[$i],$mtch)){
                     $header[$field->Name] = $mtch[1];
                 }
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
  $header=(object)$header;
    // Delivery Date / VAT_Tin in footer
   if(isset($ini->Footer->Vat_Tin)){
      // print"in footer VATTIN:<br>";
       $startRow = (int)$ini->Items->StartRow-1;
       // print"sr=$startRow<br>";
        
        $lastItemRow=getLastItemrow($rows, $ini,$startRow);
        
        $polastrow=count($rows);
        //print"<br>polastrow=$polastrow<br>";
        
        $vattin_regex=$ini->Footer->Vat_Tin->Regex[0];
        //print"<br>Regex:$vattin_regex<br>";
        
        $vattin="";
//        $vattinrow=$rows[$lastItemRow];
//        print"$vattinrow<br>";
        for($r=$lastItemRow; $r<=$polastrow; $r++){
            if (preg_match($vattin_regex, $rows[$r], $matches)){ 
          //      print_r($matches);
                    $vattin = $matches[1]; 
                    break;
            }
        }
        $header->Vat_Tin=$vattin;
            //print"<br>HDVT: $header->Vat_Tin<br>";
        
   }
   if (isset($ini->Footer->Delivery_Date)) {
        $startRow = (int)$ini->Items->StartRow-1;
       // print"sr=$startRow<br>";
        
        $lastItemRow=getLastItemrow($rows, $ini,$startRow);
       // print"<br>lr=$lastItemRow<br>";      
        $polastrow=count($rows);
      //  print"<br>polastrow=$polastrow<br>";
        
        $del_date_regex=$ini->Footer->Delivery_Date->Regex[0];
        $expiry_date_regex=$ini->Footer->Expiry_Date->Regex[0];
       // print"<br>Regex:$del_date_regex<br>";        
//        $jump=0;
//        if (isset($ini->Footer->Delivery_Date->jump)) {
//             $jump=$ini->Footer->Delivery_Date->jump;
//        }
//        
//        $del_daterow_no=$lastItemRow+$jump;
       for($r=$lastItemRow; $r<=$polastrow; $r++){
           if(preg_match($del_date_regex, $rows[$r])){
               $del_daterow=$rows[$r];
               break;
           }          
       }
       for($r=$lastItemRow; $r<=$polastrow; $r++){
           if(preg_match($expiry_date_regex, $rows[$r])){
               $exp_daterow=$rows[$r];
               break;
           }          
       }
        //$del_daterow=$rows[$del_daterow_no];
        //print"<br>Delivery_date row:$del_daterow<br>";
        //print"<br>Expiry_date row:$exp_daterow<br>";
       
        
       
        $date="";
        $date1="";
        if (preg_match($del_date_regex, $del_daterow , $matches)){ 
                $date = $matches[1]; 
        }
        //print"<br>Deliverydate:$date<br>";
        
        if (preg_match($expiry_date_regex, $exp_daterow , $matches)){ 
                $date1 = $matches[1]; 
        }
        //print"<br>Expirydate:$date1<br>";
        
      
        if (isset($ini->Footer->Delivery_Date->format)) {
          //  print"<br>set format<br>";
		$header->Delivery_DateFormat = $ini->Footer->Delivery_Date->format;
        }
        
         if (isset($ini->Footer->Expiry_Date->format)) {
            //print"<br>set format<br>";
		$header->Expiry_DateFormat = $ini->Footer->Expiry_Date->format;
        }
       
            $header->Delivery_Date=$date;
            $header->Expiry_Date=$date1;
            // print"<br>HD:$header->Delivery_Date<br>";
            //print"<br>HD:$header->Expiry_Date<br>";
   }
 //  print_r($header);
return (object)$header;
}

function getItems($rows, $ini,$master_dealer_id) {   
    
   // print_r($ini);
    $startRow = 0;
    if($master_dealer_id == 3 || $master_dealer_id == 4){//temp if for ABRL
    $startRow = (int)$ini->Items->StartRow-3;
    
    }else{
    $startRow = (int)$ini->Items->StartRow-2;
    }
    //print"sr=$startRow<br>";
    $lastItemRow=getLastItemrow($rows, $ini,$startRow);
    //print"lr=$lastItemRow";
    
    $items = array();
    //$foundFooter=false;
    //print"strt1=$startRow<br>end1=$lastItemRow";
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
    if(trim($ini->Items->RowsPerItem)==-1){ // appay all regerx to all rows if itemline spencer/Reliance
      //  print"<br>in -1 case --------------if<br>";
        $PER = new eachRowRegex();
        $items = $PER->fetchItems($startRow,$lastItemRow,$rows,$ini,$master_dealer_id);
      //  print"<br>return array items=<br> ";
     //   print_r($items);
    }
    if(trim($ini->Items->RowsPerItem)==-2){ // word wrap items case ABRL
        $ww = new wordWrapItemsCase();
        $items = $ww->fetchItems($startRow,$lastItemRow,$rows,$ini);
    }
    if(trim($ini->Items->RowsPerItem)==-3){ //  Max_Hypermarket_Case Item in 3/4 lines with * in item line or above itemline
        $MH = new Max_Hypermarket_Case();
        $items = $MH->fetchItems($startRow,$lastItemRow,$rows,$ini);
    }  
    if(trim($ini->Items->RowsPerItem)==-4){ // appay all regerx to all rows ABRL
        //print"<br>in mixABRL case --------------if<br>";
        $MCA = new mixcaseABRL();
        $items = $MCA->fetchItems($startRow,$lastItemRow,$rows,$ini);
//        print"<br>return array items=<br> ";
//        print_r($items);
        //print"<br>";
    }
    if(trim($ini->Items->RowsPerItem)==-5){ // itemname n tax positions changes line wise optionally ABRL
        //print"<br>in mixABRL case2 itemname n tax comes in upper or same line --------------if<br>";
        $MCA2 = new mixcase2ABRL();
        $items = $MCA2->fetchItemsAB($startRow,$lastItemRow,$rows,$ini);
//        print"<br>return array items=<br> ";
//        print_r($items);
    //    print"<br>";
    }
    if(trim($ini->Items->RowsPerItem)==-6){ // overlapping case ABRL
      //  print"<br>in overlapping Case tax value comes overlapped --------------if<br>";
        $OVC = new overlappingCase();
        $items = $OVC->fetchItemsOVC($startRow,$lastItemRow,$rows,$ini);
//        print"<br>return array items=<br> ";
//        print_r($items);
        //print"<br>";
    }
    else {
           for ($i=$startRow; $i<$lastItemRow; $i++) { 
           //    echo "Line will be: $rows[$i]<br>";
            if(trim($ini->Items->RowsPerItem)==1){    
             $keywords=array();
             $iarray = array();
             echo $ini->Items->Regex."<br>";
            if (preg_match($ini->Items->Regex, $rows[$i], $fields)) {
//               print"in if";
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
                 
          //      print "<br><br><br><br>BEFORE IF CONDN FIELDS ARR: <br>";
              //  print_r($fields);
            //    print "<br><br>";
                
               if(!empty($fields)){
              //   print "<br>in not empty fields";
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
                   
                 /*  $fields['Iname']="";
                   if($master_dealer_id==2){ //Future retails- fetch PO item description
                       $current_line = $i;
                         $current_line++;
                         $line = $rows[$current_line];
                         if(preg_match('/(\S+)\s+(\d+)?\s+(\d\S*)\s+(\d\S*)/',$line,$mtch)){
                             $artno = trim($mtch[1]);
                             $fields['ArticleNo'] = trim($artno);
                         }
                         $current_line++;
                         $iname = "";
                         $flag=0;
                         for ($i=$current_line; $i<$lastItemRow; $i++) {
                             $linetext = $rows[$i];
                          //   $regex = $ini->Items->Regex[0]; 
                             if (preg_match($ini->Items->Regex, $rows[$i], $fields1));
                             if(!empty($fields1)){   
                            // if(!empty($fields1)){
                                $Itemname = trim($iname);
                                $Itemname = str_replace("\n"," ",$Itemname);
                                 if(preg_match('/(.*)\s+Page\s*\d+\s*of\s*\d+/',$Itemname,$mtch123)){
                                    $Itemname = trim($mtch123[1]);
                                }
                                if(preg_match('/(.*)\s+Terms\s+\&\s+Conditions/',$Itemname,$mtch123)){
                                    $Itemname = trim($mtch123[1]);
                                }
                                 if(preg_match('/(.*)\s+Total/',$Itemname,$mtch123)){
                                    $Itemname = trim($mtch123[1]);
                                }
                                $fields['Itemname'] = trim($Itemname);
                                 $i--;  
                                 $iname="";
                                 break; 
                             }else if(preg_match('/Total/',$linetext)==1){
                                                             
                             }else{
                                  $iname .= " ".$linetext;
                             }
//                             echo "INAME: $iname<br>";
                             if(preg_match('/Total/',$rows[$i])==1){
//                                 echo "<br>Inside total clause<br>";
                               //  $current_line++;
                               //  $current_line = $i;
                                 $Itemname .= " ".$iname; 
                               //  $Itemname = $Itemname;
//                                 echo "ianme actually: $iname<br>";
//                                  echo "INAME: $Itemname<br>";
                                  if(preg_match('/(.*)\s+Page\s*\d+\s*of\s*\d+/',$Itemname,$reg1)){
                                    $Itemname = trim($reg1[1]);
                                }
                                if(preg_match('/(.*)\s+Terms\s+\&\s+Conditions/',$Itemname,$reg2)){
                                    $Itemname = trim($reg2[1]);
                                }
                                 if(preg_match('/(.*)\s+Total/',$Itemname,$reg3)){
                                    $Itemname = trim($reg3[1]);
                                }
                                 
                               $fields['Iname']="";
//                                 print_r($mtch12);
                                for ($k=$i; $k<$lastItemRow; $k++) {
                                  $line = $rows[$k];
                                  
//                                  $Itemname ="";
                                    //echo "Current line: ".$k."<br>";
                                  // echo "Line is: $line<br>";
                                    if(preg_match('/Description\s+of/',$line)==1){
                                     // continue;
                                       echo "Inside desc of";
                                       $current_line = $k;                                        
                                        $current_line++;
                                        $current_line++;
                                        for ($l=$current_line; $l<$lastItemRow; $l++) {
                                             $line = $rows[$l];
                                    //    echo "Line *********$l<>$line<br>";
                                        if (preg_match($ini->Items->Regex, $line, $fields12));
                                        if(empty($fields12)){
                                            $Itemname .= " ".trim($line);
                                            
//                                            echo "Line *********$current_line<>$line<br>";
                                        }else{
                                            $Itemname = str_replace("\n"," ",trim($Itemname));
                                            echo "Complete item name:". $Itemname."<br>";
                                            echo "<br><br><br>";
                                            if(preg_match('/(.*)\s+Page\s*\d+\s*of\s*\d+/',$Itemname,$reg1)){
                                                print_r($reg1);echo "<br><br><br>";
                                                $Itemname = trim($reg1[1]);
                                            }                                           
                                            if(preg_match('/(.*)\s+Terms\s+\&\s+Conditions/',$Itemname,$reg2)){
                                                print_r($reg2);echo "<br><br><br>";
                                                $Itemname = trim($reg2[1]);
                                            }
                                             if(preg_match('/(.*)\s+Total/',$Itemname,$reg3)){
                                                 print_r($reg3);echo "<br><br><br>";
                                                $Itemname = trim($reg3[1]);
                                            }
                                            $fields['Iname'] = str_replace("\n"," ",trim($Itemname));
                                          //  $fields['Itemname'] = trim($Itemname);
                                             //$Itemname .= trim($iname);
                                           //  $Itemname = str_replace("\n"," ",$Itemname);
                                          //     $Itemname = $Itemname;
                                          //  echo $fields['Iname'];
                                             $flag=1;
                                            --$l;  
                                            $current_line = $l;
                                            $iname="";
                                            break;  
                                        }
                                        }    
                                        //echo "Complete item name111: $Itemname<br>";
                                    } 
                                    if($flag==1){
                                        break;
                                    }
                                    //break;
                               }
                               
                               // echo "Complete item name111111: $Itemname<br>";
                             }
                           //  echo "Complete item name11111177777: $Itemname<br>";
                     }
                      if(preg_match('/(.*)\s+Page\s*\d+\s*of\s*\d+/',$Itemname,$reg1)){
                        $Itemname = trim($reg1[1]);
                    }
                    if(preg_match('/(.*)\s+Terms\s+\&\s+Conditions/',$Itemname,$reg2)){
                        $Itemname = trim($reg2[1]);
                    }
                     if(preg_match('/(.*)\s+Total/',$Itemname,$reg3)){
                        $Itemname = trim($reg3[1]);
                    }
                     if(trim($fields['Iname'])!=""){
                  * 
                  * 
                         $fields['Itemname'] = trim($fields['Iname']);
                     }
                   
                   //  echo "Complete item name11111199999999999999: $Itemname<br>";
                   }*/
                   
                    
                   if($master_dealer_id==2 || $master_dealer_id==30 || $master_dealer_id==40){ //Future retails- fetch PO item description
                       $fields['Iname']="";
                   $fields['ArticleNo'] = "";
                       $current_line = $i;
                         $current_line++;
                         $line = $rows[$current_line];
//                         echo $line."<br>";
                         if(preg_match('/(\d+)\s+(\d+)?\s+(\d\S*)\s*(\d\S*)?/',$line,$mtch) && preg_match('/Total/',$line)!=1){
                             $artno = trim($mtch[1]);
                             $fields['ArticleNo'] = trim($artno);
                         }
                         if(trim($fields['ArticleNo'])==""){
                //             echo "Inside special case<br>";
//                             callFunction($i,$lastItemRow);
                             $flag12=0;
                              $current_line = $i;                                        
                                        $current_line++;
                                        $current_line++;
                             for ($k=$current_line; $k<$lastItemRow; $k++) {
                                  $line = $rows[$k];
                    //              echo "$line<br>";
                                    if(preg_match('/Description\s+of/',$line)==1){
                                    //  continue;
                  //                     echo "Inside desc of1";
                                       $current_line = $k;                                        
                                        $current_line++;
                                        $current_line++;
                                        for ($l=$current_line; $l<$lastItemRow; $l++) {
                                             $line = $rows[$l];
                      //                  echo "Line *********$l<>$line<><br><br>";
                                        if(preg_match('/(\d+)\s+(\d+)?\s+(\d\S*)\s+(\d\S*)/',$line,$mtch) && preg_match('/Total/',$line)!=1 && trim($fields['ArticleNo'])==""){
                                            $artno = trim($mtch[1]);
                                            $fields['ArticleNo'] = trim($artno);
                                        }else if (preg_match($ini->Items->Regex, $line)!=1){
                                         //   $fields['Itemname'] .= " ".trim($Itemname);
                                            $line = str_replace("\n","",$line);
                                            $Itemname .= " ".$line;
                                        }else{
                                            $flag12=1;
                                           // $i--;
                                            break;
                                        }
                                        
                                        }  
                                        break;
                                        //echo "Complete item name111: $Itemname<br>";
                                    } 
                                    if($flag12==1){
                                        break;
                                    }                                    
                               }
                               $fields['Itemname'] = trim($Itemname);
                         }
                         
                        else{
                         $current_line++;
                         $iname = "";
                         $flag=0;
                         for ($i=$current_line; $i<$lastItemRow; $i++) {
                             $linetext = $rows[$i];
                          //   $regex = $ini->Items->Regex[0]; 
                             if (preg_match($ini->Items->Regex, $rows[$i], $fields1));
                             if(!empty($fields1)){   
                            // if(!empty($fields1)){
                                $Itemname = trim($iname);
                                $Itemname = str_replace("\n"," ",$Itemname);
                                 if(preg_match('/(.*)\s+Page\s*\d+\s*of\s*\d+/',$Itemname,$mtch123)){
                                    $Itemname = trim($mtch123[1]);
                                }
                                if(preg_match('/(.*)\s+Terms\s+\&\s+Conditions/',$Itemname,$mtch123)){
                                    $Itemname = trim($mtch123[1]);
                                }
                                 if(preg_match('/(.*)\s+Total/',$Itemname,$mtch123)){
                                    $Itemname = trim($mtch123[1]);
                                }
                                $fields['Itemname'] = trim($Itemname);
                                 $i--;  
                                 $iname="";
                                 break; 
                             }else if(preg_match('/Total/',$linetext)==1){
                             }else if(preg_match('/Article\s+DUOM/',$linetext)==1){
                                    $fields['Itemname'] = trim($Itemname);
                                    break;
                             }else{
                                  $iname .= " ".$linetext;
                             }
//                             echo "INAME: $iname<br>";
                             if(preg_match('/Total/',$rows[$i])==1){
                        //         echo "<br>Inside total clause<br>";
                               //  $current_line++;
                               //  $current_line = $i;
                                 $Itemname .= " ".$iname; 
                               //  $Itemname = $Itemname;
                               //  echo "ianme actually: $iname<br>";
                          //        echo "INAME: $Itemname<br>";
                                  if(preg_match('/(.*)\s+Page\s*\d+\s*of\s*\d+/',$Itemname,$reg1)){
                                    $Itemname = trim($reg1[1]);
                                }
                                if(preg_match('/(.*)\s+Terms\s+\&\s+Conditions/',$Itemname,$reg2)){
                                    $Itemname = trim($reg2[1]);
                                }
                                 if(preg_match('/(.*)\s+Total/',$Itemname,$reg3)){
                                    $Itemname = trim($reg3[1]);
                                }
                                 
                               $fields['Iname']="";
                             //  echo $Itemname;
//                                 print_r($mtch12);
                                for ($k=$i; $k<$lastItemRow; $k++) {
                                  $line = $rows[$k];
                                  
//                                  $Itemname ="";
                                    //echo "Current line: ".$k."<br>";
                                  // echo "Line is: $line<br>";
                                    if(preg_match('/Description\s+of/',$line)==1){
                                     // continue;
                                    //   echo "Inside desc of";
                                       $current_line = $k;                                        
                                        $current_line++;
                                        $current_line++;
                                        for ($l=$current_line; $l<$lastItemRow; $l++) {
                                             $line = $rows[$l];
                                    //    echo "Line *********$l<>$line<br>";
                                        if (preg_match($ini->Items->Regex, $line, $fields12));
                                        if(empty($fields12)){
                                            $Itemname .= " ".trim($line);
                                            
//                                            echo "Line *********$current_line<>$line<br>";
                                        }else{
                                            $Itemname = str_replace("\n"," ",trim($Itemname));
                                        //    echo "Complete item name:". $Itemname."<br>";
                                        //    echo "<br><br><br>";
                                            if(preg_match('/(.*)\s+Page\s*\d+\s*of\s*\d+/',$Itemname,$reg1)){
                                               // print_r($reg1);echo "<br><br><br>";
                                                $Itemname = trim($reg1[1]);
                                            }                                           
                                            if(preg_match('/(.*)\s+Terms\s+\&\s+Conditions/',$Itemname,$reg2)){
                                              //  print_r($reg2);echo "<br><br><br>";
                                                $Itemname = trim($reg2[1]);
                                            }
                                             if(preg_match('/(.*)\s+Total/',$Itemname,$reg3)){
                                            //     print_r($reg3);echo "<br><br><br>";
                                                $Itemname = trim($reg3[1]);
                                            }
                                            $fields['Iname'] = str_replace("\n"," ",trim($Itemname));
                                          //  $fields['Itemname'] = trim($Itemname);
                                             //$Itemname .= trim($iname);
                                           //  $Itemname = str_replace("\n"," ",$Itemname);
                                          //     $Itemname = $Itemname;
                                          //  echo $fields['Iname'];
                                             $flag=1;
                                            --$l;  
                                            $current_line = $l;
                                            $iname="";
                                            break;  
                                        }
                                        }    
                                        //echo "Complete item name111: $Itemname<br>";
                                    } 
                                    if($flag==1){
                                        break;
                                    }
                                    //break;
                               }
                               
                               // echo "Complete item name111111: $Itemname<br>";
                             }
                           //  echo "Complete item name11111177777: $Itemname<br>";
                     }
                   }
                      if(preg_match('/(.*)\s+Page\s*\d+\s*of\s*\d+/',$Itemname,$reg1)){
                        $Itemname = trim($reg1[1]);
                    }
                    if(preg_match('/(.*)\s+Terms\s+\&\s+Conditions/',$Itemname,$reg2)){
                        $Itemname = trim($reg2[1]);
                    }
                     if(preg_match('/(.*)\s+Total/',$Itemname,$reg3)){
                        $Itemname = trim($reg3[1]);
                    }
                     if(trim($fields['Iname'])!=""){
                         $fields['Itemname'] = trim($fields['Iname']);
                     }
                   
                   //  echo "Complete item name11111199999999999999: $Itemname<br>";
                   }
                   
                  // print"<br>**************an=$artno<br>ean=$EAN<br>car=$CAR<br>mrp=$mrp<br>vat=$VAT<br>qty=$Qty<br>rate=$Rate<br>amt=$Amount<br>";
                   //print"<br>itemname=$Itemname<br>";
                   
                   if($artno !="" || $EAN!="" ){
                   if( $mrp!="" && $Qty!="" && $Rate!="" && $Amount!="")//&& $VAT!="" $CAR!="" &&
                   { 
                     //  print "<br> PF 1st case: $pushFlag ";
                      // print"in if";
                       if($artno!=""){
                            if(preg_match("/[0-9]/",$artno)){
                                $pregFlag=1;
                         //      print"<br> $artno true case---$pregFlag";
                            }else{
                                $pregFlag=0;
                              //  print"<br> $artno false case---$pregFlag";
                            }
                       }
                       if( $EAN!="" ){
                            if(preg_match("/[0-9a-zA-Z]/",$EAN)){
                                $pregFlag=1;
                         //      print"<br> $EAN true case---$pregFlag";
                            }else{
                                $pregFlag=0;
                          //      print"<br> $EAN false case---$pregFlag";
                            }
                       }
                       if($CAR!=""){
                            if(preg_match("/[0-9a-zA-Z]/",$CAR)){
                                $pregFlag=1;
                       //         print"<br> $CAR true case---$pregFlag";
                            }else{
                                $pregFlag=0;
                           //     print"<br> $CAR false case---$pregFlag";
                            }
                       }
                       
                       if((is_double($mrp))&&(is_double($Qty))&&(is_double($Rate))&&(is_double($Amount))){ //&&(is_double($VAT))
                           $pushFlag=1;
                        //   print"<br> Push Flag true case---$pushFlag";
                       }else{
                           
                          $pushFlag=0;
                         // print"<br> Push Flag false case---$pushFlag";
                        }
                       
                   }
                   }else{
                       $pushFlag=0;
                   }
                 // print"<br>pushflag=$pushFlag<br>pregflag=$pregFlag<br>"; 
//                  print"<br>lineno=$lineno && prod=="; 
//                   
                 // print "<br>fields arr: <br>"; 
                  // print_r($fields); print"<br>"; 
                  if($pushFlag==1)
                  {
                   //print "<br> in flag ITEM ARRAY true case: <br>";
                   $items[] = $fields; 
                   //echo "PO Item are: ";
                   //print_r($items);
                   //print "<br><br>";
                  }
               }
//	       print"<br> prod=="; print_r($items);print"<br>";
              // $itemname=trim(getFieldValue($fields,"itemname"));
              // print"<br>itemname=$itemname<br>";
            }
} 
//else if(trim($ini->Items->RowsPerItem)==-1){ // every item line check with each regex
//
//    $regex_size = sizeof($ini->Items->Regex);
//    $linetext = $rows[$i];
//    $continue_main_loop = 0;
//    for($j = 0 ; $j < $regex_size ; $j++){ // regex iteration loop
//        $regex = $ini->Items->Regex[$j]; 
//        $matches=array();
//        print"<br>Line no: $i <br>Regex at: $j <br> Regex is : $regex <br> Line text is: $linetext <br>";
//        preg_match($regex,trim($linetext),$matches);
//         print_r($matches); 
//         print"<br>";       
//         if(!empty($matches)){ // means regex matched
//             $matched_regex_position = $j;
//             print "<br>IN ESLE CASE Matched regex position : $matched_regex_position ";
//             //****************************************
//             if($matched_regex_position==0){
//                 if(! empty($item_parts)){
//                     array_push($all_items, $item_parts);
//                        print "<br>All Items array <br>";
//                       //print_r($all_items);
//                       unset($item_parts);
//                       $item_parts = array();
//                 }
//             }
//             $item_parts = array_merge($item_parts,$matches);
//             //*****************************************            
//                           
//             //global $item_parts;
//             
//             
//             print "<br>ITEM PARTS ARR IN ELSE CASE: <br>";
//             print_r($item_parts);
//             print "<br>";
//             $continue_main_loop = 1;
//             break;
//         }   
//         
//         if(trim($linetext)==""){ //blank line             
////             $continue_main_loop = 1;
////             $matched_regex_position++;
////             break;
//         }
//    }
//    
//    if($continue_main_loop == 1){ // go start of min item loop to fetch next item
//        print "<br> In continue main loop : $continue_main_loop ";
//        continue;
//    }

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
                    //print"<br> $l =>is blankline <br>";
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
               
                
//                print "<br> R: $r";
//                print "<br> L: $l";
//                print "<br> I REGEX[$r]:$l".$ini->Items->Regex[$r];
//                print "<br> ROW DATA ---$l----: ".$rows[$l]."<br><br>";
//                
//                print "<br>LINE TEXT: ".$linetext."<br>";
                $matches=array();
//                print"<br>matches in > 1 condn:<br>";
                preg_match($regex,trim($linetext),$matches);
                // print_r($matches); 
                 //print"<br>";
               //if(preg_match($regex,$linetext))
                 if(!empty($matches)){
                   //     print"<br>In if(linetxt)<br>";
                     //preg_match($regex,$rows[$l],$arrs);
//                     print "I REGEX[$r]:$l".$ini->Items->Regex[$r]."<br>";
//                     print "<br> ROW DATA: ".$rows[$l]."<br>";
                    //print "<br>ARRAY:".print_r($arrs)."<br>";
                     $fields = array_merge($fields,$matches);
                     $l=$l+1;
                //print"nextline=$l<br>";
                }
                 else{
                     //       print"<br>In else (linetxt)<br>";
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
                   if($artno!="" && $EAN!="" && $CAR!="" && $mrp!="" && $VAT!="" && $Qty!="" && $Rate!="" && $Amount!="") { 
                       //print "<br> PF 1st case: $pushFlag ";
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

function getVerifySum($rows, $ini) {
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
	$value = (float)trim(substr($value, $start, $length)); //print"val==$value<br>";
} else {
	$value = (float)trim(substr($value, $start)); // print"val==$value<br>";
}
return (object) array("value" => $value, "field" => $ini->Footer->VerifySum->field);
}


function fetchAddressLastRow($startRow,$stopIdentifierRegex,$rows,$regex,$jumpUp){
  //  print "<br>START ROW : $startRow :: stopIdentifierRegex : $stopIdentifierRegex :: Regex : $regex<br>";
    $eof = count($rows);
    //print "<br>EOF: $eof <br>";
   // $no_spaces_identifier = str_replace(" ", "", $identifier);
    //print "<br>NO SPACES IDENTIFIER: $no_spaces_identifier <br>";
    $last_row = 0;
    for($i=$startRow;$i<=$eof;$i++){
       //$linedata = $rows[$i];
        $linedata = "";
      //  print "<br>LINE $i:LINE DATA: $rows[$i] <br>";
        if(preg_match($regex,$rows[$i],$matches)){
                   // print"<br>addr<br>"; print_r($matches); print"<br>";
            $linedata = trim($matches[1]); //store address
            if(isset($matches["addr1"])){
                $linedata = trim($matches["addr1"]);
            }
         }
       // print "<br>LINE $i:LINE DATA: $linedata <br>";
        $no_spaces_line_data = str_replace(" ", "", $linedata);
     //   print "<br>NO SPCS LINE DATA: $no_spaces_line_data <br>";
       // if(strcmp(trim($no_spaces_identifier),trim($no_spaces_line_data))==0){
        $arr = array();
        if(preg_match($stopIdentifierRegex,$linedata,$arr)){
        //    print "<br>Matched<br>";
            $last_row = $i;
            $last_row = $last_row-$jumpUp;
            break;
        }else{
          //  print "<br>Not Matched<br>";
        }
    }
 //   print "<br>LR: $last_row <br>";
    return $last_row;
}
function getLastItemrow($rows, $ini,$startRow){
    $footerIdentifier=false;
    $lastItemRow=0;
    if (isset($ini->Footer->Rows)) {  //print"in1<br>";
	$footerRows = (int)$ini->Footer->Rows;
	$lastItemRow = count($rows) - $footerRows;
        return $lastItemRow;
    } else if (isset($ini->Footer->Identifier)) {  //print"in2<br>";
	$footerIdentifier = trim($ini->Footer->Identifier->value);
       // print"FI---------$footerIdentifier\n";
	$lastRow = count($rows);
        //print count($rows);
        //print "<br>START ROW = $startRow ,PO END ROW: $lastRow";
        $foundFooter=false;
        for ($i=$startRow; $i<$lastRow; $i++) { 
       	if ($foundFooter == false) {
          //  print"<br>row=$rows[$i]";
		$rowIdentifier = trim($rows[$i]);
                      	if (isset($ini->Footer->Identifier->start)) {
			$start = (int) $ini->Footer->Identifier->start;
//                        echo "START Debug: $start<br>";
                        if (isset($ini->Footer->Identifier->length)) {
				$length = (int) $ini->Footer->Identifier->length;
				$rowIdentifier = substr($rowIdentifier,$start,$length);
			} else {
				$rowIdentifier = substr($rowIdentifier,$start);
			}
		}
//              print "<br> RI: $rowIdentifier <br>FI: $footerIdentifier  ";
               $sval=strcmp(trim($rowIdentifier),trim($footerIdentifier));
               //print"<br>sval=$sval<br>";
		//if ((strcmp($rowIdentifier,$footerIdentifier)==0) ){
                 if($sval==0){
                    //print"<br>rowno=$i";
                    $r=count($rows);
                   // print"<br>no of rows=$r";
                   	$ini->Footer->Rows = count($rows) - $i;
                      //  print"<br>footer row<br>";print $ini->Footer->Rows;
			$foundFooter=true;
                        $lastItemRow=$i;
          //              print"lr=$lastItemRow";
			break;
		}
	}
        }
         return $lastItemRow;
    } else {//print"in3<br>";
	return false;
    }
}


function itemchk($items){
    
    $vat_skip_chainid=array(14);
    
    $noofitems=count($items);
    //print"<br>no of items in PO=$noofitems<br>";

    if($noofitems==0){
         //$ret =  POStatus::STATUS_ISSUE_AT_PROCESSING."::-1";
        $ret =  POStatus::STATUS_ISSUE_AT_PROCESSING."::-1::".IssueReason::ITEMARREMPTY;
         return $ret;
    }else{
      //  print"<br>Check items in array";
//                print_r($items);
//                print"<br>";
        foreach($items as $item){
            $Qty = str_replace(",","",trim(getFieldValue($item,"Qty")));
            $MRP = str_replace(",","",trim(getFieldValue($item,"MRP")));
            //$VAT = str_replace(",","",trim(getFieldValue($item,"VAT")));
            $Rate = str_replace(",","",trim(getFieldValue($item,"Rate")));
            $Amount = str_replace(",","",trim(getFieldValue($item,"Amount")));      

            if(is_numeric("$Qty")  && is_numeric("$Rate") && is_numeric("$MRP") && is_numeric("$Amount")){    //&& is_numeric("$VAT")                   
                if($master_dealer_id != DEF_METRO){
                    $VAT = str_replace(",","",trim(getFieldValue($item,"VAT")));
                    //if($item['VAT']<= DEF_TAX && $item['VAT'] >= 0 ){
                     if($VAT<= DEF_TAX && $VAT >= 0 && is_numeric("$VAT")){
          //              print"<br>TAX :".$item['VAT']."<=".DEF_TAX;
                    }
                    else{
        //                print"<br>invalid TAX percent<br>";
                        //$ret =  POStatus::STATUS_ISSUE_AT_PROCESSING."::-1";
                        $ret =  POStatus::STATUS_ISSUE_AT_PROCESSING."::-1::".IssueReason::INVALID_TAX;
                        return $ret;
                    }
                }

                $poAmount= str_replace(",", "", $item['Amount']);
                $cal_amt=$item['Qty']*$item['Rate'];

                if($master_dealer_id == 14){
                    if(!( trim($item['Qty']) > 0 && trim($item['MRP']) > 0 && trim($item['MRP']) <= 500 && trim($item['Rate']) > 0 && trim($item['Rate']) <= 500)){
            //            print" in Qty,MRP or Rate out of range check<br>";
                        //$ret =  POStatus::STATUS_ISSUE_AT_PROCESSING."::-1";
                        $ret =  POStatus::STATUS_ISSUE_AT_PROCESSING."::-1::".IssueReason::INVALID_MRPQTY;
                        return $ret;
                    }
                } 
              //  print"<br>calculations on processpotodb <br>Qty:".$item['Qty']."<br>Rate:".$item['Rate']."<br>calamt:".$cal_amt."<br>poAmount:".$poAmount."<br>";
                //print"<br>MD_id=$master_dealer_id<br>";
//                        if(trim($master_dealer_id) != 13){    
                  //  print"<br>chk po amt on processpotodb page";
                    if($poAmount >= $cal_amt){
                    //    print"<br>valid amount<br>";
                        continue;
                    }
                    else{
                        $diff_amt = $cal_amt - $poAmount;
                        $diff_per = ($diff_amt / $cal_amt) * 100 ;
                      //  print"<br>diff_per:".$diff_per."<br>diff_amt:".$diff_amt."<br>";
                        if($diff_per <= DEF_TAX){
                            continue;
                        }
                        else{
                        //    print"<br>invalid Amount<br>";
                            //$ret =  POStatus::STATUS_ISSUE_AT_PROCESSING."::-1";
                            $ret =  POStatus::STATUS_ISSUE_AT_PROCESSING."::-1::".IssueReason::INVALID_AMT;
                            return $ret;
                        }
                    }
//                        }
            }
            else{
            //    print_r($item);
               // print"<br>";
                //print"<br>invalid Item Values<br>";
                //$ret =  POStatus::STATUS_ISSUE_AT_PROCESSING."::-1";
                $ret =  POStatus::STATUS_ISSUE_AT_PROCESSING."::-1::".IssueReason::INVALID_ITEM;
                return $ret;
            }
        }
    }
}
//function validate_item($item_arr,$master_dealer_id){
//  print"<br> in validate item ";
//  $rate_skip_chainid=array(7);
//  $vat_skip_chainid=array(14);
//  $amt_skip_chainid=array(14);
//  $ean_skip_chainid=array(14,13,6);
//    if(!empty($item_arr)){
//         print "<br>in not empty fields--------------";
//                
//        $pushFlag=0;
//        $pregFlag=0;
//        $artno=(trim(getFieldValue($item_arr,"ArticleNo")));
//        $EAN=trim(getFieldValue($item_arr,"EAN"));
//        $Itemname=trim(getFieldValue($item_arr,"Itemname"));
//        $CAR=trim(getFieldValue($item_arr,"CAR"));
//        $mrp=doubleval(trim(getFieldValue($item_arr,"MRP")));
////        $VAT=doubleval(trim(getFieldValue($item_arr,"VAT")));
////        $Qty=doubleval(trim(getFieldValue($item_arr,"Qty")));
////        $Rate=doubleval(trim(getFieldValue($item_arr,"Rate")));
////        $Amount=doubleval(trim(getFieldValue($item_arr,"Amount")));
//        
//        $vat=trim(getFieldValue($item_arr,"VAT"));
//        $Qty=doubleval(trim(getFieldValue($item_arr,"Qty")));
//        $bRate=trim(getFieldValue($item_arr,"Rate"));
//        $amount=trim(getFieldValue($item_arr,"Amount"));
//        if($bRate!=""){
//            print"<br>rate:";
//            $Rate=doubleval($bRate);
//        }
//        if($amount!=""){
//            print"<br>Amount:";
//            $Amount=doubleval($amount);
//        }
//         if($vat!=""){
//            print"<br>vat:";
//            $VAT=doubleval($vat);
//        }
//        print"<br>masterdealerid=$master_dealer_id<br>";
//        
//        $chk_EAN= '&& $EAN!=""';
//        $chk_rate= '&& $Rate!=""';
//        $chk_vat= '&& $VAT!=""';
//        $chk_amt= '&& $Amount!=""';
//        $dbl_rate='&&(is_double($Rate))';
//        $dbl_vat='&&(is_double($VAT))';
//        $dbl_amt='&&(is_double($Amount))';
//        
//        foreach($ean_skip_chainid as $id){
//            if($master_dealer_id == $id){
//                $chk_EAN='';
//            }
//        }
//        foreach($rate_skip_chainid as $id){
//            if($master_dealer_id == $id){
//                $chk_rate='';
//                $dbl_rate='';
//            }
//        }
//        foreach($vat_skip_chainid as $id){
//            if($master_dealer_id == $id){
//                $chk_vat='';
//                $dbl_vat='';
//            }
//        }
//        foreach($amt_skip_chainid as $id){
//            if($master_dealer_id == $id){
//                $chk_amt='';
//                $dbl_amt='';
//            }
//        }
//        
//        
//        if($artno!=""  && $CAR!="" && $mrp!="" && $Qty!="" .$chk_EAN .$chk_rate .$chk_vat .$chk_amt ){
//            //if($chk_empty)
//            if(preg_match("/[0-9]/",$artno)){
//                $pregFlag=1;
//               //print"<br> $artno true case---$pregFlag";
////                $fields['ArticleNo'] = $artno;
//            }else{
//                $pregFlag=0;
//               // print"<br> $artno false case---$pregFlag";
//            }
//
//            if(preg_match("/[0-9a-zA-Z]/",$EAN)){
//                $pregFlag=1;
//               //print"<br> $EAN true case---$pregFlag";
////                $fields['EAN'] = $EAN;
//            }else{
//                $pregFlag=0;
//               // print"<br> $EAN false case---$pregFlag";
//            }
//
//            if(preg_match("/[0-9a-zA-Z]/",$CAR)){
//                $pregFlag=1;
//                //print"<br> $CAR true case---$pregFlag";
////                $fields['CAR'] = $CAR;
//            }else{
//                $pregFlag=0;
//               // print"<br> $CAR false case---$pregFlag";
//            }
//
//            if((is_double($mrp))&&(is_double($Qty))&&(trim($pregFlag)==1).$dbl_rate .$dbl_vat .$dbl_amt){
//                $pushFlag=1;
//              //  print"<br> Push Flag true case---$pushFlag";
//            }else{
//
//               $pushFlag=0;
//              // print"<br> Push Flag false case---$pushFlag";
//             }
//             
//            if($pushFlag==1){
////                   print "<br> in flag ITEM ARRAY true case: <br>";
//                   //$item[] = $item_arr; 
////                   print_r($items);
////                   print "<br><br>";
//                return 1;
//            }else{
//                return 0;
//            }
//       } 
//       
//    }
//    //return($fields); 
//}
?>
