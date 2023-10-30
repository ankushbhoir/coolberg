<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/db/DBLogic.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";

class cls_location_create extends cls_renderer{

    var $currStore;
    var $userid;
    var $params;
        // print_r($params);
        // exit();
    function __construct($params=null) {
// parent::__construct(array(UserType::Admin,UserType::WKAdmin,UserType::CRM_Manager,UserType::City_Head));
        $this->currStore = getCurrStore();
        $this->params = $params;
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
            $chainListObj = $dbl->getChainList();
            $stateListObj = $dbl->getStateList();
            $formResult = $this->getFormResult();
            ?>
            <div class="container-section">
                <div class="row">
                    <div class="col-md-8">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <h2 class="title-bar">Create Location</h2>
                                <div class="common-content-block"> 
                                    <div class="box box-primary">                                            
                                        <form  class="form-horizontal" enctype="multipart/form-data" method="post" action="formpost/create_location.php">
                                            <!--<input type="hidden" name="editusrform" value="1"/>-->
                                            <input type = "hidden" name="form_id" id="form_id" value="createlocationform">
                                            <div class="box-body">
                                                <div class="form-group">
                                                    <label id="name" class="col-md-3 control-label" >Chain Name</label>
                                                    <div class="col-md-9">
                                                        <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" id="chain" name="chain">
                                                            <option value="" disabled selected>Select Chain</option>
                                                            <?php
                                                            if (isset($chainListObj)) {
                                                                foreach ($chainListObj as $chainObj) { ?>
                                                                    <option value="<?php echo $chainObj->id ?>"><?php echo $chainObj->name; ?></option> <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label id="add" class="col-md-3 control-label">Customer Code</label>
                                                    <div class="col-md-9">
                                                        <input type="text" class="form-control" id="custCode" name="custCode" value = "">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label id="ph" class="col-md-3 control-label" >Store Code</label>
                                                    <div class="col-md-9">
                                                        <input type="text" class="form-control" id="storeCode" name="storeCode" value = "">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label id="ph" class="col-md-3 control-label" >DC Name</label>
                                                    <div class="col-md-9">
                                                        <input type="text" class="form-control" id="dcName" name="dcName" value = "" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label id="ph" class="col-md-3 control-label" >DC Address</label>
                                                    <div class="col-md-9">
                                                        <textarea type="text" style="white-space: pre-wrap" rows="4" class="form-control" id="dcAddress" name="dcAddress" value = "" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label id="ph" class="col-md-3 control-label" >DC City</label>
                                                    <div class="col-md-9">
                                                        <input type="text" class="form-control" id="dcCity" name="dcCity" value = "" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label id="ph" class="col-md-3 control-label" >DC State</label>
                                                    <div class="col-md-9">
                                                        <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" data-dropup-auto="false" id="state" name="state">
                                                            <option value="" disabled selected>Select State</option>
                                                            <?php
                                                            if (isset($stateListObj)) {
                                                                foreach ($stateListObj as $stateObj) { ?>
                                                                    <option value="<?php echo $stateObj->id ?>"><?php echo $stateObj->name; ?></option> <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /.box-body -->
                                            <div class="box-footer">
                                                <button type="submit" class="btn btn-primary pull-right" value="Submit" >Create</button>
                                            </div>                   
                                        </form>
                                        <?php if ($formResult->form_id == 'createlocationform') { ?>
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


