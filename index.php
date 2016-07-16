
<!DOCTYPE html>
<html lang="en">
<head>
    <base href="//olibenu/apps/mongovc/" />
    <title>MongoVC</title>
	<meta charset="UTF-8">
	<meta content="IE=edge" http-equiv="X-UA-Compatible">
	<meta content="initial-scale=1.0, width=device-width" name="viewport">
	
	<!-- css -->
	<link href="./assets/css/base.css" rel="stylesheet">
    <link href="./assets/css/style.css" rel="stylesheet">

	<!-- favicon -->
	<link rel="shortcut icon" href="./assets/ico/favicon.ico"/>
	
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">
	
	<!-- ie -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
 
</head>
<body class="page-<?php echo BASE_PALETTE ?> avoid-fout">
    <?php //require_once('./admin/header.php') ?>


	<script src="./assets/js/jquery.js"></script>

	<?php //require_once('./admin/footer.php') ?>

    <div class="fbtn-container">
	    <div class="fbtn-inner">
	        <a class="fbtn fbtn-red fbtn-lg" data-toggle="dropdown"><span class="fbtn-text">Menu</span>
	        <span class="fbtn-ori"><i class="fa fa-leaf"></i></span>
	        <span class="fbtn-sub fa fa-close"></span></a>
	        <div class="fbtn-dropdown">
	            <a class="fbtn" href="#"><span class="fbtn-text">Test</span><span class="fa fa-user"></span></a>
	        </div>
	    </div>
	</div>
    
    <script src="./assets/js/jquery.timeago.js"></script>
    <script type="text/javascript">
        jQuery(document).ready(function() {
          jQuery("abbr.timeago").timeago();
        });
    </script>
	<script src="./assets/js/base.min.js" type="text/javascript"></script>
</body>
</html>