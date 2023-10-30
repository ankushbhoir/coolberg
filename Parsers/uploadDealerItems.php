<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
<head>
	
	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	  ga('create', 'UA-60962033-1', 'auto');
	  ga('send', 'pageview');

	</script>
</head>

<body>
	      
	<form name="import" method="post" enctype="multipart/form-data">
    	<input type="file" name="file" /><br />
        <input type="submit" name="submit" value="Submit" />
    </form>
<?php
	require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
        require_once "lib/db/DBConn.php";
	require_once("Itemsupload.php");
        
	if(isset($_POST["submit"])){
            $file = $_FILES['file']['tmp_name'];
            $handle = fopen($file, "r");
            $db=new DBConn();
            $c = 0;
            $createtime=$db->safe(date('Y-m-d H:i:s'));
            $catid=0;
            
            $addclause="";
            $result=0;
            while(($filesop = fgetcsv($handle, 1000, ",")) !== false){    
                $dealeritemid=0;   
                $itemcode = isset($filesop[5])?trim($filesop[5]):"";
                $articleno= isset($filesop[6])?trim($filesop[6]):"";
                
                print"$itemcode---------$articleno" ;     
                if(trim($articleno)!=" " && trim($itemcode)!=" "){ 

                    $itemcode_db = $db->safe(trim($itemcode));
                    $articleno_db = $db->safe(trim($articleno));

                    if(trim($itemcode_db)!="" && trim($articleno_db)!=""){
                        $getitemQuery="select * from it_dealer_items where itemcode=$articleno_db and is_notfound=1";
                        print"<br>find dealer item=$getitemQuery<br>";
                        $dealeritemobj=$db->fetchObject($getitemQuery);
                        //$dealeritemobjs=$db->fetchAllObjects($getitemQuery);

                        if(isset($dealeritemobj)){
                            //foreach($dealeritemobjs as $dealeritemobj){
                            $dealeritemid=$dealeritemobj->id;
                            print"<br>dealer item id= $dealeritemid<br>";
//                        }
//                        if($dealeritemid>0){
                            $chkitemQuery="select * from it_master_items where itemcode=$itemcode_db";
                            print"<br>find master item=$chkitemQuery<br>";
                            $itemfndobj=$db->fetchObject($chkitemQuery);
                            //print_r($itemfnd);
                            if(isset($itemfndobj)){ 
                                $result=insertdealeritems($itemfndobj,$dealeritemid); 
                                $mstritemid= $itemfndobj->id;
                                if($result>0){
                                    getPOname($mstritemid,$dealeritemid);
                               }
                            }
                            else{
                                echo "EAN Missing in master items upload Master Items";
//                                $uploadfile= new Itemsupload();
//                                $itemcnt=$uploadfile->upload($handle);
//                               // print"<br>EAN MISSING IN MASTER<br>";
//                                if($itemcnt>0){
//                                    $chkitemQuery="select * from it_master_items where itemcode=$itemcode_db";
//                                    print"<br>find master item=$chkitemQuery<br>";
//                                    $itemfndobj=$db->fetchObject($chkitemQuery);
//                                    //print_r($itemfnd);
//                                    if(isset($itemfndobj)){ 
//                                        $result=insertdealeritems($itemfndobj,$dealeritemid); 
//                                        $mstritemid= $itemfndobj->id;
//                                        if($result>0){
//                                            //getPOname($mstritemid,$dealeritemid);
//                                        }
//                                    }                      
//                                }
                            }
                        }else{
                                echo "Article No Not Found in it_dealer_items";
                             }
                       // }
                    }else{
                        echo "Article No or EAN Code is missing in CSV file";
                         }
                    }
               if($result>0){
                        echo "Your database has imported successfully. You have inserted/updated ".$result ." recoreds";
                }else{
                        echo "Sorry! There is some problem.";
                    } 
            }  
        }
    
    function insertdealeritems($mstritemfndobj,$dealeritemid){
        print"in insert function<br>";
        $db=new DBConn();
        $eancode_db = $db->safe(trim($mstritemfndobj->itemcode));
        $itemname_db = $db->safe(trim($mstritemfndobj -> itemname));
        $masteritemid = trim($mstritemfndobj->id);       
        $is_weikfield = trim($mstritemfndobj->is_weikfield);
        
        $uqry = "update it_dealer_items set eancode = $eancode_db ,itemname = $itemname_db ,  master_item_id = $masteritemid , is_weikfield = $is_weikfield , is_notfound = 0 where id = $dealeritemid ";
        print "<br>UPDATE it_dealer_items QRY: $uqry <br>";
        $db->execUpdate($uqry);
        
        $Uquery= "update it_po_items set master_item_id= $masteritemid where dealer_item_id=$dealeritemid";
        print "<br>UPDATE  it_po_items QRY: $Uquery <br>";
        //get the po_id of same item and check in po status is missing then set it to processed
        $res = $db->execUpdate($Uquery);
        return $res;
    }

    function  getPOname($mstritemid,$dealeritemid){
        print"in getPOname function<br>";
        $db=new DBConn();
        $selQ= "select * from it_po_items where master_item_id=$mstritemid and dealer_item_id=$dealeritemid";
        print"selQ=$selQ<br>";
        $poitemobjs=$db->fetchAllObjects($selQ);
        foreach($poitemobjs as $poitemobj){
            $poid=$poitemobj->po_id;
            $poobjQry="select * from it_po where id=$poid";
/////upadate po status from article missing to sucess in it_po and it_process_status
            $poobj = $db->fetchObject($poobjQry);
            print"<br> select po status: $poobjQry";
            if(isset($poobj)){
                $status = $poobj->status;
                $id= $poobj->id;
                $filename= $poobj->filename;
                $filename_db= $db->safe($filename);
                if($status == POStatus::STATUS_ARTICLE_NO_MISSING){
                    $upqry = "update it_po set status =".POStatus::STATUS_PROCESSED .",status_msg ='" . POStatus::getStatusMsg(POStatus::STATUS_PROCESSED)."' where id = $id";
                    print"<br>update po status: $upqry<br>";
                    $db->execUpdate($upqry);
                    $upprostatQry ="update it_process_status set status = ". POStatus::STATUS_PROCESSED ." where filename = $filename_db and is_current_status=1 and status = ".POStatus::STATUS_ARTICLE_NO_MISSING ;
                    print"$upprostatQry";
                    $db->execUpdate($upprostatQry);
                }else{
                    print"<br>already successfull<br>";
                }
            }
   //$statusinsertQ="insert into it_process_status set  pdfname='$pdfname',filename='$file', master_dealer_id=$master_dealer_id,  ini_id=$iniid, status=$response, createtime= $createtime";
        }
        if(isset($poitemobj)){
            
        }
    }

?>
