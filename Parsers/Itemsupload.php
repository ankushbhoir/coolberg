<?php
require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";

class Itemsupload {
    public function __construct() {
        
    }
     public function upload($handle){
                $db=new DBConn();
		$c = 0;
                $createtime=$db->safe(date('Y-m-d H:i:s'));
                $catid=0;
                $itemid=0;
                $addclause="";
		while(($filesop = fgetcsv($handle, 1000, ",")) !== false){   
                    //print_r($filesop);
			$itemname = isset($filesop[1])?trim($filesop[1]):"";
                        $cat = isset($filesop[2])?trim($filesop[2]):"";
                        $sku = isset($filesop[3])?trim($filesop[3]):"";
                        $packtype= isset($filesop[4])?trim($filesop[4]):"";
                        //$case_size= isset($filesop[5])?trim($filesop[5]):"";
                        $itemcode = isset($filesop[5])?trim($filesop[5]):"";
//                        $product_code = isset($filesop[7])?trim($filesop[7]):"";
//                        $mrp= isset($filesop[8])?trim($filesop[8]):"";
//                        $length= isset($filesop[9])?trim($filesop[9]):"";
//                        $width= isset($filesop[10])?trim($filesop[10]):"";
//                        $height= isset($filesop[11])?trim($filesop[11]):"";
//                        $shelf_life= isset($filesop[12])?trim($filesop[12]):"";
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
//                             if(trim($case_size)!=""){
//                                     $case_size_db = $db->safe(trim($case_size));
//                                     $addClause.="  , case_size = $case_size_db";
//                            }
                            if(trim($itemcode)!=""){
                                     $itemcode_db = $db->safe(trim($itemcode));
                                     $addClause.="  , itemcode = $itemcode_db";
                            }
//                             if(trim($product_code)!=""){
//                                     $product_code_db = $db->safe(trim($product_code));
//                                     $addClause.="  , product_code = $product_code_db";
//                            }
//                             if(trim($mrp)!=""){
//                                     $mrp_db = $db->safe(trim($mrp));
//                                     $addClause.="  , mrp = $mrp_db";
//                            }
//                             if(trim($length)!=""){
//                                    $length_db = $db->safe(trim($length));
//                                     $addClause.="  , length = $length_db";
//                            }
//                             if(trim($width)!=""){
//                                     $width_db = $db->safe(trim($width));
//                                     $addClause.="  , width = $width_db";
//                            }
//                             if(trim($height)!=""){
//                                     $height_db = $db->safe(trim($height));
//                                     $addClause.="  , height = $height_db";
//                            }
//                             if(trim($shelf_life)!=""){
//                                     $shelf_lifedb = $db->safe(trim($shelf_life));
//                                     $addClause.="  , shelf_life = $shelf_life_db";
//                            }
//                            print $itemcode;
                            if($catid > 0 && trim($itemcode) != ""){
                                $chkitemQuery = "select * from it_master_items where itemcode = $itemcode_db";
                                print $chkitemQuery;
                                $itemfnd=$db->fetchObject($chkitemQuery);
                                print_r($itemfnd);
                                if(isset($itemfnd)){ 
                                    $itemid = $itemfnd->id;
                                }else{
                                    $itemid = 0;
                                }   
                                if($itemid > 0){
                                    $updatemaster ="update it_master_items set updatetime = now() , is_weikfield = 1, is_notfound = 0 $addClause where id = $itemid";
                                    print"<br>"; print"Query=$updatemaster";print"<br>";
                                    $db->execUpdate($updatemaster);

                                    $uqry = "update it_dealer_items set itemname = $itemname_db , is_weikfield = 1 where eancode = $itemcode_db ";
                                    print "<br>UPDATE it_dealer_items QRY: $uqry <br>";
                                    $db->execUpdate($uqry);

                                    $seldelitmQry ="select * from it_dealer_items where is_weikfield = 1 and eancode = $itemcode_db ";
                                    print"<br>select dealer item id: $seldelitmQry";
                                    $delitmobjarr = $db->fetchAllObjects($seldelitmQry);
                                    foreach($delitmobjarr as $delitmobj){
                                        $delitmid = $delitmobj->id;
                                        $selpoidQry = "select po_id from it_po_items where master_item_id = $itemid and dealer_item_id = $delitmid";
                                        print"<br> select poid: $selpoidQry";
                                        $poidobjs = $db->fetchAllObjects($selpoidQry);
                                        print"<br>";
                                        print_r($poidobjs);
                                        print"<br>";
                                        // select id, master_item_id, dealer_item_id, po_id, ctime from it_po_items where po_id in(select id from it_po where status = 7 and ctime >= '2017-01-20 00:00:00' and master_dealer_id = 5);
                                        foreach($poidobjs as $poidobj){
                                            $poid = $poidobj->po_id;
                                            $poobjQry = "select * from it_po where id = $poid"; 
                                            print"<br> select po status: $poobjQry";
                                            $poobj = $db->fetchObject($poobjQry);
                                            if(isset($poobj)){
                                                $status = $poobj->status;
                                                $id= $poobj->id;
                                                $filename= $poobj->filename;
                                                $filename_db= $db->safe($filename);
                                                if($status == POStatus::STATUS_MISSING_EAN){
                                                    $upqry = "update it_po set status =".POStatus::STATUS_PROCESSED .",status_msg ='" . POStatus::getStatusMsg(POStatus::STATUS_PROCESSED)."' where id = $id";
                                                    print"<br>update po status: $upqry<br>";
                                                    $db->execUpdate($upqry);
                                                    $upprostatQry ="update it_process_status set status = ". POStatus::STATUS_PROCESSED ." where filename = $filename_db and is_current_status=1 and status = ".POStatus::STATUS_MISSING_EAN ;
                                                    print"$upprostatQry";
                                                    $db->execUpdate($upprostatQry);
                                                }else{
                                                    print"<br>already successfull<br>";
                                                }
                                            }
                                        }
                                    }
                                    $c = $c + 1;
                                }else{
                                    $insertmaster ="INSERT INTO it_master_items set createtime = now() , is_weikfield = 1 $addClause";
                                    print"<br>"; print"Query=$insertmaster";print"<br>";
                                    $masteritemid = $db->execInsert($insertmaster);
                                    $c = $c + 1;
                                }
                            }
                    }else{
                        echo "Product Name , EAN OR Category Missing";
                    }
                }
                return $c;
     }
}
