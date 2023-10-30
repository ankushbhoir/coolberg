<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "session_check.php";
require_once "lib/db/DBLogic.php";

class cls_missing_master_email extends cls_renderer{

        var $currStore;
        var $params;         
        function __construct($params=null) {
            $this->currStore = getCurrStore();
            $this->params = $params;
            
        }

function extraHeaders() { ?>
<style type="text/css" title="currentStyle">
      @import "https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css";
      @import "https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css";
</style>
<script type="text/javaScript">    
$(function(){     
    var url = "ajax/tb_missing_master_email.php";
    oTable = $('#tb_missing_master_email').dataTable( {
    "bProcessing": true,
    "bServerSide": true,
    "aoColumns": [null,null,null,null],
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

    function edit(mId){
     if(mId != ""){
        window.location.href = "missing/master/email/edit/m_Id="+mId;
      }else{
        alert("Uncought error found. Contact Intouch.");
     }
    }
</script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
<?php }

        public function pageContent() {
            $menuitem = "missingmasteremail";
            include "sidemenu.php";
            $formResult = $this->getFormResult();
            if($formResult->status == 'successEdit'){
        echo '<script language="javascript">';
        echo 'alert("Missing Master EMAIL updated successfulsy.")';
        echo '</script>';
      }
?>

<div class="container-section">
    <div class="row">
      <div class="col-md-12">
          <div class="col-md-6"></div>
          <div class="col-md-3">
          </div>
      </div>
    </div><br>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <b>&nbsp;&nbsp;&nbsp;&nbsp;MISSING MASTER EMAIL</b>
                <div class="common-content-block">                     
                    <table id="tb_missing_master_email" class="table table-striped table-bordered dt-responsive nowrap" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>   
                                <th>MASTER NAME</th>
                                <th>EMAILS</th>
                                <th>ACTION</th>
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
<?php
    }
}
?>


