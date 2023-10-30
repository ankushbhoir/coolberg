<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "session_check.php";

class cls_ship_to_party_upload extends cls_renderer {

    var $currStore;
    function __construct($params = null) {
        $this->currStore = getCurrStore();
    }

    function extraHeaders() {
        ?>
        <style type="text/css" title="currentStyle">
/*            @import "https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css";
            @import "https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css";*/
            @import "css/dataTablescss/dataTables.bootstrap.min.css";
            @import "css/dataTablescss/responsive.bootstrap.min.css";
        </style>
       
        <script type="text/javaScript">  
            function downloadfile(){ 
                window.location.href="samplefiles/sample_ship_to_party_upload.csv";
            } 
        </script>
        <!--<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />-->
        <link rel="stylesheet" href="css/bootstrap-select.min.css" />
        <?php
    }

    public function pageContent() {
        $menuitem = "shiptoparty";
        include "sidemenu.php";
        $formResult = $this->getFormResult();
        $db = new DBConn();
        
        ?>
        <div class="container-section">
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <h2 class="title-bar">Upload Ship To Party File</h2>
                            <div class="common-content-block"> 
                                <div class="box box-primary"><br>
                                    <div class="box-body">
                                            
                                         <form action="formpost/upload_ship_to_party.php" id="upload_ship_to_party" name="upload_ship_to_party" method="post" enctype="multipart/form-data">
                                            <input type = "hidden" name="form_id" id="form_id" value="uploadShipToPartyform">
                                            <div class="box-body">
                                                <div class="form-group">
                                                    <label>Select File:*</label>
                                                    <input type="file" accept=".csv" name="file" id="file" required="">

                                                </div>
                                            </div>
                                             <div class="box-footer">
                                                <button type="submit" class="btn btn-primary" style="float:right;">Upload</button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="box-footer">
                                        <button  id="download_sample_csv_file" onclick="downloadfile();" class="btn btn-primary" style="float:left;">Download sample file </button> 
                                    </div>
                                        
                                        <?php if ($formResult->showhide != "none") { ?>
                                            <div class="alert alert-<?php echo $formResult->cssClass; ?> alert-dismissible" style="display:<?php echo $formResult->showhide; ?>;">
                                                <button class="close" type="button" data-dismiss="alert" aria-hidden="true">×</button>
                                                <h4> <?php echo $formResult->status; ?>
                                            </div>
                                        <?php } ?>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>           
        <?php
    }

}
?>


