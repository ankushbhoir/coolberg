<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";
require_once "lib/db/DBLogic.php";

class cls_sales_report extends cls_renderer{

  var $currStore;
  var $userid;
  var $params;

  function __construct($params=null) {
    $this->currStore = getCurrStore();
    $dbl = new DBLogic();
    $this->params = $params;
    if(isset($this->params["node_id"]) != ""){

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

      var url = "ajax/tb_salesreport.php"; 
   // alert(url);
   oTable = $('#tb_stockreporttable').dataTable( {   
     "bProcessing": true, 
     "bServerSide": true,
     "aoColumns": [null,null,null,null,null,null,null], 
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


</script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
<?php }

public function pageContent() {
  $menuitem = "sales_report";
  include "sidemenu.php";
  $formResult = $this->getFormResult();
  $dbl = new DBLogic();
  // print_r($formResult);
  ?>

  <div class="container-section">
    <div class="row">
      <div class="panel panel-default">
        <div class="panel-body">
          <h5 class="title-bar">Upload Sales Data</h5>
          <div class="common-content-block">
            <form  action="formpost/uploadSalesData.php" method="post" enctype="multipart/form-data"> 
              <input type = "hidden" name="form_id" id="form_id" value="uploadsale">
              <div class="col-md-3">
                <input class="inputfile" type="file" name="csv" id="csv" required>
              </div>
              <div class="col-md-3">
                <input type="submit" class="btn btn-primary" name="submit" value="Upload Sales CSV">
              </div>
              <div class="col-md-3">
                
              </div>
              <div class="col-md-3">
                  <a href="samplefiles/vlcc_sample_sales_data.csv" class="btn btn-primary pull-right">Download Sample CSV</a>
              </div>
            </div>
            <?php if ($formResult->form_id == 'uploadsale') { ?>
              <br>
                <div class="alert alert-<?php echo $formResult->cssClass;?> alert-dismissible" style="display:<?php echo $formResult->showhide; ?>;">
                  <button class="close" type="button" data-dismiss="alert" aria-hidden="true">×</button>
                  <h4> <?php echo $formResult->status; ?>
                </div>
              <?php } ?>
          </div>
        </div>
      </div>

      <br/>
      <div class="row">
        <div class="col-md-12">
          <div class="panel panel-default">
            <h7><b>&nbsp;&nbsp;&nbsp;&nbsp;Sales Report</b></h7>
            <div class="common-content-block">                     
              <table id="tb_stockreporttable" class="table table-striped table-bordered dt-responsive nowrap" width="100%" cellspacing="0">
                <thead>
                  <tr>  
                    <th>PO Number</th>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Value</th>
                    <th>Sale Date</th>
                    <th>Uploaded By</th>
                    <th>Uploaded On</th>
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

<!-- <script src="js/chosen/chosen.jquery.js" type="text/javascript"></script>
  <script type="text/javascript"> $(".chzn-select").chosen(); $(".chzn-select-deselect").chosen({allow_single_deselect:true});</script>           -->
            <?php // }else{ print "You are not authorized to access this page";}
          }
        }
        ?>


