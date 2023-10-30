<?php

require_once("../../it_config.php");
require_once "SaveToDB.php";
require_once "lib/db/DBConn.php";
require_once "checkAmt.php";

class eachRowRegex {

    public function _construct() {
        
    }

    public function fetchItems($startRow, $lastItemRow, $rows, $ini, $master_dealer_id) {
       print"<br> in eachrowregex class<br>";
      print "<br>START ROW : $startRow :: Last Row: $lastItemRow<br> ROWS => "; 
      // print_r($rows);
//       print "<br>INI below: <br>";
//       print_r($ini);
       echo "aSSAASAs".$master_dealer_id;
            $item_parts = array();
        $all_items = array();
        $items = array();
        $matches['Iname'] = "";
        $partial_name = "";
        $itemname = "";
        //echo $startRow."=====ganesh====".$lastItemRow;
        //$nooffields_arr = array('5'=>12); 
        for ($i = $startRow; $i < $lastItemRow; $i++) {
            $regex_size = sizeof($ini->Items->Regex);
//            echo "SIZE: $regex_size";
            $linetext = $rows[$i];
            $continue_main_loop = 0;
            //$matchregexarr=array();
            for ($j = 0; $j < $regex_size; $j++) { // regex iteration loop
                $regex = $ini->Items->Regex[$j];
                $matches = array();
                print"<br>Line sasaasno: $i <br>Regex at: $j <br> Regex is : $regex <br> Line text is: $linetext <br>";
               // echo $linetext;
               
                preg_match($regex, trim($linetext), $matches);
                print_r($matches);
                print"<br>***************************************<br>";
                if (!empty($matches)) { // means regex matched
                 print_r($matches);

                    //          $current_line = $i;
                    //Check check for spencers to fetch PO item name as from above preg match I get partial itemname
                    if ($master_dealer_id == 8) {
                     $rin_text=implode("", $rows);
                     preg_match_all('/CGST/', $rin_text, $output_array);
                     //print_r($output_array);
                     // exit;
                     if(!empty($output_array)){
                        echo "<pre>";
                       print_r($matches);
                       $VAT=$matches['VAT'];
                       $vatarr= array(0,5,12,18,28);   //IGST %
                        // echo $VAT;
                        // exit;
                                if($VAT!=""){
                                    if(! in_array($VAT, $vatarr)){
                                       echo  $VAT=$VAT*2;  
                                        $matches['CGST'] = $matches['VAT'];
                                        $matches['SGST'] = $matches['VAT'];       
                                    }
                                    else{

                                        $matches['IGST'] = $matches['VAT']; 
                                    }


                                }
                     
                     }
                        $linetext = $rows[$i];
                        if (strpos($linetext, '/P') !== false || strpos($linetext, '/F') !== false) {
//                            echo 'true';
                            $linetext = str_replace('/P', "", $linetext);
                            $linetext = str_replace('/F', "", $linetext);
                        }
//                         echo "<br><br><br><br><br><br>CURRENT LINE IS: $linetext<br><br><br><br><br><br><br>";
                        $i++;
                        $linetext = $rows[$i];
//                       echo "<br><br><br><br><br><br>NEXT LINE IS: $linetext<br><br><br><br><br><br><br>";
                        $split = explode(" ", $linetext);
                        $get_last_word = $split[count($split) - 1];
//                       echo "Last word: ".$get_last_word."<br>";

                        if (preg_match('/(\/F)/', $get_last_word) == 1 || preg_match('/(\/P)/', $get_last_word) == 1) {
//                           $partial_name = str_replace("/F"," ",$linetext);
//                             $partial_name = preg_replace("/\//",'-',$linetext);
                            if (preg_match('/(.*)\s+\/F/', $linetext, $mtch) || preg_match('/(.*)\s+\/P/', $linetext, $mtch)) {
                                $partial_name = $mtch[1];
//                               $matches['Iname'] = $partial_name;
                                $matches['Itemname'] = trim($matches['Itemname']) . " " . trim($partial_name);
                            }
                            //$partial_name = str_replace("/P"," ",$linetext);
                        } else {
                            $i--;
                            $partial_name = "";
                        }
                    }
                    if ($master_dealer_id == 3 ) { //ABRL case
                        $linetext = $rows[$i];
                        // echo "Line data : $linetext<br>";
                        

                        if (isset($matches['Itemname']) && trim($matches['Itemname']) != "") {
                            $itemname .= " " . trim($matches['Itemname']);
                            $matches['Itemname'] = "";
                            //  echo "Partial name 0: $itemname<br>";
                        }
                        if (isset($matches['EAN']) && trim($matches['EAN']) != "" && isset($matches['Rate']) && trim($matches['Rate']) != "" && isset($matches['Qty']) && trim($matches['Qty']) != "" && isset($matches['Amount']) && trim($matches['Amount']) != "" ) {

                            $EAN = $matches['EAN'];
                            $Rate = $matches['Rate'];
                            $Qty = $matches['Qty'];
                            $Amount = $matches['Amount'];
                           
                           echo $i++;

                           echo  $line = $rows[$i];
                           if($matches['VAT']=='')
                           {
                           $i--;
                           $i--;
                           echo  $line = $rows[$i];
                           preg_match($ini->Items->Regex[1], trim($line), $mtch1211);
                           print_r($mtch1211);
                           echo $VAT = $mtch1211['VAT'];
                            $i=$i+2;
                           }else{

                                echo $VAT = $matches['VAT'];
                           }
                           //exit;
                            //  $regex = $ini->Items->Regex[1]; 
                            $regex = "/(.*)\s+(SGST\/UTGST)\s*\-\s*\S+\s*\%\s+\d\S*/";
                            $regex1 = "/(.*)\s+CGST\s*\-\s*\S+\s*\%\s+\d\S*/";
                            $mtch12 = array();

                            //preg_match($regex,trim($line),$mtch12);
                            // if(!empty($mtch12)){
                            if (preg_match($regex, trim($line), $mtch12) || preg_match($regex1, trim($line), $mtch12)) {
                                $Iname = $mtch12[1];
                                 $itemname .= " " . trim($Iname);
                                //    echo "Partial name 1: $itemname<br>";
                                // $matches['Itemname'] = $itemname;
//                             $i--;
                            } else if (!preg_match('/SGST\/UTGST|CGST|IGST/', $line)) {
                                $itemname .= " " . trim($line);
                               $line;
                                //    echo "Partial name 2: $itemname<br>";
                                //  $matches['Itemname'] = $itemname;
//                             $i--;
                            } else {
                                $i--;
                            }
                          
                            $matches['Itemname'] = trim($itemname);
                            $matches['EAN'] = $EAN;
                            $matches['Rate'] = $Rate;
                            $matches['Qty'] = $Qty;
                            $matches['Amount'] = $Amount;
                            $matches['VAT']=$VAT;
                            $rin_text=implode("", $rows);
                     preg_match_all('/CGST/', $rin_text, $output_array);
                             $VAT=$matches['VAT'];
                       $vatarr= array(0,5,12,18,28);   //IGST %
                        // echo $VAT;
                        // exit;
                                if($VAT!=""){
                                    if(! in_array($VAT, $vatarr)){
                                       echo  $VAT=$VAT*2;  
                                        $matches['CGST'] = $matches['VAT'];
                                        $matches['SGST'] = $matches['VAT'];       
                                    }
                                    else{

                                        $matches['IGST'] = $matches['VAT']; 
                                    }
                                }
                                else{
                                    
                                    //echo $line;
                                    //echo $i=$i+1;
                                    echo $i;
                                    $getgst=$rows[$i];
                                    $regex_gst="/(?'Itemname'\w+.*)\S*(?'VAT1'\S+)?\s+(?'VAT'\d+)\s*(?'ign11'\S+)\s*\%?/";
                                    
                                    preg_match($regex_gst, trim($getgst), $mtch_gst);
                                    $matches['VAT']=$mtch_gst['VAT'];
                                    $matches['Itemname']=$mtch_gst['Itemname'];
                                    $VAT=$matches['VAT'];
                                     if($VAT!=""){
                                    if(! in_array($VAT, $vatarr)){
                                       echo  $VAT=$VAT*2;  
                                        $matches['CGST'] = $matches['VAT'];
                                        $matches['SGST'] = $matches['VAT'];       
                                    }
                                    else{

                                        $matches['IGST'] = $matches['VAT']; 
                                    }
                                    
                                }
                            }

                            $itemname = "";
                            $EAN = "";
                            $Rate = "";
                            $Amount = "";
                            $Qty = "";
                        }
                        echo "Data is: ". $itemname."<>".$VAT."<>".$Rate."<>".$Qty."<>".$Amount."<br>";
                      //  exit;
                    }

                    if ($master_dealer_id == 14) {
                        // echo $rows[$i];
                        // exit;
                        // exit;
                        if (trim($ini->Header->Fields[19]->Name == 'another_pdf_format')) {
                            echo "dasasd";
                           // exit;
                            $i++;
                           echo $linetext = $rows[$i];
                           echo "<br>";
                           echo $regex = $ini->Items->Regex[1];
                           echo "<br>";
                            preg_match($regex, trim($linetext), $mtch13);
                           // print_r($mtch13);
                            //exit;
                            if (!empty($mtch13)) {
                                $matches['Itemname'] = $matches['Itemname'] . "" . trim($mtch13['Itemname']);
                                $matches['Rate'] = $mtch13['Rate'];
                                $matches['EAN'] =  $mtch13['EAN'];
                               // $matches['ArticleNo'] =;
                                //$matches['CAR'] = $mtch13['CAR'];
                                $matches['ttk_CAR'] = $mtch13['ttk_CAR'];
                                $matches['ttk_qty'] = $mtch13['ttk_qty'];
                                $matches['Qty'] = $mtch13['ttk_qty'];
                                $matches['Rate'] = $mtch13['Rate'];
                            }
                            $i++;

                            echo "********************************";
                             echo "<br>";
                            echo $linetext = $rows[$i];
                            echo "<br>";

                           echo  $regex = $ini->Items->Regex[2];
                           echo "<br>";
                          // echo trim($linetext);
                           //exit;
                            preg_match($regex, trim($linetext), $mtch15);
                            print_r($mtch15);
                           
                           
                            if (!empty($mtch15)) {
                                $matches['HSN'] = $mtch15['HSN'];
                                $matches['MRP'] = $mtch15['MRP'];
                                $matches['VAT'] =  $mtch15['SGST']+$mtch15['CGST'];
                                $matches['SGST'] = $mtch15['SGST'];
                                $matches['CGST'] = $mtch15['CGST'];
                            } else {
                               echo  $regex = $ini->Items->Regex[3];
                              // exit;
                                preg_match($regex, trim($linetext), $mtch15);
                                if (!empty($mtch15)) {
//                             print_r($mtch13);  
                                     $matches['HSN'] = $mtch15['HSN'];                           
                                    $matches['MRP'] = $mtch15['MRP'];
                                    $matches['VAT'] = $mtch15['IGST'];
                                     $matches['IGST'] = $mtch15['IGST'];
                                }
                            }
                        } else if (trim($ini->Header->Fields[16]->Name == 'multiple_pdf_format')) {
                            if (!isset($matches['EAN']) && !isset($matches['Qty']) && !isset($matches['Rate']) && !isset($matches['VAT'])) {
                                $arr = array();
                                array_push($arr, $matches);
                            } else if (isset($matches['EAN']) && isset($matches['Qty']) && isset($matches['Rate']) && !isset($matches['VAT'])) {
                                array_push($arr, $matches);
                            }if (isset($matches['VAT'])) {
                                $matches['VAT'] = $matches['VAT'];
                                $matches['Amount'] = $arr[0]['Amount'];
                                $matches['ArticleNo'] = $arr[0]['ArticleNo'];
                                $matches['EAN'] = $arr[0]['ArticleNo'];
                                $matches['Itemname'] = $arr[0]['Itemname'] . " " . $arr[1]['Itemname'];
                                $matches['CAR'] = $arr[1]['CAR'];
                                $matches['Qty'] = $arr[1]['Qty'];
                                $matches['Rate'] = $arr[1]['Rate'];
                                $arr = array();
                            }
                        }
                        //adding by aniket//
                        else {

                            //echo "$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$";
                            //echo "<br>";
                            $arr = array();
                            print_r($matches);
                            if (isset($matches['Itemname']) && isset($matches['ArticleNo'])) {
                                array_push($arr, $matches);

                                $matches['Rate'] = $arr[0]['Rate'];
                                $matches['Qty'] = $arr[0]['Qty'];
                                $matches['Amount'] = $arr[0]['Amount'];
                                
                                preg_match('/^[0-9]*$/', $arr[0]['ArticleNo'], $opart);
                                if(empty($opart)){
                                    echo "article number is missing";
                                    //echo round($arr[0]['ign1']);
                                    $matches['ArticleNo']=round($arr[0]['ign1']);
                                    $matches['Itemname'] .= ' '.trim($arr[0]['ArticleNo']);
                                }
                                else{
                                    $matches['ArticleNo'] = $arr[0]['ArticleNo'];
                                }

                                //$matches['ArticleNo'] = $arr[0]['ArticleNo'];
                                $matches['VAT'] = $arr[0]['VAT'];
                                $matches['MRP'] = $arr[0]['MRP'];

                                $i++;
                                trim($rows[$i]);
                                $linetext = trim($rows[$i]);
                                if (stripos($linetext, "EAN") !== false) {
                                    $i--;
                                } else {
                                    echo "dasdasd" . $linetext;
                                    $matches['Itemname'] .= " " . trim($linetext);
                                    //  $arr = "";
                                }
                                $arr = array();
                            }
                        }
                    }

                    if ($master_dealer_id == 26) {
                        // Vishal Mega mart                      

                        if (isset($matches['Itemname']) && isset($matches['ArticleNo'])) {
                            $arr = array();
                            
                             $matches['EAN'] = $matches['ArticleNo'];
                              $matches['ttk_qty'] = $matches['TTK_QTY'];
                               $matches['IGST'] = $matches['VAT'];
                             
                            array_push($arr, $matches);
                            echo '<pre>';
                            // print_r($arr);
                            // exit;
                       }
                           // else if (isset($matches['Itemname'])) {
//                             print_r($arr);
//                             $matches['Rate'] = $arr[0]['Rate'];
//                             $matches['Qty'] = $arr[0]['Qty'];
//                             $matches['Amount'] = $arr[0]['Amount'];
//                             $matches['ArticleNo'] = $arr[0]['ArticleNo'];
//                             $matches['VAT'] = $arr[0]['VAT'];
//                             $matches['MRP'] = $arr[0]['MRP'];

//                             $i++;
//                              trim($rows[$i]);
//                             $linetext = trim($rows[$i]);
//                             $matches['Itemname'] .= " " . trim($linetext);
//                             //  $arr = "";
//                             $arr = array();
// //                             print_r($matches);
//                         }
                    }

                    // Added By aniket for Ratandeep ///
                    if ($master_dealer_id == 21) {
                        // Ratandeep                      
                        echo ">>>>>ratndeep>>>>";
                        if (!isset($matches['Itemname']) && isset($matches['ArticleNo'])) {
                            $arr = array();
                            array_push($arr, $matches);
                            echo '<pre>';
                            // print_r($arr);
                        } else if (isset($matches['Itemname'])) {
                            $arr = array();
                            echo '<pre>';
                            array_push($arr, $matches);

                            //print_r($arr);
                            $matches['Rate'] = $arr[0]['Rate'];
                            $matches['Qty'] = $arr[0]['Qty'];
                            $matches['Amount'] = $arr[0]['Amount'];
                            $matches['ArticleNo'] = $arr[0]['ArticleNo'];
                            $matches['VAT'] = $arr[0]['VAT'];
                            $matches['MRP'] = $arr[0]['MRP'];

                            $lineCount = $i + 1;

                            echo "nextline" . trim($rows[$lineCount]);
                            $linetext = trim($rows[$lineCount]);
                            if ($linetext == '') {
                                //$i++;
                                echo "itst wprking";
                                $i++;
                                echo $linetext = trim($rows[$i]);
                                $matches['Itemname'] .= " " . trim($linetext);
                            } else {
                                preg_match('/\d+/', $linetext, $rtnmatch);
                                if (empty($rtnmatch)) {
                                    echo "string";
                                    print_r($linetext);
                                    $matches['Itemname'] .= " " . trim($linetext);
                                }
                            }
                            //  $arr = "";
                            $arr = array();
//                             print_r($matches);
                        }
                    }


        if($master_dealer_id==7){    // Walmart    
                           echo    "mayur".$ini->Header->Fields[1]->Name;
                         // exit;
                         if(trim($ini->Header->Fields[4]->Name=='DiffPO')){
                          //  print_r($matches);
                            //exit;
                                 echo "while in the wrong if condition"; 
                             $matches['Qty'] *= $matches['Qty1'];
                             $matches['Rate'] = round($matches['calRate']/$matches['Qty'],2);
                             $i--;
                             $linetext = trim($rows[$i]);
                             if(preg_match('/\#\s*(\d+)/',$linetext,$mtch)){
                                 $matches['ArticleNo'] = trim($mtch[1]);                                 
                             }
                             $i++;
                             $i++;
                             $linetext = trim($rows[$i]);
                             if(preg_match('/\#\s*(\d+)/',$linetext)!=1){
                                 $matches['Itemname'] .= " ".trim($linetext);                                 
                             }                             
                         }else if(trim($ini->Header->Fields[1]->Name=='DiffPO1')){
                          

                            echo "<pre>";
                            echo "<br>";
                            echo "dasdasdsadsadasd0";
                          $regex = $ini->Items->Regex[1]; 
                            echo "MRPPPPP".$matches['MRP'];
                            echo "<br>";
                             echo "<br>";
                             //exit;
                             $regex_old="/(?'SrNo'\d+)\s+(?'Itemname'.*)\s+(?'Qty'\d+)\s+(?'Ign'\S+)\s+(?'ttk_Qty'\d+)\s*(?'ttk_CAR'\S+)\/(?'CAR'\d+\S+)\s+(?'MRP'\d\S*)\/(?'ign3'\S+)\s+(?'calrate'\d\S*)\s+(?'ign'\d\S*)\s+(?'ign1'IN:)\s+(?'ign2'\S+)\s*(?'ign4'\()\s*(?'VAT'\d\S*)\s*(?'ign5'%\)\s*\-)\s+(?'ign6'\d\S*)\s+(?'Amount'\d\S*)/";
                            if($matches['MRP']<200){
                                    echo "change the regex";
                                    echo $linetext;
                                    if(preg_match($regex_old, $linetext,$mtcha015)){
                                        
                                        print_r($mtcha015);
                                            echo $mtcha015['calrate'];
                                           echo $mtcha015['ttk_Qty'];
                            echo $matches['totQty'] = $mtcha015['Qty']*$mtcha015['ttk_Qty'];
                             
                    $matches['Rate'] = round($mtcha015['calrate']/$matches['totQty'],2);
                                 $matches['Qty']= $mtcha015['Qty'];
                                 $matches['Amount']=$mtcha015['Amount'];
                                  $matches['VAT']=$mtcha015['VAT'];
                                  $matches['ttk_CAR']=$mtcha015['ttk_CAR'];
                                   $matches['CAR']=$mtcha015['CAR'];
                                  $matches['ttk_qty']=$mtcha015['ttk_Qty'];
                                   $matches['MRP']=$mtcha015['MRP'];

                                  print_r($matches);
                                  //exit;
                                   $i++;
                                   $i++;
                                   $i++;
                                   $i++;
                                echo  "djljasdjasld".$linetext = trim($rows[$i]);
                             if(preg_match('/\#\s*(\d+)/',$linetext,$mtch1)){
                                 echo $matches['ArticleNo'] = trim($mtch1[1]);
                                 $matches['EAN']=$matches['ArticleNo'];
                             }
                                    }
                                   //exit;
                            }
                            else{
                              

                               $i++;
                            
                            
                            // $i++;
                             $linetext = trim($rows[$i]);
                            echo $linetext;
                            echo "44444444444444444444444444444444444";
                            echo "<br>";
                            echo $regex;
                               if(preg_match($regex,trim($linetext),$mtcha15)){
                                  echo "my test is not working";
                                  echo "<br>";
                                print_r($mtcha15);
                                //exit;
                                 echo "<br>";

                              
                                 echo "asasdasd".$matches['totQty'] = $mtcha15['Qty']*$mtcha15['ttk_qty'];
                                 echo "<br>";
                                 echo  $matches['Rate'] = round($mtcha15['calrate']/$matches['totQty'],2);

                                 $matches['Qty']= $mtcha15['Qty'];
                                 $matches['Amount']=$mtcha15['Amount'];
                                  $matches['VAT']=$mtcha15['VAT'];
                                  $matches['ttk_CAR']=$mtcha15['ttk_CAR'];
                                   $matches['CAR']=$mtcha15['CAR'];
                                   $matches['ttk_qty']=$mtcha15['ttk_qty'];
                                  $i++;
                                   $i++;
                                   $i++;
                                   $i++;
                                echo  "djljasdjasld".$linetext = trim($rows[$i]);
                             if(preg_match('/\#\s*(\d+)/',$linetext,$mtch1)){
                                 echo $matches['ArticleNo'] = trim($mtch1[1]);
                                 $matches['EAN']=$matches['ArticleNo'];
                             }
                             }
                             print_r($matches);
                                  $rin_text=implode("", $rows);
                     preg_match_all('/CGST/', $rin_text, $output_array);
                     //print_r($output_array);
                     // exit;
                     if(!empty($output_array)){
                        echo "<pre>";
                       print_r($matches);
                       $VAT=$matches['VAT'];
                       $vatarr= array(0,5,12,18,28);   //IGST %
                        // echo $VAT;
                        // exit;
                                if($VAT!=""){
                                    if(! in_array($VAT, $vatarr)){
                                       echo  $VAT=$VAT*2;  
                                        $matches['CGST'] = $matches['VAT'];
                                        $matches['SGST'] = $matches['VAT'];       
                                    }
                                    else{

                                        $matches['IGST'] = $matches['VAT']; 
                                    }


                                }
                     
                     }


                               }
                           
                            //exit;
                         }


                         else if(trim($ini->Header->Fields[1]->Name=='new_frt')){
                           //print_r($matches);

                            //exit;
                               $matches['Qty'] *= $matches['Qty1'];
                             $regex = $ini->Items->Regex[0]; 
                         if(preg_match($regex,trim($linetext),$mtch15)){
                            # $matches['Qty'] *= $matches['Qty1'];
                             $i++;
                             $linetext = trim($rows[$i]);
                             if(preg_match('/\#\s*(\d+)/',$linetext,$mtch)){
                                 $matches['ArticleNo'] = trim($mtch[1]);
                             }
                              $i++;
                              $i++;
                              $linetext = trim($rows[$i]);
                              if(preg_match('/\#\s*\d+/',$linetext)!=1){
                                  $matches['Itemname'] .= trim($linetext);
                              }
                              $i--;                                                           
                         }  
                        // print_r($matches);                                                       
                         }
                         else if(trim($ini->Header->Fields[1]->Name=='new_formate')){
                               $matches['Qty'] *= $matches['Qty1'];
                             $regex = $ini->Items->Regex[0]; 
                         if(preg_match($regex,trim($linetext),$mtch15)){
                             $matches['Qty'] *= $matches['Qty1'];
                             $matches['Rate'] = round(trim($matches['Amount'])/trim($matches['Qty']),2);   //check with ankit
                             $matches['Rate'] = round(($matches['MRP'] - ($matches['MRP'] * 0.34))/1.18,2);
                             $i--;
                             $linetext = trim($rows[$i]);
                             if(preg_match('/\#\s*(\d+)/',$linetext,$mtch)){
                                 $matches['ArticleNo'] = trim($mtch[1]);
                             }
                              $i++;
                              $i++;
                              $i++;
                              $linetext = trim($rows[$i]);
                              if(preg_match('/(.*)\s{3}/',$linetext)!=1){
                                  $matches['Itemname'] .= trim($linetext);
                              }
                              $i--;   
                              $i--;
                         }                                                         
                         }else{
                           // echo "culprit";
                            if(!isset($matches['Itemname']) && isset($matches['ArticleNo'])){
                                $arr1 = array();
                                array_push($arr1,$matches);
                                $matches['Qty'] *= $matches['Qty1'];
                                $matches['Rate'] = round(trim($matches['Amount'])/trim($matches['Qty']),2);
                                $matches['CAR'] = "EA";
                               // print_r($arr1);
                            }else if(isset($matches['Itemname'])){
                                $matches['Qty'] = $arr1[0]['Qty']*$arr1[0]['Qty1'];                             
                                $matches['Amount'] = $arr1[0]['Amount'];
                                $matches['ArticleNo'] = $arr1[0]['ArticleNo'];
   //                             $matches['VAT'] = $arr1[0]['VAT'];
                                $matches['EAN'] = $arr1[0]['EAN'];
                                $matches['CAR'] = "EA";
                                $matches['Rate'] = round(trim($matches['Amount'])/trim($matches['Qty']),2);
                                $i++;
                                $linetext = trim($rows[$i]);
                                $matches['Itemname'] .= " ".trim($linetext);                           
                                $arr1 = array();
                            }
                       }
                       }



                    if ($master_dealer_id == 22) {    // H&G                                                                                
                        $i++;
                        $linetext = trim($rows[$i]);
                        $regex = $ini->Items->Regex[1];
                        preg_match($regex, $linetext, $mtch123);
                        $matches['Itemname'] = trim($mtch123['Itemname']);
                    }

                    if ($master_dealer_id == 28) {    // Guardian
                        if (trim($matches['Itemname']) == "") {
                            $i--;
                            $linetext = trim($rows[$i]);
                            $regex = $ini->Items->Regex[0];
                            //            echo "<br><br><br><br>*************************Guardian: $linetext<>$regex<br>";
                            if (preg_match($regex, trim($linetext)) != 1) {
                                $matches['Itemname'] = trim($linetext);
                            }
                            $i++;
                            $i++;
                            $linetext = trim($rows[$i]);
                            $regex = $ini->Items->Regex[0];
                            //    echo "<br><br><br><br>*************************Guardian: $linetext<>$regex<br>";
                            if (preg_match($regex, trim($linetext)) != 1) {
                                $matches['Itemname'] .= " " . trim($linetext);
                            } else {
                                $i--;
                            }
                        }
                    }

                    if ($master_dealer_id == 5) {
                      // print_r($rows);
                      // exit;
                        $rin_text=implode("", $rows);
                     preg_match_all('/CGST/', $rin_text, $output_array);
                     //print_r($output_array);
                     // exit;
                     if(!empty($output_array)){
                        echo "<pre>";
                       print_r($matches);
                       $VAT=$matches['VAT'];
                       $vatarr= array(0,5,12,18,28);   //IGST %
                        // echo $VAT;
                        // exit;
                                if($VAT!=""){
                                    if(! in_array($VAT, $vatarr)){
                                       echo  $VAT=$VAT*2;  
                                        $matches['CGST'] = $matches['VAT'];
                                        $matches['SGST'] = $matches['VAT'];       
                                    }
                                    else{

                                        $matches['IGST'] = $matches['VAT']; 
                                    }


                                }
                     
                     }
                        $pack_type = array('EA');
                        if (!in_array($matches['CAR'], $pack_type)) {
                            $regex123 = $ini->Items->Regex[0];
                            print_r($matches);

                            for ($k = ++$i; $k < $lastItemRow; $k++) {
                                echo "@@@@".$k;
                                echo $linetext = trim($rows[$k]);
                                echo "<br>";
                                echo $linetext;
                                echo "<br>";
                                if(preg_match("/(?'HSN'\d+)\s+(?'ign1'\d.*)/", $linetext,$match_hsn))
                                {
                                      echo "aniket";
                                      echo "<br>";
                                     print_r($match_hsn);
                                     //exit;
                                   $matches['HSN'] = trim($match_hsn['HSN']);
                                }

                                if (preg_match('/(\d+|\d\S*)\s+(EA)\s+(\d\S*)/', $linetext, $mtch34)) {
                                    $matches['ttk_qty'] = trim($mtch34[1]);
                                    $matches['ttk_CAR'] = trim($mtch34[2]);
                                    $matches['MRP'] = trim($mtch34[3]);
                                    $matches['TQty'] = trim($matches['ttk_qty']);
                                    break;  
                                } else if (preg_match($regex123, $linetext) == 1) {
                                    break;
                                }
                                else if(!isset($matches['ttk_qty'])){
                                    echo $matches['Qty'];
                                    $matches['ttk_qty']=$matches['Qty'];
                                    echo "this is case";
                                    
                                }
                            }
                            $test_amount=str_replace(",",'',$matches['Amount']);
                            $test_qty= str_replace(",",'',$matches['Qty']);
                           // $matches['Rate'] = round($test_amount/$test_qty,2);
                            // $matches['Rate'] = round(str_replace(",", '', $matches['Amount']) / $matches['Qty'], 2);
                        }else{

                          
                           echo  "lkljaALSJAs".$matches['Qty'];
                            //exit;

                             echo $matches['ttk_qty'] = $matches['Qty'];
                            // exit;
                           
                        }
                       // print_r($matches);
                       // exit;
                    }

                         if ($master_dealer_id == 55) {
                      // print_r($rows);
                      // exit;
                        $rin_text=implode("", $rows);
                     preg_match_all('/CGST/', $rin_text, $output_array);
                     //print_r($output_array);
                     // exit;
                     if(!empty($output_array)){
                        echo "<pre>";
                       print_r($matches);
                       $VAT=$matches['VAT'];
                       $vatarr= array(0,5,12,18,28);   //IGST %
                        // echo $VAT;
                        // exit;
                                if($VAT!=""){
                                    if(! in_array($VAT, $vatarr)){
                                       echo  $VAT=$VAT*2;  
                                        $matches['CGST'] = $matches['VAT'];
                                        $matches['SGST'] = $matches['VAT'];       
                                    }
                                    else{

                                        $matches['IGST'] = $matches['VAT']; 
                                    }


                                }
                     
                     }
                        $pack_type = array('EA');
                        if (!in_array($matches['CAR'], $pack_type)) {
                            $regex123 = $ini->Items->Regex[0];

                            for ($k = ++$i; $k < $lastItemRow; $k++) {
                                echo "@@@@".$k;
                                echo $linetext = trim($rows[$k]);
                                echo "<br>";
                                echo $linetext;
                                echo "<br>";
                                if(preg_match("/(?'HSN'\d+)\s+(?'ign1'\d.*)/", $linetext,$match_hsn))
                                {
                                      echo "aniket";
                                      echo "<br>";
                                     print_r($match_hsn);
                                     //exit;
                                   $matches['HSN'] = trim($match_hsn['HSN']);
                                }
                                if (preg_match('/(\d+|\d\S*)\s+(EA)\s+(\d\S*)/', $linetext, $mtch34)) {
                                    $matches['ttk_qty'] = trim($mtch34[1]);
                                    $matches['ttk_CAR'] = trim($mtch34[2]);
                                    $matches['MRP'] = trim($mtch34[3]);
                                    $matches['TQty'] = trim($matches['ttk_qty']);
                                    break;  
                                } else if (preg_match($regex123, $linetext) == 1) {
                                    break;
                                }
                            }
                            $test_amount=str_replace(",",'',$matches['Amount']);
                            $test_qty= str_replace(",",'',$matches['Qty']);
                           // $matches['Rate'] = round($test_amount/$test_qty,2);
                            // $matches['Rate'] = round(str_replace(",", '', $matches['Amount']) / $matches['Qty'], 2);
                        }else{

                            print_r($matches);
                            echo "lkljaALSJAs".$matches['Qty'];
                            //exit;
                             $matches['ttk_qty'] = $matches['Qty'];
                           
                        }
                    }

                                             if ($master_dealer_id == 56) {
                      // print_r($rows);
                      // exit;
                        $rin_text=implode("", $rows);
                     preg_match_all('/CGST/', $rin_text, $output_array);
                     //print_r($output_array);
                     // exit;
                     if(!empty($output_array)){
                        echo "<pre>";
                       print_r($matches);
                       $VAT=$matches['VAT'];
                       $vatarr= array(0,5,12,18,28);   //IGST %
                        // echo $VAT;
                        // exit;
                                if($VAT!=""){
                                    if(! in_array($VAT, $vatarr)){
                                       echo  $VAT=$VAT*2;  
                                        $matches['CGST'] = $matches['VAT'];
                                        $matches['SGST'] = $matches['VAT'];       
                                    }
                                    else{

                                        $matches['IGST'] = $matches['VAT']; 
                                    }


                                }
                     
                     }
                        $pack_type = array('EA');
                        if (!in_array($matches['CAR'], $pack_type)) {
                            $regex123 = $ini->Items->Regex[0];

                            for ($k = ++$i; $k < $lastItemRow; $k++) {
                                echo "@@@@".$k;
                                echo $linetext = trim($rows[$k]);
                                echo "<br>";
                                echo $linetext;
                                echo "<br>";
                                if(preg_match("/(?'HSN'\d+)\s+(?'ign1'\d.*)/", $linetext,$match_hsn))
                                {
                                      echo "aniket";
                                      echo "<br>";
                                     print_r($match_hsn);
                                     //exit;
                                   $matches['HSN'] = trim($match_hsn['HSN']);
                                }
                                if (preg_match('/(\d+|\d\S*)\s+(EA)\s+(\d\S*)/', $linetext, $mtch34)) {
                                    $matches['ttk_qty'] = trim($mtch34[1]);
                                    $matches['ttk_CAR'] = trim($mtch34[2]);
                                    $matches['MRP'] = trim($mtch34[3]);
                                    $matches['TQty'] = trim($matches['ttk_qty']);
                                    break;  
                                } else if (preg_match($regex123, $linetext) == 1) {
                                    break;
                                }
                            }
                            $test_amount=str_replace(",",'',$matches['Amount']);
                            $test_qty= str_replace(",",'',$matches['Qty']);
                           // $matches['Rate'] = round($test_amount/$test_qty,2);
                            // $matches['Rate'] = round(str_replace(",", '', $matches['Amount']) / $matches['Qty'], 2);
                        }else{

                            print_r($matches);
                            echo "lkljaALSJAs".$matches['Qty'];
                            //exit;
                             $matches['ttk_qty'] = $matches['Qty'];
                           
                        }
                    }


                    if ($master_dealer_id == 11) {
                           //max   
                                            $rin_text=implode("", $rows);
                     preg_match_all('/CGST/', $rin_text, $output_array);
                     //print_r($output_array);
                     // exit;
                     if(!empty($output_array)){
                        echo "<pre>";
                       print_r($matches);
                       $VAT=$matches['VAT'];
                       $vatarr= array(0,5,12,18,28);   //IGST %
                        // echo $VAT;
                        // exit;
                                if($VAT!=""){
                                    if(! in_array($VAT, $vatarr)){
                                       echo  $VAT=$VAT*2;  
                                        $matches['CGST'] = $matches['VAT'];
                                        $matches['SGST'] = $matches['VAT'];       
                                    }
                                    else{

                                        $matches['IGST'] = $matches['VAT']; 
                                    }


                                }
                     
                     }
                       
                        if ($ini->Header->Fields[1]->Name == 'unique') {
                            
                        } else {
                            $i--;
                            $linetext = trim($rows[$i]);

                            if (is_numeric($linetext)) {
                                $matches['ArticleNo'] = trim($linetext);
                            }
                            $i++;
                            $i++;
                            $linetext = trim($rows[$i]);

                            $regex = $ini->Items->Regex[1];
                              // echo "Linetext will be $i: ".$linetext."<br>";
                                // echo $regex;
                            if (preg_match($regex, trim($linetext), $matches123)) {
                                $matches['Itemname'] .= " " . trim($matches123['Itemname']);
                                $matches['Qty'] = trim($matches123['Qty']);
                                $matches['MRP'] = trim($matches123['MRP']);
                                $matches['Rate'] = trim($matches123['Rate']);
                                $matches['ttk_qty'] = $matches['Qty'];
                            }
                            $i++;
                            $linetext = trim($rows[$i]);
                             echo "Linetext will be er :".$linetext."<br>";

                            $regex = $ini->Items->Regex[2];
                            //  echo $regex;
                            if (preg_match($regex, trim($linetext), $matches1234)) {
                                $matches['Itemname'] .= " " . trim($matches1234['Itemname']);
                            }

                            $i++;
                            $linetext = trim($rows[$i]);
                             echo "Linetext will be $i:".$linetext."<br>";
                            if (!is_numeric($linetext)) {
                                $matches['Itemname'] .= " " . trim($linetext);
                            }
                            $matches['CAR'] = "EA";
                        }
                    }
                    
                    if ($master_dealer_id == 41) {      //Nysaa     
                        for ($i++; $i < $lastItemRow; $i++) {
                            $linetext = trim($rows[$i]);
                            $regex = $ini->Items->Regex[0];
                            echo "<br><br>REGEX will be: " . $regex;
                            echo $linetext;
                            if (preg_match($regex, $linetext) != 1) {
//                                   echo "<br><br><br><br><br><br>$regex<>$linetext<br><br>";
                                $matches['Itemname'] .= " " . trim($linetext);
                            } else {
                                $i--;
                                break;
                            }
                        }
                    }

                    if ($master_dealer_id == 27) {
                        $car_arr = array('PC', 'PCS', 'pc', 'Pc', 'Pcs', 'pcs', 'EA');
                        if (!in_array($matches['CAR1'], $car_arr)) {
                            $matches['Rate'] = round($matches['Rate'] / $matches['Qty'], 2);
                        } else {
                            $matches['Qty'] = $matches['Qty1'];
                        }
                    }


                    if ($master_dealer_id == 44) {
                        $matches['ArticleNo'] = $matches['EAN'];
                        $matches['Amount'] = $matches['Amount'] * (1 + $matches['VAT'] / 100);

                        $regex = $ini->Items->Regex[0];
                        for ($i++; $i < $lastItemRow; $i++) {
                            $linetext = trim($rows[$i]);
                            if (preg_match($regex, $linetext) != 1 && preg_match('/GRAND\s+TOTAL/', $linetext) != 1) {
                                $matches['Itemname'] .= " " . trim($linetext);
                            } else {
                                $i--;
                                break;
                            }
                        }
                    }


                    if ($master_dealer_id == 20) {
                        $regexxx = $ini->Items->Regex[0];
                        $regex = $ini->Items->Regex[1];

                        if (preg_match($regexxx, $linetext) == 1) {
                            $matches['Itemname'] .= " " . trim($matches['Itemname1']);
                            $itm = $matches['Itemname'];
                        } else if (preg_match($regex, $linetext) == 1) {
                            //  echo "inside aadhar";
                            $i--;
                            $line = $rows[$i];
                            $matches['Itemname'] = $itm . " " . trim($matches['Itemname']);
                            if (preg_match($regexxx, $line) != 1) {
                                if (preg_match('/(.*)\s+\d\S+\s+\d\S+/', $line, $mtch)) {
                                    $matches['Itemname'] .= " " . trim($mtch[1]);
                                } else {
                                    $matches['Itemname'] .= " " . trim($line);
                                }
                            }
                            $i++;
                            $i++;
                            $line = $rows[$i];
                            if (preg_match('/EAN/', $line) != 1) {
                                if (preg_match('/(.*)\s+\d\S+\s+\d\S+/', $line, $mtch)) {
                                    $matches['Itemname'] .= " " . trim($mtch[1]);
                                } else {
                                    $matches['Itemname'] .= " " . trim($line);
                                }
                            }
                        }
                    }

                    if ($master_dealer_id == 46) {  //SNNR
//                         echo "inside snnr";
                        $matches['ArticleNo'] = $matches['EAN'];
                        if (trim($matches['Rate']) == "" && trim($matches['Qty']) == "" && trim($matches['Amount']) == "" && trim($matches['MRP']) == "") {
                            $i++;
                            $line = $rows[$i];
                            $regex = $ini->Items->Regex[0];
                            echo "$regex<>$line<br>";
                            if (preg_match($regex, $line, $mtch)) {
                                // print_r($mtch);
                                $matches['Rate'] = trim($mtch['Rate']);
                                $matches['Amount'] = trim($mtch['Amount']);
                                $matches['Qty'] = trim($mtch['Qty']);
                                $matches['MRP'] = trim($mtch['MRP']);
                                $matches['VAT'] = trim($mtch['VAT']);
                            }
                        }

                        $i++;
                        $line = $rows[$i];
                        $regex = $ini->Items->Regex[0];
                        $regex1 = $ini->Items->Regex[1];
                        $regex2 = $ini->Items->Regex[2];

                        if (preg_match($regex, $line) != 1 && preg_match($regex1, $line) != 1 && preg_match($regex2, $line) != 1) {
                            $matches['Itemname'] .= " " . trim($line);
                        } else {
                            $i--;
                        }

                        $matches['Amount'] = $matches['Amount'] * (1 + $matches['VAT'] / 100);
                    }

                    if ($master_dealer_id == 47) {  //market 99
//                         echo "inside snnr";
                        $matches['ArticleNo'] = $matches['EAN'];
                        if (trim($matches['Rate']) == "" && trim($matches['Qty']) == "" && trim($matches['Amount']) == "" && trim($matches['MRP']) == "") {
                            $i++;
                            $line = $rows[$i];
                            $regex = $ini->Items->Regex[0];
                            echo "$regex<>$line<br>";
                            if (preg_match($regex, $line, $mtch)) {
                                // print_r($mtch);
                                $matches['Rate'] = trim($mtch['Rate']);
                                $matches['Amount'] = trim($mtch['Amount']);
                                $matches['Qty'] = trim($mtch['Qty']);
                                $matches['MRP'] = trim($mtch['MRP']);
                                $matches['VAT'] = trim($mtch['VAT']);
                            }
                        }

                        $i++;
                        $line = $rows[$i];
                        $regex = $ini->Items->Regex[0];
                        $regex1 = $ini->Items->Regex[1];
                        $regex2 = $ini->Items->Regex[2];

                        if (preg_match($regex, $line) != 1 && preg_match($regex1, $line) != 1 && preg_match($regex2, $line) != 1) {
                            $matches['Itemname'] .= " " . trim($line);
                        } else {
                            $i--;
                        }
                        $matches['Amount'] = $matches['Amount'] * (1 + $matches['VAT'] / 100);
                    }

                    /* if($master_dealer_id==49){  //V mart
                      if(!isset($matches['ArticleNo']) && !isset($matches['EAN'])){
                      $matches['ArticleNo'] = "-";
                      $matches['EAN'] = "-";
                      $regex = $ini->Items->Regex[0];
                      for ($i++; $i < $lastItemRow; $i++) {
                      $linetext = trim($rows[$i]);
                      if(preg_match($regex,$linetext)==1){
                      $i--;
                      break;
                      }else{
                      $matches['Itemname'] .= " ".trim($linetext);
                      }
                      }
                      }else{
                      $matches['ArticleNo'] = $matches['EAN'];

                      $regex = $ini->Items->Regex[0];
                      for ($i++; $i < $lastItemRow; $i++) {
                      $linetext = trim($rows[$i]);
                      if(preg_match($regex,$linetext)==1){
                      $i--;
                      break;
                      }else{
                      $matches['Itemname'] .= " ".trim($linetext);
                      }
                      }
                      }
                      } */

                      if ( $master_dealer_id == 54) {
                         // echo "askdkasdk";
                         // exit;
                                     $VAT=$matches['VAT'];
                       $vatarr= array(0,5,12,18,28);   //IGST %
                        // echo $VAT;
                        // exit;
                                if($VAT!=""){
                                    if(! in_array($VAT, $vatarr)){
                                       echo  $VAT=$VAT*2;  
                                    $matches['CGST'] = $matches['VAT'];
                                    $matches['SGST'] = $matches['VAT'];       
                                    }
                                    else{

                                        $matches['IGST'] = $matches['VAT']; 
                                    }


                                }
                      $matches['ArticleNo']= "Doesn't Exist";
                      $i++;
                      $i++;
                      $line=$rows[$i];
                      $regex = "/(?'ign'\S+)\s*:\s*(?'HSN'\d+)\]?/";
                      if(preg_match($regex, $line,$mtch1))
                              {
                          $matches['HSN']=$mtch1['HSN'];
                              }
                             print_r($matches);
                              //exit;
                  }
                          if ( $master_dealer_id == 51) {
                         // echo "askdkasdk";
                         // exit;
                                     $VAT=$matches['VAT'];
                       $vatarr= array(0,5,12,18,28);   //IGST %
                        // echo $VAT;
                        // exit;
                                if($VAT!=""){
                                    if(! in_array($VAT, $vatarr)){
                                       echo  $VAT=$VAT*2;  
                                    $matches['CGST'] = $matches['VAT'];
                                    $matches['SGST'] = $matches['VAT'];       
                                    }
                                    else{

                                        $matches['IGST'] = $matches['VAT']; 
                                    }


                                }
                      $matches['ArticleNo']= "Doesn't Exist";
                      $i++;
                      $i++;
                      $line=$rows[$i];
                      $regex = "/(?'ign'\S+)\s*:\s*(?'HSN'\d+)\]?/";
                      if(preg_match($regex, $line,$mtch1))
                              {
                          $matches['HSN']=$mtch1['HSN'];
                              }
                             print_r($matches);
                              //exit;
                  }


                    if ($master_dealer_id == 49) {  //V mart
                        if (!isset($matches['ArticleNo']) && !isset($matches['EAN'])) {
                            $matches['ArticleNo'] = "-";
                            $matches['EAN'] = "-";
                            $regex = $ini->Items->Regex[0];
                            for ($i++; $i < $lastItemRow; $i++) {
                                $linetext = trim($rows[$i]);
                                if (preg_match($regex, $linetext) == 1) {
                                    $i--;
                                    break;
                                } else {
                                    $matches['Itemname'] .= " " . trim($linetext);
                                }
                            }
                        } else {
                            $matches['ArticleNo'] = $matches['EAN'];

                            $regex = $ini->Items->Regex[0];
                            for ($i++; $i < $lastItemRow; $i++) {
                                $linetext = trim($rows[$i]);
                                if (preg_match($regex, $linetext) == 1) {
                                    $i--;
                                    break;
                                } else {
                                    $matches['Itemname'] .= " " . trim($linetext);
                                }
                            }
                        }
                    }

                    if ($master_dealer_id == 32) {  //Vijetha
                        $i++;
                        $linetext = $rows[$i];
                        $regex = $ini->Items->Regex[0];
                        if (preg_match($regex, $linetext) != 1) {
                            $matches['Itemname'] .= " " . trim($linetext);
                        } else {
                            $i--;
                        }
                    }

                    if ($master_dealer_id == 24) {    // H&B                                                                               
                        // $i++;
                        $linetext = trim($rows[$i]);
                        $regex = $ini->Items->Regex[1];
                        if (preg_match($regex, $linetext, $mtch123)) {
                            $matches['Itemname'] .= " " . trim($mtch123['Itemname']);
                            $matches['Rate'] = trim($mtch123['Rate']);
                            $matches['Qty'] = trim($mtch123['Qty']);
                            $matches['MRP'] = trim($mtch123['MRP']);
                            $matches['Amount'] = trim($mtch123['Amount']);
                        }/* else{
                          $i--;
                          continue;
                          } */
                    }

                            

                     if ($master_dealer_id == 50){
                         
                         $matches['Itemname'] = trim($matches['Itemname']);
                         if (isset($matches['ArticleNo']) && isset($matches['Qty']) && isset($matches['Itemname'])) {
                            $i++;
                           
                            $line = $rows[$i];
                                   
                            $regex = "/(?'Itemname'^.{1,30})\s+(?'EAN'\d{5,})/";
                         
                            echo "$regex<br>$line<br>";
                            if (preg_match($regex, $line, $mtch)) {
                               
                                $matches['Itemname'] .= " ".trim($mtch['Itemname']);
                                $matches['EAN'] .= trim($mtch['EAN']);
                            }
                           
                           $regex11=$ini->Items->Regex[0];
                            $i++;
                            $line = $rows[$i];
                            if(!(preg_match($regex11, $line,$mtch1)) && $i<$lastItemRow)
                              {
                                    $matches['Itemname'] .= " ".trim($line);
                                    $i++;
                                    $line = $rows[$i];
                                    if(!(preg_match($regex11, $line,$mtch3))&& $i<$lastItemRow )
                                      {
                                                   $matches['Itemname'] .= " ". trim($line);
                                                 
                                      }else{
                                          $i--;
                                      }
                     
                              }else{
                                  $i--;
                              }
                           
                        }
                       }
                        if ( $master_dealer_id == 54) {
                             $matches['ArticleNo']= $matches['EAN'];
                        }
                            if ( $master_dealer_id == 57) {
                                                $VAT=$matches['VAT'];
                       $vatarr= array(0,5,12,18,28);   //IGST %
                        // echo $VAT;
                        // exit;
                                if($VAT!=""){
                                    if(! in_array($VAT, $vatarr)){
                                       echo  $VAT=$VAT*2;  
                                    $matches['CGST'] = $matches['VAT'];
                                    $matches['SGST'] = $matches['VAT'];       
                                    }
                                    else{

                                        $matches['IGST'] = $matches['VAT']; 
                                    }


                                }
                             $matches['ArticleNo']= $matches['EAN'];
                             $matches['ttk_qty'] = $matches['Qty'];
                             $matches['Itemanme']= 'X';
                             print_r($matches);

                        }

                    //  if($matches['Itemanme'] && $matches['ArticleNo']){
                    //  echo "Data is:<br> Itemname : ".$matches['Itemname']."<br>Rate : ".$matches['Rate']."<br>EAN : ".$matches['EAN']."<br>Article No: ".$matches['ArticleNo']."<br>Qty: ".$matches['Qty']."<br>Amount: ".$matches['Amount'].
                    // "<br>MRP: ".$matches['MRP']."<br>VAT: ".$matches['VAT']."<br>";

                    $matched_regex_position = $j;
//                     print "<br>IN ESLE CASE Matched regex position : $matched_regex_position ";
                    //****************************************
                    echo "<br>";
                    echo $j;
                    echo "44444444444444444444444444444";
                    if ($matched_regex_position == 0) {
                        if ($master_dealer_id == 5) {
//                            print"First regex matched<br>";
                            //$i++;
                        }
                        if (!empty($item_parts)) {
                            //      print_r($item_parts);                 
                            array_push($all_items, $item_parts);
                            //    print "<br>All Items array <br>";
                            //    print_r($all_items);
                            unset($item_parts);
                            $item_parts = array();
                        }
                    }
                    if ($master_dealer_id == 3 || $master_dealer_id == 4) {
                        if (trim($matches['Itemname']) != "") {
                            $item_parts = array_merge($item_parts, $matches);
                        }
                    } else {
                        echo "djdjdjdjdjdjdjdjd";
                           print_r($matches);
                        $item_parts = array_merge($item_parts, $matches);
                    }
                    //*****************************************            
                      echo "<br><br>(((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((<br><br>";
                    //   print_r($item_parts);
                    //  echo "<br><br>(((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((<br><br>";
                    //global $item_parts;
                    count($item_parts);
                    if (!empty($item_parts)) {
//                ***********new code starts**********

                        if ($master_dealer_id != 13 && $master_dealer_id != DEF_METRO) {
                            // if($master_dealer_id != DEF_METRO){    
                            //    print"<br> inside  if to call check amount <br>";

                            $Qty = doubleval(trim(getFieldValue($item_parts, "Qty")));
                            $Rate = str_replace(",", "", trim(getFieldValue($item_parts, "Rate")));
                            $Amount = str_replace(",", "", trim(getFieldValue($item_parts, "Amount")));

                            if (($Qty != "" && $Qty != 0) && ($Rate != "" && $Rate != 0) && ($Amount != "" && $Amount != 0)) {
                                $check_amt = new checkAmt();
                                $response = $check_amt->chkValue($item_parts, $master_dealer_id);
                                if ($response == 1) {
                                      print"<br>valid amount<br>";
                                    break;
                                } else {
                                     print"<br>invalid Amount continue with next regex<br>";
                                    continue;
                                }
                            }
                        }
//                ***********new code ends**********
                    }


//                     print "<br>ITEM PARTS ARR IN ELSE CASE: <br>";
//                     print_r($item_parts);
//                     print "<br>";
                    $continue_main_loop = 1;
                    break;
                }

                if (trim($linetext) == "") { //blank line             
                    //             $continue_main_loop = 1;
                    //             $matched_regex_position++;
                    //             break;
                }
            }
            //end of for loop
            if ($continue_main_loop == 1 && $master_dealer_id != 8) { // go start of min item loop to fetch next item
                //   print "<br> In continue main loop : $continue_main_loop ";
                continue;
            }
//            print "<br>ITEM PARTS <br>"; 
//            print_r($item_parts);
            // $item = valid_item($item_parts,$master_dealer_id);
        }
        if (!empty($item_parts)) {
            array_push($all_items, $item_parts);
            //     print"<br>all_items are=<br>".count($all_items);
           // print_r($item_parts);
            $item_res = valid_item($all_items, $master_dealer_id);
            // $items = $all_items;
            $items = $item_res;
            echo "<br>";
            echo "return---------------------";
            print_r($items);
        }
        return $items;
    }

}

function valid_item($item, $master_dealer_id) {
     // echo "Inside valid_items<br>";
    // print_r($item);
//    echo "Partial : $partial_name";
    // print"<br> in validate item ";
    $car_skip_chainid = array(18);
    $rate_skip_chainid = array(18);
    $vat_skip_chainid = array(14);
    $amt_calculate = array(14);
    $ean_skip_chainid = array(14, 13, 6);
    // print"<br>items are=<br>".count($item);
    //print_r($item);
    $cnt = 0;
    foreach ($item as $item_arr) {
        print"<br>cnt=$cnt<br>";
        $cnt++;
        if (!empty($item_arr)) {
            //    print "<br>in not empty fields--------------<br>";
            //      print_r($item_arr);   
//            
            $pushFlag = 0;
            $pregFlag = 0;
            $artno = (trim(getFieldValue($item_arr, "ArticleNo")));
            $EAN = trim(getFieldValue($item_arr, "EAN"));
            $Itemname = trim(getFieldValue($item_arr, "Itemname"));
            $CAR = trim(getFieldValue($item_arr, "CAR"));
            $mrp = (trim(getFieldValue($item_arr, "MRP")));

//        $item_arr['Itemname'] .= " ".$partial_name;
//        echo "COMPLETE ITEMNAME IS: ".$Itemname."<br>";
//        $VAT=doubleval(trim(getFieldValue($item_arr,"VAT")));
//        $Qty=doubleval(trim(getFieldValue($item_arr,"Qty")));
//        $Rate=doubleval(trim(getFieldValue($item_arr,"Rate")));
//        $Amount=doubleval(trim(getFieldValue($item_arr,"Amount")));
//        $VAT=trim(getFieldValue($item_arr,"VAT"));
            $Qty = doubleval(trim(getFieldValue($item_arr, "Qty")));
//        $Rate=trim(getFieldValue($item_arr,"Rate"));
//        $Amount=trim(getFieldValue($item_arr,"Amount"));
            $VAT = str_replace(",", "", trim(getFieldValue($item_arr, "VAT")));
            $Rate = str_replace(",", "", trim(getFieldValue($item_arr, "Rate")));
            $Amount = str_replace(",", "", trim(getFieldValue($item_arr, "Amount")));

            if (!isset($item_arr['MRP'])) {
                $mrp = doubleval(str_replace(",", "", trim(getFieldValue($item_arr, "MRP2"))));
                $item_arr['MRP'] = $mrp;
            } else {
                $mrp = doubleval(str_replace(",", "", trim($item_arr['MRP'])));
            }

            //done by Nivedita on 03082018 need to review
            if (trim($mrp) == "" || trim($mrp) == 0) {
                $mrp = $Rate;
            }
            $VAT = 0;
            /////////////////////////////////////
//            if(isset($item_arr['TQty']) && trim($item_arr['TQty']) != ""){
//                $Qty = trim(getFieldValue($item_arr,"TQty"));
//            }else{
//                 $Qty = trim(getFieldValue($item_arr,"Qty"));   
//            }

            $chk_EAN = '&& $EAN!=""';
            $chk_rate = '&& $Rate!=""';
            $chk_vat = '&& $VAT!=""';
            $chk_CAR = '&& $CAR!=""';
            //$chk_amt= '&& $Amount!=""';
//        $dbl_rate='&&(is_double($Rate))';
//        $dbl_vat='&&(is_double($VAT))';
            $dbl_rate = '&&(is_numeric($Rate))';
            $dbl_vat = '&&(is_numeric($VAT))';
            //$dbl_amt='&&(is_double($Amount))';
            // print"<br>CAR:$CAR";
            if (trim($CAR) == "") {
                foreach ($car_skip_chainid as $id) {
                    if ($master_dealer_id == $id) {
                        $chk_CAR = '';
                    } else {
                        //     print"<br>CAR issue:$CAR"; 
                    }
                }
            }
            // print"<br>rate:$Rate";
            if (trim($Rate) != "") {
                if (is_numeric($Rate)) {
                    $Rate = $Rate;
                    // print"<br>rate:$Rate";
                } else {
                    // print"<br>rate issue:$Rate";
                }
            } else {
                foreach ($rate_skip_chainid as $id) {
                    if ($master_dealer_id == $id) {
                        //    print"rate skipped for $master_dealer_id<br>";
                        $chk_rate = '';
                        $dbl_rate = '';
                        $item_arr["Rate"] = 0;
                    } else {
                        //   print"<br>rate issue:$Rate"; 
                    }
                }
            }

            //  print"<br>vat:$VAT"; 
            if (trim($VAT) != "") {
                $VAT = doubleval($VAT);
                // print"<br>vat:$VAT"; 
            } else {
                foreach ($vat_skip_chainid as $id) {
                    if ($master_dealer_id == $id) {
                        $chk_vat = '';
                        $dbl_vat = '';
                    } else {
                        //     print"<br>vat issue:$VAT"; 
                    }
                }
            }

            //   print"<br>Amount:$Amount";
            if (trim($Amount) != " " && $Amount != 0) {
                if (is_numeric($Amount)) {
                    //    print"<br> in amt!=empty<br>";
                    ///  //$Amount=doubleval($Amount);
                    //   print"<br>Amount:$Amount";
                } else {
                    //       print"<br>Amountissue:$Amount";
                }
            } else {
                //   print"<br> in amt==empty<br>";
                foreach ($amt_calculate as $id) {
                    if ($master_dealer_id == $id) {
                        // print"<br>calculate:$Qty * $Rate:".doubleval($Qty*$Rate)."<br>";
                        $Amount = $Qty * $Rate;
                        if (is_numeric($Amount)) {
                            $item_arr["Amount"] = $Amount;
                            //       print $item_arr["Amount"];
                        } else {
                            //  print"<br>Amount issue:$Amount";
                        }
                    } else {
                        //  print"<br>Amount issue:$Amount"; 
                    }
                }
            }
            //  print"<br>masterdealerid=$master_dealer_id<br>";

            foreach ($ean_skip_chainid as $id) {
                if ($master_dealer_id == $id) {
                    $chk_EAN = '';
                }
            }

//              print"<br>an=$artno<br>ean=$EAN<br>car=$CAR<br>mrp=$mrp<br>vat=$VAT<br>qty=$Qty<br>rate=$Rate<br>amt=$Amount<br>";
//               echo "CAR=$chk_CAR<br>EAN=$chk_EAN<br>Rate=$chk_rate<br>$chk_vat<br>";
            if ($artno != "" && $mrp != "" && $Qty != "" && $Amount != "") {//.$chk_CAR .$chk_EAN .$chk_rate .$chk_vat){
                //if($chk_empty)
//                  print"<br>chkempty<br>";
                if (preg_match("/[0-9]/", $artno)) {
                    $pregFlag = 1;
                    //   print"<br> $artno true case---$pregFlag";
//                $fields['ArticleNo'] = $artno;
                } else {
                    $pregFlag = 0;
                    //     print"<br> $artno false case---$pregFlag";
                }
                if ($EAN != "") {
                    if (preg_match("/[0-9a-zA-Z]/", $EAN)) {
                        $pregFlag = 1;
                        //    print"<br> $EAN true case---$pregFlag";
                        //                $fields['EAN'] = $EAN;
                    } else {
                        $pregFlag = 0;
                        //     print"<br> $EAN false case---$pregFlag";
                    }
                    if ($master_dealer_id == 49 && trim($EAN) == "-" && trim($artno) == "-") {
                        $pregFlag = 1;
                    }
                }
                if ($CAR != "") {
                    if (preg_match("/[0-9a-zA-Z]/", $CAR)) {
                        $pregFlag = 1;
                        //   print"<br> $CAR true case---$pregFlag";
                        //                $fields['CAR'] = $CAR;
                    } else {
                        $pregFlag = 0;
                        //  print"<br> $CAR false case---$pregFlag";
                    }
                }
                //if( (is_double($Qty)) &&(trim($pregFlag)==1) &&(is_numeric($Amount)).$dbl_rate .$dbl_vat){//(is_double($mrp)) &&
                //   print"<br>$Amount<br>";
                if ((is_numeric($Qty)) && (trim($pregFlag) == 1) && (is_numeric($Amount)) . $dbl_rate . $dbl_vat) {//(is_double($mrp)) &&
                    $pushFlag = 1;
                    //    print"<br> Push Flag true case---$pushFlag";
                } else {
                    $pushFlag = 0;
                    //      print"<br> Push Flag false case---$pushFlag";
                }

                if ($pushFlag == 1) {
                    //     print "<br> in flag ITEM ARRAY true case: <br>";
                    //     print_r($item_arr);
                    //    print"<br>----------------------------------------------------<br>";
                    $items[] = $item_arr;
                    //   print_r($items);
                    //    print "<br><br>";
                } else {
                    //   print"pushflag=0<br>";
                }
            }
        }
    }
    // echo "**********************Complete data is*******************";
//  print_r($items);
    // echo "**********************End**************************";
    return($items);
}