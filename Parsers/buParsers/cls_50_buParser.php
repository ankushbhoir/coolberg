<?php
require_once "lib/db/DBConn.php";

class cls_50_buParser{
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
            
            if(trim($line)==""){ continue; }

            if(preg_match('/(Delivery\s+&\s+Billing).*/',$line,$matches)){
              //  print "<br> IN D IF: <br>";
                $start_line_no = $lineno;
              //  print "<br> START LINE NO: $start_line_no <br>";
                break;
            }

        }
         for ($lineno = $start_line_no ; $lineno < $numlines; $lineno++) {
            $line =  $lines[$lineno];

            if(preg_match('/(Article\s+EAN).*/',$line,$matches)){
                $end_line_no = $lineno-1;
                break;
            }
        }
        $cnt=0;
        print "<br> START LINE NO: $start_line_no <br>";
        print "<br> END LINE NO: $end_line_no <br>";
        return;
        $regex="/(?<addr>.{0,74})/";
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

        $shipping_address = str_replace("Billing","",$shipping_address);
        $shipping_address = str_replace("Address","",$shipping_address);
        $shipping_address = str_replace("Delivery &","",$shipping_address);
        $shipping_address = str_replace("Delivery","",$shipping_address);
        $shipping_address = str_replace("Terms","",$shipping_address);
            
        print "<br><br> SHIPPING ADDRESS: $shipping_address <br>";
       // return $shipping_address;
        
        $no_spaces = str_replace(" ", "", $shipping_address);
        $no_spaces_db = $db->safe(trim($no_spaces));
        print "<br> NO SPACE: $no_spaces <br>";
        $check = " replace(shipping_address ,' ','') = $no_spaces_db ";
        print "<br> CHECK : $check <br>";
        $query = " select * from it_shipping_address where $check "; 
        print "<br> QUERY: $query <br>";
      
        $sobj = $db->fetchObject($query);
        if(isset($sobj)){
            $db->closeConnection();
           // return $shipping_address;
            $inimasterdealerid = $sobj->master_dealer_id;
            print "<br>MASTER Dealer ID : $inimasterdealerid </br>";
            return $shipping_address."::".$inimasterdealerid;
        }else{
            $db->closeConnection();
            print "<br>CALL NEXT 50_1 BU PARSER (Write new BU Parser)<br>";
            // $clsname = "cls_5_1_buParser";
            // if(file_exists(ROOTPATH."home/Parsers/buParsers/".$clsname.".php")){
                   
            //         require_once ROOTPATH."home/Parsers/buParsers/$clsname.php";
            //         $parser = new $clsname();
            //         $db->closeConnection();
            //         $shipping_address = $parser->process($file_text);
                    
            //         return $shipping_address;
            // }else{
            //     $db->closeConnection();
                return "NotFound::-1";
            // }
            
        }
    }
}

