<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
<head>
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
	
	if(isset($_POST["submit"]))
	{
            $file = $_FILES['file']['tmp_name'];
            $handle = fopen($file, "r");
            $uploadfile= new Itemsupload();
            $result=$uploadfile->upload($handle);
            if($result>0){
		    echo "Your database has imported successfully. You have inserted/updated ". $result ." recoreds";
		}else{
		    echo "Sorry! There is some problem.";
		}
	}
   
?>
</body>
</html>
