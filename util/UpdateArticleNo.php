<?php

require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once "lib/db/DBConn.php";

$db= new DBConn();
//define("DEF_PROCESS_PATH" ,"/home/ykirad/dev/subversion/onlinePOS/vlcc_dt/home/Parsers/movedFiles_".$dt."_1/");
//$moved_files = "/home/ykirad/dev/subversion/onlinePOS/vlcc_dt/home/Parsers/movedFiles_".$dt."_1/";
//$moved_files = "/home/vlcc/public_html/vlcc_dt/home//Parsers/movedFiles_".$dt."_1/";
$moved_files = "/home/vlcc/public_html/vlcc_dt/home/Parsers/movedFiles_".$dt."_1/";

echo "\n\n";
//print_r($moved_files);
echo "\n\n";
//if(file_exists($moved_files)){
//    $dirs = scandir($moved_files);  
//   //echo count($dirs);
//    if(count($dirs)>0)
       // foreach($dirs as $dir){
//        echo "\n\n";
           // print_r($dir);
//            $c = str_replace("_", " ", $dir);
//            $chain_name = $db->safe(trim($c));
//            $chain_name="Future Retail Limited";
            $master_dealer_id = 2;
           $ch_name =  $db->fetchObject("select * from it_master_dealers where id=$master_dealer_id");
           if($ch_name){
//               print_r($ch_name);
               $dir = str_replace(" ","_",$ch_name->name);
               //echo $moved_files.$dir."\n";
              $sub_dir = $moved_files.$dir."/eanMissingPOs";
              echo $sub_dir."\n";
            //  $subdir =  scandir($moved_files.$dir);
              //foreach($subdir as $sub_dir){
//                  echo "\n\n";
//                  print_r($sub_dir);
                 // if($sub_dir=='eanMissingPOs'){
                      //echo "Yes\n";
//                      echo $moved_files.$dir."/".$sub_dir;
              if(file_exists($sub_dir)){
                     $child_sub_dir = scandir($sub_dir);
                     foreach($child_sub_dir as $child_dir){
                         $narr = explode(".",$child_dir);    
//                         print_r($narr);
                         if($narr[1]=="pdf"){
//                            echo $child_dir."\n";
                            // echo "select id,invoice_text,invoice_no,ctime from it_po where filename='$child_dir' and master_dealer_id=$master_dealer_id and id=1421\n";
                            $obj = $db->fetchObject("select id,invoice_text,invoice_no,ctime from it_po where filename='$child_dir' and master_dealer_id=$master_dealer_id");
                            if(isset($obj) && !empty($obj)){
                                $invoice_text = $obj->invoice_text;
                                $text = explode("\n",$invoice_text);
//                                echo count($text)."\n";
                                for($i=0;$i<count($text);$i++){
                                    $line = $text[$i];                                    
                                    if(preg_match('/(\d+)\s+\d+\s+(\d+\.\d+)\s+(\S+)\s+(EA)\s+(\d+\.\d+)\s+(\d+\.\d+)\s+(\S+)\s*%\s+(\S+)\s+%\s+(\d+\.\d+)/',$line,$mtch)){
                                     //   print_r($mtch);
                                        $ean = $db->safe($mtch[1]);
                                        $mrp = $mtch[2];
                                        $qty = $mtch[3];
                                        $car = $db->safe($mtch[4]);
                                        $rate = $mtch[5];
                                        $vat = $mtch[7]+$mtch[8];
                                        $amt = $mtch[9];
                                        
                                        $master_item = $db->fetchObject("select * from it_master_items where itemcode=$ean");
                                        if(isset($master_item) && !empty($master_item)){
                                            $master_item_id = $master_item->id;
                                        }else{
                                            $master_item_id = "";
                                        }
                                        //fetch article number
                                        $i++;
                                        $line = $text[$i];
                                        if(preg_match('/(\d+)\s+\S+\s+\d+\.\d+\s+\d+\.\d+/',$line,$matches)){
                                            $article_no = $db->safe($matches[1]);
                                        }
                                        $item[] = array(
                                            "master_item_id"=>$master_item_id,
                                            "eancode"=>$ean,
                                            "article_no"=>$article_no,
                                            "mrp"=>$mrp,
                                            "vat"=>$vat,
                                            "rate"=>$rate,
                                            "qty"=>$qty,
                                            "pack_type"=>$car,
                                            "amt"=>$amt
                                        );
                                        
                                        //print_r($item);
                                        $get_dealer_itm_id = $db->fetchObject("select dealer_item_id from it_po_items where po_id=$obj->id and master_item_id=$master_item_id and mrp=$mrp and vat=$vat and cost_price=$rate and qty=$qty and pack_type=$car"); 
                                     //   echo "select dealer_item_id from it_po_items where po_id=$obj->id and master_item_id=$master_item_id and mrp=$mrp and vat=$vat and cost_price=$rate and qty=$qty and pack_type=$car\n";
                                        
                                        if(isset($get_dealer_itm_id) && !empty($get_dealer_itm_id)){
                                           $dealer_itm = $db->fetchObject("select * from it_dealer_items where id=$get_dealer_itm_id->dealer_item_id and master_dealer_id=$master_dealer_id");
                                        //   echo "select * from it_dealer_items where id=$get_dealer_itm_id->dealer_item_id and master_dealer_id=$master_dealer_id\n";
                                           if(isset($dealer_itm) && !empty($dealer_itm) && (trim($dealer_itm->itemcode)=="" || trim($dealer_itm->itemcode)==NULL)){
                                               echo "$child_dir<>$obj->invoice_no<>$ean<>$article_no\n";
                                               echo "update it_dealer_items set itemcode=$article_no,updatetime=now(),updatedby=-1 where id=$dealer_itm->id\n";
                                               $db->execUpdate("update it_dealer_items set itemcode=$article_no,updatetime=now(),updatedby=-1 where id=$dealer_itm->id");
                                               echo "\n*******************************************************************\n";
                                              // break;
                                               
                                           }
                                           
                                        }
                                        
                                        }
                            }
                               // echo $invoice_text."\n";
                            }
                         }
                     }
              }
                 // }
             // }
           }
       // }
//}
