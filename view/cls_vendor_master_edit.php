<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/db/DBLogic.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";

class cls_vendor_master_edit extends cls_renderer{

    var $params;
    var $vendorId;
        
    function __construct($params=null) {
        $this->params = $params;
        if (isset($this->params["vendorid"]) != "") {
            $this->vendorId = $this->params["vendorid"];
        }
    }

    function extraHeaders() {
        ?>
   
        <style type="text/css" title="currentStyle">
/*          @import "https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css";
         @import "https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css";*/
          @import "css/dataTablescss/dataTables.bootstrap.min.css";
          @import "css/dataTablescss/responsive.bootstrap.min.css";

        </style>
        <script type="text/javaScript">   
            
           $(function () {
           });
           
       </script>
       <!--<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />-->
       <link rel="stylesheet" href="css/bootstrap-select.min.css" />
       <?php
   }

   public function pageContent() {
            $menuitem = "vendormaster";//pagecode
            include "sidemenu.php";
            $dbl = new DBLogic();
            $formResult = $this->getFormResult();
            $vObj = $dbl->getVendorMasterDetails($this->vendorId);
            // print_r($locObj);
            ?>
            <div class="container-section">
                <div class="row">
                    <div class="col-md-8">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <h2 class="title-bar">Edit  Honasa Plant Master</h2>
                                <div class="common-content-block"> 
                                    <div class="box box-primary">                                            
                                        <form  class="form-horizontal" enctype="multipart/form-data" method="post" action="formpost/edit_vendor_master.php">
                                            <input type="hidden" name="vendorid" value="<?php echo $this->vendorId ?>"/>
                                            <input type = "hidden" name="form_id" id="form_id" value="editlvendormasterform">
                                            <input type="hidden" name="old_vendorno" value="<?php echo $vObj->vendor_number ?>"/>
                                            <input type="hidden" name="mdid" value="<?php echo $vObj->mdid ?>"/>
                                            <input type="hidden" name="old_plant" value="<?php echo $vObj->plant ?>"/>
                                            <input type="hidden" name="old_storage_location_code" value="<?php echo $vObj->storage_location_code ?>"/>

                                            <div class="box-body">
                                                <div class="form-group">
                                                    <label id="name" class="col-md-3 control-label" >Chain Name</label>
                                                    <div class="col-md-9">
                                                        <input type="text" readonly id="chain" name="chain" class="form-control" value = "<?php echo $vObj->displayname ?>" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label id="add" class="col-md-3 control-label">Plant Postal Number</label>
                                                    <div class="col-md-9">
                                                        <input type="number" class="form-control" id="vendorno" name="vendorno" value = "<?php echo $vObj->vendor_number ?>" min="0" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label id="ph" class="col-md-3 control-label" >Plant Code</label>
                                                    <div class="col-md-9">
                                                        <input type="number" class="form-control" id="plant" name="plant" value = "<?php echo $vObj->plant ?>" min="0" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label id="ph" class="col-md-3 control-label" >Storage Location Code</label>
                                                    <div class="col-md-9">
                                                        <input type="text" class="form-control" id="storage_location_code" name="storage_location_code" value = "<?php echo $vObj->storage_location_code ?>" required>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                            <!-- /.box-body -->
                                            <div class="box-footer">
                                                <button type="submit" class="btn btn-primary pull-right" value="Submit" >Submit</button>
                                            </div>                   
                                        </form>
                                        <?php if ($formResult->form_id == 'editlocationform') { ?>
                                            <div class="alert alert-<?php echo $formResult->cssClass;?> alert-dismissible" style="display:<?php echo $formResult->showhide; ?>;">
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
<!--
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>           -->

            <?php // }else{ print "You are not authorized to access this page";}
        }
    }
    ?>


