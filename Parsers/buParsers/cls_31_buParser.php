<?php
require_once "lib/db/DBConn.php";

class cls_31_buParser{
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
//            print "<br> Line $lineno: $line";
            
            if(preg_match('/Ship\s+To\s*:/',$line)==1){ 
                $lineno++;
                $start_line_no = $lineno;                
                break;
            }          
        }
        
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);   
//            echo "Data=>".$line."\n";
            if(preg_match('/GST\s+Number\s*:/',$line)==1){
                $lineno--;  
                $lineno--;  
                $end_line_no = $lineno;                
                break;
            }          
        }
     
        $cnt=0;
        print "<br> START LINE NO: $start_line_no <br>";
        print "<br> END LINE NO: $end_line_no <br>";
        $regex="/(.{60}$)/";  
      //  $regex="/(.{40}$)/";  
        $shipping_address = "";
        for($i=$start_line_no;$i <= $end_line_no;$i++){
            $cnt++;
             $line = trim($lines[$i]);               
             if(trim($line)==""){ continue; }                        
             $result = array();
             if(preg_match($regex,$line,$result)){
                 $shipping_address .= " ".$result[1];
                 echo "Addr1: $shipping_address<br>";
             }                       
        }
            
        print "<br><br> SHIPPING ADDRESS: $shipping_address <br>";
      
        $no_spaces = str_replace(" ", "", $shipping_address);
        $no_spaces_db = $db->safe(trim($no_spaces));
       // print "<br> NO SPACE: $no_spaces <br>";
        $check = " replace(shipping_address ,' ','') = $no_spaces_db ";
       // print "<br> CHECK : $check <br>";
        $query = " select * from it_shipping_address where $check "; 
        print "<br> QUERY: $query <br>";
      
        $sobj = $db->fetchObject($query);
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

