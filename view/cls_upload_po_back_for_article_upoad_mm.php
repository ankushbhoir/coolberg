<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";
require_once "lib/db/DBLogic.php";

class cls_upload_po extends cls_renderer {

    var $currStore;

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
        <style type="text/css" title="currentStyle">
            /*  @import "js/datatables/media/css/demo_page.css";
              @import "js/datatables/media/css/demo_table.css";*/
            @import "https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css";
            @import "https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css";
        </style>
        <script type="text/javaScript">    
            $(function(){      

            });


        </script>
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
    <?php
    }

    public function pageContent() {
        $menuitem = "uploadpo";
        include "sidemenu.php";
        $formResult = $this->getFormResult();
        $dbl = new DBLogic();
        $chainListObj = $dbl->getChainList();
//            $dbl = new DBLogic();            
        ?>

        <div class="container-section">
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <h2 class="title-bar">Upload POs</h2>
                            <div class="common-content-block">
                                <form  role="form" id="createuser" name="createuser" enctype="multipart/form-data" method="post" action="formpost/uploadPOs.php">
                                    <input hidden id="form_id" name="form_id" value="uploadPOs"/>
                                    <div class="box box-primary"><br>

                                        <div class="col-md-6">
                                            <input name="upload[]" type="file" accept="application/pdf" multiple="multiple" />
                                        </div>
                                        <div class="col-md-6">
                                            <button type="submit" class="btn btn-primary pull-right">Uplaod</button>
                                        </div>

                                    </div>   
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
        <?php if ($formResult->form_id == 'uploadPOs') { ?>
                        <div class="alert alert-<?php echo $formResult->cssClass; ?> alert-dismissible" style="display:<?php echo $formResult->showhide; ?>;">
                            <button class="close" type="button" data-dismiss="alert" aria-hidden="true">×</button>
                            <h4> <?php echo $formResult->status; ?>
                        </div>
        <?php } ?>
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


