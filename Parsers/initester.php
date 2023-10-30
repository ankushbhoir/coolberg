<?php

class initester {
    public function getIni($inistr,$fpath){
       //$rows= $newPo
        $rows = array();
       print "<br>FPATH: $fpath <br>" ;
       $rows = file($fpath);
       print"<br>Rows=<br>";
       print_r($rows);
       print"<br>initxt in initester bf decoding=$inistr<br>";
       $ini = json_decode($inistr);   
       print"<br>initxt in initester after decoding=<br>";
       print_r($ini);
       
    if (!isset($ini->Header->Fields) || !is_array($ini->Header->Fields) || count($ini->Header->Fields) == 0){
        print"JSON not read";return;    
    }
    $fields = $ini->Header->Fields;
    print"<br> header fields<br>";
    print_r($fields);
    print"<br>";
    $header = array();
    $matches=array();
        foreach ($fields as $field) {
            if (isset($field->value)) {
                print "<br>IN FIELD VALUE CASE: $field->value <br>";
                $header[$field->Name] = trim($field->value);
                print_r($header);
                print "<br>";
            }else if (isset($field->fromFilename)) {
                print"<br>IN from filename case fieldvalule<br>";
                print_r($field->value);
                $value = $ini->fileName;
                if (isset($field->start)){
                    if (isset($field->length)) {
                        $value = trim(substr($value,$field->start,$field->length));  //print"val---$value";			
                        }
                    else{
                        $value = trim(substr($value,$field->start));   //print"val---$value";
                        }
                    }
                $header[$field->Name] = $value;    
                print_r($header);
                print "<br>";
                        
            }else if(is_array($field->row)){ 
                        print"<br> in row array<br>";
                if(isset($field->Regex)){
                            print"in if regex condn: <br>";
                            print "<br> Regex:<br> ";
                             print_r ($field->Regex);
                             $str="";
                             $totlines=count($field->row);
                             for($i=0;$i<$totlines;$i++){                    
                                $rowIndex=$field->row[$i]-1;
                                print"$rowIndex<br>";
                                print "<br> Regex: ".$field->Regex[$i]."<br>";
                                print"<br>row $rowIndex : =$rows[$rowIndex]";
                                if(preg_match($field->Regex[$i],$rows[$rowIndex],$matches)){
                                print"<br>addr<br>"; print_r($matches); print"<br>";
                                //$str = $str." ".trim($matches[1]); //store address
                                $str = $str." ".trim($matches[1]); //store address
                                }
                             }
                             print"address===> $str<br>";
                             //$address=preg_replace('/\d{2}\/\d{2}\/\d{4}/','',$str);
                              //print"address===$address<br>";
                             $header[$field->Name] =trim($str);
                             print"<br> out of row array<br>";
                             print_r($header);
                            print "<br>";
                }else{
                            print"in else not regex case <br>";
                             $str="";
                             $totlines=count($field->row);
                             for($i=0;$i<$totlines;$i++)
                             {                    
                                $rowIndex=$field->row[$i]-1;
                                $str = $str.$rows[$rowIndex];  
                             }
                             print"address2===$str<br>";
                             //$address=preg_replace('/\d{2}\/\d{2}\/\d{4}/','',$str);
                             //print"address===$address<br>";
                             $header[$field->Name] = $str;
                             print_r($header);
                             print "<br>";

                }
            }else{
                print "<br>IN NOT ARRAY CASE: <br>";
                $rowIndex = (int)$field->row - 1;
                $value = trim($rows[$rowIndex]);

               if (isset($field->regex)) {
                   print"val---$value<br><br>";	
                        if (preg_match($field->regex, $value, $matches)) $value = $matches[1];
                } else if (isset($field->start)) {
                        if (isset($field->length)) {
                                $value = trim(substr($value,$field->start,$field->length));	//print"val---$value<br><br>";			
                        } else {
                                $value = trim(substr($value,$field->start));     //  print"val---$value<br><br>";		
                        }
                }
                $header[$field->Name] = $value;
                print_r($header);
                print "<br>";
            }
            if (isset($field->format)) {
                    $header[$field->Name."Format"] = $field->format;
                    print_r($header);
                    print "<br>";
            }
    }
             print"<br> header contents<br>";
             print_r($header);
             print"<br>";
           // if(! isset($header->DealerCity)){
             if(! isset($header['DealerCity'])){ 
                 print" Missing Dealer Address <br> ";
            }
            else{
              //  $Dealer_address=str_replace(trim(" ","",$header['DealerCity'])); // Dealer City treated as Dealer address , naming convention issue
                $Dealer_address = str_replace(" ","",$header['DealerCity']);
                print "Dealer Address=>".trim($Dealer_address)."<br>";
                return trim($Dealer_address);
            }
        }
}

