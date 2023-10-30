<?php
require_once "view/cls_renderer.php";
require_once "lib/core/Constants.php";
require_once "lib/db/DBConn.php";
require_once ("lib/db/DBLogic.php");
require_once "session_check.php";

class cls_user_edit extends cls_renderer {
    var $params;
    var $currStore;
    var $userid;
    function __construct($params=null) {
        // $this->currStore = getCurrUser();
	//parent::__construct(array(UserType::Admin, UserType::CKAdmin)); 
     parent::__construct(array());
        $this->currStore = getCurrStore();       
//        $this->params = $params;
//        $this->userid = 2;
         if($params && isset($params['userid'])){
                  $this->userid = $params['userid']; 
         }
    }

    function extraHeaders() { ?>

        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/gijgo@1.9.10/js/gijgo.min.js" type="text/javascript"></script>
    <link href="https://cdn.jsdelivr.net/npm/gijgo@1.9.10/css/gijgo.min.css" rel="stylesheet" type="text/css" /> 
    <style type="text/css" title="currentStyle">
    /*  @import "js/datatables/media/css/demo_page.css";
    @import "js/datatables/media/css/demo_table.css";*/
    @import "https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css";
    @import "https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css";
  </style>
    <script type="text/javascript">
    
</script>  
<?php
    } // extraHeaders

    public function pageContent() {
            $menuitem = "users";//pagecode
            include "sidemenu.php";
            $formResult = $this->getFormResult();
        $db = new DBConn();
        $query = "select * from it_users where id= $this->userid ";
        $uobj = $db->fetchObject($query);
        if($uobj){
        ?>
        <div class="container-section">
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-body">
                        <h2 class="title-bar">Edit User</h2>
                        <div class="common-content-block"> 
                            <div class="box box-primary">                                            
                                <form  class="form-horizontal" id="edituserform" name="edituserform" enctype="multipart/form-data" method="post" action="formpost/editUser.php">
                                    <!--<input type="hidden" name="editusrform" value="1"/>-->
                                    <input type = "hidden" name="form_id" id="form_id" value="edituserform">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label id="name" class="col-md-3 control-label" >Name</label>
                                            <div class="col-md-9">
                                                <input id="prevName" hidden name="prevName" value = "<?php echo $uobj->name ;?>">
                                                <input type="text" class="form-control" id="name" name="name" value = "<?php echo $uobj->name ;?>" required>
                                                <input type="hidden" name="u_id" value="<?php echo $uobj->id ;?>"/> 
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label id="add" class="col-md-3 control-label">Email</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" id="email" name="email"  value = "<?php echo $uobj->email ;?>" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label id="ph" class="col-md-3 control-label" >Phone</label>
                                            <div class="col-md-9">
                                                <input type="number" class="form-control" id="phone" name="phone"  value = "<?php echo $uobj->phoneno ;?>" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label id="un" class="col-md-3 control-label">Username</label>
                                            <div class="col-md-9">
                                                <input id="prevUsername" name="prevUsername" hidden value = "<?php echo $uobj->username ;?>">
                                                <input type="text" class="form-control" id="username" name="username"  value = "<?php echo $uobj->username ;?>" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label id="ph" class="col-md-3 control-label" >Password</label>
                                            <div class="col-md-9">
                                                <input type="password" class="form-control" id="password" name="password"  value = "" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label id="status" class="col-md-3 control-label" >Status</label>
                                            <div class="col-md-9">
                                               <input type="radio" class="radio-inline" value="0" id="inactive" name="inactive" <?php if($uobj->inactive == 0){ echo "checked"; } ?> > Active
                                               <input type="radio" class="radio-inline" value="1" id="inactive" name="inactive" <?php if($uobj->inactive == 1){ echo "checked"; } ?> > In active
                                           </div>
                                        </div>
                                    </div>
                                    <!-- /.box-body -->
                                    <div class="box-footer">
                                        <button type="submit" class="btn btn-primary" value="Submit" >Submit</button>
                                    </div>                   
                                </form>
                                <?php if ($formResult->form_id == 'edituserform') { ?>
                                <div class="alert alert-<?php echo $formResult->cssClass;?> alert-dismissible" style="display:<?php echo $formResult->showhide; ?>;">
                                    <button class="close" type="button" data-dismiss="alert" aria-hidden="true">×</button>
                                    <h4> <?php echo $formResult->status; ?>
                                    <?php   
                                    if (isset($formResult->cssClass) && $formResult->cssClass == 'success' && !isset($_SESSION['creditnoteupload'])) {                                                        
                                                    ?>
                                        
                                    <?php } ?>   
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                        </div>
                    </div>   
                </div>
            </div>
        </div>            
    <?php
        }
    } //pageContent
}//class
?>
