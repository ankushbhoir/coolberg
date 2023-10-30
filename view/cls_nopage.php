<?php
require_once "view/cls_renderer.php";

class cls_nopage extends cls_renderer {

	function __construct($params=null) {

	}

	function extraHeaders() { ?>
		
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<?php }

	public function pageContent() { ?>

		<div class="container-section">
                    <div class="row">
                        <div class="col-md-12">
                            <br>
			  <div class="common-content-block">The page you requested was not found or your session has timed out.<br>Please go back to the <a href="<?php echo DEF_SITEURL; ?>">Login page</a></div>
                        </div>
                    </div>    
		</div>
<?php
	}

}
?>
