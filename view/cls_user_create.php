<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/db/DBLogic.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";

class cls_user_create extends cls_renderer{

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
/*            @import "https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css";
            @import "https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css";*/
            @import "css/dataTablescss/dataTables.bootstrap.min.css";
            @import "css/dataTablescss/responsive.bootstrap.min.css";
/*            option{
                font-weight:bold; 
            }
            div.alert *
            {
                color: red;
            }*/
        </style>
        <script type="text/javaScript">   
            
             $(function () {
        });
            
        </script>
        <!--        <link rel="stylesheet" href="css/bigbox.css" type="text/css" />-->
        <!--<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />-->
        <link rel="stylesheet" href="css/bootstrap-select.min.css" />
        <?php
        }

        public function pageContent() {
            $menuitem = "users";//pagecode
            include "sidemenu.php";
            $dbl = new DBLogic();
            $formResult = $this->getFormResult();
            // print_r($_SESSION['form_post']);
//             print_r($formResult);
?>
<div class="container-section">
            <div class="row">
                <div class="col-md-8">
                    <div class="panel panel-default">
                        <div class="panel-body">
                        <h2 class="title-bar">Create User</h2>
                        <div class="common-content-block"> 
                            <div class="box box-primary">                                            
                                <form  class="form-horizontal" id="createuserform" enctype="multipart/form-data" method="post" action="formpost/createuser.php">
                                    <!--<input type="hidden" name="editusrform" value="1"/>-->
                                    <input type = "hidden" name="form_id" id="form_id" value="createuserform">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label id="name" class="col-md-3 control-label" >Select Role</label>
                                            <div class="col-md-9">
                                                <select id="utypesel" name="utypesel" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" onchange="getBySelectedRole(this.value);">
                                                    <?php
                                                    $roles = $dbl->getAllRoles();
                                                    if (!empty($roles)) {
                                                        ?>
                                                        <option disabled selected value="">Select Role</option>
                                                        <?php
                                                        foreach ($roles as $role) {
                                                            if (isset($role) && !empty($role) && $role != null) {
                                                                ?>
                                                                <option value="<?php echo $role->id; ?>"><?php echo $role->name; ?></option>
                                                            <?php
                                                            }
                                                        }
                                                    }
                                                    ?>

                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label id="name" class="col-md-3 control-label" >Name</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" id="name" name="name" value = "" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label id="add" class="col-md-3 control-label">Email</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" id="email" name="email"  value = "" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label id="ph" class="col-md-3 control-label" >Phone</label>
                                            <div class="col-md-9">
                                                <input type="number" class="form-control" id="phone" name="phone"  value = "" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label id="un" class="col-md-3 control-label">Username</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" id="username" name="username"  value = "" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label id="ph" class="col-md-3 control-label" >Password</label>
                                            <div class="col-md-9">
                                                <input type="password" class="form-control" id="password" name="password"  value = "" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label id="ph" class="col-md-3 control-label" >Confirm Password</label>
                                            <div class="col-md-9">
                                                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword"  value = "" required>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.box-body -->
                                    <div class="box-footer">
                                        <button type="submit" class="btn btn-primary pull-right" value="Submit" >Create</button>
                                    </div>                   
                                </form>
                                <?php if ($formResult->form_id == 'createuserform') { ?>
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


