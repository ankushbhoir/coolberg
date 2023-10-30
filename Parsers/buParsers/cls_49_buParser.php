<?php
require_once "lib/db/DBConn.php";

class cls_49_buParser{
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
            $line = $lines[$lineno];
            
            if(trim($line)==""){ continue; }      

            if(preg_match('/V\-Mart\s+Retail\s+Limited\s*-\s*.*/',$line,$matches)){            
                $start_line_no = $lineno+1;            
            }
            
            if(preg_match('/Phone\s*:\s*\S+\,\s*E\-Mail/',$line,$matches)){
                $end_line_no = $lineno-1;
                break;
            }else if(preg_match('/Ph\s+No.\s*:?\s*\d+\s+Email/',$line)==1){
                 $end_line_no = $lineno-1;
                break;
            }
        }
        $cnt=0;
        echo "sline: ".$start_line_no."<br>";
        echo "eline: ".$end_line_no."<br>";
          $regex="/(.*)/";
        $shipping_address = "";
        for($i=$start_line_no;$i <= $end_line_no;$i++){
            $cnt++;                      
             $result = array();
             $line = $lines[$i];
             if(preg_match($regex,$line,$result)){
                 $shipping_address .= " ".$result[1];
             }                          
        }
            echo "<br>S address: ".$shipping_address."<br>";
        $no_spaces = str_replace(" ", "", $shipping_address);
        $no_spaces_db = $db->safe(trim($no_spaces));     
        $check = " replace(shipping_address ,' ','') = $no_spaces_db ";     
        $query = " select * from it_shipping_address where $check ";     
      
        $sobj = $db->fetchObject($query);
         $inimasterdealerid = "";
         
         if(isset($sobj)){
          $db->closeConnection();
          $inimasterdealerid = $sobj->master_dealer_id;
          print "<br>MASTER Dealer ID : $inimasterdealerid </br>";
          return $shipping_address."::".$inimasterdealerid;
        }else{
          $db->closeConnection();
          return "NotFound::-1";
        }
    }
}

