<?php
require_once "lib/db/DBConn.php";

class cls_5_1_buParser
{
    public function __construct()
    {

    }

    public function process($file_text)
    {
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


            //  if(trim($line)==""){ continue; }
            if (trim($line) != "") {
                // print "<br> Line $lineno: $line";

                if (preg_match('/(Delv Add).*/', $line, $matches)) {
                    $start_line_no = $lineno + 1;
                    echo "jaavus";
                    echo $start_line_no + 1;
                    //echo $lineno;
                    //exit;
                    //chk if next line is blank                    
                    $ldata = $lines[$start_line_no];
                    if (trim($ldata) == "") {
                        $start_line_no = $start_line_no + 1;
                    }
                }
                if (preg_match('/(E-Mail).*/', $line, $matches)) {
                    $end_line_no = $lineno - 1;
                }

                //            
            }


        }
        //exit;

        $cnt = 0;
        print "<br> START LINE NO: $start_line_no <br>";
        print "<br> END LINE NO: $end_line_no <br>";
        $regex = "/^.{0,30}(?<addr>.{30,100}).{0,40}/";






        $shipping_address = "";
        for ($i = $start_line_no; $i <= $end_line_no; $i++) {




            $cnt++;




            // if ($cnt == 5) {
            //     continue;
            // }
            $line = trim($lines[$i]);
            print "<br><br> LINE $i: $line ";
            if (trim($line) == "") {
                continue;
            }
            $result = array();
            if (preg_match($regex, $line, $result)) {
                print "<br>";
                print_r($result);
                $shipping_address .= " " . $result['addr'];
            }


        }

        print "<br><br> SHIPPING ADDRESS: $shipping_address <br>";
        $no_spaces = str_replace(" ", "", $shipping_address);
        $no_spaces_db = $db->safe(trim($no_spaces));
        $no_spaces_db = str_replace('\r', "", $no_spaces_db);
        print "<br> NO SPACE: $no_spaces <br>";
        $check = " replace(shipping_address ,' ','') = $no_spaces_db ";
        print "<br> CHECK : $check <br>";
        $query = " select * from it_shipping_address where $check ";
        print "<br> QUERY: $query <br>";

        $sobj = $db->fetchObject($query);
        $inimasterdealerid = "";
        if (isset($sobj)) {
            $db->closeConnection();
            $inimasterdealerid = $sobj->master_dealer_id;
            print "$shipping_address  : $inimasterdealerid </br>";
            return $shipping_address . "::" . $inimasterdealerid;
            // exit;
        } else {
            $db->closeConnection();
            print "<br>CALL NEXT 5_2 BU PARSER <br>";
            $clsname = "cls_5_2_buParser";
            if (file_exists("buParsers/" . $clsname . ".php")) {

                require_once "buParsers/$clsname.php";
                $parser = new $clsname();
                $response = $parser->process($file_text);
                return $response;
            } else {
                //echo "sdsadasd";
                return "NotFound::-1";
            }

        }


    }
}
//echo "comment";