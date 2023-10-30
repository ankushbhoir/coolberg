<?php
require_once "lib/db/DBConn.php";

class cls_3_3_buParser{
    public function __construct() {
        
    }
    
    public function process($file_text){
        $text = file_get_contents($file_text);        
        $db = new DBConn();
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
                $end_line_no = $lineno-4;
                }
            }

        }
        $cnt=0;
        print "<br> START LINE NO: $start_line_no <br>";
        print "<br> END LINE NO: $end_line_no <br>";
        $regex="/^(?<addr>.{0,60})/";
        $shipping_address = "";
        for($i=$start_line_no;$i <= $end_line_no;$i++){
            $cnt++;
//            if($cnt==2){
//                continue;
//            }
             $line = trim($lines[$i]);            
             print "<br><br> LINE $i: $line ";
             if(trim($line)==""){ continue; }                        
             $result = array();
             if(preg_match($regex,$line,$result)){
                 print "<br>";
                 print_r($result);
                 $shipping_address .= " ".$result['addr'];
             }
             
             
        }
            
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
             $inimasterdealerid = $sobj->master_dealer_id;
          print "<br>MASTER Dealer ID : $inimasterdealerid </br>";
            return $shipping_address."::".$inimasterdealerid;
        }else{
            
            print "<br>CALL NEXT 3_4 BU PARSER <br>";
            $clsname = "cls_3_4_buParser";
            if(file_exists("buParsers/".$clsname.".php")){
                   
                    require_once "buParsers/$clsname.php";
                    $parser = new $clsname();
                   $db->closeConnection();
                    $response = $parser->process($file_text);
                    return $response;
            }else{
               $db->closeConnection();
                return "NotFound::-1";
            }
            
        }
    }
}

