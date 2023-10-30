<?php
require_once "lib/db/DBConn.php";
//Metro Cash & carry
class cls_14_1_buParser{
    public function __construct() {
        
    }
    
    public function process($file_text){
        

        $text = file_get_contents($file_text);        

        $db = new DBConn();
        $lines = explode("\n", $text);
        $numlines = count($lines);        
      //  print "<br>CNT: $numlines";
        //exit;
        $chain_name = "NotFound";
        $start_line_no = 0;
        $end_line_no = 0;
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            
            if(trim($line)==""){ continue; }   //echo $line;
           // exit;                                                      
            if(preg_match('/METRO\s+CASH\s+\&\s+CARRY\s+INDIA\s+PVT.\s+LTD./',$line,$matches)){                
                $start_line_no = $lineno+2;
               // print "<br> START LINE NO: $start_line_no <br>";
             //  exit;
            }
            
          
            
            if(preg_match('/Delivery\s+to\s+Goods\s+Receiving\s+Food/',$line,$matches)){
                $end_line_no = $lineno-2;
break;
            }
                
                
                if(preg_match('/Fax:\s+METRO\s+CASH\s+&\s+CARRY\s+INDIA\s+PVT[.]LTD[.]/',$line,$matches)){                
                $start_line_no = $lineno+2;
                print "<br> STAasRT LINE NO: $start_line_no <br>";
              //  exit;
                
            }


        }
        
              
       print "<br> START LINE NO: $start_line_no <br>";
      //  exit;
        print "<br> END LINE NO: $end_line_no <br>";
        //exit;
        //$regex="/^(?<addr>.{0,44})/";
        $shipping_address = "";
        echo $start_line_no."=====".$end_line_no;

        for($i=$start_line_no;$i <= $end_line_no;$i++){ 
             $line = $lines[$i]; 
             $shipping_address .= " ".$line;            

        }

        print "<br><br> SHIPPING ADDRESSasas: $shipping_address <br>";        
       // exit;
       echo $no_spaces = str_replace(" ", "", $shipping_address);

         $no_spaces_db = $db->safe(trim($no_spaces));
      
      //  print "<br> NO SPACE: $no_spaces <br>";
        $check = " replace(shipping_address ,' ','') = $no_spaces_db ";
     //   print "<br> CHECK : $check <br>";        
        echo $query = "select * from it_shipping_address where $check "; 
       // exit;
       
        //print "<br> QUERY: $query <br>";
      
        $sobj = $db->fetchObject($query);
        
        print_r($sobj);
        
         $inimasterdealerid = "";
         
         if(isset($sobj)){
          $inimasterdealerid = $sobj->master_dealer_id;
         print "<br>MASTER Dealer ID : $inimasterdealerid </br>";
        
            return $shipping_address."::".$inimasterdealerid;
        }else{               
            //For PDF another format
            $new_start_line_no = 0;
        $new_end_line_no = 0;
             for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            
            if(trim($line)==""){ continue; }                                                        
            if(preg_match('/METRO\s+CASH\s+\&\s+CARRY\s+INDIA\s+PVT\s+LTD/',$line,$matches)){                
                $new_start_line_no = $lineno+1;
               print "<br> START LINE NO: $new_start_line_no <br>";
            }
            
            if(preg_match('/TEL\s*:/',$line,$matches)){
                $new_end_line_no = $lineno;
                $new_end_line_no--;
                break;
            }         
        }
              
     //   print "<br> New START LINE NO: $new_start_line_no <br>";
     //   print "<br> New END LINE NO: $new_end_line_no <br>";
        //$regex="/^(?<addr>.{0,44})/";
       
        $shipping_address = "";
        for($i=$new_start_line_no;$i <= $new_end_line_no;$i++){ 
            $line = $lines[$i]; 
            echo $shipping_address .= " ".$line;            
        }
            
        print "<br><br>aasas SHIPPING ADDRESS: $shipping_address <br>";        
        $no_spaces = str_replace(" ", "", $shipping_address);
        $no_spaces_db = $db->safe(trim($no_spaces));
     //   print "<br> NO SPACE: $no_spaces <br>";
        $check = " replace(shipping_address ,' ','') = $no_spaces_db ";
    //    print "<br> CHECK : $check <br>";        
       echo $query = " select * from it_shipping_address where $check "; 
        print "<br> QUERY: $query <br>";
      //exit;
        $sobj = $db->fetchObject($query);
         $inimasterdealerid = "";
               // return "NotFound::-1";      
         
          if(isset($sobj)){
          $inimasterdealerid = $sobj->master_dealer_id;
      //    print "<br>MASTER Dealer ID : $inimasterdealerid </br>";
            return $shipping_address."::".$inimasterdealerid;
        }else{                                     
            //For PDF another format
            $again_new_start_line = 0;
        $again_new_end_line = 0;
             for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            
            if(trim($line)==""){ continue; }                                                        
            if(preg_match('/Order\s+for\s+Store\s+no.\s*:\s*\S+\s+Address/',$line,$matches)){                
                $again_new_start_line = $lineno;
                $again_new_end_line = $lineno;
                //print "<br> START LINE NO: $new_start_line_no <br>";
            }                            
        }
              
     //   print "<br> AGAIN New START LINE NO: $again_new_start_line <br>";
     //   print "<br> AGAIN New END LINE NO: $again_new_end_line <br>";
        //$regex="/^(?<addr>.{0,44})/";
        $shipping_address = "";
        for($i=$again_new_start_line;$i <= $again_new_end_line;$i++){ 
            $line = $lines[$i]; 
         //   echo "$line<br>";
            if(preg_match('/Order\s+for\s+Store\s+no.\s*:\s*\S+\s+Address\S*:\S*(.*)/',$line,$matches)){   
                $shipping_address = $matches[1];            
            }
            
        }
            
        print "<br><br> SHIPPING ADDRESS: $shipping_address <br>"; 
        //exit;       
        $no_spaces = str_replace(" ", "", $shipping_address);
        $no_spaces_db = $db->safe(trim($no_spaces));
      //  print "<br> NO SPACE: $no_spaces <br>";
        $check = " replace(shipping_address ,' ','') = $no_spaces_db ";
     //   print "<br> CHECK : $check <br>";        
        $query = " select * from it_shipping_address where $check "; 
        print "<br> QUERY: $query <br>";
      
        $sobj = $db->fetchObject($query);
         $inimasterdealerid = "";
               // return "NotFound::-1";      
         
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
    }
}
