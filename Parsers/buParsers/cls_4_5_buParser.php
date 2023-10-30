<?php
require_once "lib/db/DBConn.php";

class cls_4_5_buParser{
    public function __construct() {
        
    }
    
    public function process($file_text){
       
        
        $text = file_get_contents($file_text);        
        
        $lines = explode("\n", $text);
        $numlines = count($lines);        
        print "<br>CNT: $numlines";
        $chain_name = "NotFound";
        $start_line_no = 0;
        $end_line_no = 0;
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            
            //if(trim($line)==""){ continue; }
            if(trim($line)!=""){ 
                print "<br> Line $lineno: $line";
                if(preg_match('/(Shipping\s+?Address:)/',$line,$matches)){
                    
                    $start_line_no = $lineno+1;
                    //chk if next line is blank                    
                    $ldata = $lines[$start_line_no];
                    if(trim($ldata)==""){
                       $start_line_no = $start_line_no +1 ;    
                    }
                }

                if(preg_match('/(TIN\s+?No:)/',$line,$matches)){
                 $end_line_no = $lineno;
                }
            }
         if($start_line_no>0 && $end_line_no>0){
             break;
         }
        }
        $response=getAddress1($lines,$start_line_no,$end_line_no);
        return $response; 
    
    }
    }


 function getAddress1($lines,$start_line_no,$end_line_no1){
        $jump= array(1,2,3,4,5,6);
        $db = new DBConn();
        //$shipping_address = "Not Found";
        $found=0;
        $len=count($jump);
        print"<br> jumparray size= $len<br>";
        print "<br> END LINE NO: $end_line_no1 <br>";
//        for($i=0;$i<$len;$i++){
//            print"chk$i and $jump[$i] ";
//        }
        for($i=0;$i<$len;$i++){
            print"<br> endline index=$i and $jump[$i]<br>";
            $end_line_no = $end_line_no1-$jump[$i];
                   
//                $cnt=0;
                print "<br> START LINE NO: $start_line_no <br>";
                print "<br> END LINE NO: $end_line_no <br>";
                $regex="/^(?<addr>.{0,58})/";
                $shipping_address = " ";
                for($j=$start_line_no;$j <= $end_line_no;$j++){
                   // $cnt++;
        //            if($cnt==2){
        //                continue;
        //            }
                     $line = trim($lines[$j]);            
                     print "<br><br> LINE $j: $line ";
                     if(trim($line)!=""){                         
                        $result = array();
                            if(preg_match($regex,$line,$result)){
                                print "<br>";
                                print_r($result);
                                $shipping_address .= " ".$result['addr'];
                            }
                      }
                } // end of for($i=$start_line_no;$i <= $end_line_no;$i++)
                     $found= chkDB1($shipping_address, $db);
                     print"<br>found=$found<br>";
                     if(trim($found) == 1){
                         print"<br>break when found";
                         break;
                     }else{ 
                     print"<br>go to loop 2<br>";
                     continue;
                     }
                     //$i++;
                     print"<br> i=$i<br>";
                     print"<br>go to loop <br>";
        }// end of jump loop  for($i=0;$i<$len;$i++)
         
        $foundstr = explode("::",$found);   
        print "<br>Returned string :<br>"; 
        print_r($foundstr);
        if($foundstr[0]=="1"){
                     print"<br>return shipping address<br>";
                     
                     return $shipping_address."::".$foundstr[1];
                     }
                     else{
                         print"<br>return  not found<br>";
                         return "Not Found::-1";
                        // return $shipping_address;
                     }
                        
}    
             
        //fn db 0/1
function chkDB1($shipping_address, $db){
        print "<br><br> SHIPPING ADDRESS: $shipping_address <br>";
        //shld chk with db 
        // blnk  result set then call other bu parser for fetching complete
        // named cls_3_1_buParser.php
        //return $shipping_address;
        $no_spaces = str_replace(" ", "", $shipping_address);
        $no_spaces_db = $db->safe(trim($no_spaces));
        print "<br> NO SPACE: $no_spaces <br>";
        $check = " replace(shipping_address ,' ','') = $no_spaces_db ";
        print "<br> CHECK : $check <br>";
        $query = " select * from it_shipping_address where $check "; 
        print "<br> QUERY: $query <br>";
        
        $sobj = $db->fetchObject($query);
          $inimasterdealerid = "";
        if(isset($sobj)){ 
            $db->closeConnection(); 
            print"<br>return 1<br>";
            $inimasterdealerid = $sobj->master_dealer_id;
            print "<br>MASTER Dealer ID : $inimasterdealerid </br>";
            return "1"."::".$inimasterdealerid;
        }else{
            $db->closeConnection();
            print"<br>return 0<br>";
            return "0"."::"."-1";
        }
           
        }