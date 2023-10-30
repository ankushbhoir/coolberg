<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/db/DBLogic.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";

class cls_missing_master_email_edit extends cls_renderer{

    var $params;
    var $m_id;
        
    function __construct($params=null) {
        $this->params = $params;
        if (isset($this->params["m_id"]) != "") {
            $this->m_id = $this->params["m_id"];
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
            $menuitem = "missingmasteremail";//pagecode
            include "sidemenu.php";
            $dbl = new DBLogic();
            $formResult = $this->getFormResult();
            $mObj = $dbl->getMissingMasterEmailDetails($this->m_id);
            // print_r($locObj);
            ?>
            <div class="container-section">
                <div class="row">
                    <div class="col-md-8">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <h2 class="title-bar">Edit SKU Master</h2>
                                <div class="common-content-block"> 
                                    <div class="box box-primary">                                            
                                        <form  class="form-horizontal" enctype="multipart/form-data" method="post" action="formpost/edit_missing_master_email.php">
                                            <input type="hidden" name="mid" value="<?php echo $this->m_id ?>"/>
                                            <input type = "hidden" name="form_id" id="form_id" value="editmissingmasteremail">
                                            <input type="hidden" name="old_email" value="<?php echo $mObj->emails ?>"/>
                                            <div class="box-body">
                                                
                                                <div class="form-group">
                                                    <label id="add" class="col-md-3 control-label">MASTER NAME</label>
                                                    <div class="col-md-9">
                                                        <input type="text" class="form-control" id="master_name" name="master_name" value = "<?php echo $mObj->master_name ?>" readonly>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label id="ph" class="col-md-3 control-label" >EMAIL</label>
                                                    <div class="col-md-9">
                                                        <input type="text" class="form-control" id="emails" name="emails" value = "<?php echo $mObj->emails ?>">
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


