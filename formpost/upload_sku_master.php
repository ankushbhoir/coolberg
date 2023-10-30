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
    $newfile = $dir."Sku_Data_user".$userid."_".date("Ymd-His") . ".csv";
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
        $redirect = "sku/master/upload";
    } else {
        unset($_SESSION['form_errors']);
        // $_SESSION['form_success'] = $success;
        $redirect = "sku/master";
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
      
      if (strcmp(strtolower(str_replace(" ", "", $data[0])), "chainname") !== 0){
        $resp .= "<br/>column " . $col++ . " is not Master Dealer Name";
      }
      else{
        $col++;
      }
      
      if (strcmp(strtolower(str_replace(" ", "", $data[1])), "sku") !== 0){
        $resp .= "<br/>column " . $col++ . " is not SKU";
      }
      else{
        $col++;
      }
      if (strcmp(strtolower(str_replace(" ", "", $data[2])), "ean") !== 0){
        $resp .= "<br/>column " . $col++ . " is not EAN";
      }
      else{
        $col++;
      }
      if (strcmp(strtolower(str_replace(" ", "", $data[3])), "category") !== 0){
        $resp .= "<br/>column " . $col++ . " is not category";
      }
      else{
        $col++;
      }
      //Newly Added Code for Ean Sku Mapping Field
      if (strcmp(strtolower(str_replace(" ", "", $data[4])), "productname") !== 0){
        $resp .= "<br/>column " . $col++ . " is not Product Name";
      }
      else{
        $col++;
      }
      if (strcmp(strtolower(str_replace(" ", "", $data[5])), "mrp") !== 0){
        $resp .= "<br/>column " . $col++ . " is not mrp";
      }
      else{
        $col++;
      }
      if (strcmp(strtolower(str_replace(" ", "", $data[6])), "gst") !== 0){
        $resp .= "<br/>column " . $col++ . " is not gst";
      }
      else{
        $col++;
      }
      if (strcmp(strtolower(str_replace(" ", "", $data[7])), "innersize") !== 0){
        $resp .= "<br/>column " . $col++ . " is not inner size";
      }
      else{
        $col++;
      }
      if (strcmp(strtolower(str_replace(" ", "", $data[8])), "outersize") !== 0){
        $resp .= "<br/>column " . $col++ . " is not outer size";
      }
      else{
        $col++;
      }
      if (strcmp(strtolower(str_replace(" ", "", $data[9])), "purchaseratew/ogst") !== 0){
        $resp .= "<br/>column " . $col++ . " is not Purchase Rate W/O GST";
      }
      else{
        $col++;
      }
      if (strcmp(strtolower(str_replace(" ", "", $data[10])), "moq") !== 0){
        $resp .= "<br/>column " . $col++ . " is not MOQ";
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

        if ( $flag == 1 && isset($data[0]) && isset($data[1]) && isset($data[2]) && isset($data[3]) && isset($data[4]) && isset($data[5]) && isset($data[6]) && isset($data[7]) && isset($data[8]) && isset($data[9]) && isset($data[10]) )  {
          $masterdealername = isset($data[0]) ? $data[0] : "";
          $sku = isset($data[1]) ? $data[1] : "";
          $ean = isset($data[2]) ? $data[2] : "";
          $category = isset($data[3]) ? $data[3] : "";
          $productname = isset($data[4]) ? $data[4] : "";
          $mrp = isset($data[5]) ? $data[5] : "";
          $gst = isset($data[6]) ? $data[6] : "";
          $innersize = isset($data[7]) ? $data[7] : "";
          $outersize = isset($data[8]) ? $data[8] : "";
          $purchaseratewogst = isset($data[9]) ? $data[9] : "";
          $moq = isset($data[10]) ? $data[10] : "";

          $masterdealer_db = $db->safe(strtolower(str_replace(" ", "", $masterdealername)));
          
          $line++;

          if (trim($sku) != "") {
            if (preg_match("/[!$#@%,:]/", $sku) == 1) {
              $resp .= "<br/>Error at line $line. Special Characters are not allowed in SKU.";
            }
          }else{
                $resp .= "<br/>Error at line $line. Provide SKU.";
          }

          if (trim($ean) != "") {
            if (preg_match("/[!$#@%,:]/", $ean) == 1) {
              $resp .= "<br/>Error at line $line. Special Characters are not allowed in EAN.";
            }
          }else{
                $resp .= "<br/>Error at line $line. Provide EAN.";
          }
          if (trim($category) != "") {
            if (preg_match("/[!$#@%,:]/", $category) == 1) {
              $resp .= "<br/>Error at line $line. Special Characters are not allowed in category.";
            }
          }else{
                $resp .= "<br/>Error at line $line. Provide category.";
          }
          if (trim($productname) != "") {
            if (preg_match("/[!$#@,:]/", $productname) == 1) {
              $resp .= "<br/>Error at line $line. Special Characters are not allowed in Product Name.";
            }
          }else{
                $resp .= "<br/>Error at line $line. Provide Product Name.";
          }
          if (trim($mrp) != "") {
            if (preg_match("/[!$#@%,:]/", $mrp) == 1) {
              $resp .= "<br/>Error at line $line. Special Characters are not allowed in mrp.";
            }
          }else{
                $resp .= "<br/>Error at line $line. Provide MRP.";
          }
          if (trim($gst) != "") {
            if (preg_match("/[!$#@,:]/", $gst) == 1) {
              $resp .= "<br/>Error at line $line. Special Characters are not allowed in GST.";
            }
          }else{
                $resp .= "<br/>Error at line $line. Provide GST.";
          }
          if (trim($innersize) != "") {
            if (preg_match("/[!$#@%,:]/", $innersize) == 1) {
              $resp .= "<br/>Error at line $line. Special Characters are not allowed in Inner Size.";
            }
          }else{
                $resp .= "<br/>Error at line $line. Provide Inner Size.";
          }
          if (trim($outersize) != "") {
            if (preg_match("/[!$#@%,:]/", $outersize) == 1) {
              $resp .= "<br/>Error at line $line. Special Characters are not allowed in Outer Size.";
            }
          }else{
                $resp .= "<br/>Error at line $line. Provide Outer Size.";
          }
          if (trim($purchaseratewogst) != "") {
            if (preg_match("/[!$#@%,:]/", $purchaseratewogst) == 1) {
              $resp .= "<br/>Error at line $line. Special Characters are not allowed in Purchase Rate W/O GST.";
            }
          }else{
                $resp .= "<br/>Error at line $line. Provide Purchase Rate W/O GST.";
          }
          if (trim($moq) != "") {
            if (preg_match("/[!$#@%,:]/", $moq) == 1) {
              $resp .= "<br/>Error at line $line. Special Characters are not allowed in MOQ.";
            }
          }else{
                $resp .= "<br/>Error at line $line. Provide MOQ.";
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

        if ( $flag == 1 && isset($data[0]) && isset($data[1]) && isset($data[2]) && isset($data[3]) && isset($data[4]) && isset($data[5]) && isset($data[6]) && isset($data[7]) && isset($data[8]) && isset($data[9]) && isset($data[10]))  {
          $masterdealername = isset($data[0]) ? $data[0] : "";
          $sku = isset($data[1]) ? $data[1] : "";
          $ean = isset($data[2]) ? $data[2] : "";
          $category = isset($data[3]) ? $data[3] : "";
          $productname = isset($data[4]) ? $data[4] : "";
          $mrp = isset($data[5]) ? $data[5] : "";
          $gst = isset($data[6]) ? $data[6] : "";
          $innersize = isset($data[7]) ? $data[7] : "";
          $outersize = isset($data[8]) ? $data[8] : "";
          $purchaseratewogst = isset($data[9]) ? $data[9] : "";
          $moq = isset($data[10]) ? $data[10] : "";
   
          $line++;
          $masterdealer_db = $db->safe(strtolower(str_replace(" ", "", $masterdealername)));
          $sku_db = $db->safe(trim(strtoupper($sku)));
          $ean_db = $db->safe(trim(strtoupper($ean)));
          $category_db = $db->safe(trim(strtoupper($category)));
          $productname_db = $db->safe(trim(strtoupper($productname)));
          // Convert the input to UTF-8
$productname_db = iconv('ISO-8859-1', 'UTF-8//TRANSLIT', $productname_db);

// Remove specific unwanted characters
$unwanted_characters = array('ï¿½');
$productname_db = str_replace($unwanted_characters, '', $productname_db);
          $mrp_db = $db->safe(trim(strtoupper($mrp)));
          $gst_db = $db->safe(trim(strtoupper($gst)));
          $innersize_db = $db->safe(trim(strtoupper($innersize)));
          $outersize_db = $db->safe(trim(strtoupper($outersize)));
          $purchaseratewogst_db = $db->safe(trim(strtoupper($purchaseratewogst)));
          $moq_db = $db->safe(trim(strtoupper($moq)));
          
          $skuarray['Chain Name'] = $masterdealername . '::' . $masterdealername;
          $skuarray['SKU'] = $sku_db . '::' . $sku_db;
          $skuarray['EAN'] = $ean_db . '::' . $ean_db;
          $skuarray['Category'] = $category_db . '::' . $category_db;
          $skuarray['Product Name'] = $productname_db . '::' . $productname_db;
          $skuarray['MRP'] = $mrp_db . '::' . $mrp_db;
          $skuarray['GST'] = $gst_db . '::' . $gst_db;
          $skuarray['Inner Size'] = $innersize_db . '::' . $innersize_db;
          $skuarray['Outer Size'] = $outersize_db . '::' . $outersize_db;
          $skuarray['Purchase Rate GST'] = $purchaseratewogst_db . '::' . $purchaseratewogst_db;
          $skuarray['MOQ'] = $moq_db . '::' . $moq_db;
          
//          $json_obj = json_encode($skuarray);
          $json_obj = json_encode(array_map(function($item) { return str_replace("'", "", $item); }, $skuarray));
//          print_r($json_obj); exit();
          
          $master_obj = $db->fetchobject("select id from it_master_dealers where lower(replace(displayname, ' ', '')) = $masterdealer_db");
          $masterdealerid = $master_obj->id;

          $prod_qry = "select id from it_ean_sku_mapping where sku = $sku_db and ean = $ean_db and master_dealer_id = $masterdealerid";
//          $prod_qry = "select id,ean from it_ean_sku_mapping where ean = $ean_db"; //and product_name = $productname_db and mrp = $mrp_db and gst = $gst_db and inner_size = $innersize_db  and case_size = $casesize_db and category=$category_db";
          $prod_obj = $db->fetchobject($prod_qry);
//           echo $prod_qry;
          if(isset($prod_obj) && !empty($prod_obj)){
            //do nothing if already exist
              $updatequery = "update it_ean_sku_mapping set master_dealer_id = $masterdealerid, ean = $ean_db, product_name = $productname_db, mrp = $mrp_db, gst = $gst_db, inner_size = $innersize_db, category=$category_db,outer_size = $outersize_db, purchase_rate_gst = $purchaseratewogst_db, moq = $moq_db where id = $prod_obj->id";
//                        echo ($updatequery);
                    //     exit;
              $results = $db->execUpdate($updatequery);
          }else{
            $insert_qry = "insert into it_ean_sku_mapping set master_dealer_id = $masterdealerid, sku = $sku_db, ean = $ean_db, product_name = $productname_db, mrp = $mrp_db, gst = $gst_db, inner_size = $innersize_db, category=$category_db,outer_size = $outersize_db, purchase_rate_gst = $purchaseratewogst_db, moq = $moq_db";
//             echo $insert_qry ;
//             exit;
            $result = $db->execInsert($insert_qry);
            
            if (isset($json_obj) && $json_obj !== null && $json_obj !== "null" && !empty($json_obj)) {
                        $insert_qry = "insert into it_masters_logs set master_id = $result, master_type = 'SKU Master', updateby_id = $userid, change_data = '$json_obj',createtime = now(),updatetime = now()";
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
