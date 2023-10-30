  <?php
  require_once("../../it_config.php");
  require_once("session_check.php");
  require_once "lib/db/DBConn.php";
  require_once "lib/db/DBLogic.php";
  require_once "lib/core/Constants.php";
  $errors = array();
  $success = "Uploaded Sucessfully";
  $db = new DBConn();
  $dbl = new DBLogic();
  extract($_POST);

  $_SESSION['form_post'] = $_POST;
  $_SESSION['form_id'] = $form_id;

  $fileName = $_FILES['csv']['name'];
  $tmpName = $_FILES['csv']['tmp_name'];

  $ext = end((explode(".",$fileName)));

  $currStore = getCurrStore();
  $userid = getCurrStoreId();

  if($ext != "csv" ){
    $errors["name"] = "Please upload .csv file only";
  }else{
    $dir = "../uploads/";
    $newfile = $dir."Sales_Data_".date("Ymd-His") . ".csv";
    if (!move_uploaded_file($tmpName, $newfile)) {
        $errors['fileerr'] = "File unable to load";
    }else{
      // print_r($newfile);
      $resp=checkSequenceFile($newfile);
      // print_r($resp);  
      $result = explode("<>", $resp);
      if($result[0] == 1){
        $resp = checkFile($newfile);
        // print_r($resp);
        $result = explode("<>", $resp);
        if($result[0] == 1){
          $resultInsert = saveData($newfile, $userid);
          if(!$resultInsert){
            $errors['error_insert'] = "Data insertion fail.";
          }
        }else{
          $errors['err'] = $result[1];
        }
      }else{
        $errors['err'] = $result[1];
      }
    }
    if(count($errors) == 0){
      $response = array(
        "error"   => 0,
        "spanid" => "success",
        "message" => $success
      ); 
    }else{
      $response = array(
        "error"   => 0,
        "spanid" => "success",
        "message" => "Unable to load clusters. Please try again later."
      ); 
    }
  }

  END:
  // print_r($errors);
      if (count($errors) > 0) {
        $_SESSION['form_errors'] = $errors;
        $redirect = "sales/report";
    } else {
        unset($_SESSION['form_errors']);
        $_SESSION['form_success'] = $success;
        $redirect = "sales/report";
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
      if (strcmp(strtolower(str_replace(" ", "", $data[0])), "ponumber") !== 0){
        $resp .= "<br/>column " . $col++ . " is not PO Number";
      }
      else{
        $col++;
      }
      if (strcmp(strtolower(str_replace(" ", "", $data[1])), "product") !== 0){
        $resp .= "<br/>column " . $col++ . " is not Product";
      }
      else{
        $col++;
      }
      if (strcmp(strtolower(str_replace(" ", "", $data[2])), "quantity") !== 0){
        $resp .= "<br/>column " . $col++ . " is not Quantity";
      }
      else{
        $col++;
      }
      if (strcmp(strtolower(str_replace(" ", "", $data[3])), "value") !== 0){
        $resp .= "<br/>column " . $col++ . " is not Value";
      }
      if (strcmp(strtolower(str_replace(" ", "", $data[4])), "saledate") !== 0){
        $resp .= "<br/>column " . $col++ . " is not Sale Date";
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

        if ( $flag == 1 && isset($data[0]) && isset($data[1]) && isset($data[2]) && isset($data[3]) && isset($data[4]))  {
          $po_number = isset($data[0]) ? $data[0] : "";
          $product = isset($data[1]) ? $data[1] : "";
          $quantity = isset($data[2]) ? $data[2] : "";
          $value = isset($data[3]) ? $data[3] : "";
          $sale_date = isset($data[4]) ? $data[4] : "";
          $line++;
          // print_r($data);
          if (trim($po_number) != "") {
            if (preg_match("/[!$#@%,:]/", $po_number) == 1) {
              $resp .= "<br/>Error at line $line. Special Characters are not allowed in PO Number.";
            }
          }else{
                $resp .= "<br/>Error at line $line. Provide PO number.";
          }

          if (trim($product) != "") {
            if (preg_match("/[!$#@%,:]/", $product) == 1) {
              $resp .= "<br/>Error at line $line. Special Characters are not allowed in Product Name.";
            }else{
              $product_db = $db->safe(trim($product));
              $prod_qry = "select id from it_master_items where itemname = $product_db";
              // echo $prod_qry;
              $prod_obj = $db->fetchobject($prod_qry);
              if(!isset($prod_obj) || empty($prod_obj)){
                $resp .= "<br/>Error at line $line. Product Name $product_db not found.";
              }                              
            }
          }else{
                $resp .= "<br/>Error at line $line. Provide Product Name.";
          }

          if (trim($quantity) != "") {
            if (preg_match("/[!$#@%,:]/", $quantity) == 1) {
              $resp .= "<br/>Error at line $line. Special Characters are not allowed in Quantity.";
            }else if(!$quantity || !ctype_digit($quantity) || $quantity < 0) {
                $resp .= "<br/>Error at line $line. Please enter correct Quantity.";
            }
          }else{
                $resp .= "<br/>Error at line $line. Provide Quantity.";
          }

          if (trim($value) != "") {
            if (preg_match("/[!$#@%,:]/", $value) == 1) {
              $resp .= "<br/>Error at line $line. Special Characters are not allowed in Value.";
            }else if(!$value || !ctype_digit($value) || $value < 0) {
                $resp .= "<br/>Error at line $line. Please enter correct Value.";
            }
          }else{
                $resp .= "<br/>Error at line $line. Provide Value.";
          }

          if (trim($sale_date) != "") {
            if (preg_match("/[!$#@%,:]/", $sale_date) == 1) {
              $resp .= "<br/>Error at line $line. Special Characters are not allowed in Sale Date.";
            }else if(!isDate($sale_date)){
              $resp .= "<br/>Error at line $line. Provide Sale date in dd-mm-yyyy format.";
            }
          }else{
                $resp .= "<br/>Error at line $line. Provide Sale Date.";
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

  function saveData($newfile, $userid){
    $db = new DBConn();
    if (($handle = fopen("$newfile", "r")) !== FALSE) {
      $line = 1;      
      $flag = 0;
      while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        if ($flag == 0) {
          $flag = 1;
          continue;
        }

        if ( $flag == 1 && isset($data[0]) && isset($data[1]) && isset($data[2]) && isset($data[3]) && isset($data[4]))  {
          $po_number = isset($data[0]) ? $data[0] : "";
          $product = isset($data[1]) ? $data[1] : "";
          $quantity = isset($data[2]) ? $data[2] : "";
          $value = isset($data[3]) ? $data[3] : "";
          $sale_date = isset($data[4]) ? $data[4] : "";
          $line++;

          $newsale_date = date("Y-m-d", strtotime($sale_date));

          $product_db = $db->safe(trim($product));
          $prod_qry = "select id from it_master_items where itemname = $product_db";
          $prod_obj = $db->fetchobject($prod_qry);


          $insert_qry = "insert into it_sales_data set invoice_no = '$po_number', item_id = '$prod_obj->id', quantity = '$quantity', value = '$value', sale_date = '$newsale_date', updated_by = '$userid', createtime = now()";
          $result = $db->execInsert($insert_qry);

          if($result == 0 || $result == -1){
            return 0;
          }else{
            $resultFlag = 1;
          }
      }
    }
    return $resultFlag;
  }
}

function isDate($string) {
    $matches = array();
    $pattern = '/^([0-9]{1,2})\\-([0-9]{1,2})\\-([0-9]{4})$/';
    if (!preg_match($pattern, $string, $matches)) return false;
    if (!checkdate($matches[2], $matches[1], $matches[3])) return false;
    return true;
}
  

?>
