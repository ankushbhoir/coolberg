<?php
require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");

?>

<form id="chk" action="../../home/Parsers/processVentasysTxt.php" method="post" enctype="multipart/form-data">
    
    <label>Write ini file: </label>                                
        <p> <textarea name="txt" rows="30" cols="100"></textarea>  </p>   
          
        <p><label>Select Text File To Parse</label>                    
            <input type="file" name="file" value="">    
        </p>                
                
        
        
        
      <input type="submit" value="Check">
</form>                
