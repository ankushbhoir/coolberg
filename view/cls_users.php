<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";
require_once "lib/db/DBLogic.php";

class cls_users extends cls_renderer{

        var $currStore;
        var $userid;
        var $dtrange;
        var $params;
        var $cid;
        var $uid;
        var $pid;
        var $sid = -1;
        var $postatus = "";
         
        function __construct($params=null) {
            $this->currStore = getCurrStore();
            $this->params = $params;
            if(isset($this->params["postatus"]) != ""){
                $this->postatus = $this->params["postatus"];
            }else{
                $this->postatus = 0;
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
<script type="text/javaScript">    
$(function(){     
    var url = "ajax/tb_users.php";
    //alert(url);
    oTable = $('#tb_users').dataTable( {
	"bProcessing": true,
	"bServerSide": true,
        "aoColumns": [null,null,null,null,null,null,null,{bSortable:false}],
	"sAjaxSource": url,
        "aaSorting": []  
    } );
// search on pressing Enter key only
    $('.dataTables_filter input').unbind('keyup').bind('keyup', function(e){
	if (e.which == 13){                     
		oTable.fnFilter($(this).val(), null, false, true);
	}
    });    
});
    
function createUser(){
    window.location.href = "user/create";
}    
  
function deleteUser(userid){
    var r = confirm("Are you sure you want to mark this User as Inactive");
    if(r){
     var ajaxURL = "ajax/inactiveUser.php?userid=" + userid;
         $.ajax({
         url:ajaxURL,
             dataType: 'json',
             success:function(data){
                 //alert(data.error);
                 if (data.error == "1") {
                     alert(data.msg);
                 } else {
                     window.location.href = "users";
                 }
             }
         });
    }
}

function editUser(id){
    window.location.href = "user/edit/userid="+id;
}     

</script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
<?php }

        public function pageContent() {
            $menuitem = "users";
            include "sidemenu.php";
//            $dbl = new DBLogic();            
            $poarray = POStatus::getAll();
            //print_r($poarray);
?>

<div class="container-section">
    <div class="row">
        <div class="col-md-3">
        </div>
        <div class="col-md-3">
        </div>
        <div class="col-md-3">
        </div>
        <div class="col-md-3">
            <button type="button" class="btn btn-primary pull-right" onclick="createUser();">Create New User</button>
        </div>
    </div>
    
    <br/>
    <div class="row">
        
        <div class="col-md-12">
            
            <div class="panel panel-default">
                <h7><b>&nbsp;&nbsp;&nbsp;&nbsp;Users List</b></h7>
                <div class="common-content-block">                     
                    <table id="tb_users" class="table table-striped table-bordered dt-responsive nowrap" width="100%" cellspacing="0">
                        <thead>
                            <tr>  
                                <th>User Id</th>
                                <th>Name</th>
                                <th>User Type</th>
                                <th>Username</th>
                                <th>Inactive</th>
                                <th>Created by</th>                                
                                <th>Created Date</th>                                                                
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                          <tr>
                             <td colspan="7" class="dataTables_empty">Loading data from server</td>
                         </tr>
                     </tbody>
                 </table>
             </div>
         </div>
     </div>
 </div>

            <?php // }else{ print "You are not authorized to access this page";}
	}
}
?>


