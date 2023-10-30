<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";
require_once "lib/db/DBLogic.php";

class cls_dashboard extends cls_renderer {

	var $currStore;
	var $userid;
	var $fromdate;
	var $todate;
	var $params;

	function __construct($params = null) {
		$this->currStore = getCurrStore();
		$this->params = $params;
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
                showBar();
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


				function showBar(){
					var fromdate = "<?php echo $this->fromdate ?>";
					var todate = "<?php echo $this->todate ?>";

					var top3CustomersSale = "<?php echo StatisticsReason::TOP3CUSTOMERS ?>";
					//setBarChartData(fromdate,todate,statisticsReason,chartId,xkey,ykeys,labels)
					setBarChartData(fromdate,todate,top3CustomersSale,'cust_bar','customer',['sale'],['Orders-Rs']);

					var top3CategorySale = "<?php echo StatisticsReason::TOP3CATEGORY ?>";
					//setBarChartData(fromdate,todate,statisticsReason,chartId,xkey,ykeys,labels)
					setBarChartData(fromdate,todate,top3CategorySale,'cat_bar','category',['sale'],['Orders-Rs']);

					var top3ProductsSale = "<?php echo StatisticsReason::TOP3PRODUCTS ?>";
					//setBarChartData(fromdate,todate,statisticsReason,chartId,xkey,ykeys,labels)
					setBarChartData(fromdate,todate,top3ProductsSale,'prod_bar','product',['sale'],['Orders-Rs']);

					var top3RegionsSale = "<?php echo StatisticsReason::TOP3REGIONS ?>";
					//setBarChartData(fromdate,todate,statisticsReason,chartId,xkey,ykeys,labels)
					setBarChartData(fromdate,todate,top3RegionsSale,'region_bar','region',['sale'],['Orders-Rs']);

					var bottom3CustomersSale = "<?php echo StatisticsReason::BOTTOM3CUSTOMERS ?>";
					//setBarChartData(fromdate,todate,statisticsReason,chartId,xkey,ykeys,labels)
					setBarChartData(fromdate,todate,bottom3CustomersSale,'cust_bar_bottom','customer',['sale'],['Orders-Rs']);

					var bottom3CategorySale = "<?php echo StatisticsReason::BOTTOM3CATEGORY ?>";
					//setBarChartData(fromdate,todate,statisticsReason,chartId,xkey,ykeys,labels)
					setBarChartData(fromdate,todate,bottom3CategorySale,'cat_bar_bottom','category',['sale'],['Orders-Rs']);

					var bottom3ProductsSale = "<?php echo StatisticsReason::BOTTOM3PRODUCTS ?>";
					//setBarChartData(fromdate,todate,statisticsReason,chartId,xkey,ykeys,labels)
					setBarChartData(fromdate,todate,bottom3ProductsSale,'prod_bar_bottom','product',['sale'],['Orders-Rs']);

					var bottom3RegionsSale = "<?php echo StatisticsReason::BOTTOM3REGIONS ?>";
					//setBarChartData(fromdate,todate,statisticsReason,chartId,xkey,ykeys,labels)
					setBarChartData(fromdate,todate,bottom3RegionsSale,'region_bar_bottom','region',['sale'],['Orders-Rs']);


	              }

	              function setBarChartData(fromdate,todate,statisticsReason,chartId,xkey,ykeys,labels){
	              	parameters = "";
					if(fromdate != "" && todate != ""){
						parameters += "&fromdate="+fromdate+"&todate="+todate;
					}

					var urlSet = "ajax/getStatisticData.php?StatisticsReason="+statisticsReason+parameters;
	              	// alert(urlSet);
	              	$.ajax({
	              		type: "POST",
	              		url: urlSet,
	              		data: 0,
	              		dataType: 'json',
	              		success: function(data){
				 			// alert(JSON.stringify(data.error));
				 			if(data.error == 1){
				 				// alert(JSON.stringify(data.result));
				 				$('#'+chartId).text(JSON.stringify(data.result));
				 				$('#'+chartId).css("color", "red");
				 			}else{ 
				 				Morris.Bar({
				 					element: chartId,
				 					data: data.result,
				 					xkey: xkey,
				 					ykeys: ykeys,
				 					resize: true,
				 					hideHover: 'auto',
				 					xLabelAngle: 45,
				 					labels: labels,
				 					barSizeRatio:0.50
				 					
				 				}).on('click', function(i, row) {  
				 					showDetails(fromdate,todate,row);
						       });
				 			}

				 		}
				 	});
	              }

	              function showDetails(fromdate,todate,row){
	              	//Dashboard overview page will use to show details
	              	var url = "dashboard/overview";
	              	const obj = JSON.parse(JSON.stringify(row));

	              	if(fromdate != "" && todate != ""){
						url += "/fromdate="+fromdate+"/todate="+todate;
					}

					var urlSet = "";
					if(obj.customer){
	              		urlSet = "ajax/setParameterForDetails.php?key=customer&value="+obj.customer;
	              	}else if(obj.category){
	              		urlSet = "ajax/setParameterForDetails.php?key=category&value="+obj.category;
	              	}else if(obj.product){
	              		var newName = obj.product.replace("&", "and");
	              		urlSet = "ajax/setParameterForDetails.php?key=product&value="+newName;
	              	}else if(obj.region){
	              		urlSet = "ajax/setParameterForDetails.php?key=region&value="+obj.region;
	              	}else{
	              		alert("Data not found");
	              	}
					
	              	// alert(urlSet);
	              	$.ajax({
	              		type: "POST",
	              		url: urlSet,
	              		data: 0,
	              		dataType: 'json',
	              		success: function(data){
				 			
				 			if(data.error == 1){
				 				//error
				 				alert(JSON.stringify(data.msg));
				 			}else{ 
				 				//success
				 				window.location.href = url+data.parameters;
				 			}

				 		}
				 	});
				 	
	              }

	              function reload(){
	            	// alert("here");
	            	var daterange = $("#daterange").text();
	            	var daterangeArr = daterange.split(" To ");
	            	var url = 'dashboard';
	            	var fromdate = "";
	            	var todate = "";

	            	if(daterangeArr.length == 2){
	            		fromdate = daterangeArr[0].trim();
	            		todate = daterangeArr[1].trim();
	            	}
	            	if(fromdate != ''){
	            		url += '/fromdate='+fromdate;
	            	}
	            	if(todate != ''){
	            		url += '/todate='+todate;
	            	}
	            	window.location.href = url;
	            }



        </script>

        <?php
    }

    public function pageContent() {
    	$menuitem = "dashboard";
    	include "sidemenu.php";  
    	$formResult = $this->getFormResult(); 
    	$dbl = new DBLogic();

        if(!$this->fromdate || !$this->todate){
			//current month's date range
			$fromdate = date('Y-m-01');
			$todate = date('Y-m-d');
		}else{
			$fromdate = date("Y-m-d", strtotime($this->fromdate));
			$todate = date("Y-m-d", strtotime($this->todate));
		}
    	$dataObj = $dbl->getRegionWisePercentileSale($fromdate,$todate);

    	$totalNetSale = 0;
    	$north = 0;
    	$east = 0;
    	$west = 0;
    	$south = 0;
    	$central = 0;
    	if($dataObj != null || $dataObj != "" || isset($dataObj)){
    		foreach ($dataObj as $data) {
    			// print_r($totalNetSale."\n");
    			if($data->zone == 'NORTH'){
    				$north = $data->percentage;
    				$totalNetSale = $totalNetSale + $data->value;
    			}else if($data->zone == 'EAST'){
    				$east = $data->percentage;
    				$totalNetSale = $totalNetSale + $data->value;
    			}else if($data->zone == 'WEST'){
    				$west = $data->percentage;
    				$totalNetSale = $totalNetSale + $data->value;
    			}else if($data->zone == 'SOUTH'){
    				$south = $data->percentage;
    				$totalNetSale = $totalNetSale + $data->value;
    			}else if($data->zone == 'CENTRAL'){
    				$central = $data->percentage;
    				$totalNetSale = $totalNetSale + $data->value;
    			}
    		}
    	}
    	setlocale(LC_MONETARY, 'en_IN');
    	// setlocale(LC_MONETARY, 'en_US');
    	
		$totalNetSale = money_format('%!i', $totalNetSale);
    	// print_r($totalNetSale);
    	?>

    	<div class="container-section">
    		<div class="row">
    			<div class="col-md-12">
    				<div class="panel panel-default">
    					<div class="panel-body">
    						<!-- <h2 class="title-bar">Dashboard</h2> -->
    						<h7><b>Dashboard</b></h7>
    						<div class="common-content-block">

    							<div class="row">
    								<div class="col-md-4">
    									<label>National Orders:</label>
    									<div class="selectbox">
    										<div>Rs. <?php echo $totalNetSale ?></div>
    									</div>
    								</div>
    								<div class="col-md-4">
    									<label>Select Daterange:</label>
    									<div id="daterange" class="selectbox">
    										<i class="fa fa-calendar"></i>
    										<span id="selDate" class="selDateClass"></span> 
    										<b class="caret"></b>
    									</div>
    								</div>
    								<div class="col-md-4">
    									<br>
    									<button type="button" class="btn btn-primary form-control" onclick="reload();">Reload</button>
    								</div>
    							</div>
    							<br>
    							<div class="box box-primary">
    							</div>
    							<div class="row">
											<!-- <div class="col-md-1">
											</div> -->
											<div class="col-md-3">
												<div class="panel panel-default">
													<h7><b>&nbsp;&nbsp;&nbsp;&nbsp;Top 3 Customers</b></h7>
													<div id="cust_bar" align="center" style="height: 200px;">
													</div>			                    
												</div>
											</div>
											<div class="col-md-3">
												<div class="panel panel-default">
													<h7><b>&nbsp;&nbsp;&nbsp;&nbsp;Top 3 Category</b></h7>
													<div id="cat_bar" align="center" style="height: 200px;"></div>
												</div>
											</div>
											<div class="col-md-3">
												<div class="panel panel-default">
													<h7><b>&nbsp;&nbsp;&nbsp;&nbsp;Top 3 Products</b></h7>
													<div id="prod_bar" align="center" style="height: 200px;"></div>
												</div>
											</div>
											<div class="col-md-3">
												<div class="panel panel-default">
													<h7><b>&nbsp;&nbsp;&nbsp;&nbsp;Top 3 Regions</b></h7>
													<div id="region_bar" align="center" style="height: 200px;"></div>
												</div>
											</div>
										</div>
										<div class="box box-primary">
										</div>
										<div class="row">
											<div class="col-md-3">
												<div class="panel panel-default">
													<h7><b>&nbsp;&nbsp;&nbsp;&nbsp;Bottom 3 Customers</b></h7>
													<div id="cust_bar_bottom" align="center" style="height: 200px;"></div>
												</div>
											</div>
											<div class="col-md-3">
												<div class="panel panel-default">
													<h7><b>&nbsp;&nbsp;&nbsp;&nbsp;Bottom 3 Category</b></h7>
													<div id="cat_bar_bottom" align="center" style="height: 200px;"></div>
												</div>
											</div>
											<div class="col-md-3">
												<div class="panel panel-default">
													<h7><b>&nbsp;&nbsp;&nbsp;&nbsp;Bottom 3 Products</b></h7>
													<div id="prod_bar_bottom" align="center" style="height: 200px;"></div>
												</div>
											</div>
											<div class="col-md-3">
												<div class="panel panel-default">
													<h7><b>&nbsp;&nbsp;&nbsp;&nbsp;Bottom 3 Regions</b></h7>
													<div id="region_bar_bottom" align="center" style="height: 200px;"></div>
												</div>
											</div>
										</div>
										<div class="box box-primary">
										</div>
										<div class="row">
										<div class="col-md-2">
	    									<label>NET Orders:</label>
	    									<div class="selectbox">
	    										<div>Rs. <?php echo $totalNetSale ?></div>
	    									</div>
	    								</div>
	    								<div class="col-md-2">
	    									<label>NORTH:</label>
	    									<div class="selectbox">
	    										<div><?php echo $north ?> %</div>
	    									</div>
	    								</div>
	    								<div class="col-md-2">
	    									<label>SOUTH:</label>
	    									<div class="selectbox">
	    										<div><?php echo $south ?> %</div>
	    									</div>
	    								</div>
	    								<div class="col-md-2">
	    									<label>EAST:</label>
	    									<div class="selectbox">
	    										<div><?php echo $east ?> %</div>
	    									</div>
	    								</div>
	    								<div class="col-md-2">
	    									<label>WEST:</label>
	    									<div class="selectbox">
	    										<div><?php echo $west ?> %</div>
	    									</div>
	    								</div>
	    								<div class="col-md-2">
	    									<label>CENTRAL:</label>
	    									<div class="selectbox">
	    										<div><?php echo $central ?> %</div>
	    									</div>
	    								</div>
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


