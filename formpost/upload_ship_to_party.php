  <?php
  require_once("../../it_config.php");
  require_once("session_check.php");
  require_once "lib/db/DBConn.php";
  require_once "lib/core/Constants.php";

  $errors = array();
  
  $db = new DBConn();
  extract($_POST);
   // print_r($_POST);
  $_SESSION['form_post'] = $_POST;
  $_SESSION['form_id'] = $form_id;
  $fileName = $_FILES['file']['name'];
  $tmpName = $_FILES['file']['tmp_name'];

  $ext = end((explode(".",$fileName)));

  $userid = getCurrStoreId();
//  print_r($userid);
  // echo $ext;
  if($ext != "csv" ){
    $errors["name"] = "Please upload .csv file only";
  }else{
    $dir = "../uploads/";
    $newfile = $dir."Ship_to_party_user".$userid."_".date("Ymd-His") . ".csv";
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

  // END:
  // print_r($errors);
    if (count($errors) > 0) {
        unlink($newfile);    
        $_SESSION['form_errors'] = $errors;
        $redirect = "ship/to/party/upload";
    } else {
        unset($_SESSION['form_errors']);
        // $_SESSION['form_success'] = $success;
        $redirect = "ship/to/party";
    }

    session_write_close();
    header("Location: ".DEF_SITEURL.$redirect);
    exit;

  function checkSequenceFile($newfile) {
    $resp = "";
    $col = 1;
    if (($handle = fopen("$newfile", "r")) !== FALSE) {
      $data = fgetcsv($handle, 200, ",");
      // print_r($data);
      
      if (strcmp(strtolower(str_replace(" ", "", $data[0])), "masterdealername") !== 0){
        $resp .= "<br/>column " . $col++ . " is not Master Dealer Name";
      }
      else{
        $col++;
      }
      if (strcmp(strtolower(str_replace(" ", "", $data[1])), "siteidentifier") !== 0){
        $resp .= "<br/>column " . $col++ . " is not Site Identifier";
      }
      else{
        $col++;
      }
      if (strcmp(strtolower(str_replace(" ", "", $data[2])), "siteidentifiertype") !== 0){
        $resp .= "<br/>column " . $col++ . " is not Site Identifier type ";
      }
      else{
        $col++;
      }
      if (strcmp(strtolower(str_replace(" ", "", $data[3])), "shiptoparty") !== 0){
        $resp .= "<br/>column " . $col++ . " is not Ship To Party";
      }
      else{
        $col++;
      }
//      if (strcmp(strtolower(str_replace(" ", "", $data[4])), "category") !== 0){
//        $resp .= "<br/>column " . $col++ . " is not category";
//      }
//      else{
//        $col++;
//      }
//       if (strcmp(strtolower(str_replace(" ", "", $data[4])), "emails") !== 0){
//        $resp .= "<br/>column " . $col++ . " is not emails";
//      }
//      else{
//        $col++;
//      }
      //Newly Added Code for Plant,Customer Name,Margin field 
//      if (strcmp(strtolower(str_replace(" ", "", $data[5])), "plant") !== 0){
//        $resp .= "<br/>column " . $col++ . " is not plants";
//      }
//      else{
//        $col++;
//      }
      if (strcmp(strtolower(str_replace(" ", "", $data[4])), "customername") !== 0){
        $resp .= "<br/>column " . $col++ . " is not Customer Name";
      }
      else{
        $col++;
      }
//      if (strcmp(strtolower(str_replace(" ", "", $data[7])), "margin") !== 0){
//        $resp .= "<br/>column " . $col++ . " is not Margin";
//      }
//      else{
//        $col++;
//      }
      if (strcmp(strtolower(str_replace(" ", "", $data[5])), "distributionchannel") !== 0){
        $resp .= "<br/>column " . $col++ . " is not Distribution Channel";
      }
      else{
        $col++;
      }
      if (strcmp(strtolower(str_replace(" ", "", $data[6])), "salesdocumenttype") !== 0){
        $resp .= "<br/>column " . $col++ . " is not Sales Document Type ";
      }
      else{
        $col++;
      }
      if (strcmp(strtolower(str_replace(" ", "", $data[7])), "distributionchannelcode") !== 0){
        $resp .= "<br/>column " . $col++ . " is not Distribution channel Code ";
      }
      else{
        $col++;
      }
    }
//    print_r($resp);exit();

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

        if ( $flag == 1 && isset($data[0]) && isset($data[1]) && isset($data[2]) && isset($data[3]) && isset($data[4]) && isset($data[5]) && isset($data[6]) && isset($data[7]))  {
          $masterdealername = isset($data[0]) ? $data[0] : "";
          $siteidentifier = isset($data[1]) ? $data[1] : "";
          $siteidentifiertype = isset($data[2]) ? $data[2] : "";
          $shiptoparty = isset($data[3]) ? $data[3] : "";
//          $category = isset($data[4]) ? $data[4] : "";
//          $emails = isset($data[4]) ? $data[4] : "";
//          $plant = isset($data[5]) ? $data[5] : "";
          $customername = isset($data[4]) ? $data[4] : "";
//          $margin = isset($data[7]) ? $data[7] : "";
          $distributionchannel = isset($data[5]) ? $data[5] : "";
          $salesdocumenttype = isset($data[6]) ? $data[6] : "";
          $distributionchannelcode = isset($data[7]) ? $data[7] : "";
          
         $masterdealer_db = $db->safe(strtolower(str_replace(" ", "", $masterdealername)));

          $line++;

          if (trim($masterdealername) != "") {
            if (preg_match("/[!$#@%,:]/", $masterdealername) == 1) {
              $resp .= "<br/>Error at line $line. Special Characters are not allowed in Master Dealer Name.";
            }
          }else{
                $resp .= "<br/>Error at line $line. Provide Master Dealer Name.";
          }

          if (trim($siteidentifier) != "") {
            if (preg_match("/[!$#@%,:]/", $siteidentifier) == 1) {
              $resp .= "<br/>Error at line $line. Special Characters are not allowed in Site.";
            }
          }else{
                $resp .= "<br/>Error at line $line. Provide Site.";
          }
          
          if (trim($siteidentifiertype) != "") {
            if (preg_match("/[!$#@%,:]/", $siteidentifiertype) == 1) {
              $resp .= "<br/>Error at line $line. Special Characters are not allowed in Site Identifier type.";
            }
          }else{
                $resp .= "<br/>Error at line $line. Provide Site Identifier type.";
          }

          if (trim($shiptoparty) != "") {
            if (preg_match("/[!$#@%,:]/", $shiptoparty) == 1) {
              $resp .= "<br/>Error at line $line. Special Characters are not allowed in Ship To Party.";
            }
          }else{
                $resp .= "<br/>Error at line $line. Provide Ship To Party.";
          }

//         if (trim($category) != "") {
//            if (preg_match("/[!$#@%,:]/", $category) == 1) {
//              $resp .= "<br/>Error at line $line. Special Characters are not allowed in category.";
//            }
//            
//          }else{
//                $resp .= "<br/>Error at line $line. Provide category.";
//          }
//          if (trim($emails) != "") {
//            if (preg_match("/[():;<>]/", $emails) == 1) {
//              $resp .= "<br/>Error at line $line. Special Characters are not allowed in emails.";
//            }
//            
//          }else{
//                $resp .= "<br/>Error at line $line. Provide emails.";
//          }
//          if (trim($plant) != "") {
//            if (preg_match("/[!$#@%,:]/", $plant) == 1) {
//              $resp .= "<br/>Error at line $line. Special Characters are not allowed in Plant.";
//            }
//          }else{
//                $resp .= "<br/>Error at line $line. Provide Plant.";
//          }
          if (trim($customername) != "") {
            if (preg_match("/[!$#@%,:]/", $customername) == 1) {
              $resp .= "<br/>Error at line $line. Special Characters are not allowed in Customer Name.";
            }
          }else{
                $resp .= "<br/>Error at line $line. Provide Customer Name.";
          }
//          if (trim($margin) != "") {
//            if (preg_match("/[!$#@,:]/", $margin) == 1) {
//              $resp .= "<br/>Error at line $line. Special Characters are not allowed in Margin.";
//            }
//          }else{
//                $resp .= "<br/>Error at line $line. Provide Margin.";
//          }
          if (trim($distributionchannel) != "") {
            if (preg_match("/[!$#@%,:]/", $distributionchannel) == 1) {
              $resp .= "<br/>Error at line $line. Special Characters are not allowed in Distribution Channel.";
            }
          }else{
                $resp .= "<br/>Error at line $line. Provide Distribution Channel.";
          }
          if (trim($salesdocumenttype) != "") {
            if (preg_match("/[!$#@%,:]/", $salesdocumenttype) == 1) {
              $resp .= "<br/>Error at line $line. Special Characters are not allowed in Sales Document Type.";
            }
          }else{
                $resp .= "<br/>Error at line $line. Provide Sales Document Type.";
          }
          if (trim($distributionchannelcode) != "") {
            if (preg_match("/[!$#@%,:]/", $distributionchannelcode) == 1) {
              $resp .= "<br/>Error at line $line. Special Characters are not allowed in Distribution channel Code.";
            }
          }else{
                $resp .= "<br/>Error at line $line. Provide Distribution channel Code.";
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
        if ( $flag == 1 && isset($data[0]) && isset($data[1]) && isset($data[2]) && isset($data[3]) && isset($data[4]) && isset($data[5]) && isset($data[6]) && isset($data[7]) )  {
          $masterdealername = isset($data[0]) ? $data[0] : "";
          $siteidentifier = isset($data[1]) ? $data[1] : "";
          $siteidentifiertype = isset($data[2]) ? $data[2] : "";
          $shiptoparty = isset($data[3]) ? $data[3] : "";
//          $category = isset($data[4]) ? $data[4] : "";
//          $emails = isset($data[4]) ? $data[4] : "";
//          $plant = isset($data[5]) ? $data[5] : "";
          $customername = isset($data[4]) ? $data[4] : "";
//          $margin = isset($data[7]) ? $data[7] : "";
          $distributionchannel = isset($data[5]) ? $data[5] : "";
          $salesdocumenttype = isset($data[6]) ? $data[6] : "";
          $distributionchannelcode = isset($data[7]) ? $data[7] : "";

          $line++;
          $masterdealer_db = $db->safe(strtolower(str_replace(" ", "", $masterdealername)));
          $siteidentifier_db = $db->safe(trim(strtoupper($siteidentifier)));
          $siteidentifiertype_db = $db->safe(trim(strtoupper($siteidentifiertype)));
          $shiptoparty_db = $db->safe(trim(strtoupper($shiptoparty)));
//          $category_db=$db->safe(trim(strtoupper($category)));
//          $emails_db=$db->safe(trim($emails));
//          $plant_db = $db->safe(trim(strtoupper($plant)));
          $customername_db = $db->safe(trim(strtoupper($customername)));
//          $margin_db=$db->safe(trim(strtoupper($margin)));
          $distributionchannel_db=$db->safe(trim(strtoupper($distributionchannel)));
          $salesdocumenttype_db=$db->safe(trim(strtoupper($salesdocumenttype)));
          $distributionchannelcode_db=$db->safe(trim(strtoupper($distributionchannelcode)));

          $master_obj = $db->fetchobject("select id from it_master_dealers where lower(replace(displayname, ' ', '')) = $masterdealer_db");
          $masterdealerid = $master_obj->id;

          
          $stparray['Master Dealer Name'] = $masterdealername . '::' . $masterdealername;
          $stparray['Ship To Party'] = $shiptoparty_db . '::' . $shiptoparty_db;
          $stparray['Site'] = $siteidentifier_db . '::' . $siteidentifier_db;
          $stparray['Site Identifier Type'] = $siteidentifiertype_db . '::' . $siteidentifiertype_db;
          $stparray['Customer Name'] = $customername_db . '::' . $customername_db;
          $stparray['Distribution Channel'] = $distributionchannel_db . '::' . $distributionchannel_db;
          $stparray['Sales Document Type'] = $salesdocumenttype_db . '::' . $salesdocumenttype_db;
          $stparray['Distribution Channel Code'] = $distributionchannelcode_db . '::' . $distributionchannelcode_db;
          
//          $json_obj = json_encode($stparray);
          $json_obj = json_encode(array_map(function($item) { return str_replace("'", "", $item); }, $stparray));
          //print_r($json_obj);

//          $qry = "select id from it_ship_to_party where master_dealer_id = $masterdealerid and site = $site_db and ship_to_party = $shiptoparty_db and category=$category";
//          $qry = "select id from it_ship_to_party where master_dealer_id = $masterdealerid and site = $site_db and ship_to_party = $shiptoparty_db and category = $category_db and plant = $plant_db and customer_name = $customername_db and margin = $margin_db";
          $qry = "select id from it_ship_to_party where ship_to_party = $shiptoparty_db and master_dealer_id = $masterdealerid";

//            echo $qry;
      // exit;
          $result_obj = $db->fetchobject($qry);

          if(isset($result_obj) && !empty($result_obj)){
            //do nothing if already exist
            $updatequery = "update it_ship_to_party set master_dealer_id = $masterdealerid, site = $siteidentifier_db, site_identifier_type = $siteidentifiertype_db,customer_name = $customername_db,distribution_channel = $distributionchannel_db, sales_document_type = $salesdocumenttype_db, distribution_channel_code = $distributionchannelcode_db,updatetime = now() where id = $result_obj->id";
//              echo $updatequery;
//                         exit;
              $result = $db->execUpdate($updatequery);
//            exit;
              
//              if (isset($json_obj) && $json_obj !== null && $json_obj !== "null" && !empty($json_obj)) {
//                        $insert_qry = "insert into it_masters_logs set master_id = $result_obj->id, master_type = 'Ship To Party', updateby_id = $userid, change_data = '$json_obj',createtime = now(),updatetime = now()";
////             echo $insert_qry;
////             exit;
//                        $result = $db->execInsert($insert_qry);
//                    }
              
          }else{
            $insert_qry = "insert into it_ship_to_party set master_dealer_id = $masterdealerid, site = $siteidentifier_db, site_identifier_type = $siteidentifiertype_db, ship_to_party = $shiptoparty_db,customer_name = $customername_db,distribution_channel = $distributionchannel_db, sales_document_type = $salesdocumenttype_db, distribution_channel_code = $distributionchannelcode_db,createtime = now()";
//             echo $insert_qry;
             
            $result = $db->execInsert($insert_qry);
//            exit;
            
               if (isset($json_obj) && $json_obj !== null && $json_obj !== "null" && !empty($json_obj)) {
                        $insert_qry = "insert into it_masters_logs set master_id = $result, master_type = 'Ship To Party', updateby_id = $userid, change_data = '$json_obj',createtime = now(),updatetime = now()";
//             echo $insert_qry;
//             exit;
                        $result = $db->execInsert($insert_qry);
                    }
                }
      }
    }
  }
}
 

?>
