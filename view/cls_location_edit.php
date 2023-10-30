<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/db/DBLogic.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";

class cls_location_edit extends cls_renderer{

    var $params;
    var $shipping_id;
        
    function __construct($params=null) {
        $this->params = $params;
        if (isset($this->params["shippingid"]) != "") {
            $this->shipping_id = $this->params["shippingid"];
        }
    }

    function extraHeaders() {
        ?>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/gijgo@1.9.10/js/gijgo.min.js" type="text/javascript"></script>
        <link href="https://cdn.jsdelivr.net/npm/gijgo@1.9.10/css/gijgo.min.css" rel="stylesheet" type="text/css" />
        <style type="text/css" title="currentStyle">
            /*  @import "js/datatables/media/css/demo_page.css";
            @import "js/datatables/media/css/demo_table.css";*/
            @import "https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css";
            @import "https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css";
        </style>
        <script type="text/javaScript">   
            
           $(function () {
           });
           
       </script>
       <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />      
       <?php
   }

   public function pageContent() {
            $menuitem = "locations";//pagecode
            include "sidemenu.php";
            $dbl = new DBLogic();
            $formResult = $this->getFormResult();
            $locObj = $dbl->getLocationDetailsByShippingAddress($this->shipping_id);
            // print_r($locObj);
            ?>
            <div class="container-section">
                <div class="row">
                    <div class="col-md-8">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <h2 class="title-bar">Edit Location</h2>
                                <div class="common-content-block"> 
                                    <div class="box box-primary">                                            
                                        <form  class="form-horizontal" enctype="multipart/form-data" method="post" action="formpost/edit_location.php">
                                            <input type="hidden" name="shippingid" value="<?php echo $this->shipping_id ?>"/>
                                            <input type = "hidden" name="form_id" id="form_id" value="editlocationform">
                                            <input type="hidden" name="old_custCode" value="<?php echo $locObj->customer_code ?>"/>
                                            <input type="hidden" name="old_storeCode" value="<?php echo $locObj->store_code ?>"/>
                                            <input type="hidden" name="old_dcName" value="<?php echo $locObj->dc_name ?>"/>
                                            <input type="hidden" name="old_dcAddress" value="<?php echo $locObj->dc_address ?>"/>
                                            <input type="hidden" name="old_shippingAddress" value="<?php echo $locObj->shipping_address ?>"/>
                                            <input type="hidden" name="old_dcCity" value="<?php echo $locObj->dc_city ?>"/>
                                            <div class="box-body">
                                                <div class="form-group">
                                                    <label id="name" class="col-md-3 control-label" >Chain Name</label>
                                                    <div class="col-md-9">
                                                        <input type="text" readonly id="chain" name="chain" class="form-control" value = "<?php echo $locObj->name ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label id="add" class="col-md-3 control-label">Customer Code</label>
                                                    <div class="col-md-9">
                                                        <input type="text" class="form-control" id="custCode" name="custCode" value = "<?php echo $locObj->customer_code ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label id="ph" class="col-md-3 control-label" >Store Code</label>
                                                    <div class="col-md-9">
                                                        <input type="text" class="form-control" id="storeCode" name="storeCode" value = "<?php echo $locObj->store_code ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label id="ph" class="col-md-3 control-label" >DC Name</label>
                                                    <div class="col-md-9">
                                                        <input type="text" class="form-control" id="dcName" name="dcName" value = "<?php echo $locObj->dc_name ?>" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label id="ph" class="col-md-3 control-label" >DC Address</label>
                                                    <div class="col-md-9">
                                                        <textarea type="text" rows="4" class="form-control" id="dcAddress" name="dcAddress"required><?php echo $locObj->dc_address ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label id="ph" class="col-md-3 control-label" >DC City</label>
                                                    <div class="col-md-9">
                                                        <input type="text" class="form-control" id="dcCity" name="dcCity" value = "<?php echo $locObj->dc_city ?>" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label id="ph" class="col-md-3 control-label" >DC State</label>
                                                    <div class="col-md-9">
                                                        <input type="text" readonly id="state" name="state" class="form-control" value = "<?php echo $locObj->dc_state ?>">
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


