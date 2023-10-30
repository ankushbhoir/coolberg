<?php
require_once("../../it_config.php");
require_once("lib/db/DBConn.php");

class headerReaderParser{
    public function __construct() {
        
    }
    
    public function headerParser($file_text){
//        print"<br> in header parser<br>";
        $chainpidlist=array();
        $chainparts=array();
        $chainname_res=" Not Found";
        $brkflag=0;
        $brkchain=0;
        if(file_exists($file_text)){
//            print"<br>find Chain name<br>";
            $scarr="";
            $text = file_get_contents($file_text);        
            $db = new DBConn();
            $lines = explode("\n", $text);
            $numlines = count($lines);        
//            print "<br>CNT: $numlines";
            $chain_name = "NotFound";
            //for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $endloop = 0;
            if($numlines < 20){
               $endloop = $numlines; 
            }else{
               $endloop = 20; 
            }
            
            $qry="select * from it_master_dealers where parser_identification is not null";
            $chainobjs=$db->fetchAllObjects($qry);
            foreach($chainobjs as $chainobj){
            if(isset($chainobj)){
               $parseridentification = $chainobj->parser_identification;
               $parseridentifications= explode(",",$parseridentification);
               foreach($parseridentifications as $parseid)
               if(!in_array($parseid, $chainpidlist)){
                   array_push($chainpidlist,$parseid);
               }
            }
            }
            //print"Parser identification for chains<br>";
//            print_r($chainpidlist);
            
            foreach($chainpidlist as $chainpid){
                $chainpidparts=explode(" ",$chainpid);
//                print"<br>chain =";
//                print_r($chainpidparts);
                $noofParts= count($chainpidparts);
                //print"<br>noofParts++++++++++++++++++++++++=$noofParts<br>";
               // print_r($chainpidparts);
                $cnt=0;  
                for ($lineno = 0; $lineno < $endloop; $lineno++){
                    $cnt=0;  
                    foreach($chainpidparts as $chainpidpart){
                        $line = trim($lines[$lineno]);
                        if(trim($line)==""){ 
                            continue; 
                        }
//                            print "<br> Line: $line";
                        $matches = array();
                        preg_match("/(\w+.*)/",$line, $matches);
                         //   print_r($matches);
                        if( ! empty($matches)){
                            $chain_n = str_replace("'", "", $matches[0]);
                            $chain_name=$chain_n;
                           echo "<br>Chain name: $chain_name<br>";
                           
                        }
                        if(trim($chain_name) != ""){   
                            $carr = explode(" ",$chain_name);      
                            $noofwords=count($carr);
                            for($i=0;$i<$noofwords;$i++){
                                $scarr = str_replace("'","",$carr[$i]);
                                if(trim($scarr)!=""){
    //                                print "<br>linepart----$scarr<br>";
      //                              print"<br>chainpart----$chainpidpart<br>";
        //                            echo "-----------";
                                    if(strcasecmp("$chainpidpart","$scarr")==0) { 
          //                              print"<br>in string cmp success<br>"; 
                                        $cnt++; 
//                                        print"<br>COUNT=$cnt<br>";
                                        $brkflag=1;
//                                        print"<br>brkflag=$brkflag";
                                        break;
                                    }else{
                                        // print"<br>in continue1<br>";
                                         $brkflag=0;
                                         continue;
                                    }
                                }                       
                            }           
                        }
            //            print"<br>brkflag=$brkflag";
              //          echo "chainpid"."$chainpid";
                        if($brkflag==1){
                            continue;
                        }
                    }
                    if($noofParts==$cnt){
                        //print"<br>in break<br>";
                        $chainname_res=$chainpid;
                       // print"<br>chainname=$chainname_res===========$chainpid";
                        $brkchain=1;
                        break;
                    }else{
                        $brkchain=0;
                    }
                } 
                if($brkchain==1){
                    break;
                }
            }
          // print"<br> return chain=$chainname_res<br>";
            return $chainname_res;
        }else{
            return "NotFound";
        }
    }
}

