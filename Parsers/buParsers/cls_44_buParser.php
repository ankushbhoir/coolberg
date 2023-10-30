<?php
require_once "lib/db/DBConn.php";

class cls_44_buParser{
    public function __construct() {
        
    }
    
    public function process($file_text){
        $text = file_get_contents($file_text);        
        $db = new DBConn();
        $lines = explode("\n", $text);
        $numlines = count($lines);        
        $chain_name = "NotFound";
        $start_line_no = 0;
        $end_line_no = 0;        
  
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = $lines[$lineno];            
            if(trim($line)==""){ continue; }
            if(preg_match('/Delivery\s+Details/',$line)){
              $lineno++;
              $lineno++;
              $start_line_no = $lineno;
              break;
            }
        }
        
          for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = $lines[$lineno];            
            if(trim($line)==""){ continue; }
            if(preg_match('/ITEM\s+CODE\s+ITEM\s+DESCRIPTION/',$line)){
              $lineno--;
              $lineno--;
              $end_line_no = $lineno;
              break;
            }
        }
                 
        print "<br> START LINE NO: $start_line_no <br>";
        print "<br> END LINE NO: $end_line_no <br>";
        $regex="/(?<addr>.{0,60}$)/";
        $shipping_address = "";
        for($i=$start_line_no;$i <= $end_line_no;$i++){            
             $line = $lines[$i];                         
             if(trim($line)==""){ continue; }                        
             $result = array();
             if(preg_match($regex,$line,$result)){                 
                 $shipping_address .= " ".$result['addr'];
             }                          
        }
            
        print "<br><br> SHIPPING ADDRESS: $shipping_address <br>";       
        $no_spaces = str_replace(" ","", $shipping_address);        
        $no_spaces_db = $db->safe(trim($no_spaces));        
        $check = " replace(shipping_address ,' ','') = $no_spaces_db ";        
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
            print "<br>CALL NEXT 44_1 BU PARSER <br>";
            $clsname = "cls_44_1_buParser";
            if(file_exists("../Parsers/buParsers/".$clsname.".php")){
                   $db->closeConnection();
                    require_once "../Parsers/buParsers/$clsname.php";
                    $parser = new $clsname();
                    $response = $parser->process($file_text);
                    return $response;
            }else{
              $db->closeConnection();
              return "NotFound::-1";
            }
            
        }
    }
}

