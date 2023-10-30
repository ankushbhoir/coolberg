<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "session_check.php";
require_once "lib/db/DBLogic.php";

class cls_mamaearth_history extends cls_renderer{

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
      table {
        border: 1px solid #000;   
        border-collapse: collapse;
        width: 100%;
    }

/*    th, td {
        border: 1px solid #000;  1px solid black border 
        padding: 8px;
        text-align: left;
    }
    
    th {
        border: 1px solid #000;  1px solid black border for table headers 
        background-color: #f2f2f2;  Optional: Add a background color for table headers 
    }*/
    
</style>
<script type="text/javaScript">    
$(function(){     
    var url = "ajax/tb_mamaearth_history.php";
    oTable = $('#tb_mamaearth_history').dataTable( {
    "bProcessing": true,
    "bServerSide": true,
    "aoColumns": [null,null,null,null,null,null],
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
    
//    function changedata(id,change_data){
//        alert(id);
////      console.log("Change Data:", change_data);
//        alert(change_data);
////        var chageid = id
////        $("#span1").text(id);
//        $("#span2").text(change_data);
////        alert(chageid);
////      window.location.href = "mamaearth/history";
//    } 

function changedata(id, change_data) {
//    alert(id);
//    alert(change_data);
        // Parse the change_data JSON
    var data = JSON.parse(change_data);
//     alert(data);
    // Get the table body
    var tableBody = document.getElementById("table-body");

    // Clear any existing rows in the table
    tableBody.innerHTML = "";

    // Loop through the data and create table rows
    for (var key in data) {
        if (data.hasOwnProperty(key)) {
            var rowData = data[key].split("::");
            var fieldName = key;
            var previousData = rowData[0];
            var newData = rowData[1];

            // Create a table row and populate it with data
            var row = document.createElement("tr");
            var fieldNameCell = document.createElement("td");
            fieldNameCell.textContent = fieldName;
            var previousDataCell = document.createElement("td");
            previousDataCell.textContent = previousData;
            var newDataCell = document.createElement("td");
            newDataCell.textContent = newData;

            // Append cells to the row
            row.appendChild(fieldNameCell);
            row.appendChild(previousDataCell);
            row.appendChild(newDataCell);

            // Append the row to the table body
            tableBody.appendChild(row);
        }
    }
}


</script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
<?php }

        public function pageContent() {
            $menuitem = "mamaearthhistory";
            include "sidemenu.php";
            $formResult = $this->getFormResult();
            if($formResult->status == 'successEdit'){
        echo '<script language="javascript">';
        echo 'alert("Sku Master updated successfulsy.")';
        echo '</script>';
      }
?>

<div class="container-section">
<!--    <div class="row">
      <div class="col-md-12">
          <div class="col-md-6"></div>
          <div class="col-md-3">
              <button type="button" id = "download" class="btn btn-primary pull-right" onclick="download();">Export To Excel</button>
          </div>
          <?php if($this->currStore->id=='2') { ?>
          <div class="col-md-3">
              <button type="button" id = "addnew" class="btn btn-primary pull-right" onclick="addNew();">Add New</button>
          </div>
        <?php } ?>
      </div>
    </div><br>-->
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;HISTORY</b>
                <div class="common-content-block">                     
                    <table id="tb_mamaearth_history" class="table table-striped table-bordered dt-responsive nowrap" width="100%" cellspacing="0">
                        <thead>
                            <tr>   
                                <th>ID</th>
                                <th>USERNAME NAME</th>
                                <th>MASTER TYPE</th>
                                <th>MASTER ID</th>
                                <th>Update DateTime</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                          <tr>
                             <td colspan="13" class="dataTables_empty">Loading data from server</td>
                         </tr>
                     </tbody>
                 </table>
             </div>
         </div>
     </div>
        
        <div class="modal fade" id="modal-default" style="padding-top: 150px" data-backdrop="false" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" style="margin-right: 255px; width: 40%;">
        <div class="modal-content">
           <div class="modal-body">
                <br>
                <div class="box box-primary">
                    <div class="box-header">
                        <?php
//                        $changedataquery = "select change_data from it_masters_logs where id=18";
//                        $changedata_obj = $db->fetchObject($changedataquery);
//
//                        if ($changedata_obj) {
//                            $change_data_obj = json_decode($changedata_obj->change_data);
//
//                            if ($change_data_obj !== null) {
//                                // Create an array to store data
//                                $data = [];
//
//                                foreach ($change_data_obj as $key => $value) {
//                                    // Split the value into Old Data and New Data based on spaces
//                                    $dataParts = explode("::", $value);
////                                    $fieldName = array_shift($dataParts);
//                                    $oldData = $dataParts[0];
//                                    $newData = $dataParts[1];
//
//                                    // Store data in an array
//                                    $data[] = [
//                                        'Field Name' => $key, // Use the first part as the Field Name
//                                        'Old Data' => $oldData,
//                                        'New Data' => $newData,
//                                    ];
//                                }
//
//                                // Output data in a table format
//                                echo '<table border="1">';
//                                echo '<tr>';
//                                echo '<th class="cell">Field Name</th>';
//                                echo '<th class="cell">Previous Data</th>';
//                                echo '<th class="cell">Modify Data</th>';
//                                echo '</tr>';
//
//                                foreach ($data as $row) {
//                                    echo '<tr>';
////                                    echo '<td>' . $row['Field Name'] . '</td>';
////                                    echo '<td>' . $row['Old Data'] . '</td>';
////                                    echo '<td>' . $row['New Data'] . '</td>';
//                                    echo '<td class="cell">' . $row['Field Name'] . '</td>';
//                                    echo '<td class="cell">' . $row['Old Data'] . '</td>';
//                                    echo '<td class="cell">' . $row['New Data'] . '</td>';
//                                    echo '</tr>';
//                                }
//
//                                echo '</table>';
//                            } else {
//                                echo "JSON decoding failed.";
//                            }
//                        } else {
//                            echo "No data found.";
//                        }
                        ?>
                         <table class="table">
                            <thead>
                                <tr>
                                    <th>Field Name</th>
                                    <th>Previous Data</th>
                                    <th>Modify Data</th>
                                </tr>
                            </thead>
                            <tbody id="table-body">
                                <!-- Table rows will be added dynamically here -->
                            </tbody>
                        </table>
                        
                    </div>
                    <div class="modal-footer">
                        <div class="form-group">
                            <button type="button" class="btn btn-primary pull-right" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

 </div>
<?php
    }
}
?>


