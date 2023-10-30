<?php
require_once "lib/db/DBConn.php";

class cls_22_buParser{
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
        //    print "<br> Line $lineno: $line";
            
            if(preg_match('/Address\s*:\s*(.*)/',$line,$matches)){                
                $start_line_no = $lineno;                
                print "<br> START LINE NO: $start_line_no <br>";
                break;
            }               
        }
         for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            
            if(trim($line)==""){ continue; }
          //  print "<br> Line $lineno: $line";
            
            if(preg_match('/To\,/',$line,$matches)){                                
                $end_line_no = $lineno;
//                $end_line_no--;
//                $end_line_no--;
                print "<br> ENd LINE NO: $end_line_no <br>";
                break;
            }               
        }
     
        $cnt=0;
        print "<br> START LINE NO: $start_line_no <br>";
        print "<br> END LINE NO: $end_line_no <br>";
        $regex="/Address\s*:\s*(.*)/";
        $regex1="/(.*)\s+GST\s+Tin\s*:/";
        $shipping_address = "";
        for($i=$start_line_no;$i <= $end_line_no;$i++){
            $cnt++;
             $line = trim($lines[$i]);            
          //   print "<br><br> LINE $i: $line ";
             if(trim($line)==""){ continue; }                        
             $result = array();
             if(preg_match($regex,$line,$result)){                
                 $shipping_address .= " ".$result[1];
             }else  if(preg_match($regex1,$line,$result)){                
                 $shipping_address .= " ".$result[1];
             }
             
             
        }
            
        print "<br><br> SHIPPING ADDRESS: $shipping_address <br>";
      
        $no_spaces = str_replace(" ", "", $shipping_address);
        $no_spaces_db = $db->safe(trim($no_spaces));
        print "<br> NO SPACE: $no_spaces <br>";
        $check = " replace(shipping_address ,' ','') = $no_spaces_db ";
        print "<br> CHECK : $check <br>";
        $query = " select * from it_shipping_address where $check "; 
        print "<br> QUERY: $query <br>";
      
        $sobj = $db->fetchObject($query);
        print_r($sobj);
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

