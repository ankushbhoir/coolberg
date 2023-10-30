<?php
require_once("../../it_config.php");
require_once "SaveToDB.php";
require_once "lib/db/DBConn.php";

class mixcaseABRL{
    
    public function _construct(){
        
    }
    
    public function fetchItems($startRow,$lastItemRow,$rows,$ini){
       print "<br>START ROW : $startRow :: Last Row: $lastItemRow<br> ROWS => "; 
       //print_r($rows);
       exit;
       print "<br>Mixcase INI below: <br>";
       print_r($ini);
       $all_items=array();  
       $item = array();
       for ($i=$startRow; $i<$lastItemRow; $i++) { 
           $linedata = $rows[$i];
           $regular_regex = $ini->Items->Regex[0];
           $matches = array();
           if(preg_match($regular_regex, $linedata,$matches)){
               print "<br>Items<br>";
               print_r($matches);
               $valid = validateitems($matches);
               if(($valid)==1){
                   //check if ean code is present in master list
                   //step 1 : chk if regular regex provides itm description
                   //         if not then fetch
                   $EAN=trim(getFieldValue($matches,"EAN"));
                   $fetch_item_descrp = checkEANno($EAN);

                   if(trim($fetch_item_descrp)==1){
                       //step 1 : fetch itm if not found wth regular regex
                        $Itemname=trim(getFieldValue($matches,"Itemname"));
                        if(trim($Itemname)==""){
                            // call function to fetch itemname using optional regex
                            $previous_line_no = $i-1;
                            $previous_line = $rows[$previous_line_no];
                            $optional_regex = $ini->Items->Regex[1];
                            print"<br>$previous_line<br>$optional_regex<br>";
                            $itmdesp = fetchItemDesp($optional_regex,$previous_line);
                            $matches['Itemname'] = $itmdesp; 
                        }                                                                    
                  }
                  print"<br>Current Lineno=$i<br>";
                  $Amount=trim(getFieldValue($matches,"Amount"));
                  if(trim($Amount)==""){
                      // call function to fetch amount using optional regex
                      $next_lineno=$i+2;
                      $next_line=$rows[$next_lineno];
                      $amt_regex=$ini->Items->Regex[2];
                       print"<br>$next_line<br>$amt_regex<br>";
                      $amt = fetchAmount($amt_regex,$next_line);
                      $matches['Amount'] = doubleval($amt); 
                  } 
                  if($matches['VAT']==""){
                      $next_lineno=$i+2;
                      $next_line=$rows[$next_lineno];
                      $vat_regex=$ini->Items->Regex[2];
                      print"<br>$next_line<br>$amt_regex<br>";
                      $vat = getVat($vat_regex,$next_line);
                      $matches['VAT'] = doubleval($vat); 
                     //$matches['VAT'] = 0;
                  }
                  $all_items[] = $matches;
                  
                  print "<br>All Items Arr: <br>";
                  print_r($all_items);
                  
                  unset($matches);
                  $matches = array();
               }
           }
       }   
       return $all_items;
    }
    
  
} // end of class


function validateitems($item_arr){
        $item = array();
//        $fields=array();
       //fetch all details of all items
        $pushFlag=0;
        $pregFlag=0;
        $artno=(trim(getFieldValue($item_arr,"ArticleNo")));
        $EAN=trim(getFieldValue($item_arr,"EAN"));
        $Itemname=trim(getFieldValue($item_arr,"Itemname"));
        $CAR=trim(getFieldValue($item_arr,"CAR"));
        $mrp=doubleval(trim(getFieldValue($item_arr,"MRP")));
        $VAT=doubleval(str_replace("%","",trim(getFieldValue($item_arr,"VAT"))));
        $Qty=doubleval(trim(getFieldValue($item_arr,"Qty")));
        $Rate=doubleval(trim(getFieldValue($item_arr,"Rate")));
        $Amount=(trim(getFieldValue($item_arr,"Amount")));
        print"<br>amount======$Amount<br>";
       
        
       if($artno!="" && $EAN!="" && $CAR!="" && $mrp!="" && $Qty!="" && $Rate!="" ){//&& $Amount!="" && $VAT!=""
           print"<br>in non empty if <br>";
            if(preg_match("/[0-9]/",$artno)){
                $pregFlag=1;
               print"<br> $artno true case---$pregFlag";
//                $fields['ArticleNo'] = $artno;
            }else{
                $pregFlag=0;
              // print"<br> $artno false case---$pregFlag";
            }

            if(preg_match("/[0-9a-zA-Z]/",$EAN)){
                $pregFlag=1;
               //print"<br> $EAN true case---$pregFlag";
//                $fields['EAN'] = $EAN;
            }else{
                $pregFlag=0;
               // print"<br> $EAN false case---$pregFlag";
            }

            if(preg_match("/[0-9a-zA-Z]/",$CAR)){
                $pregFlag=1;
                //print"<br> $CAR true case---$pregFlag";
//                $fields['CAR'] = $CAR;
            }else{
                $pregFlag=0;
               // print"<br> $CAR false case---$pregFlag";
            }

            if((is_double($mrp))&&(is_double($Qty))&&(is_double($Rate))&& trim($pregFlag)==1){  //&&(is_double($Amount))&&(is_double($VAT))
                $pushFlag=1;
               print"<br> Push Flag true case---$pushFlag";
            }else{

               $pushFlag=0;
               print"<br> Push Flag false case---$pushFlag";
             }
             
            if($pushFlag==1){
                   print "<br> in flag ITEM ARRAY true case: <br>";
                   $items[] = $item_arr; 
                   print_r($items);
                   print "<br><br>";
                return 1;
            }else{
                return 0;
            }
       } 
       
    }
    
    
    function fetchItemDesp($optional_regex,$previous_line){
        print"<br> in fetch item desp<br>";
        $itemdescrp = "";
        $arr = array();
        if(preg_match($optional_regex,$previous_line,$arr)){
           $itemdescrp = $arr[0];
        }
        print"<br>itemdescrp=$itemdescrp";
        return $itemdescrp;
    }
    
      function fetchAmount($amt_regex,$next_line){
          print"<br> in fetch item amt<br>";
        $amt = "";
        $arr = array();
        if(preg_match($amt_regex,$next_line,$arr)){
           //print"<br>array:"; print_r($arr);
           $amt = $arr['Amount'];
        }
        print"amt=$amt";
        return $amt;
    }
    
     function getVat($vat_regex,$next_line){
          print"<br> in fetch item vat<br>";
        $vat = "";
        $arr = array();
        print"<br>regex=$vat_regex";
        print"<br>line=$next_line";
        if(preg_match($vat_regex,$next_line,$arr)){
           print"<br>array:"; print_r($arr);
           $vat = $arr['VAT'];
        }
        print"vat=$vat";
        return $vat;
    }
    
    function checkEANno($EAN){
        $db = new DBConn();
        $EAN_db = $db->safe(trim($EAN));
        $query = "select * from it_master_items where itemcode = $EAN_db "; //and is_weikfield = 1";
        print "<br>EAN CHK QRY $query <br>";
        $obj = $db->fetchObject($query);
        if(isset($obj)){
            // return 0; // no need to fetch itm descrption 
            return 1;   //logic change as all items are inserted in master_items 23/08/2016 by deepali 
        }else{
            return 1; // fetch itm descrption
        }
    }
