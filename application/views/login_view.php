<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="author" content="Chris Desiderio">
		<title>Treasury Portal</title>
		<!-- Tell the browser to be responsive to screen width -->
		<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
		<!-- Bootstrap core CSS -->
		<link href="<?php echo base_url('resources/templates/bootstrap-3.3.7/css/bootstrap.min.css');?>" rel="stylesheet" >
		<!-- Admin LTE core CSS -->
		<link href="<?php echo base_url('resources/templates/AdminLTE-2.4.2/dist/css/AdminLTE.min.css');?>" rel="stylesheet" >
		<!-- Custom styles for this template -->
		<link href="<?php echo base_url('resources/css/custom_login.css');?>" rel="stylesheet" >
	</head>
	<body class="page-login-v2 layout-full page-dark">
		<div class="page animsition" style="opacity: .85; animation-duration: 800ms;">
			<div class="page-content">
				<div class="page-brand-info">
					<div class="brands">
						<h2 class="brand-text font-size-40 font-weight-400"></h2>
					</div>
				</div>
				
				<div class="page-login-main">
					<div class="brand" style="text-align: center;">
						<img class="brand-img" style="margin-bottom: 10px;" src="<?php echo base_url('resources/images/logo_white.png') ?>" alt="...">
						<p style="font-size: 15px;">Treasury Portal</p>
					</div>
					
					<form id="login-form" role="form">
						<h3 style="font-size: 15px;">Sign in to start your session</h3>
						
						<!--ERROR MESSAGE-->
						<div class="alert alert-warning">
							<h4>Login Failed!</h4>
							 Either your username or password is incorrect.
						</div>
						
						<div class="form-group">
							<input type="text" class="form-control" id="p_username" placeholder="Username">
						</div>
						<div class="form-group">
							<input type="password" class="form-control" id="p_password" placeholder="Password">
						</div>
			 
						<button type="submit" class="btn btn-danger btn-flat btn-block">Sign in</button>
						<p></p>
					</form>
					<footer class="page-copyright">
						<p>Management Information System</p>
						<p>© 2017. All Rights Reserved.</p>
					</footer>
				</div>
			</div>
		</div>
		
		<script src="<?php echo base_url('resources/js/jquery-3.2.1/dist/jquery.min.js');?>"></script>
		<script src="<?php echo base_url('resources/templates/bootstrap-3.3.7/js/bootstrap.min.js');?>"></script>
		<script>
			$(document).ready(function() {
				
				$('.alert').hide();
				
				$('#login-form').submit(function(e){
					
					e.preventDefault();
					var p_username = $('#p_username').val();
					var p_password = $('#p_password').val();
					
					$.ajax({
						type:'POST',
						data:{
							username : p_username,
							password : p_password
						},
						url:'<?php echo base_url();?>login/ajax_validate',
						success:function(response){
							if(response != 'success'){
								$('.alert').show('slow');
							}
							else {
								window.location.href = 'receivables/transaction/per_customer';
							}
						}
					});
				});
			});
		</script>
	</body>
</html>
