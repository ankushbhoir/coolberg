  <?php
  require_once("../../it_config.php");
  require_once("session_check.php");
  require_once "lib/db/DBConn.php";
  require_once "lib/core/Constants.php";

  $errors = array();
  $success = "Uploaded Sucessfully";
  $db = new DBConn();
  extract($_POST);
  // print_r($_POST);
  $_SESSION['form_post'] = $_POST;
  $_SESSION['form_id'] = $form_id;
  $fileName = $_FILES['file']['name'];
  $tmpName = $_FILES['file']['tmp_name'];

  $ext = end((explode(".",$fileName)));

  $userid = getCurrStoreId();
  // echo $ext;
  if($ext != "csv" ){
    $errors["name"] = "Please upload .csv file only";
  }else{
    $dir = "../uploads/";
    $newfile = $dir."Vendor_Master_user".$userid."_".date("Ymd-His") . ".csv";
    if (!move_uploaded_file($tmpName, $newfile)) {
        $errors['fileerr'] = "File unable to load. Permission denied";
    }else{
      $resp=checkSequenceFile($newfile);
      $result = explode("<>", $resp);
      if($result[0] == 1){
        $resp = checkFile($newfile);
        $result = explode("<>", $resp);
        if($result[0] == 1){
          $resultInsert = saveData($newfile);
        }else{
          $errors['err'] = $result[1];
        }
      }else{
        $errors['err'] = $result[1];
      }
    }

  }

  END:
  // print_r($errors);
    if (count($errors) > 0) {
        unlink($newfile);
        $_SESSION['form_errors'] = $errors;
        $redirect = "vendor/master/upload";
    } else {
        unset($_SESSION['form_errors']);
        // $_SESSION['form_success'] = $success;
        $redirect = "vendor/master";
    }

    session_write_close();
    header("Location: ".DEF_SITEURL.$redirect);
    exit;

  function checkSequenceFile($newfile) {
    $resp = "";
    $col = 1;
    if (($handle = fopen("$newfile", "r")) !== FALSE) {
      $data = fgetcsv($handle, 100, ",");
      // print_r($data);
      if (strcmp(strtolower(str_replace(" ", "", $data[0])), "masterdealername") !== 0){
        $resp .= "<br/>column " . $col++ . " is not Master Dealer Name";
      }
      else{
        $col++;
      }
      if (strcmp(strtolower(str_replace(" ", "", $data[1])), "vendornumber") !== 0){
        $resp .= "<br/>column " . $col++ . " is not Vendor Number";
      }
      else{
        $col++;
      }
      if (strcmp(strtolower(str_replace(" ", "", $data[2])), "plantcode") !== 0){
        $resp .= "<br/>column " . $col++ . " is not Plant Code";
      }
      else{
        $col++;
      }
//      if (strcmp(strtolower(str_replace(" ", "", $data[3])), "postalcode") !== 0){
//        $resp .= "<br/>column " . $col++ . " is not Postal Code";
//      }
//      else{
//        $col++;
//      }
      if (strcmp(strtolower(str_replace(" ", "", $data[3])), "storagelocationcode") !== 0){
        $resp .= "<br/>column " . $col++ . " is not Storage Location Code";
      }
      else{
        $col++;
      }
    }
    if ($resp != "") {
      return "0<>" . $resp;
    } else {
      return "1<>OK";
    }
  }
  function checkFile($newfile){
    $db = new DBConn();
    $resp = "";
    $cls_name_arr = array();
    if (($handle = fopen("$newfile", "r")) !== FALSE){
      $line = 1;
      $flag = 0;
      while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        if ($flag == 0) {
          $flag = 1;
          continue;
        }

        if ( $flag == 1 && isset($data[0]) && isset($data[1]) && isset($data[2]) && isset($data[3]) )  {
          $masterdealername = isset($data[0]) ? $data[0] : "";
          $vendornumber = isset($data[1]) ? $data[1] : "";
          $plantcode = isset($data[2]) ? $data[2] : "";
//          $postalcode = isset($data[3]) ? $data[3] : "";
          $storagelocationcode = isset($data[3]) ? $data[3] : "";

          $masterdealer_db = $db->safe(strtolower(str_replace(" ", "", $masterdealername)));

          $line++;

          if (trim($masterdealername) != "") {
            if (preg_match("/[!$#@%,:]/", $masterdealername) == 1) {
              $resp .= "<br/>Error at line $line. Special Characters are not allowed in Master Dealer Name.";
            }
          }else{
                $resp .= "<br/>Error at line $line. Provide Master Dealer Name.";
          }

          if (trim($vendornumber) != "") {
            if (preg_match("/[!$#@%,:]/", $vendornumber) == 1) {
              $resp .= "<br/>Error at line $line. Special Characters are not allowed in Vendor Number.";
            }
          }else{
                $resp .= "<br/>Error at line $line. Provide Vendor Number.";
          }

          if (trim($plantcode) != "") {
            if (preg_match("/[!$#@%,:]/", $plantcode) == 1) {
              $resp .= "<br/>Error at line $line. Special Characters are not allowed in Plant Code.";
            }
          }else{
                $resp .= "<br/>Error at line $line. Provide Plant Code.";
          }
          
//          if (trim($postalcode) != "") {
//            if (preg_match("/[!$#@%,:]/", $postalcode) == 1) {
//              $resp .= "<br/>Error at line $line. Special Characters are not allowed in Postal Code.";
//            }
//          }else{
//                $resp .= "<br/>Error at line $line. Provide Postal Code.";
//          }
          
          if (trim($storagelocationcode) != "") {
            if (preg_match("/[!$#@%,:]/", $storagelocationcode) == 1) {
              $resp .= "<br/>Error at line $line. Special Characters are not allowed in Storage Location Code.";
            }
          }else{
                $resp .= "<br/>Error at line $line. Provide Storage Location Code.";
          }

          $master_obj = $db->fetchobject("select id from it_master_dealers where lower(replace(displayname, ' ', '')) = $masterdealer_db");
          if(!isset($master_obj) || empty($master_obj)){
               $resp .= "<br/>Error at line $line. Master Dealer Name Does not Exist.";
          }

        }else{
          $resp .= "<br/>Error at line $line . Provide valid data.";
        }  
      }    
    }
  
  if ($resp != "") {
    return "0<>" . $resp;
  } else {
    return "1<>Valid csv file";
  }
  fclose($handle);
  }

  function saveData($newfile){
    $db = new DBConn();
    $userid = getCurrStoreId();
    if (($handle = fopen("$newfile", "r")) !== FALSE) {
      $line = 1;      
      $flag = 0;
      while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        if ($flag == 0) {
          $flag = 1;
          continue;
        }
        if ( $flag == 1 && isset($data[0]) && isset($data[1]) && isset($data[2]) && isset($data[3]) )  {
          $masterdealername = isset($data[0]) ? $data[0] : "";
          $vendornumber = isset($data[1]) ? $data[1] : "";
          $plantcode = isset($data[2]) ? $data[2] : "";
//          $postalcode = isset($data[3]) ? $data[3] : "";
          $storagelocationcode = isset($data[3]) ? $data[3] : "";

          $line++;
          $masterdealer_db = $db->safe(strtolower(str_replace(" ", "", $masterdealername)));
          $vendornumber_db = $db->safe(trim(strtoupper($vendornumber)));
          $plantcode_db = $db->safe(trim(strtoupper($plantcode)));
//          $postalcode_db = $db->safe(trim(strtoupper($postalcode)));
          $storagelocationcode_db = $db->safe(trim(strtoupper($storagelocationcode)));
          
          $vmarray['Master Dealer'] = $masterdealername . '::' . $masterdealername;
          $vmarray['Plant Postal No'] = $vendornumber_db . '::' . $vendornumber_db;
          $vmarray['Plant Code'] = $plantcode_db . '::' . $plantcode_db;
          $vmarray['Storage Location Code'] = $storagelocationcode_db . '::' . $storagelocationcode_db;
          
//          $json_obj = json_encode($vmarray);
          $json_obj = json_encode(array_map(function($item) { return str_replace("'", "", $item); }, $vmarray));
//          print_r($json_obj);

          $master_obj = $db->fetchobject("select id from it_master_dealers where lower(replace(displayname, ' ', '')) = $masterdealer_db");
          $masterdealerid = $master_obj->id;

//          $qry = "select id from it_vendor_plant_mapping where master_dealer_id = $masterdealerid and vendor_number = $vendornumber_db and plant = $plant_db";
          $qry = "select id from it_vendor_plant_mapping where vendor_number = $vendornumber_db and master_dealer_id = $masterdealerid";
          $result_obj = $db->fetchobject($qry);
//          echo $qry; 
          if(isset($result_obj) && !empty($result_obj)){
            //do nothing if already exist
            $updatequery = "update it_vendor_plant_mapping set master_dealer_id = $masterdealerid, plant = $plantcode_db, storage_location_code = $storagelocationcode_db, updatetime = now() where id = $result_obj->id ";
//            echo ($updatequery);
                    //     exit;
            $results = $db->execUpdate($updatequery);
          }else{
            $insert_qry = "insert into it_vendor_plant_mapping set master_dealer_id = $masterdealerid, vendor_number = $vendornumber_db, plant = $plantcode_db, storage_location_code = $storagelocationcode_db, createtime = now()";
            $result = $db->execInsert($insert_qry);
//            echo $insert_qry;
            
                    if (isset($json_obj) && $json_obj !== null && $json_obj !== "null" && !empty($json_obj)) {
                        $insert_qry = "insert into it_masters_logs set master_id = $result, master_type = 'Honasa Plant master', updateby_id = $userid, change_data = '$json_obj',createtime = now(),updatetime = now()";
//                        echo $insert_qry;
//                        exit;
                        $result = $db->execInsert($insert_qry);
                    }
                } 
      }
    }
  }
}
 

?>
