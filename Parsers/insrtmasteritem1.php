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
	require_once("../../it_config.php");
        require_once "lib/db/DBConn.php";
	
	if(isset($_POST["submit"]))
	{
		$file = $_FILES['file']['tmp_name'];
		$handle = fopen($file, "r");
                $db=new DBConn();
		$c = 0;
                $createtime=$db->safe(date('Y-m-d H:i:s'));
                $catid=0;
                $itemid=0;
                $addclause="";
		while(($filesop = fgetcsv($handle, 1000, ",")) !== false)
		{    
                    print_r($filesop);
			$itemname = isset($filesop[1])?trim($filesop[1]):"";
                        $cat = isset($filesop[2])?trim($filesop[2]):"";
                        $sku = isset($filesop[3])?trim($filesop[3]):"";
                        $packtype= isset($filesop[4])?trim($filesop[4]):"";
                        $case_size= isset($filesop[5])?trim($filesop[5]):"";
                        $itemcode = isset($filesop[6])?trim($filesop[6]):"";
                        $product_code = isset($filesop[7])?trim($filesop[7]):"";
                        $mrp= isset($filesop[8])?trim($filesop[8]):"";
                        $length= isset($filesop[9])?trim($filesop[9]):"";
                        $width= isset($filesop[10])?trim($filesop[10]):"";
                        $height= isset($filesop[11])?trim($filesop[11]):"";
                        $shelf_life= isset($filesop[12])?trim($filesop[12]):"";
                        //$update=(date('Y-m-d H:i:s'));
                        if(trim($itemname)!=" " && trim($cat)!=" " && trim($itemcode)!=" "){
                            
                            $getcat="select * from it_category where category = '$cat'";
                            print $getcat;
                            $catobj= $db->fetchObject($getcat); 
                            if(isset($catobj))
                            {
                                $catid=$catobj->id;
                            }//else insert cat
                            else{
                                $inscat="INSERT INTO it_category (category,createtime) VALUES ('$cat',$createtime)";
                                $catid=$db->execInsert($inscat);
                            }
 
                            if(trim($itemname)!=""){
                                     $itemname_db = $db->safe(trim($itemname));
                                     $addClause="  , itemname = $itemname_db";
                            }
                             if(trim($catid)!=""){
                                     $catid_db = $db->safe(trim($catid));
                                     $addClause.="  , category_id = $catid_db";
                            }
                             if(trim($sku)!=""){
                                     $sku_db = $db->safe(trim($sku));
                                     $addClause.="  , sku = $sku_db";
                            }
                             if(trim($packtype)!=""){
                                     $packtype_db = $db->safe(trim($packtype));
                                     $addClause.="  , pack_type = $packtype_db";
                            }
                             if(trim($case_size)!=""){
                                     $case_size_db = $db->safe(trim($case_size));
                                     $addClause.="  , case_size = $case_size_db";
                            }
                             if(trim($itemcode)!=""){
                                     $itemcode_db = $db->safe(trim($itemcode));
                                     $addClause.="  , itemcode = $itemcode_db";
                            }
                             if(trim($product_code)!=""){
                                     $product_code_db = $db->safe(trim($product_code));
                                     $addClause.="  , product_code = $product_code_db";
                            }
                             if(trim($mrp)!=""){
                                     $mrp_db = $db->safe(trim($mrp));
                                     $addClause.="  , mrp = $mrp_db";
                            }
                             if(trim($length)!=""){
                                    $length_db = $db->safe(trim($length));
                                     $addClause.="  , length = $length_db";
                            }
                             if(trim($width)!=""){
                                     $width_db = $db->safe(trim($width));
                                     $addClause.="  , width = $width_db";
                            }
                             if(trim($height)!=""){
                                     $height_db = $db->safe(trim($height));
                                     $addClause.="  , height = $height_db";
                            }
                             if(trim($shelf_life)!=""){
                                     $shelf_lifedb = $db->safe(trim($shelf_life));
                                     $addClause.="  , shelf_life = $shelf_life_db";
                            }
                            
                            if($catid>0 && trim($itemcode)!=""){
                                        $chkitemQuery="select * from it_master_items where itemcode=$itemcode_db";
                                        print $chkitemQuery;
                                        $itemfnd=$db->fetchObject($chkitemQuery);
                                        print_r($itemfnd);
                                        if(isset($itemfnd)){ 
                                            $itemid=$itemfnd->id;
                                        }else{
                                            $itemid=0;
                                        }   
                                            if($itemid>0){
                                                $updatemaster="update it_master_items set updatetime=now() , is_weikfield = 1 , is_notfound = 0 $addClause where id=$itemid";
                                                print"<br>"; print"Query=$updatemaster";print"<br>";
                                                $db->execUpdate($updatemaster);
                                            }else{
                                                $insertmaster="INSERT INTO it_master_items set createtime=now() , is_weikfield = 1 $addClause";
                                                print"<br>"; print"Query=$insertmaster";print"<br>";
                                                $masteritemid= $db->execInsert($insertmaster);
                                                $c = $c + 1;
                                            }
                                        
                            }
                    }else{
                        echo "Product Name , EAN OR Category Missing";
                    }
                }
		if($catid>0){
		    echo "Your database has imported successfully. You have inserted ". $c ." records";
		}else{
		    echo "Sorry! There is some problem.";
		}
                
	}
   
?>

</body>
</html>
