<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/db/DBLogic.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";

class cls_chain_wise_product_edit extends cls_renderer{

    var $params;
    var $product_id;
        
    function __construct($params=null) {
        $this->params = $params;

        if (isset($this->params["productid"]) != "") {
            $this->product_id = $this->params["productid"];
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
            $prodObj = $dbl->getProductDetailsByDealerItemId($this->product_id);
            // print_r($prodObj);
            ?>
            <div class="container-section">
                <div class="row">
                    <div class="col-md-8">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <h2 class="title-bar">Edit Location</h2>
                                <div class="common-content-block"> 
                                    <div class="box box-primary">                                            
                                        <form  class="form-horizontal" enctype="multipart/form-data" method="post" action="formpost/edit_product_master.php">
                                            <input type="hidden" name="productId" value="<?php echo $this->product_id ?>"/>
                                            <input type="hidden" name="itemId" value="<?php echo $prodObj->itemId ?>"/>
                                            <input type = "hidden" name="form_id" id="form_id" value="editproductform">
                                            <input type="hidden" name="old_chain_article" value="<?php echo $prodObj->Artical_No ?>"/>
                                            <input type="hidden" name="old_prod_desc" value="<?php echo $prodObj->Product_Description ?>"/>
                                            <input type="hidden" name="old_mrp" value="<?php echo $prodObj->MRP ?>"/>
                                            <input type="hidden" name="old_ean" value="<?php echo $prodObj->EAN ?>"/>
                                            <input type="hidden" name="old_fg_code" value="<?php echo $prodObj->FG_Code ?>"/>
                                            <div class="box-body">
                                                <div class="form-group">
                                                    <label id="name" class="col-md-3 control-label" >Chain Name</label>
                                                    <div class="col-md-9">
                                                        <input type="text" readonly id="chain" name="chain" class="form-control" value = "<?php echo $prodObj->Chain_Name ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label id="add" class="col-md-3 control-label">Chain Article No.</label>
                                                    <div class="col-md-9">
                                                        <input type="text" class="form-control" id="chain_article" name="chain_article" value = "<?php echo $prodObj->Artical_No ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label id="ph" class="col-md-3 control-label" >Master Product Desciption</label>
                                                    <div class="col-md-9">
                                                        <input type="text" class="form-control" id="prod_desc" name="prod_desc" value = "<?php echo $prodObj->Product_Description ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label id="ph" class="col-md-3 control-label" >Master MRP</label>
                                                    <div class="col-md-9">
                                                        <input type="text" class="form-control" id="mrp" name="mrp" value = "<?php echo $prodObj->MRP ?>" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label id="ph" class="col-md-3 control-label" >Master EAN</label>
                                                    <div class="col-md-9">
                                                        <input type="text" rows="4" class="form-control" id="ean" name="ean" value = "<?php echo $prodObj->EAN ?>" required > 
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label id="ph" class="col-md-3 control-label" >Master FG Code</label>
                                                    <div class="col-md-9">
                                                        <input type="text" class="form-control" id="fg_code" name="fg_code" value = "<?php echo $prodObj->FG_Code ?>" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /.box-body -->
                                            <div class="box-footer">
                                                <button type="submit" class="btn btn-primary pull-right" value="Submit" >Submit</button>
                                            </div>                   
                                        </form>
                                        <?php if ($formResult->form_id == 'editproductform') { ?>
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


