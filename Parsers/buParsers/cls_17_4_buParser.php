<?php
require_once "lib/db/DBConn.php";
//Olympia
class cls_17_4_buParser{
    public function __construct() {
        
    }
    
    public function process($file_text){
        print"in buParser 17<br>";
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
            print "<br> Line $lineno: $line";
            $regex1 = "/(Despatch\s+To).*/";
            $regex2 = "/(Supplier)/";
            print "<br> Regex1: $regex1 <br>";
            print "<br> Regex2: $regex2 <br>";
            
            if(preg_match($regex1,$line,$matches)){
                print "<br> IN D IF: <br>";
                $start_line_no = $lineno + 2;
                print "<br> START LINE NO: $start_line_no <br>";
            }
            if($start_line_no > 0){
            if(preg_match($regex2,$line,$matches)){
                $end_line_no = $lineno - 1;
            }
            if($end_line_no > 0){
              break;   
            }
            }
        }
        $cnt=0;
        print "<br> START LINE NO: $start_line_no <br>";
        print "<br> END LINE NO: $end_line_no <br>";
        $regex="/^(?<addr>.{0,50})/";
        $shipping_address = "";
        for($i=$start_line_no;$i <= $end_line_no;$i++){
            $cnt++;
//            if($cnt==2){
//                continue;
//            }
           $line = trim($lines[$i]); 
//           if(preg_match($regex1,$line,$matches)){
//                 $line = trim(str_replace("Shipping Address ","",$line)); 
//           }
              
             print "<br><br> LINE $i: $line ";
             if(trim($line)==""){ continue; }                        
             $result = array();
             if(preg_match($regex,$line,$result)){
                 print "<br>result=";
                 print_r($result);
                 print "<br>";
                 print_r($result);
                 $shipping_address .= " ".$result['addr'];
             }
             
             
        }
            
        print "<br><br> SHIPPING ADDRESS: $shipping_address <br>";
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
          $db->closeConnection();
//            print "<br>CALL NEXT 17_5 BU PARSER <br>";
//            $clsname = "cls_17_5_buParser";
//            if(file_exists("buParsers/".$clsname.".php")){
//                   
//                    require_once "buParsers/$clsname.php";
//                    $parser = new $clsname();
//                    $response = $parser->process($file_text);
//                    return $response;
//            }else{
                return "NotFound::-1";
//            }           
        }
    }
}

