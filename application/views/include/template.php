<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		<meta name="description" content="treasury-portal">
		<meta name="author" content="chris-tupe-r">
		<link rel="icon" href="<?php echo base_url('/favicon.ico')?>">
		<title><?php echo $head_title; ?></title>
		
		<!-- Bootstrap core CSS -->
		<link href="<?php echo base_url('resources/templates/bootstrap-3.3.7/css/bootstrap.min.css');?>" rel="stylesheet" >
		<!-- Admin LTE core CSS -->
		<link href="<?php echo base_url('resources/templates/AdminLTE-2.4.2/dist/css/AdminLTE.min.css');?>" rel="stylesheet" >
		<link href="<?php echo base_url('resources/templates/AdminLTE-2.4.2/dist/css/skins/skin-red.min.css');?>" rel="stylesheet" >
		<!-- Font Awesome -->
		<link href="<?php echo base_url('resources/fonts/font-awesome-4.7.0/css/font-awesome.min.css');?>" rel="stylesheet" >
		<!-- Google Font -->
		<link href="<?php echo base_url('resources/css/google-font.css');?>" rel="stylesheet" >

		<!-- jQuery 3.0.0 -->
		<script src="<?php echo base_url('resources/js/jquery-3.2.1/dist/jquery.min.js');?>"></script>
		
	</head>
	<body class="hold-transition skin-red sidebar-mini">
		<div class="wrapper">
			<?php $this->load->view('include/header.php'); ?>
			<?php $this->load->view('include/sidebar-menu.php'); ?>
			<div class="content-wrapper">
				<section class="content-header">
					<h1><?php echo $title; ?><small><?php echo @$subtitle; ?></small></h1>
				</section>
				<?php $this->load->view($content); ?>
			</div>
			<?php $this->load->view('include/footer.php'); ?>
			<?php $this->load->view('include/control-sidebar.php'); ?>
		</div>
		<link href="<?php echo base_url('resources/templates/AdminLTE-2.4.2/dist/css/AdminLTE.min.css');?>" rel="stylesheet" >
		<!-- Custom styles for this template -->
		<link href="<?php echo base_url('resources/css/custom.css');?>" rel="stylesheet" >
		
		<!-- Bootstrap core JavaScript
		================================================== -->
		<!-- Placed at the end of the document so the pages load faster -->
		<!-- Bootstrap 3.3.6 -->
		<script src="<?php echo base_url('resources/templates/bootstrap-3.3.7/js/bootstrap.min.js');?>"></script>
		<!-- Admin LTE app js -->
		<script src="<?php echo base_url('resources/templates/AdminLTE-2.4.2/dist/js/adminlte.min.js');?>"></script>
	</body>
</html>
