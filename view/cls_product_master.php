<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "session_check.php";
require_once "lib/db/DBLogic.php";

class cls_product_master extends cls_renderer {

    var $currStore;
    var $params;

    function __construct($params = null) {
        $this->currStore = getCurrStore();
        $this->params = $params;
    }

    function extraHeaders() {
        ?>
        <style type="text/css" title="currentStyle">
            @import "https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css";
            @import "https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css";
        </style>
        <script type="text/javaScript">    
        $(function(){
            var url = "ajax/tb_cool_products.php";
                        oTable = $('#tb_cool_products').dataTable( {
                       "bProcessing": true,
                       "bServerSide": true,
                       "bDestroy": true,
                       "aoColumns": [null,null,null,null,null,null,null,null,null,null,null,null,null,null,{bSortable: false}],
                       "sAjaxSource": url     
                      } );
                // search on pressing Enter key only
               $('.dataTables_filter input').unbind('keyup').bind('keyup', function(e){
                        if (e.which == 13){                     
                     oTable.fnFilter($(this).val(), null, false, true);
                         }
                    }); 
        });


//            function addNew(){
//                window.location.href = "ship/to/party/upload";
//            }
//
//            function download(){
//                  var chain_id = $('#chain').val();
//                            if(chain_id != ""){
//                                window.location.href = "formpost/downloadshiptopartyDetails.php?chain_id="+chain_id;
//        //                        window.open("formpost/downloadCustomerMasterDetails.php?chain_id="+chain_id);
//                            }else{
//                                alert("Please select chain.");
//                            }
//             }
//
//           function edit(shippingId){
//              if(shippingId != ""){
//        <?php // if ($this->currStore->id == '2') { ?>
//                    window.location.href = "ship/to/party/edit/shippingId="+shippingId;
//        <?php // } ?>
//              }else{
//                alert("Uncought error found. Contact Intouch.");
//             }
//            }
        </script>
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
    <?php
    }

    public function pageContent() {
        $menuitem = "productmaster";
        include "sidemenu.php";
        $dbl = new DBLogic();
        $formResult = $this->getFormResult();
//            $chainListObj=$dbl->getChainList();
        if ($formResult->status == 'successEdit') {
            echo '<script language="javascript">';
            echo 'alert("products updated successfulsy.")';
            echo '</script>';
        }
        ?>

        <div class="container-section">
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-6">
                    </div>
                    <div class="col-md-3">
                        <!--<button type="button" id = "download" class="btn btn-primary pull-right" onclick="download();">Export to Excel</button>-->
                    </div>

                    <div class="col-md-3">              
                        <!--<button type="button" id = "addnew" class="btn btn-primary pull-right" onclick="addNew();">Add New</button>-->
                    </div>
                </div>
            </div><br>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <b>&nbsp;&nbsp;&nbsp;&nbsp;Product Master</b>
                        <div class="common-content-block">                     
                            <table id="tb_cool_products" class="table table-striped table-bordered dt-responsive nowrap" width="100%" cellspacing="0">
                                <thead>
                                    <tr>   
                                        <th>ID</th>
                                        <th>EAN CODE</th>
                                        <th>PRODUCT NAME</th>
                                        <th>PRODUCT ID</th>
                                        <th>CODE</th>
                                        <th>MRP</th>
                                        <th>GROUP NAME</th>
                                        <th>GROUP ID</th>
                                        <th>STOCK UNIT ID</th>
                                        <th>BARCODE</th>
                                        <th>RACK BOX</th>
                                        <th>HSN CODE</th>
                                        <th>Created DateTime</th>
                                        <th>Updated DateTime</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="15" class="dataTables_empty">Loading data from server</td>
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