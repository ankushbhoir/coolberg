<?php
require_once("../../it_config.php");
require_once "SaveToDB.php";
require_once "lib/db/DBConn.php";

class Max_Hypermarket_Case{
    
    public function _construct(){
        
    }
    
    public function fetchItems($startRow,$lastItemRow,$rows,$ini){
        print"<br>Max_Hypermarket_Case->fetchItems<br>";

       // print "<br>START ROW : $startRow :: Last Row: $lastItemRow<br> ROWS => "; 
        
        $all_items=array();
        //$item = array();
        $arr=array();
        for ($i=$startRow; $i<$lastItemRow; $i++) { 
            $linedata = $rows[$i];
            $regular_regex = $ini->Items->Regex[0]; //In max case if line matches with 0th reg match then only it enters into main loop and capture other info
            $matches = array();
            $last_regex = $ini->Items->Regex[5];
            $last_regex123 = $ini->Items->Regex[0];
//            echo "Last Regex: $last_regex<br>";
            $Iname="";$Iname1="";$Iname2="";$Iname3="";$name="";$Iname4="";
//            if(preg_match($regular_regex, $linedata,$matches)){
            echo $i.$regular_regex;

            echo "<br>";
            echo $linedata;

            if(preg_match($regular_regex, $linedata,$matches)){
                print "<br>in 1st loop ITems<br>";
                echo "<pre>";
               print_r($matches);
               //exit;
                $EAN=trim(getFieldValue($matches,"EAN")); 
                $fetch_item_descrp = checkEAN_Max($EAN);
                $Iname = $matches['Itemname'];
                echo "Iname 0: $Iname<br>";
                
                //Capture Article no
                $i--;
               echo $linedata = $rows[$i];

              //  echo "Linedata: $linedata<br>";
                if(preg_match('/^[0-9 ]*$/',$linedata)==1){  //check if on this line article no present or not
                   echo "Inside fetch article no";

                     $artregex = $ini->Items->Regex[4];                                                                     
                 if(preg_match($artregex, $linedata,$array1)){                                            
                     $matches['ArticleNo'] = trim($array1['ArtcleNo']);                                                                      
                 }
                }else if(preg_match('/(\d+)\s+(\d+)\s+(SGST|CGST|GST|IGST)\s*\-\s*(\d\S*)\s*%\s*\-\s*\S+/',$linedata,$mtch)){
                    $matches['ArticleNo'] = trim($mtch[1]);
                    $matches['EAN'] = trim($mtch[2]);
                    $matches['VAT'] = trim($mtch[4]);
            }else{
                    echo $name = $linedata;
                    echo "Iname 100: $name<br>";
                    //exit;
                    $i--;
                $linedata = $rows[$i];
               // echo "Linedata: $linedata<br>";
                $Iname4="";
                if(preg_match('/^[0-9 ]*$/',$linedata)==1){  //check if on this line article no present or not
                  //  echo "Inside fetch article no";
                     $artregex = $ini->Items->Regex[4];                                                                     
                 if(preg_match($artregex, $linedata,$array1)){                                            
                     $matches['ArticleNo'] = trim($array1['ArtcleNo']);                                                                      
                 }
                 $i++;
                }  
                }
                
                
                    $i++;
                $i++;
                echo $linedata = $rows[$i];
             //   exit;
                if(trim($fetch_item_descrp)==1){                
                echo $optional_regex = $ini->Items->Regex[1];
              //  $regexx = $ini->Items->Regex[0];
                $itmdesp = fetchItemDescription_Max($optional_regex,$linedata);  
                echo "fetchItemDescription_Max<br>";
                print_r($itmdesp);
                echo "<br>----------&&&&&&&&&&&&&&&&)))))))))))))))))))))";
                
                if(!empty($itmdesp)){
                if(isset($itmdesp['Itemname']) && $itmdesp['Itemname']!=""){
                  $Iname1 = trim($itmdesp['Itemname']);
                  echo "Iname 1: $Iname1<br>";
                } 
                    $rin_text=implode("", $rows);
                     preg_match_all('/CGST/', $rin_text, $output_array);
                     print_r($output_array);
                     
                     if(!empty($output_array)){
                        echo "<pre>";

                         $matches['VAT'] = $matches['VAT'];
                      
                        $VAT=$matches['VAT'];
                      //exit;
                       $vatarr= array(0,5,12,18,28);   //IGST %
                        // echo $VAT;
                        // exit;
                                if($VAT!=""){
                                    if(! in_array($VAT, $vatarr)){

                                         $VAT=$VAT*2;  
                                       
                                        $matches['CGST'] = $matches['VAT'];
                                        $matches['SGST'] = $matches['VAT'];       
                                    }
                                    else{

                                        $matches['IGST'] = $matches['VAT']; 
                                    }


                                }
                     
                     }
                echo $matches['CGST'];
              if($itmdesp['Qty']=='')
              {
                echo "sadasdasd".$matches['Qty'];
                //print_r($matches);
                //print_r($itmdesp);
                //exit;
              }
              else{
                $matches['Qty'] = $itmdesp['Qty'];
                $matches['MRP'] = $itmdesp['MRP'];
                $matches['Rate'] = $itmdesp['Rate']; 
                 $matches['ttk_qty'] = $itmdesp['Qty']; 
             }
//                echo "QTY: ".$matches['Qty'];
//                echo "Rate: ".$matches['Rate'];
//                echo "MRP: ".$matches['MRP'];
              /*  if(trim($matches['Qty'])=="" && trim($matches['Rate'])=="" && trim($matches['MRP'])==""){
                    echo "Inside kps: $linedata";
                if(preg_match($regexx, $linedata,$ups)){
                    $matches['Qty'] = $ups['Qty'];
                    $matches['MRP'] = $ups['MRP'];
                    $matches['Rate'] = $ups['Rate'];  
                }
                }*/
                } 
                            
                $i++;
              echo  $linedata = $rows[$i];
                
                echo $digit_regex=$ini->Items->Regex[2];
                                                                        
            //     print"<br>In if1<br>";
                 if(preg_match($digit_regex, $linedata,$arr)){
                   //  print_r($arr);
                     $Iname2 = trim($arr['Itemname']);
                     echo "Iname 2: $Iname2<br>";
                   //  $ArticleNo = $arr[2];
                    // $matches['ArticleNo'] = $ArticleNo;                                                                     
                 }

                $i++;
                $linedata = $rows[$i];
                if(!preg_match('/^[0-9 ]*$/',$linedata)){
                    $item_search_regex = $ini->Items->Regex[3];
                if(preg_match($item_search_regex,$linedata,$arr1)){   
//                    print_r($arr1);
                     $Iname3 = trim($arr1['Itemname']);
                     echo "Iname 3: $Iname3";
                     $i++;
                     $linedata = $rows[$i];
                     if(preg_match('/^[0-9 ]*$/',$linedata)!=1 && preg_match('/Total/',$linedata)!=1 && preg_match('/\d+\s+\d+\s+(C|S)GST\s*\-\s*\S+\s*%/',$linedata)!=1){
                    $item_search_regex = $ini->Items->Regex[3];
                if(preg_match($item_search_regex,$linedata,$arr1)){   
//                    print_r($arr1);
                     $Iname4 = trim($arr1['Itemname']);
                     echo "<br>Iname 4: $Iname4<br>";
                 }
                }
                 }
                }else{
                    echo "New item found<br>";
                }
                        $final_itmname = str_replace("*","",trim($Iname)." ".trim($name)." ".trim($Iname1)." ".trim($Iname2)." ".trim($Iname3)." ".trim($Iname4));                                               
                 $matches['Itemname'] = trim($final_itmname);
                }
                
                echo "Details are: ";
                print_r($matches);
                if($itmdesp['Qty']){
                 $matches['ttk_qty'] = $itmdesp['Qty']; 
                }
                else
                {
                   $matches['ttk_qty']= $matches['Qty']; 
                }
                    $valid = validateitem_Max($matches);
                   // print"<br>Valid=$valid<br>";
                    if(($valid)==1){
                        $all_items[] = $matches;
                     //   print "<br>All Items Arr: <br>";
                     //   print_r($all_items);
                        unset($matches);
                        $matches = array();
                    }
            }else if(preg_match($last_regex, $linedata,$matches)){
                 print "<br>in 2nd loop ITems<br>";
                $Iname = $matches['Itemname'];
                echo "Iname 0: $Iname<br>";
                
                //Capture Article no
                $i--;
                $linedata = $rows[$i];
              //  echo "Linedata: $linedata<br>";
                if(preg_match('/^[0-9 ]*$/',$linedata)==1){  //check if on this line article no present or not
                //    echo "Inside fetch article no";
                     $artregex = $ini->Items->Regex[4];                                                                     
                 if(preg_match($artregex, $linedata,$array1)){                                            
                     $matches['ArticleNo'] = trim($array1['ArtcleNo']);                                                                      
                 }
                }else if(preg_match('/(\d+)\s+(\d+)\s+(SGST|CGST|GST|IGST)\s*\-\s*(\d\S*)\s*%\s*\-\s*\S+/',$linedata,$mtch)){
                    $matches['ArticleNo'] = trim($mtch[1]);
                    $matches['EAN'] = trim($mtch[2]);
                    $matches['VAT'] = trim($mtch[4]);
            }
            print "Alternate case:";
            print_r($matches);
                $i++;
                $linedata = $rows[$i];
                if(preg_match('/\d+\s+\*\s+(CGST|SGST|IGST)\s*\-\s*\S+\s*%\s*\-\s*\d\S*/',$linedata)==1){
                    continue;
                }

                   $valid = validateitem_Max($matches);
                   // print"<br>Valid=$valid<br>";
                    if(($valid)==1){
                        $all_items[] = $matches;
                     //   print "<br>All Items Arr: <br>";
                     //   print_r($all_items);
                        unset($matches);
                        $matches = array();
                    }
            }/*else if(preg_match($last_regex123, $linedata,$matches)){
                 print "<br>in 3rd loop ITems<br>";
                
                $i++;
                $linedata = $rows[$i];
                $reges = $ini->Items->Regex[1];
                if(preg_match($reges, $linedata,$mtch)){
                    $matches['Itemname'] = trim($mtch['Itemname']);
                }
                print_r($matches);
                $valid = validateitem_Max($matches);
                   // print"<br>Valid=$valid<br>";
                    if(($valid)==1){
                        $all_items[] = $matches;
                     //   print "<br>All Items Arr: <br>";
                     //   print_r($all_items);
                        unset($matches);
                        $matches = array();
                    }
            }*/
        }     
        return $all_items;
    }  
} // end of class


function validateitem_Max($item_arr){
    print"<br> in validate item<br>";
//   print_r($item_arr);
        $item = array();
//        $fields=array();
       //fetch all details of all items
        $pushFlag=0;
        $pregFlag=0;
        //$artno=(trim(getFieldValue($item_arr,"ArticleNo")));
        $EAN=trim(getFieldValue($item_arr,"EAN"));
        $Itemname=trim(getFieldValue($item_arr,"Itemname"));
        //$CAR=trim(getFieldValue($item_arr,"CAR"));
        $mrp=doubleval(trim(getFieldValue($item_arr,"MRP")));
        $VAT=doubleval(trim(getFieldValue($item_arr,"VAT")));
        $Qty=doubleval(trim(getFieldValue($item_arr,"Qty")));
        $Rate=doubleval(trim(getFieldValue($item_arr,"Rate")));
        $Amount=doubleval(trim(getFieldValue($item_arr,"Amount")));
        
//        print" EAN= $EAN &&  $mrp && $VAT && $Qty && $Rate && $Amount<br>";
        
        if( trim($EAN)!="" &&  trim($mrp)!="" && trim($VAT)!="" && trim($Qty)!="" && trim($Rate)!="" && trim($Amount)!=""){
          //  print"in non empty<br>"; 
            if(preg_match("/[0-9a-zA-Z]/",$EAN)){
                $pregFlag=1;
            }else{
                $pregFlag=0;            
            }

            if((is_double($mrp))&&(is_double($VAT))&&(is_double($Qty))&&(is_double($Rate))&&(is_double($Amount)&& trim($pregFlag)==1)){
                $pushFlag=1;
             //  print"<br> Push Flag true case---$pushFlag";
            }else{

               $pushFlag=0;
           //    print"<br> Push Flag false case---$pushFlag";
             }
             
            if($pushFlag==1){
                return 1;
            }else{
                return 0;
            }
        }      
    }
        
    function fetchItemDescription_Max($optional_regex,$previous_line){
       // print"In fetch item name";
        $itemdescrp = "";
        $arr = array();
             
        if(preg_match($optional_regex, $previous_line,$arr)){
        }
         return $arr;
        //return $itemdescrp;
    }

    function checkEAN_Max($EAN){
        $db = new DBConn();
        $EAN_db = $db->safe(trim($EAN));
        $query = "select * from it_master_items where itemcode = $EAN_db"; //and is_weikfield = 1 ";
      //  print "<br>EAN CHK QRY $query <br>";
        $obj = $db->fetchObject($query);
        if(isset($obj)){
           // return 0; // no need to fetch itm descrption 
            return 1;   //logic change as all items are inserted in master_items 23/08/2016 by deepali      
        }else{
            return 1; // fetch itm descrption
        }
    }