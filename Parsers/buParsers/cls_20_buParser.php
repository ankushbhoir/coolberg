<?php
require_once "lib/db/DBConn.php";
//Premarc Pecan
class cls_20_buParser{
    public function __construct() {
        
    }
    
    public function process($file_text){
        $text = file_get_contents($file_text);        
        $db = new DBConn();
        $lines = explode("\n", $text);
        $numlines = count($lines);        
       // print "<br>CNT: $numlines";
        $chain_name = "NotFound";
        $start_line_no = 0;
        $end_line_no = 0;
        
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);            
            if(trim($line)==""){ continue; }
            
            if(preg_match("/Ship\s+To/",$line,$matches)){                
                $start_line_no = $lineno + 1;                
            }
            
            if(preg_match("/Material\s+No\s+\//",$line,$matches)){
                $end_line_no = $lineno - 1;
                break;
            }          
        }
        
      //  print "<br> START LINE NO: $start_line_no <br>";
     //   print "<br> END LINE NO: $end_line_no <br>";       
        $shipping_address = "";
       for ($lineno = $start_line_no; $lineno < $end_line_no; $lineno++) {
            $line = trim($lines[$lineno]);  
           $shipping_address .= " ".trim($line); 
       }
            
        print "<br><br> SHIPPING ADDRESS: $shipping_address <br>";
        
        $no_spaces = str_replace(" ", "", $shipping_address);
        $no_spaces_db = $db->safe(trim($no_spaces));
     //   print "<br> NO SPACE: $no_spaces <br>";
        $check = " replace(shipping_address ,' ','') = $no_spaces_db ";
     //   print "<br> CHECK : $check <br>";
        $query = " select * from it_shipping_address where $check "; 
        print "<br> QUERY: $query <br>";
      
        $sobj = $db->fetchObject($query);
         $inimasterdealerid = "";
         
         if(isset($sobj)){
          $db->closeConnection();
          $inimasterdealerid = $sobj->master_dealer_id;
        //  print "<br>MASTER Dealer ID : $inimasterdealerid </br>";
            return $shipping_address."::".$inimasterdealerid;
        }else{           
          $db->closeConnection();
                return "NotFound::-1";                      
        }
    }
}

