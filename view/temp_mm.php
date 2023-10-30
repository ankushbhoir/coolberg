<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";
require_once "lib/db/DBLogic.php";

class cls_sales_charts extends cls_renderer {

    var $currStore;
    var $userid;
    var $dtrange;
    var $params;
    var $cid;
    var $uid;
    var $pid;
    var $sid = -1;

    function __construct($params = null) {
        $this->currStore = getCurrStore();
        //print_r($this->currStore);
        //echo $this->currStore->usertype;
        $this->params = $params;
    }

    function extraHeaders() {
        ?>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
        <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/gijgo@1.9.10/js/gijgo.min.js" type="text/javascript"></script>
        <link href="https://cdn.jsdelivr.net/npm/gijgo@1.9.10/css/gijgo.min.css" rel="stylesheet" type="text/css" />
        
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
        
        <style type="text/css" title="currentStyle">
            /*  @import "js/datatables/media/css/demo_page.css";
            @import "js/datatables/media/css/demo_table.css";*/
            @import "css/app.min.css";
            /*@import "css/adminLte.min.css";*/
            @import "https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css";
            @import "https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css";
        </style>
        <script type="text/javaScript">    
            $(function(){                            
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

                Morris.Line({
                  element: 'line-example',
                  data: [
                  { y: '2006', a: 100, b: 90 },
                  { y: '2007', a: 75,  b: 65 },
                  { y: '2008', a: 50,  b: 40 },
                  { y: '2009', a: 100,  b: 20 },
                  { y: '2010', a: 50,  b: 40 },
                  { y: '2011', a: 45,  b: 100 },
                  { y: '2012', a: 100, b: 90 }
                  ],
                  xkey: 'y',
                  ykeys: ['a', 'b'],
                  labels: ['Series A', 'Series B']
              });


                Morris.Area({
                  element: 'area-example',
                  data: [
                    { y: '2006', a: 100, b: 90 },
                    { y: '2007', a: 75,  b: 65 },
                    { y: '2008', a: 50,  b: 89 },
                    { y: '2009', a: 75,  b: 65 },
                    { y: '2010', a: 89,  b: 40 },
                    { y: '2011', a: 75,  b: 65 },
                    { y: '2012', a: 100, b: 90 }
                  ],
                  xkey: 'y',
                  ykeys: ['a', 'b'],
                  labels: ['Series A', 'Series B']
                });

                Morris.Bar({
                  element: 'bar-example',
                  data: [
                    { y: '2006', a: 560, b: 10 },
                    { y: '2007', a: 75,  b: 65 },
                    { y: '2008', a: 10,  b: 40 },
                    { y: '2009', a: 75,  b: 65 },
                    { y: '2010', a: 50,  b: 40 },
                    { y: '2011', a: 75,  b: 65 },
                    { y: '2012', a: 100, b: 90 }
                  ],
                  xkey: 'y',
                  ykeys: ['a', 'b'],
                  labels: ['Series A', 'Series B']
                });

                Morris.Donut({
                  element: 'donut-example',
                  data: [
                    {label: "Download Sales", value: 12},
                    {label: "In-Store Sales", value: 30},
                    {label: "Mail-Order Sales", value: 20}
                  ]
                });
            }); 

            $(window).bind('resize', function(e)
                {
                  if (window.RT) clearTimeout(window.RT);
                  window.RT = setTimeout(function()
                  {
                    // @import url('//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css');
                    this.location.reload(false); /* false to get page from cache */
                  }, 100);
                });

        </script>
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
        <?php
    }

    public function pageContent() {
        $menuitem = "sales_chart";
        include "sidemenu.php";  
        $formResult = $this->getFormResult(); 
        $dbl = new DBLogic();
        // print_r($formResult);
        $chainListObj = $dbl->getChainList();
        $categoryListObj = $dbl->getItemCategoryList();
        // print_r($chainListObj);
               
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
                <div class="col-md-2">
                    <select class="selectpicker form-control" data-live-search="true" id="catagory" name="catagory" onchange="reload(this)">
                    <option value="" disabled selected>Select Category</option>
                    <option value="-1">All</option>
                    <?php
                    if (isset($categoryListObj)) {
                                                foreach ($categoryListObj as $categoryObj) {
                                                    ?>
                                                    <option value="<?php echo $categoryObj->id ?>"><?php echo $categoryObj->category; ?></option>
                                                    <?php
                                                }
                                            }
                    ?>
                </select>
                </div>
                <div class="col-md-2">
                    <select class="selectpicker form-control" data-live-search="true" id="product" name="product" onchange="reload(this)">
                    <option value="" disabled selected>Select Product</option>
                    <option value="-1">All</option>
                    <?php
                    if (isset($categoryListObj)) {
                                                foreach ($categoryListObj as $categoryObj) {
                                                    ?>
                                                    <option value="<?php echo $categoryObj->id ?>"><?php echo $categoryObj->category; ?></option>
                                                    <?php
                                                }
                                            }
                    ?>
                </select>
                </div>
                <div class="col-md-2">
                    <select class="selectpicker form-control" data-live-search="true" id="state" name="state" onchange="reload(this)">
                    <option value="" disabled selected>Select State</option>
                    <option value="-1">All</option>
                    <?php
                    if (isset($categoryListObj)) {
                                                foreach ($categoryListObj as $categoryObj) {
                                                    ?>
                                                    <option value="<?php echo $categoryObj->id ?>"><?php echo $categoryObj->category; ?></option>
                                                    <?php
                                                }
                                            }
                    ?>
                </select>
                </div>
                <div class="col-md-3">
                    <select class="selectpicker form-control" data-live-search="true" id="city" name="city" onchange="reload(this)">
                    <option value="" disabled selected>Select City</option>
                    <option value="-1">All</option>
                    <?php
                    if (isset($categoryListObj)) {
                                                foreach ($categoryListObj as $categoryObj) {
                                                    ?>
                                                    <option value="<?php echo $categoryObj->id ?>"><?php echo $categoryObj->category; ?></option>
                                                    <?php
                                                }
                                            }
                    ?>
                </select>
                </div>
            </div>
            <br/>
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <h7><b>&nbsp;&nbsp;&nbsp;&nbsp;Sales Data </b></h7>
                        <div class="common-content-block">  
                            <div id="line-example"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <h7><b>&nbsp;&nbsp;&nbsp;&nbsp;Sales Data </b></h7>
                        <div class="common-content-block">  
                            <div id="area-example"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <h7><b>&nbsp;&nbsp;&nbsp;&nbsp;Sales Data </b></h7>
                        <div class="common-content-block">  
                            <div id="bar-example"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <h7><b>&nbsp;&nbsp;&nbsp;&nbsp;Sales Data </b></h7>
                        <div class="common-content-block">  
                            <div id="donut-example"></div>
                        </div>
                    </div>
                </div>
            </div>
           <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
             <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
             <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>              -->
           <!-- <script src="js/chosen/chosen.jquery.js" type="text/javascript"></script>
               <script type="text/javascript"> $(".chzn-select").chosen(); $(".chzn-select-deselect").chosen({allow_single_deselect:true});</script>           -->
               <?php
            // }else{ print "You are not authorized to access this page";}
           }

       }
       ?>


