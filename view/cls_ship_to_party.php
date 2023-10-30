<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "session_check.php";
require_once "lib/db/DBLogic.php";

class cls_ship_to_party extends cls_renderer{

        var $currStore;
        var $params;         
        function __construct($params=null) {
            $this->currStore = getCurrStore();
            $this->params = $params;
            
        }

function extraHeaders() { ?>
<style type="text/css" title="currentStyle">
/*      @import "https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css";
      @import "https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css";*/
      @import "css/dataTablescss/dataTables.bootstrap.min.css";
      @import "css/dataTablescss/responsive.bootstrap.min.css";
</style>
<script type="text/javaScript">    
$(function(){       
});

      function reload(sel){
                var url = "ajax/tb_ship_to_party.php?chain_id="+sel.value;
                oTable = $('#tb_ship_to_party').dataTable( {
               "bProcessing": true,
               "bServerSide": true,
               "bDestroy": true,
               "aoColumns": [null,null,null,null,null,null,null,null,null,null,null,{bSortable: false}],
               "sAjaxSource": url     
              } );
        // search on pressing Enter key only
       $('.dataTables_filter input').unbind('keyup').bind('keyup', function(e){
                if (e.which == 13){                     
             oTable.fnFilter($(this).val(), null, false, true);
                 }
            });  
       }
    function addNew(){
        window.location.href = "ship/to/party/upload";
    }

    function download(){
          var chain_id = $('#chain').val();
                    if(chain_id != ""){
                        window.location.href = "formpost/downloadshiptopartyDetails.php?chain_id="+chain_id;
//                        window.open("formpost/downloadCustomerMasterDetails.php?chain_id="+chain_id);
                    }else{
                        alert("Please select chain.");
                    }
     }

   function edit(shippingId){
      if(shippingId != ""){
        <?php if($this->currStore->id=='2') { ?>
        window.location.href = "ship/to/party/edit/shippingId="+shippingId;
      <?php } ?>
      }else{
        alert("Uncought error found. Contact Intouch.");
     }
    }
</script>
<!--<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />-->
<link rel="stylesheet" href="css/bootstrap-select.min.css" />
<?php }

        public function pageContent() {
            $menuitem = "shiptoparty";
            include "sidemenu.php";
            $dbl=new DBLogic();
            $formResult = $this->getFormResult(); 
            $chainListObj=$dbl->getChainList();
            if($formResult->status == 'successEdit'){
        echo '<script language="javascript">';
        echo 'alert("ship to party updated successfulsy.")';
        echo '</script>';
    }
?>

<div class="container-section">
    <div class="row">
      <div class="col-md-12">
          <div class="col-md-4">
          <select class="selectpicker form-control" data-live-search="true" id="chain" name="chain" onchange="reload(this)">
                    <option value="" disabled selected>Select Chain</option>
                    <option value="-1">All</option>
                    <?php
                    if (isset($chainListObj)) {
                                                foreach ($chainListObj as $chainObj) {
                                                    ?>
                                                    <option value="<?php echo $chainObj->id ?>"><?php echo $chainObj->displayname; ?></option>
                                                    <?php
                                                }
                                            }
                    ?>
            </select>
          </div>
          <div class="col-md-4">
            <button type="button" id = "download" class="btn btn-primary pull-right" onclick="download();">Export to Excel</button>
          </div>
          <?php if($this->currStore->id=='2') {?> 
          <div class="col-md-4">              
             <button type="button" id = "addnew" class="btn btn-primary pull-right" onclick="addNew();">Add New</button>
          </div>
        <?php } ?>
      </div>
    </div><br>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <b>&nbsp;&nbsp;&nbsp;&nbsp;SHIP TO PARTY</b>
                <div class="common-content-block">                     
                    <table id="tb_ship_to_party" class="table table-striped table-bordered dt-responsive nowrap" width="100%" cellspacing="0">
                        <thead>
                            <tr>   
                                <th>ID</th>
                                <th>SHIP TO PARTY</th>
                                <th>MASTER DEALER</th>
                                <th>SITE INDENTIFIER</th>
                                <th>SITE INDENTIFIER TYPE</th>
                                <!--<th>CLASSIFICATION</th>-->
                                <!--<th>Emails</th>-->
                                <!--<th>PLANT</th>-->
                                <th>CUSTOMER NAME</th>
                                <!--<th>MARGIN</th>-->
                                <th>DISTRIBUTION CHANNEL</th> 
                                <th>SALES DOCUMENT TYPE</th>
                                <th>DISTRIBUTION CHANNEL CODE</th> 
                                <th>Created DateTime</th>
                                <th>Updated DateTime</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                          <tr>
                             <td colspan="9" class="dataTables_empty">Loading data from server</td>
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