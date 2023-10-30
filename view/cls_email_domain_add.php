<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/db/DBLogic.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";

class cls_email_domain_add extends cls_renderer{

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
            $menuitem = "emaildomain";//pagecode
            include "sidemenu.php";
            $dbl = new DBLogic();
            $formResult = $this->getFormResult();
            
            ?>
            <div class="container-section">
                <div class="row">
                    <div class="col-md-8">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <h2 class="title-bar">Email Domain Add</h2>
                                <div class="common-content-block"> 
                                    <div class="box box-primary">                                            
                                        <form  class="form-horizontal" enctype="multipart/form-data" method="post" action="formpost/add_email_domain.php">
                                            
                                            <input type = "hidden" name="form_id" id="form_id" value="addemaildomain">
                                            <div class="box-body">
                                                <div class="form-group">
                                                    <label id="name" class="col-md-3 control-label" >Email Domain</label>
                                                    <div class="col-md-9">
                                                        <input type="text" id="domain" name="domain" class="form-control" value = "" pattern="^[a-z0-9._%+-]+\.[a-z]{2,4}$" required>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                            <!-- /.box-body -->
                                            <div class="box-footer">
                                                <button type="submit" class="btn btn-primary pull-right" value="Submit" >Submit</button>
                                            </div>                   
                                        </form>
                                        <?php if ($formResult->form_id == 'addemaildomain') { ?>
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


