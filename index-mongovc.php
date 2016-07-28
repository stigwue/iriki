
<!DOCTYPE html>
<html lang="en">
<head>
    <base href="//olibenu/apps/mongovc/" />
    <title>MongoVC</title>
	<meta charset="UTF-8">
	<meta content="IE=edge" http-equiv="X-UA-Compatible">
	<meta content="initial-scale=1.0, width=device-width" name="viewport">

	<!-- css -->
	<link href="./assets/css/base.min.css" rel="stylesheet">
	<link href="./assets/css/project.min.css" rel="stylesheet">

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

	<header class="header">
        <a class="header-logo" href="./index.php"><i class="fa fa-leaf"></i> MongoVC</a>
	</header>

	<script src="./assets/js/jquery.js"></script>

	<?php //require_once('./admin/footer.php') ?>

    <!--div class="fbtn-container">
	    <div class="fbtn-inner">
	        <a class="fbtn fbtn-red fbtn-lg" data-toggle="dropdown"><span class="fbtn-text">Menu</span>
	        <span class="fbtn-ori"><i class="fa fa-leaf"></i></span>
	        <span class="fbtn-sub fa fa-close"></span></a>
	        <div class="fbtn-dropdown">
	            <a class="fbtn" href="#"><span class="fbtn-text">Test</span><span class="fa fa-user"></span></a>
	        </div>
	    </div>
	</div-->

	<div class="fbtn-container">
		<div class="fbtn-inner open">
			<a class="fbtn fbtn-lg fbtn-brand-accent waves-attach waves-circle waves-light waves-effect" data-toggle="dropdown" aria-expanded="true"><span class="fbtn-text fbtn-text-left">Links</span><span class="fbtn-ori icon">apps</span><span class="fbtn-sub icon">close</span></a>
			<div class="fbtn-dropup">
				<a class="fbtn waves-attach waves-circle waves-effect" href="https://github.com/Daemonite/material" target="_blank"><span class="fbtn-text fbtn-text-left">Fork me on GitHub</span><span class="icon">code</span></a>
				<a class="fbtn fbtn-brand waves-attach waves-circle waves-light waves-effect" href="https://twitter.com/daemonites" target="_blank"><span class="fbtn-text fbtn-text-left">Follow Daemon on Twitter</span><span class="icon">share</span></a>
				<a class="fbtn fbtn-green waves-attach waves-circle waves-effect" href="http://www.daemon.com.au/" target="_blank"><span class="fbtn-text fbtn-text-left">Visit Daemon Website</span><span class="icon">link</span></a>
			</div>
		</div>
	<div></div></div>

    <script src="./assets/js/jquery.timeago.js"></script>
    <script type="text/javascript">
        jQuery(document).ready(function() {
          jQuery("abbr.timeago").timeago();
        });
    </script>

	<!-- js -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
	<script src="./assets/js/base.min.js"></script>
	<script src="./assets/js/project.min.js"></script>
</body>
</html>
