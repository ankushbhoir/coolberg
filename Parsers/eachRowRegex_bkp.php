<?php
require_once("../../it_config.php");
require_once "SaveToDB.php";
require_once "lib/db/DBConn.php";
require_once "checkAmt.php";

class eachRowRegex{
    
    public function _construct(){
        
    }
    public function fetchItems($startRow,$lastItemRow,$rows,$ini,$master_dealer_id){
       print"<br> in eachrowregex class<br>";
       print "<br>START ROW : $startRow :: Last Row: $lastItemRow<br> ROWS => "; 
       print_r($rows);
       print "<br>INI below: <br>";
       print_r($ini);
       $item_parts = array();
       $all_items=array();
       $items = array();
       $nooffields_arr = array('5'=>11); 
       for ($i=$startRow; $i<$lastItemRow; $i++) {
            $regex_size = sizeof($ini->Items->Regex);
            $linetext = $rows[$i];
            $continue_main_loop = 0;
            //$matchregexarr=array();
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
//                ***********code starts********** not in use 
//                              if($master_dealer_id != 13 || $master_dealer_id != 14){
//                                 $check_amt= new checkAmt();
//                                 $response = $check_amt->chkValue($item_parts);
//                                 if($response == 1){
//                                     array_push($all_items, $item_parts);
//                                 } 
//                                 else{
//                                      $ret =  POStatus::STATUS_ISSUE_AT_PROCESSING."::-1";
//                                      return $ret;
//                                 }                          
//                             }else{
//                                 array_push($all_items, $item_parts);
//                             }
//                ***********code ends**********
                               array_push($all_items, $item_parts); 
                             
                               print "<br>All Items array <br>";
                               print_r($all_items);
                               unset($item_parts);
                               $item_parts = array();
                         }
                     }
//                     merge only when all fields are not in array
                     print"<br>count=".count($item_parts);
                     print"<br>fields=".$nooffields_arr[$master_dealer_id];
//                     if(count($item_parts) <= $nooffields_arr[$master_dealer_id] && $item_parts != null){
//                          print"<br> merge item parts<br>";
//                            if(isset($matches['Srno'])){
//                                $item_parts = array_push($item_parts,$matches['Srno']);
//                            }
//                            if(isset($matches['ArticleNo'])){
//                                //$item_parts = array_merge($item_parts['ArticleNo'],$matches['ArticleNo']);
//                                $item_parts = array_push($item_parts,$matches['ArticleNo']);
//                            }
//                            if(isset($matches['Itemname'])){
//                                //$item_parts = array_merge($item_parts,$matches['Itemname']);
//                                $item_parts = array_push($item_parts,$matches['Itemname']);
//                                
//                            }
//                            if(isset($matches['Qty'])){
//                                //$item_parts = array_merge($item_parts,$matches['Qty']);
//                                $item_parts = array_push($item_parts,$matches['Qty']);
//                            }
//                            if(isset($matches['CAR'])){
//                                //$item_parts = array_merge($item_parts,$matches['CAR']);
//                                $item_parts = array_push($item_parts,$matches['CAR']);
//                            }
//                            if(isset($matches['MRP2'])){
//                                //$item_parts = array_merge($item_parts,$matches['MRP2']);
//                                $item_parts = array_push($item_parts,$matches['MRP2']);
//                            }
//                            if(isset($matches['Rate'])){
//                                //$item_parts = array_merge($item_parts,$matches['Rate']);
//                                $item_parts = array_push($item_parts,$matches['Rate']);
//                            }
//                            if(isset($matches['VAT'])){
//                                //$item_parts = array_merge($item_parts,$matches['VAT']);
//                                $item_parts = array_push($item_parts,$matches['VAT']);
//                            }
//                            if(isset($matches['Amount'])){
//                                //$item_parts = array_merge($item_parts,$matches['Amount']);
//                                $item_parts = array_push($item_parts,$matches['Amount']);
//                            }
//                            if(isset($matches['TQty'])){
//                                //$item_parts = array_merge($item_parts,$matches['TQty']);
//                                $item_parts = array_push($item_parts,$matches['TQty']);
//                            }
//                            if(isset($matches['MRP'])){
//                                //$item_parts = array_merge($item_parts,$matches['MRP']);  
//                                $item_parts = array_push($item_parts,$matches['MRP']);
//                            }
//                          
//                           print_r($item_parts);
//                     }

                         
                    $item_parts = array_merge($item_parts,$matches);
                     //*****************************************            

                     //global $item_parts;
                     if(!empty($item_parts)){
//                ***********new code starts**********
                         
                    if($master_dealer_id != 13 && $master_dealer_id != DEF_METRO){
                   // if($master_dealer_id != DEF_METRO){    
                        print"<br> inside  if to call check amount <br>";
                        $Qty=doubleval(trim(getFieldValue($item_parts,"Qty")));
                        $Rate = str_replace(",","",trim(getFieldValue($item_parts,"Rate")));
                        $Amount = str_replace(",","",trim(getFieldValue($item_parts,"Amount")));

                        if(($Qty != "" && $Qty !=0) && ($Rate != "" && $Rate !=0) && ($Amount != "" && $Amount !=0) ){
                        $check_amt= new checkAmt();
                        $response = $check_amt->chkValue($item_parts);
                            if($response == 1){
                                print"<br>valid amount<br>";
                                break;
                            } 
                            else{
                                 print"<br>invalid Amount continue with next regex<br>";
                                 continue;
                            }                          
                        }
                    }
//                ***********new code ends**********
//*******old code start***********
//                        if($master_dealer_id != 13 || $master_dealer_id != 14){    
//                         $Qty=doubleval(trim(getFieldValue($item_parts,"Qty")));
//                         $Rate = str_replace(",","",trim(getFieldValue($item_parts,"Rate")));
//                         $Amount = str_replace(",","",trim(getFieldValue($item_parts,"Amount")));
//                         if(($Qty != "" && $Qty !=0) && ($Rate != "" && $Rate !=0) && ($Amount != "" && $Amount !=0) ){
//                            $Amount= str_replace(",", "", $Amount);
//                            $Qty= str_replace(",", "", $Qty);
//                            $Rate= str_replace(",", "", $Rate);
//                            
//                            $cal_amt= $Qty*$Rate;
//
//                            print"<br>calculations<br>Qty:".$item_parts['Qty']."<br>Rate:".$item_parts['Rate']."<br>calamt:".$cal_amt."<br>poAmount:".$Amount."<br>";
//
//                            if($Amount >= $cal_amt){
//                                print"<br>valid amount<br>";
//                                break;
//                                //continue;
//                            }
//                            else{
//                                $diff_amt = $cal_amt - $Amount;
//                                $diff_per = ($diff_amt / $cal_amt) * 100 ;
//                                print"<br>diff_per:".$diff_per."<br>diff_amt:".$diff_amt."<br>";
//                                if($diff_per <= DEF_TAX){
//                                break;
//                                }
//                                else{
//                                print"<br>invalid Amount continue with next regex<br>";
////                                $ret =  POStatus::STATUS_ISSUE_AT_PROCESSING."::-1";
////                                return $ret;
//                                 continue;
//                                }
//                            }
//                         }
//                     }
//***********old code ends***********
                 }


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
            //print "<br>ITEM PARTS <br>"; 
            //print_r($item_parts);
           // $item = valid_item($item_parts,$master_dealer_id);
        }
        if(! empty($item_parts)){
            array_push($all_items, $item_parts);
            print"<br>all_items are=<br>".count($all_items);
            print_r($all_items);
            $item_res = valid_item($all_items,$master_dealer_id);
           // $items = $all_items;
            $items=$item_res;
            print_r($items);
        }
        return $items;
    }
} 
function valid_item($item,$master_dealer_id){
    print"<br> in validate item ";
    $rate_skip_chainid=array(7);
    $vat_skip_chainid=array(14);
    $amt_calculate=array(14);
    $ean_skip_chainid=array(14,13,6);
    print"<br>items are=<br>".count($item);
    //print_r($item);
    $cnt=0;
    foreach($item as $item_arr){
        print"<br>cnt=$cnt<br>";
        $cnt++;
    if(!empty($item_arr)){
         print "<br>in not empty fields--------------";
           print_r($item_arr);   
            
        $pushFlag=0;
        $pregFlag=0;
        $artno=(trim(getFieldValue($item_arr,"ArticleNo")));
        $EAN=trim(getFieldValue($item_arr,"EAN"));
        $Itemname=trim(getFieldValue($item_arr,"Itemname"));
        $CAR=trim(getFieldValue($item_arr,"CAR"));
        $mrp=(trim(getFieldValue($item_arr,"MRP")));
//        $VAT=doubleval(trim(getFieldValue($item_arr,"VAT")));
//        $Qty=doubleval(trim(getFieldValue($item_arr,"Qty")));
//        $Rate=doubleval(trim(getFieldValue($item_arr,"Rate")));
//        $Amount=doubleval(trim(getFieldValue($item_arr,"Amount")));
        
//        $VAT=trim(getFieldValue($item_arr,"VAT"));
        $Qty=doubleval(trim(getFieldValue($item_arr,"Qty")));
//        $Rate=trim(getFieldValue($item_arr,"Rate"));
//        $Amount=trim(getFieldValue($item_arr,"Amount"));
        $VAT = str_replace(",","",trim(getFieldValue($item_arr,"VAT")));
        $Rate = str_replace(",","",trim(getFieldValue($item_arr,"Rate")));
        $Amount = str_replace(",","",trim(getFieldValue($item_arr,"Amount"))); 
        
        if(! isset($item_arr['MRP'])){
            
                $mrp =  doubleval(str_replace(",","",trim(getFieldValue($item_arr,"MRP2"))));
                $item_arr['MRP']=$mrp;
            }else{
                $mrp = doubleval(str_replace(",","",trim($item_arr['MRP'])));
            }

//            if(isset($item_arr['TQty']) && trim($item_arr['TQty']) != ""){
//                $Qty = trim(getFieldValue($item_arr,"TQty"));
//            }else{
//                 $Qty = trim(getFieldValue($item_arr,"Qty"));   
//            }
        
        $chk_EAN= '&& $EAN!=""';
        $chk_rate= '&& $Rate!=""';
        $chk_vat= '&& $VAT!=""';
        //$chk_amt= '&& $Amount!=""';
//        $dbl_rate='&&(is_double($Rate))';
//        $dbl_vat='&&(is_double($VAT))';
         $dbl_rate='&&(is_numeric($Rate))';
         $dbl_vat='&&(is_numeric($VAT))';
        //$dbl_amt='&&(is_double($Amount))';
        
        print"<br>rate:$Rate";
        if(($Rate!="")){
            if(is_numeric($Rate)){
            $Rate=$Rate;
             print"<br>rate:$Rate";
            }
            else{
                 print"<br>rate issue:$Rate";
            }
        }else {
            foreach($rate_skip_chainid as $id){
                if($master_dealer_id == $id){
                    $chk_rate='';
                    $dbl_rate='';
                }
            }
        }
        
        print"<br>vat:$VAT"; 
        if($VAT!=""){
           $VAT=doubleval($VAT);    
           print"<br>vat:$VAT"; 
        }else{
            foreach($vat_skip_chainid as $id){
                if($master_dealer_id == $id){
                    $chk_vat='';
                    $dbl_vat='';
                }
            } 
        }
 
        print"<br>Amount:$Amount";
        if($Amount!=" " && $Amount!= 0){
            if(is_numeric($Amount)){
              print"<br> in amt!=empty<br>";
            //$Amount=doubleval($Amount);
            print"<br>Amount:$Amount";
            }
            else{
                 print"<br>Amountissue:$Amount";
            }
        }else{
            print"<br> in amt==empty<br>";
            foreach($amt_calculate as $id){
                if($master_dealer_id == $id){
                    print"<br>calculate:$Qty * $Rate:".doubleval($Qty*$Rate)."<br>";
                    $Amount=$Qty*$Rate;
                    if(is_numeric($Amount)){
                        $item_arr["Amount"] =  $Amount;
                        print $item_arr["Amount"];
                    }
                    else{
                         print"<br>Amount issue:$Amount";
                    }
                        
                }
            }
        }
        
        print"<br>masterdealerid=$master_dealer_id<br>";
               
        foreach($ean_skip_chainid as $id){
            if($master_dealer_id == $id){
                $chk_EAN='';
            }
        }

        print"<br>an=$artno<br>ean=$EAN<br>car=$CAR<br>mrp=$mrp<br>vat=$VAT<br>qty=$Qty<br>rate=$Rate<br>amt=$Amount<br>";
        
        if($artno!=""  && $CAR!="" && $mrp!="" && $Qty!="" && $Amount!="" .$chk_EAN .$chk_rate .$chk_vat){
            //if($chk_empty)
            print"<br>chkempty<br>";
            if(preg_match("/[0-9]/",$artno)){
                $pregFlag=1;
               print"<br> $artno true case---$pregFlag";
//                $fields['ArticleNo'] = $artno;
            }else{
                $pregFlag=0;
                print"<br> $artno false case---$pregFlag";
            }

            if(preg_match("/[0-9a-zA-Z]/",$EAN)){
                $pregFlag=1;
              print"<br> $EAN true case---$pregFlag";
//                $fields['EAN'] = $EAN;
            }else{
                $pregFlag=0;
                print"<br> $EAN false case---$pregFlag";
            }

            if(preg_match("/[0-9a-zA-Z]/",$CAR)){
                $pregFlag=1;
                print"<br> $CAR true case---$pregFlag";
//                $fields['CAR'] = $CAR;
            }else{
                $pregFlag=0;
                print"<br> $CAR false case---$pregFlag";
            }

            //if( (is_double($Qty)) &&(trim($pregFlag)==1) &&(is_numeric($Amount)).$dbl_rate .$dbl_vat){//(is_double($mrp)) &&
            print"<br>$Amount<br>";
            if( (is_numeric($Qty)) &&(trim($pregFlag)==1) &&(is_numeric($Amount)).$dbl_rate .$dbl_vat){//(is_double($mrp)) &&
                $pushFlag=1;
               print"<br> Push Flag true case---$pushFlag";
            }else{
               $pushFlag=0;
               print"<br> Push Flag false case---$pushFlag";
             }
             
            if($pushFlag==1){
                   print "<br> in flag ITEM ARRAY true case: <br>";
                  // print_r($item_arr);
                  // print"<br>----------------------------------------------------<br>";
                   $items[] = $item_arr; 
                   print_r($items);
//                 print "<br><br>";
            } else{
                print"pushflag=0<br>";
            }
        }   
    }
  }
    return($items); 
}

