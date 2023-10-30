<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";
require_once "lib/db/DBLogic.php";

class cls_dashboard_overview extends cls_renderer {

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
    var $region;
    var $dc;

	function __construct($params = null) {

		$this->currStore = getCurrStore();
        $this->params = $params;
        if (isset($this->params["chain"]) != "") {
            $this->chain = $this->params["chain"];
        }
        if (isset($this->params["region"]) != "") {
            $this->region = $this->params["region"];
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
        if ($params && isset($params['dc'])) {
            $this->dc = $params['dc'];
        }

	}

	function extraHeaders() {
		?>
		<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
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
                	var start = moment().startOf('month');
                	var end = moment().endOf('month');
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
                setDatatables();
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

				function reload(){
            	// alert("here");
            	var daterange = $("#daterange").text();
            	var daterangeArr = daterange.split(" To ");
            	

                var chainId = $('#chain').val();
                var regionName = $('#region').val();
                var categoryId = $('#category').val();
                var productId = $('#product').val();
                var stateId = $('#state').val();
                var cityId = $('#city').val();
                var dc = $('#dc').val();

                var url = 'dashboard/overview';
                var fromdate = "";
                var todate = "";
                if(daterangeArr.length == 2){
                	fromdate = daterangeArr[0].trim();
            		todate = daterangeArr[1].trim();
                }

                if(chainId != '' && chainId != -1){
                    url += '/chain='+chainId;
                }
                if(regionName != '' && regionName != -1){
                    url += '/region='+regionName;
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
                if(dc != '' && dc != -1){
                    url += '/dc='+dc;
                }

                window.location.href = url;
                
                // alert('cat='+categoryId+',prod='+productId+',state='+stateId+',city='+cityId);

            }

            function setDatatables(){

            	var daterange = $("#daterange").text();
            	var daterangeArr = daterange.split(" To ");
            	

                var chainId = $('#chain').val();
                var regionName = $('#region').val();
                var categoryId = $('#category').val();
                var productId = $('#product').val();
                var stateId = $('#state').val();
                var cityId = $('#city').val();
                var dc = $('#dc').val();

                var url = 'ajax/tb_dashboard_overview.php?';
                var fromdate = "";
                var todate = "";
                if(daterangeArr.length == 2){
                	fromdate = daterangeArr[0].trim();
            		todate = daterangeArr[1].trim();
                }

                if(chainId != '' && chainId != -1){
                    url += '&chain='+chainId;
                }
                if(regionName != '' && regionName != -1){
                    url += '&region='+regionName;
                }
                if(categoryId != '' && categoryId != -1){
                    url += '&category='+categoryId;
                }
                if(productId != '' && productId != -1){
                    url += '&product='+productId;                   
                }
                if(stateId != '' && stateId != -1){
                    url += '&state='+stateId;
                }
                if(cityId != '' && cityId != -1){
                    url += '&city='+cityId;
                }
                if(fromdate != ''){
                    url += '&fromdate='+fromdate;
                }
                if(todate != ''){
                    url += '&todate='+todate;
                }
                if(dc != '' && dc != -1){
                    url += '&dc='+dc;
                }


			    // alert(url);
			    oTable = $('#tb_users').dataTable( {
			    	"bProcessing": true,
			    	"bServerSide": true,
			    	"destroy" : true,
			    	"aoColumns": [{ "bSortable": false },null,null,null,null],
			    	"sAjaxSource": url,
			    	"aaSorting": [],
			    	"iDisplayLength": 10
			    } );
				// search on pressing Enter key only
				$('.dataTables_filter input').unbind('keyup').bind('keyup', function(e){
					if (e.which == 13){                     
						oTable.fnFilter($(this).val(), null, false, true);
					}
				});    
            }

            function setOptions(){
            	// alert("here");
                var regionId = $('#region').val();
                var stateId = $('#state').val();
                var city = $('#city').val();
                var chainId = $('#chain').val();
                var dc = $('#dc').val();
                var categoryId = $('#category').val();
                var productId = $('#product').val();

                if(regionId != -1){
                	setStates();
                }
                if(stateId != -1){
                	setCities();
                }
                if(city != -1){
                	setChains();
                }
                if(chainId != -1){
                	// alert("here");
                	setDC();
                }
                if(categoryId != -1){
                	setProducts();
                }

            }

            function setRegions(){

            	

            }
            
            function setStates(){
            	var regionId = $('#region').val();
            	var stateId = $('#state').val();
            	var params = "";
            	if(stateId != -1 && stateId != null){
            		params = params+"&state="+stateId;
            	}

	            var ajaxURL = "ajax/getStateSelections.php?region=" + regionId + params; 
	            // alert(ajaxURL);
	            $.ajax({
		            url:ajaxURL,
		            //dataType: 'json',
		            cache: false, 
		            success:function(html){
			            // alert(html);
			            $('#state').html(html); 
			            $('#state').selectpicker('refresh');
		            }
	            });
            }
            
            function setCities(){

            	var stateId = $('#state').val();
            	var cityId = $('#city').val();

            	var params = "";
            	if(cityId != -1 && cityId != null){
            		params = params+"&city="+cityId;
            	}
	            var ajaxURL = "ajax/getCitySelections.php?state=" + stateId + params; 
	            // alert(ajaxURL);
	            $.ajax({
		            url:ajaxURL,
		            //dataType: 'json',
		            cache: false, 
		            success:function(html){
			            // alert(html);
			            $('#city').html(html); 
			            $('#city').selectpicker('refresh');
		            }
	            });
            }
            
            function setChains(){

            	var city = $('#city').val();
            	var chainId = $('#chain').val();

            	var params = "";
            	if(chainId != -1 && chainId != null){
            		params = params+"&chain="+chainId;
            	}
	            var ajaxURL = "ajax/getChainSelections.php?city=" + city + params; 
	            // alert(ajaxURL);
	            $.ajax({
		            url:ajaxURL,
		            //dataType: 'json',
		            cache: false, 
		            success:function(html){
			            // alert(html);
			            $('#chain').html(html); 
			            $('#chain').selectpicker('refresh');
		            }
	            });
            }

            function setDC(){

            	var chainId = $('#chain').val();
            	var dc = $('#dc').val();
            	var city = $('#city').val();
            	// alert(dc);
            	var params = "";
            	if(dc != -1 && dc != null){
            		params = params+"&dc="+dc;
            	}
            	if(city != -1 && city != null){
            		params = params+"&city="+city;
            	}
	            var ajaxURL = "ajax/getDCSelections.php?chain=" + chainId + params; 
	            // alert(ajaxURL);
	            $.ajax({
		            url:ajaxURL,
		            //dataType: 'json',
		            cache: false, 
		            success:function(html){
			            // alert(html);
			            $('#dc').html(html); 
			            $('#dc').selectpicker('refresh');
		            }
	            });
            }
            
            function setCategories(){
            	
            }
            
            function setProducts(){
            	var categoryId = $('#category').val();
            	var productId = $('#product').val();
            	// alert(dc);
            	var params = "";
            	if(productId != -1 && productId != null){
            		params = params+"&product="+productId;
            	}

	            var ajaxURL = "ajax/getProductSelections.php?category=" + categoryId + params; 
	            // alert(ajaxURL);
	            $.ajax({
		            url:ajaxURL,
		            //dataType: 'json',
		            cache: false, 
		            success:function(html){
			            // alert(html);
			            $('#product').html(html); 
			            $('#product').selectpicker('refresh');
		            }
	            });
            }

            

			</script>

			<?php
		}

		public function pageContent() {
			$menuitem = "dashboard_overview";
			include "sidemenu.php";  
			$formResult = $this->getFormResult(); 
			$dbl = new DBLogic();
        // print_r($formResult);
			$chainListObj = $dbl->getChainList();
			$categoryListObj = $dbl->getItemCategoryList();
			$stateListObj = $dbl->getStateList();
			$productListObj = $dbl->getAllProductList();
			$cityListObj = $dbl->getCityList();
			$regionListObj = $dbl->getRegionList();
			$dcListObj = $dbl->getDCAddressList();

    	// print_r($this->region);
			?>

			<div class="container-section">
				<div class="row">
					<div class="panel panel-default">
						<h7><b>&nbsp;&nbsp;&nbsp;&nbsp;Dashboard Overview</b></h7>
						<div class="panel-body">
							<div class="common-content-block">
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
										<label>Select Region:</label>
										<select class="selectpicker form-control" data-live-search="true" id="region" name="region" onchange="setOptions()">
											<!-- <option value="" disabled selected>Select Category</option> -->
											<option value="-1" selected>All</option>
											<?php
											if (isset($regionListObj)) {

												foreach ($regionListObj as $regionObj) {
													$selected = "";
													if(strcasecmp($regionObj->id, $this->region) == 0){ $selected = "selected";}
													?>
													<option value="<?php echo $regionObj->id ?>" <?php echo $selected ?> ><?php echo $regionObj->name; ?></option>
													<?php
												}
											}
											?>
										</select>
									</div>
									<div class="col-md-3">
										<label>Select State:</label>
										<select class="selectpicker form-control" data-live-search="true" id="state" name="state" onchange="setOptions()">
											<!-- <option value="" disabled selected>Select State</option> -->
											<option value="-1" selected>All</option>
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
										<select class="selectpicker form-control" data-live-search="true" id="city" name="city" onchange="setOptions()">
											<!-- <option value="" disabled selected>Select City</option> -->
											<option value="-1" selected >All</option>
											<?php
											if (isset($cityListObj)) {
												foreach ($cityListObj as $cityObj) {
													$selected = "";
													if(strtolower($cityObj->dc_city) == strtolower($this->city)){ $selected = "selected";}
													?>
													<option value="<?php echo $cityObj->dc_city ?>" <?php echo $selected ?> ><?php echo $cityObj->dc_city; ?></option>
													<?php
												}
											}
											?>
										</select>
									</div>
								</div>
								<div class="row">
									<div class="col-md-3">
										<label>Select Chain:</label>
										<select class="selectpicker form-control" data-live-search="true" id="chain" name="chain" onchange="setOptions()">
											<!-- <option value="" disabled selected>Select Category</option> -->
											<option value="-1" selected>All</option>
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
										<label>Select DC:</label>
										<select class="selectpicker form-control" data-live-search="true" id="dc" name="dc" onchange="setOptions()">
											<!-- <option value="" disabled selected>Select Category</option> -->
											<option value="-1" selected>All</option>
											<?php
											if (isset($dcListObj)) {
												foreach ($dcListObj as $dcObj) {
													$selected = "";
													if($dcObj->id == $this->dc){ $selected = "selected";}
													?>
													<option value="<?php echo $dcObj->id ?>" <?php echo $selected ?> ><?php echo $dcObj->customer_code."-".$dcObj->dc_address; ?></option>
													<?php
												}
											}
											?>
										</select>
									</div>
									<div class="col-md-3">
										<label>Select Category:</label>
										<select class="selectpicker form-control" data-live-search="true" id="category" name="category" onchange="setOptions()">
											<!-- <option value="" disabled selected>Select City</option> -->
											<option value="-1" selected>All</option>
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
										<select class="selectpicker form-control" data-live-search="true" id="product" name="product" onchange="setOptions()">
											<!-- <option value="" disabled selected>Select Product</option> -->
											<option value="-1" selected>All</option>
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

									</div>
									<div class="col-md-3">
										
									</div>
									<div class="col-md-3">
										
									</div>
									<div class="col-md-3">
										<br>
										<button type="button" class="btn btn-primary pull-right" onclick="reload();">Reload</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="panel panel-default">
						<h7><b>&nbsp;&nbsp;&nbsp;&nbsp;Dashboard Overview</b></h7>
						<div class="panel-body">
							<div class="common-content-block">
								<div class="row">                     
									<table id="tb_users" class="table table-striped table-bordered dt-responsive nowrap" width="100%" cellspacing="0">
										<thead>
											<tr>  
												<th>Sr. No.</th>
												<th>Category</th>
												<th>Product</th>
												<th>Quantity</th>
												<th>Value(Rs.)</th>
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
				</div>
			</div>
			<br/>

			<?php
		}

	}
	?>


