<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";
require_once "lib/db/DBLogic.php";

class cls_failed_po extends cls_renderer{

        var $currStore;
        var $userid;
        var $dtrange;
        var $params;
        var $cid;
        var $uid;
        var $pid;
        var $sid = -1;
       
        function __construct($params=null) {
            $this->currStore = getCurrStore();
            //print_r($this->currStore);
            //echo $this->currStore->usertype;
            $this->params = $params;
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
    @import "css/app.min.css";
      @import "https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css";
      @import "https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css";
</style>
<script type="text/javaScript">    
$(function(){      
    var url = "ajax/tb_failed_po.php";
        //             alert(url);
                        oTable = $('#tb_po').dataTable({
                            "bProcessing": true,
                            "bServerSide": true,
                            "aoColumns": [{"bSortable": false},null,null,null,{"bSortable": false},{"bSortable": false}],  
                            "sAjaxSource": url,
                            "aaSorting": []
                        }); 
                // search on pressing Enter key only
                        $('.dataTables_filter input').unbind('keyup').bind('keyup', function (e) {
                            if (e.which == 13) {
                                oTable.fnFilter($(this).val(), null, false, true);
                            }
                        });
                        
    var start = moment().subtract(0, 'days');
        var end = moment();

        function cb(start, end) {
        $('#daterange span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
        }

        $('#daterange').daterangepicker({
        startDate: start,
        endDate: end,
        ranges: { 
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
        }, cb);

        cb(start, end); 

        $("#example-select-all").click(function () {
            $('.selPO').prop('checked', this.checked);
            });
                                            
            $("#downloadExcel").click(function () { 
            var table = $('#tb_po').dataTable();
            var selectedIds = table.$(".selPO:checked", {"page": "all"});
            var ids = [];
            selectedIds.each(function(index,elem){
            var checkbox_value = $(elem).val();
            ids.push(checkbox_value);
            });
            if(ids.join() != ""){
                window.location.href="formpost/downloadPOsData.php?ids="+ids.join();
            }else{
                alert("Please select at least one PO.");
            }                            
                                            
            });
});

function reloadpage(){
        var dtrng = $("#selDate").text();
        var chain_id = $("#chain").val();
        if(chain_id == null){
            alert("Please select chain.");
        }else{
            var url = "ajax/tb_failed_po.php?selDateRange="+dtrng+"&chainId="+chain_id;    
                // alert(url); 
            oTable = $('#tb_po').dataTable({
                                "bProcessing": true,
                                "bServerSide": true,
                                "aoColumns": [{"bSortable": false},null,null,null,{"bSortable": false},{"bSortable": false}],  
                                "sAjaxSource": url,
                                "aaSorting": [],
                                "bDestroy": true
                            }); 
                    // search on pressing Enter key only
                            $('.dataTables_filter input').unbind('keyup').bind('keyup', function (e) {
                                if (e.which == 13) {
                                    oTable.fnFilter($(this).val(), null, false, true);
                                }
                            });
            }
        } 

</script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
<?php }

        public function pageContent() {
            $menuitem = "failedPOs";
            include "sidemenu.php";
            $dbl = new DBLogic();
            $chainListObj = $dbl->getChainList();
//            $dbl = new DBLogic();            
?>

<div class="container-section">
    <div class="row">
                <div class="col-md-3">
                    <div id="daterange" class="selectbox">
                        <i class="fa fa-calendar"></i>
                        <span id="selDate"></span> 
                        <b class="caret"></b>
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="selectpicker form-control" data-live-search="true" id="chain" name="chain">
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
                <div class="col-md-3">
                    <button type="button" class="btn btn-primary pull-left" id="reload" onclick="reloadpage();">Reload</button>
                </div>
            
            </div>
            <br/>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <h7><b>&nbsp;&nbsp;&nbsp;&nbsp;Processed POs List </b></h7>
                <div class="common-content-block">                     
                    <table id="tb_po" class="table table-striped table-bordered dt-responsive nowrap" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th><input name="example-select-all" value="-1" id="example-select-all" type="checkbox" /></th>  
                                <!--<th>Filename</th>-->
                                <th>PO Number</th>
                                <th>Chain Name</th> 
                                <th>Details</th>         
                                <th>Date Time</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                          <tr>
                             <td colspan="8" align="center" class="dataTables_empty">Loading data from server</td>
                         </tr>
                     </tbody>
                 </table>
             </div>
         </div>
     </div>
    </div>
</div>
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>              -->
<!-- <script src="js/chosen/chosen.jquery.js" type="text/javascript"></script>
<script type="text/javascript"> $(".chzn-select").chosen(); $(".chzn-select-deselect").chosen({allow_single_deselect:true});</script>           -->
            <?php // }else{ print "You are not authorized to access this page";}
    }
}
?>


