<?php
require_once "lib/db/DBConn.php";
define("JUMP", 1);

class cls_26_buParser{
    public function __construct() {
        
    }
    
    public function process($file_text){
    //    echo "***************************************************************";
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
//            print "<br> Line $lineno: $line";
            if(preg_match('/Delivery\s+\&\s+Billing\s+Address\s+/',$line)==1){
           //     echo "Current Line: $line";
                echo $lineno += 2*JUMP;
                 $start_line_no = $lineno;               
                 echo "START Line Found: $start_line_no<br>";
                 echo $line;
                // exit;
            }else if(preg_match('/MC\s+MC\s+Description/',$line)==1){
                $lineno -= 2    *JUMP;
                 $end_line_no = $lineno;
             //    echo "END Line Found: $end_line_no<br>";
                 break;
            }
        }

        $cnt=0;
        print "\n START LINE NO: $start_line_no \n";
       print "\n END LINE NO: $end_line_no \n";

        $regex="/^(?<addr>.{0,50})/";
        $shipping_address = "";
        for($i=$start_line_no;$i <= $end_line_no;$i++){
            $cnt++;
             $line = trim($lines[$i]);            
            print "<br><br> LINE $i: $line ";
             if(trim($line)==""){ continue; }                        
             $result = array();
             if(preg_match($regex,$line,$result)){
             //    print "<br>";
              //   print_r($result);
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
        
       
         if(isset($sobj)){
             $db->closeConnection();
          $inimasterdealerid = $sobj->master_dealer_id;
      //    print "<br>MASTER Dealer ID : $inimasterdealerid </br>";
            return $shipping_address."::".$inimasterdealerid;
        }else{
            $db->closeConnection();
            return "NotFound::-1";            
        }
    }
}

