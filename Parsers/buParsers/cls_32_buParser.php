<?php
require_once "lib/db/DBConn.php";
//Trent
class cls_32_buParser{
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
            if(preg_match('/VIJETHA\s+VIZAG\s+WAREHO/',$line)==1){
                $start_line_no = $lineno + 2;
            } 
            if(preg_match('/Credit\s+Days/',$line)==1){
                $end_line_no = $lineno -1;
            } 
        }
        $cnt=0;
//        echo $start_line_no;
        $regex="/^(?<addr>.{0,55})/";
        $shipping_address = "";
        for($i=$start_line_no;$i <= $end_line_no;$i++){
            $cnt++;
           $line = $lines[$i];           
                        
             if(trim($line)==""){ continue; }                        
             $result = array();
             if(preg_match($regex,$line,$result)){
                 $shipping_address .= " ".$result[1];
             }                       
        }
            
        print "<br><br> SHIPPING ADDRESS: $shipping_address <br>";        
        $no_spaces = str_replace(" ", "", $shipping_address);
        $no_spaces_db = $db->safe(trim($no_spaces));       
        $check = " replace(shipping_address ,' ','') = $no_spaces_db ";       
        $query = " select * from it_shipping_address where $check "; 
        //print "<br> QUERY: $query <br>";
      
        $sobj = $db->fetchObject($query);
         $inimasterdealerid = "";
         
         if(isset($sobj)){
            $db->closeConnection();
            $inimasterdealerid = $sobj->master_dealer_id;          
            return $shipping_address."::".$inimasterdealerid;
        }else{           
            $db->closeConnection();
                return "NotFound::-1";         
    }
    }
}

