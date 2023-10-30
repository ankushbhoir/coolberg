<?php
require_once "lib/db/DBConn.php";

class cls_33_buParser{
    public function __construct() {
        
    }
    
    public function process($file_text){
        $text = file_get_contents($file_text);        
        $db = new DBConn();
        $lines = explode("\n", $text);
        $numlines = count($lines);        
    //    print "<br>CNT: $numlines";
        $chain_name = "NotFound";
        $start_line_no = 0;
        $end_line_no = 0;
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            
            if(trim($line)==""){ continue; }
        //    print "<br> Line $lineno: $line";            
            if(preg_match('/Store\s+Name\s*:/',$line,$matches)){                
                $start_line_no = $lineno+2;                             
                break;
            }else if(preg_match('/Name\s*:/',$line)==1){
                 $start_line_no = $lineno+3;                             
                break;
            }          
        }
       
          for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            
            if(trim($line)==""){ continue; }               
            if(preg_match('/Email\s*:\s*cp/',$line,$matches)){                
                $end_line_no = $lineno-1;                             
                break;
            }          
        }
                
        print "<br> START LINE NO: $start_line_no <br>";
        print "<br> END LINE NO: $end_line_no <br>";
        $regex="/(?'addr'^.{0,50})/";
        
//        $result = array();
//        $regex = "/(.*)/";
//        $regex="/^(?<addr>.{0,40})/";
        $shipping_address = "";
        for($i=$start_line_no;$i <= $end_line_no;$i++){            
             $line = $lines[$i]; 
             $line1 = preg_replace("/[^a-zA-Z0-9.,:$@\/!#%\&\*\(\)\-\_\;]/"," ",$line);
//             echo $line1."<br>";
             if(trim($line1)==""){ continue; }                                     
             if(preg_match($regex,$line1,$result)){
               //  echo $regex."<><><><><>".$line1."<br>";
//                 print "<br>";
               //  print_r($result);
                 $shipping_address .= " ".$result['addr'];
             }
             
             
        }
            
        print "<br><br> SHIPPING ADDRESS: $shipping_address <br>";       
        
        $no_spaces = str_replace(" ", "", $shipping_address);
        $no_spaces_db = $db->safe(trim($no_spaces));
       // print "<br> NO SPACE: $no_spaces <br>";
        $check = " replace(shipping_address ,' ','') = $no_spaces_db ";
      //  print "<br> CHECK : $check <br>";
        $query = " select * from it_shipping_address where $check "; 
        print "<br> QUERY: $query <br>";
      
        $sobj = $db->fetchObject($query);
        if(isset($sobj)){
            $db->closeConnection();           
            $inimasterdealerid = $sobj->master_dealer_id;
            print "<br>MASTER Dealer ID : $inimasterdealerid </br>";
            return $shipping_address."::".$inimasterdealerid;
        }else{
          //  echo "Inside 1";
                $start_line_no = 0;
        $end_line_no = 0;
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            
            if(trim($line)==""){ continue; }
        //    print "<br> Line $lineno: $line";            
            if(preg_match('/Store\s+Name\s*:/',$line,$matches)){                
                $start_line_no = $lineno+2;                             
                break;
            }        
        }
       
          for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            
            if(trim($line)==""){ continue; }               
            if(preg_match('/Email\s*:\s*cp/',$line,$matches)){                
                $end_line_no = $lineno-2;                             
                break;
            }          
        }
                
        print "<br> START LINE NO: $start_line_no <br>";
        print "<br> END LINE NO: $end_line_no <br>";
            $regex1="/(?'addr'^.{0,100})/";
             $shipping_address = "";
        for($i=$start_line_no;$i <= $end_line_no;$i++){            
             $line1 = $lines[$i];              
             echo $line1."<br>";
             if(trim($line1)==""){ continue; }                                     
             if(preg_match($regex1,$line1,$result)){
                // echo $regex1."<><><><><>".$line1."<br>";
//                 print "<br>";
//                 print_r($result);
                 $shipping_address .= " ".$result['addr'];
             }                          
        }
            
        print "<br><br> SHIPPING ADDRESS: $shipping_address <br>";    
        
         $no_spaces = str_replace(" ", "", $shipping_address);
        $no_spaces_db = $db->safe(trim($no_spaces));
       // print "<br> NO SPACE: $no_spaces <br>";
        $check = " replace(shipping_address ,' ','') = $no_spaces_db ";
      //  print "<br> CHECK : $check <br>";
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
            
              // 
        }
    }
}

