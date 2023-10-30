<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/db/DBLogic.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";

class cls_sku_master_edit extends cls_renderer{

    var $params;
    var $sku_id;
        
    function __construct($params=null) {
        $this->params = $params;
        if (isset($this->params["sku_id"]) != "") {
            $this->sku_id = $this->params["sku_id"];
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
            $menuitem = "skumaster";//pagecode
            include "sidemenu.php";
            $dbl = new DBLogic();
            $formResult = $this->getFormResult();
            $sObj = $dbl->getSkuMasterDetails($this->sku_id);
//             print_r($sObj);
            ?>
            <div class="container-section">
                <div class="row">
                    <div class="col-md-8">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <h2 class="title-bar">Edit SKU Master</h2>
                                <div class="common-content-block"> 
                                    <div class="box box-primary">                                            
                                        <form  class="form-horizontal" enctype="multipart/form-data" method="post" action="formpost/edit_sku_master.php">
                                            <input type="hidden" name="skuid" value="<?php echo $this->sku_id ?>"/>
                                            <input type = "hidden" name="form_id" id="form_id" value="editskumaster">
                                            <input type="hidden" name="old_sku" value="<?php echo $sObj->sku ?>"/>
                                            <input type="hidden" name="old_ean" value="<?php echo $sObj->ean ?>"/>
                                            <input type="hidden" name="old_category" value="<?php echo $sObj->category ?>"/>
                                            <input type="hidden" name="old_product_name" value="<?php echo $sObj->product_name ?>"/>
                                            <input type="hidden" name="old_mrp" value="<?php echo $sObj->mrp ?>"/>
                                            <input type="hidden" name="old_gst" value="<?php echo $sObj->gst ?>"/>
                                            <input type="hidden" name="old_inner_size" value="<?php echo $sObj->inner_size ?>"/>
                                            <!--<input type="hidden" name="old_case_size" value="<?php // echo $sObj->case_size ?>"/>-->
                                            <input type="hidden" name="old_outer_size" value="<?php echo $sObj->outer_size ?>"/>
                                            <input type="hidden" name="old_purchase_rate_gst" value="<?php echo $sObj->purchase_rate_gst ?>"/>
                                            <input type="hidden" name="old_moq" value="<?php echo $sObj->moq ?>"/>
                                            
                                            <div class="box-body">
                                                
                                                <div class="form-group">
                                                    <label id="name" class="col-md-3 control-label" >Chain Name</label>
                                                    <div class="col-md-9">
                                                        <input type="text" readonly id="chain" name="chain" class="form-control" value = "<?php echo $sObj->displayname ?>" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label id="add" class="col-md-3 control-label">SKU</label>
                                                    <div class="col-md-9">
                                                        <input type="text" class="form-control" id="sku" name="sku" value = "<?php echo $sObj->sku ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label id="ph" class="col-md-3 control-label" >EAN</label>
                                                    <div class="col-md-9">
                                                        <input type="number" class="form-control" id="ean" name="ean" value = "<?php echo $sObj->ean ?>" min="0" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label id="ph" class="col-md-3 control-label" >Classification</label>
                                                    <div class="col-md-9">
                                                        <input type="text" class="form-control" id="classification" name="category" value = "<?php echo $sObj->category ?>" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label id="ph" class="col-md-3 control-label" >Product Name</label>
                                                    <div class="col-md-9">
                                                        <input type="text" class="form-control" id="product_name" name="product_name" value = "<?php echo $sObj->product_name ?>" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label id="ph" class="col-md-3 control-label" >MRP</label>
                                                    <div class="col-md-9">
                                                        <input type="number" class="form-control" id="mrp" name="mrp" value = "<?php echo $sObj->mrp ?>" min="0" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label id="ph" class="col-md-3 control-label" >GST</label>
                                                    <div class="col-md-9">
                                                        <input type="text" class="form-control" id="gst" name="gst" value = "<?php echo $sObj->gst ?>" pattern="\d+(\.\d+)?%" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label id="ph" class="col-md-3 control-label" >Inner Size</label>
                                                    <div class="col-md-9">
                                                        <input type="number" class="form-control" id="inner_size" name="inner_size" value = "<?php echo $sObj->inner_size ?>" min="0" required>
                                                    </div>
                                                </div>
<!--                                                <div class="form-group">
                                                    <label id="ph" class="col-md-3 control-label" >Case Size</label>
                                                    <div class="col-md-9">
                                                        <input type="number" class="form-control" id="case_size" name="case_size" value = "<?php echo $sObj->case_size ?>" min="0" required>
                                                    </div>
                                                </div>-->
                                                <div class="form-group">
                                                    <label id="ph" class="col-md-3 control-label" >Outer Size</label>
                                                    <div class="col-md-9">
                                                        <input type="number" class="form-control" id="outer_size" name="outer_size" value = "<?php echo $sObj->outer_size ?>" min="0" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label id="ph" class="col-md-3 control-label" >Purchase Rate W/O GST</label>
                                                    <div class="col-md-9">
                                                        <input type="number" class="form-control" id="purchase_rate_gst" name="purchase_rate_gst" value = "<?php echo $sObj->purchase_rate_gst ?>" step="0.01" min="0" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label id="ph" class="col-md-3 control-label" >MOQ</label>
                                                    <div class="col-md-9">
                                                        <input type="number" class="form-control" id="moq" name="moq" value = "<?php echo $sObj->moq ?>" min="0" required>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                            <!-- /.box-body -->
                                            <div class="box-footer">
                                                <button type="submit" class="btn btn-primary pull-right" value="Submit" >Submit</button>
                                            </div>                   
                                        </form>
                                        <?php if ($formResult->form_id == 'editskumasterform') { ?>
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


