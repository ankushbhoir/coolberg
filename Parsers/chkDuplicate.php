<?php
require_once("../../it_config.php");
require_once("lib/db/DBConn.php");
require_once "lib/core/Constants.php";

class chkDuplicate {
   public function __construct() {
        
    }
    public function process($poinfo,$filename_db,$masterdealerid){ 
        print"<br>IN CHK DUPLICATE CODE<br>";
        $db= new DBConn();
        if(isset($poinfo)){//115
            $id=$poinfo->id;
            $poname_db=$db->safe($poinfo->filename);
            if($masterdealerid == 4){
                $masterdealerid=3;
            }
//            $E_id='';
//            print"<br>already exist po name=$poname_db<br>";
//            print"<br>po name to be insert =$filename_db<br>";
                      
            $Eqry="select * from it_receivedpos where filename= $poname_db and master_dealer_id = $masterdealerid";
            print"<br>find Existing po Query-> $Eqry<br>";
            $Eobj=$db->fetchObject($Eqry);
            
            if(isset($Eobj)&& !is_null($Eobj)){  //not null chk added
                //$E_id= $Eobj->id;
//                print"<br> po found<br>";
                $Erep_ctime= $Eobj->createtime;
            } 
            
            $Nqry="select * from it_receivedpos where filename= $filename_db and master_dealer_id = $masterdealerid";           
            print"<br>find current po Query-> $Nqry<br>";
            $Nobj=$db->fetchObject($Nqry);

            if(isset($Nobj) && !is_null($Nobj)){   //not null chk added
               // $N_id= $Nobj->id;
//                 print"<br> po found<br>";
                $Nrep_ctime= $Nobj->createtime;
            }   
//              print"<br>old po time=$Eobj->createtime<br>";
//              print"<br>new po time=$Nobj->createtime<br>";
            if(isset($Erep_ctime) && $Erep_ctime!='' && isset($Nrep_ctime) && $Nrep_ctime!=''){ 
                if($Erep_ctime <= $Nrep_ctime){
//                    print"<br> mark old as duplicate<br>";
                    $qry = "select * from it_process_status where filename = $poname_db and (status =".POStatus::STATUS_PROCESSED ." || status =".POStatus::STATUS_ARTICLE_NO_MISSING.")";
                    print"<br>select qry= $qry<br>";
                    $obj = $db->fetchObject($qry);
                    if(isset($obj)){
                    $filepath = $obj->pdfname;   
                    $pro_state_id=$obj->id;
                    markDuplicate($id,$poname_db,$filepath,$pro_state_id);// mark existing as duplicate
                    return 1;    
                    }
                }   
                else{
                    //do nothing
                    print"<br> in do nothing - mark new as duplicate in it_process_status only<br>";
                    return 0; 
                }                   
            }                  
        }
    }
}

function markDuplicate($id,$poname_db,$dupfilepath,$pro_state_id){
    print"<br>in mark duplicate function<br>";
    $db=new DBConn();
    $movedpath=moveToDuplPO($poname_db,$dupfilepath,POStatus::STATUS_DUPLICATE_PO );
    $movedpath_db = $db->safe(trim($movedpath));
    $uqry="update it_process_status set status= ".POStatus::STATUS_DUPLICATE_PO .", pdfname= $movedpath_db where id=$pro_state_id" ; //filename=$filename_db 
//    print"<br>Update Query-> $uqry<br>";
    $dup_no=$db->execUpdate($uqry);
//    print"<br>rows updated=$dup_no<br>";
    $upqry="update it_po set status= ".POStatus::STATUS_DUPLICATE_PO .",status_msg='" . POStatus::getStatusMsg(POStatus::STATUS_DUPLICATE_PO) ."' where id=$id"; //filename=$filename_db 
//    print"<br>Update Query-> $upqry<br>";
    $db->execUpdate($upqry);
}


function moveToDuplPO($dupfilename,$dupfilepath,$status){
    print"<br>moveToDuplPO<br>";
    $PATH =$dupfilepath;//source
    $file = substr(strrchr( $PATH, "/" ),0); 
//    print"$file";
    $dir = str_replace( $file, '', $PATH );
//    print"<br>$dir<br>";
    $src=$dir."/";
//    print"<br>src=$src";
    $file1 = substr( strrchr( $dir, "/" ),1); 
    $dirpath = str_replace( $file1, '', $dir );
//    print"<br>$dirpath<br>";
    //$dest= $dirpath.statusFolder::getStatusMsg(10)."/";
    $dest= $dirpath.statusFolder::getStatusMsg($status)."/";
//    print"<br>destination=$dest<br>";
    $filearr=explode('.', $dupfilepath);
    $Ext= ".".$filearr[1];
//    print"Extension= $Ext";  
    if (!file_exists($dest)) {
        mkdir($dest,  0777 , true);
    }
    $narr = explode(".",$dupfilename);
    $file_pdf = str_replace("'","",$narr[0].$Ext);
    $file_text =  str_replace("'","",$narr[0].".txt");
//    print"<br>dupfilename=$dupfilename<br><br>";
//    print"<br>file_pdf name=$file_pdf<br><br>";
    $pdfname=$dest.$file_pdf;
//    print"<br>pdf name=$pdfname<br><br>";
                
    $delete =  array();

    //first move pdf file
    if(file_exists($src.$file_pdf)){  //move only if po present in processed folder
        if (copy($src.$file_pdf, $dest.$file_pdf)) {
            $delete[] = $src.$file_pdf;
        }
        //than move txt file
        if (copy($src.$file_text, $dest.$file_text)) {
            $delete[] = $src.$file_text;
        }        
    }else{
        print"<br>File is not present in the source folder<br>";
    }
     // unlink files
    if(! empty($delete)){
        foreach ($delete as $file_pdf) {
            if(trim($file_pdf)!="" && trim($file_pdf)!="." && trim($file_pdf) != ".."){  
               unlink($file_pdf);
            }
        } 
    } 
    return $pdfname;
}
