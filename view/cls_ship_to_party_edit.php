<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/db/DBLogic.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";

class cls_ship_to_party_edit extends cls_renderer{

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
            $menuitem = "shiptoparty";//pagecode
            include "sidemenu.php";
            $dbl = new DBLogic();
            $formResult = $this->getFormResult();
            $locObj = $dbl->getshiptopartyDetailsByShippingId($this->shipping_id);
            // print_r($locObj);
            ?>
            <div class="container-section">
                <div class="row">
                    <div class="col-md-8">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <h2 class="title-bar">Edit Ship To Party</h2>
                                <div class="common-content-block"> 
                                    <div class="box box-primary">                                            
                                        <form  class="form-horizontal" enctype="multipart/form-data" method="post" action="formpost/edit_ship_to_party.php">
                                            <input type="hidden" name="shippingid" value="<?php echo $this->shipping_id ?>"/>
                                            <input type = "hidden" name="form_id" id="form_id" value="editshiptopartyform">
                                            <input type="hidden" name="old_shiptoparty" value="<?php echo $locObj->ship_to_party ?>"/>
                                            <input type="hidden" name="old_siteidentifier" value="<?php echo $locObj->site ?>"/>
                                            <input type="hidden" name="old_site_identifier_type" value="<?php echo $locObj->site_identifier_type ?>"/>
<!--                                            <input type="hidden" name="old_category" value="<?php // echo $locObj->category ?>"/>
                                            <input type="hidden" name="old_plant" value="<?php // echo $locObj->plant ?>"/>-->
                                            <input type="hidden" name="old_customer_name" value="<?php echo $locObj->customer_name ?>"/>
                                            <!--<input type="hidden" name="old_margin" value="<?php echo $locObj->margin ?>"/>-->
                                            <input type="hidden" name="old_distribution_channel" value="<?php echo $locObj->distribution_channel ?>"/>
                                            <input type="hidden" name="old_sales_document_type" value="<?php echo $locObj->sales_document_type ?>"/>
                                            <input type="hidden" name="old_distribution_channel_code" value="<?php echo $locObj->distribution_channel_code ?>"/>
                                            <div class="box-body">
                                                <div class="form-group">
                                                    <label id="name" class="col-md-3 control-label" >Chain Name</label>
                                                    <div class="col-md-9">
                                                        <input type="text" readonly id="chain" name="chain" class="form-control" value = "<?php echo $locObj->displayname ?>" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label id="add" class="col-md-3 control-label">Ship To Party</label>
                                                    <div class="col-md-9">
                                                        <input type="number" class="form-control" id="shiptoparty" name="shiptoparty" value = "<?php echo $locObj->ship_to_party ?>" min="0" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label id="ph" class="col-md-3 control-label" >Site Indentifier</label>
                                                    <div class="col-md-9">
                                                        <input type="text" class="form-control" id="siteindentifier" name="siteindentifier" value = "<?php echo $locObj->site ?>"  pattern="^[A-Za-z0-9]+$" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label id="ph" class="col-md-3 control-label" >Site Indentifier Type</label>
                                                    <div class="col-md-9">
                                                        <input type="text" class="form-control" id="site_identifier_type" name="site_identifier_type" value = "<?php echo $locObj->site_identifier_type ?>" required>
                                                    </div>
                                                </div>
<!--                                                <div class="form-group">
                                                    <label id="ph" class="col-md-3 control-label" >Classification</label>
                                                    <div class="col-md-9">
                                                        <input type="text" class="form-control" id="classification" name="category" value = "<?php // echo $locObj->category ?>" pattern="[A-Za-z_]+" required>
                                                    </div>
                                                </div>-->
<!--                                                <div class="form-group">
                                                    <label id="ph" class="col-md-3 control-label" >Plant</label>
                                                    <div class="col-md-9">
                                                        <input type="number" class="form-control" id="plant" name="plant" value = "<?php echo $locObj->plant; ?>" min="0" required>
                                                    </div>
                                                </div>-->
                                                <div class="form-group">
                                                    <label id="ph" class="col-md-3 control-label" >Customer Name</label>
                                                    <div class="col-md-9">
                                                        <input type="text" class="form-control" id="customer_name" name="customer_name" value = "<?php echo $locObj->customer_name; ?>" required>
                                                    </div>
                                                </div>
<!--                                                <div class="form-group">
                                                    <label id="ph" class="col-md-3 control-label" >Margin</label>
                                                    <div class="col-md-9">
                                                        <input type="text" class="form-control" id="margin" name="margin" value = "<?php // echo $locObj->margin; ?>" pattern="\d{1,2}(\.\d{1,2})?%" required>
                                                    </div>
                                                </div>-->
                                                <div class="form-group">
                                                    <label id="ph" class="col-md-3 control-label" >Distribution Channel</label>
                                                    <div class="col-md-9">
                                                        <input type="text" class="form-control" id="distribution_channel" name="distribution_channel" value = "<?php echo $locObj->distribution_channel; ?>" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label id="ph" class="col-md-3 control-label" >Sales Document Type</label>
                                                    <div class="col-md-9">
                                                        <input type="text" class="form-control" id="sales_document_type" name="sales_document_type" value = "<?php echo $locObj->sales_document_type; ?>" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label id="add" class="col-md-3 control-label">Distribution Channel Code</label>
                                                    <div class="col-md-9">
                                                        <input type="number" class="form-control" id="distribution_channel_code" name="distribution_channel_code" value = "<?php echo $locObj->distribution_channel_code ?>" min="0" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /.box-body -->
                                            <div class="box-footer">
                                                <button type="submit" class="btn btn-primary pull-right" value="Submit" >Submit</button>
                                            </div>                   
                                        </form>
                                        <?php if ($formResult->form_id == 'editshiptopartyform') { ?>
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


