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
    var $fromdate;
    var $todate;
    var $params;
    var $product;
    var $category;
    var $state;
    var $city;
    var $chain;

    function __construct($params = null) {

        $this->currStore = getCurrStore();
        $this->params = $params;
        if (isset($this->params["chain"]) != "") {
            $this->chain = $this->params["chain"];
        }
        if (isset($this->params["product"]) != "") {
            $this->product = $this->params["product"];
        }
        if (isset($this->params["category"]) != "") {
            $this->category = $this->params["category"];
        }
        if (isset($this->params["state"]) != "") {
            $this->state = $this->params["state"];
        }
        if (isset($this->params["city"]) != "") {
            $this->city = $this->params["city"];
        }
        if ($params && isset($params['fromdate'])) {
            $this->fromdate = $params['fromdate'];
        }

        if ($params && isset($params['todate'])) {
            $this->todate = $params['todate'];
        }

    }

    function extraHeaders() {
        ?>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
        <!-- <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script> -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/gijgo@1.9.10/js/gijgo.min.js" type="text/javascript"></script>
        <link href="https://cdn.jsdelivr.net/npm/gijgo@1.9.10/css/gijgo.min.css" rel="stylesheet" type="text/css" />
        
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
        <!-- <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script> -->
        <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
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
            	var fromdate = "<?php echo $this->fromdate ?>";
                var todate = "<?php echo $this->todate ?>";
                if(fromdate != "" && todate != ""){
                	//pattern to ckeck date formate dd-mm-yyyy
                	var pattern =/^([0-9]{2})\-([0-9]{2})\-([0-9]{4})$/;
                	if(pattern.test(fromdate) && pattern.test(todate)){
                		var fromdate = getStandardDateTime(fromdate);
                		var todate = getStandardDateTime(todate);
                		var start = fromdate;
                		var end = todate;
                	}else{
                		alert("Please check daterange format");
                	}

                }else{
                	var start = moment().subtract(29, 'days');
                	var end = moment();
                }
                

                function cb(start, end) {
                    $('#daterange span').html(start.format('DD-MM-YYYY') + ' To ' + end.format('DD-MM-YYYY'));
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

                
                
                showLine()
                showArea()
                showBar()
                showDonut();

                

            }); 

            // this function returns date in formate - Wed Mar 25 2015 05:30:00 GMT+0530
            function getStandardDateTime(date){
            		var dateArr = date.split("-");

                	var date = dateArr[0];
                	var month = dateArr[1];
                	var year = dateArr[2];

                	var m = moment(); // Initial moment object

					// Create the new date
					var datenew = new Date(year+'-'+month+'-'+date);
					var datenew = moment(datenew);

					// Inject it into the initial moment object
					m.set(datenew.toObject());
					return datenew;
            }

            function showLine(){
              var type = 1;

              var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
			 			Morris.Line({
		                  element: 'Line_example',
		                  data: [
                          { m: '2006-01', a: 1000},
                          { m: '2007-05', a: 750},
                          { m: '2008-08', a: 800},
                          { m: '2009-11', a: 750},
                          { m: '2010-12', a: 500},
                          { m: '2011-07', a: 1156},
                          { m: '2012-03', a: 1000}
                          ],
		                  xkey: 'm',
		                  ykeys: ['a'],
		                  labels: ['Quantity'],
		                  resize: true,
		                  hideHover: 'auto',
		                  behaveLikeLine: true,
		                  xLabelFormat: function (x) { return months[x.getMonth()]; }
		              });

            }

            function showArea(){
              Morris.Area({
                  element: 'area-example',
                  data: [
                  { m: '2006', a: 1000, b: 900 },
                  { m: '2007', a: 750,  b: 650 },
                  { m: '2008', a: 800,  b: 890 },
                  { m: '2009', a: 750,  b: 750 },
                  { m: '2010', a: 500,  b: 400 },
                  { m: '2011', a: 1156,  b: 1200 },
                  { m: '2012', a: 1000, b: 900 }
                  ],
                  xkey: 'm',
                  ykeys: ['a', 'b'],
                  labels: ['Sales(Rs.)', 'Qty'],
		          resize: true,
		          hideHover: 'auto'
              });
            }

            function showBar(){
              Morris.Bar({
                  element: 'bar-example',
                  data: [
                  { y: '2006', a: 5600, b: 1000 },
                  { y: '2007', a: 750,  b: 6500 },
                  { y: '2008', a: 1000,  b: 4000 },
                  { y: '2009', a: 7500,  b: 6500 },
                  { y: '2010', a: 5000,  b: 4000 },
                  { y: '2011', a: 7500,  b: 6500 },
                  { y: '2012', a: 1000, b: 9000 }
                  ],
                  xkey: 'y',
                  ykeys: ['a', 'b'],
                  labels: ['D-mart', 'Big Bazar'],
                  hideHover: 'auto',
		          resize: true
              });
            }

            function showDonut(){
              Morris.Donut({
                  element: 'donut-example',
                  data: [
                  {label: "Orders in Last Month", value: 80},
                  {label: "Completed Orders", value: 30},
                  {label: "Completed Orders2", value: 300},
                  {label: "Cancelled Orders", value: 20}
                  ],
                  hideHover: 'auto',
		          resize: true
              });
            }

            // $(window).bind('resize', function(e){
            //       if (window.RT) clearTimeout(window.RT);
            //       window.RT = setTimeout(function()
            //       {
            //             this.location.reload(false); /* false to get page from cache */
            //         }, 100);
            //   });

            
            function reload(){
            	// alert("here");
            	var daterange = $("#daterange").text();
            	var daterangeArr = daterange.split(" To ");
            	
                var chain = document.getElementById("chain");
                var cat = document.getElementById("catagory");
                var prod = document.getElementById("product");
                var st = document.getElementById("state");
                var ct = document.getElementById("city");

                var chainId = chain.options[chain.selectedIndex].value;
                var categoryId = cat.options[cat.selectedIndex].value;
                var productId = prod.options[prod.selectedIndex].value;
                var stateId = st.options[st.selectedIndex].value;
                var cityId = ct.options[ct.selectedIndex].value;

                var url = 'sales/charts';
                var fromdate = "";
                var todate = "";
                if(daterangeArr.length == 2){
                	fromdate = daterangeArr[0].trim();
            		todate = daterangeArr[1].trim();
                }

                if(chainId != '' && chainId != -1){
                    url += '/chain='+chainId;
                }
                if(categoryId != '' && categoryId != -1){
                    url += '/category='+categoryId;
                }
                if(productId != '' && productId != -1){
                    url += '/product='+productId;                   
                }
                if(stateId != '' && stateId != -1){
                    url += '/state='+stateId;
                }
                if(cityId != '' && cityId != -1){
                    url += '/city='+cityId;
                }
                if(fromdate != ''){
                    url += '/fromdate='+fromdate;
                }
                if(todate != ''){
                    url += '/todate='+todate;
                }
                window.location.href = url;
                // alert('cat='+categoryId+',prod='+productId+',state='+stateId+',city='+cityId);

            }

        </script>
        
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
        $stateListObj = $dbl->getStateList();
        $productListObj = $dbl->getAllProductList();
        $cityListObj = $dbl->getCityList();
        
        // print_r($stateListObj);

        ?>

        <div class="container-section">
            <div class="row">
                <div class="col-md-3">
                    <label>Select Daterange:</label>
                    <div id="daterange" class="selectbox">
                        <i class="fa fa-calendar"></i>
                        <span id="selDate" class="selDateClass"></span> 
                        <b class="caret"></b>
                    </div>
                </div>
                <div class="col-md-3">
                    <label>Select Chain:</label>
                    <select class="selectpicker form-control" multiple data-live-search="true" id="chain" name="chain" >
                        <!-- <option value="" disabled selected>Select Category</option> -->
                        <option value="-1" selected>All (Default)</option>
                        <?php
                        if (isset($chainListObj)) {
                            foreach ($chainListObj as $chainObj) {
                                $selected = "";
                                if($chainObj->id == $this->chain){ $selected = "selected";}
                                ?>
                                <option value="<?php echo $chainObj->id ?>" <?php echo $selected ?> ><?php echo $chainObj->name; ?></option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Select Category:</label>
                    <select class="selectpicker form-control" data-live-search="true" id="catagory" name="catagory">
                        <!-- <option value="" disabled selected>Select Category</option> -->
                        <option value="-1" selected>All (Default)</option>
                        <?php
                        if (isset($categoryListObj)) {
                            
                            foreach ($categoryListObj as $categoryObj) {
                                $selected = "";
                                if($categoryObj->id == $this->category){ $selected = "selected";}
                                ?>
                                <option value="<?php echo $categoryObj->id ?>" <?php echo $selected ?> ><?php echo $categoryObj->category; ?></option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Select Product:</label>
                    <select class="selectpicker form-control" data-live-search="true" id="product" name="product" >
                        <!-- <option value="" disabled selected>Select Product</option> -->
                        <option value="-1" selected>All (Default)</option>
                        <?php
                        if (isset($productListObj)) {
                            foreach ($productListObj as $productObj) {
                                $selected = "";
                                if($productObj->id == $this->product){ $selected = "selected";}
                                ?>
                                <option value="<?php echo $productObj->id ?>" <?php echo $selected ?> ><?php echo $productObj->itemname; ?></option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                </div>
                </div>
                <div class="row">
                <div class="col-md-3">
                    <label>Select State:</label>
                    <select class="selectpicker form-control" data-live-search="true" id="state" name="state" >
                        <!-- <option value="" disabled selected>Select State</option> -->
                        <option value="-1" selected>All (Default)</option>
                        <?php
                        if (isset($stateListObj)) {
                            foreach ($stateListObj as $stateObj) {
                                $selected = "";
                                if($stateObj->id == $this->state){ $selected = "selected";}
                                ?>
                                <option value="<?php echo $stateObj->id ?>" <?php echo $selected ?> ><?php echo $stateObj->name; ?></option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Select City:</label>
                    <select class="selectpicker form-control" data-live-search="true" id="city" name="city" >
                        <!-- <option value="" disabled selected>Select City</option> -->
                        <option value="-1" selected>All (Default)</option>
                        <?php
                        if (isset($cityListObj)) {
                            foreach ($cityListObj as $cityObj) {
                                $selected = "";
                                if($cityObj->id == $this->city){ $selected = "selected";}
                                ?>
                                <option value="<?php echo $cityObj->id ?>" <?php echo $selected ?> ><?php echo $cityObj->city; ?></option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <br>
        			<button type="button" class="btn btn-primary form-control" onclick="reload();">Reload</button>
                </div>
            </div>
            <br/>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <h7><b>&nbsp;&nbsp;&nbsp;&nbsp;Sales Data (Quantity)</b></h7>
                        <div class="common-content-block">  
                            <div id="Line_example"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <h7><b>&nbsp;&nbsp;&nbsp;&nbsp;Sales Data (Rs. and Qty)</b></h7>
                        <div class="common-content-block">  
                            <div id="area-example"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <h7><b>&nbsp;&nbsp;&nbsp;&nbsp;Sales Data (Chain Wise) </b></h7>
                        <div class="common-content-block">  
                            <div id="bar-example"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <h7><b>&nbsp;&nbsp;&nbsp;&nbsp;Orders Fill Rate </b></h7>
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


