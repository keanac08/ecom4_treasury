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

		<link href="<?php echo base_url('resources/css/custom_lock_screen.css');?>" rel="stylesheet" >
		<link href="<?php echo base_url('resources/css/google-font.css');?>" rel="stylesheet" >

 
	</head>
	<body class="hold-transition lockscreen">
		<!-- Automatic element centering -->
		<div class="lockscreen-wrapper">
			<div class="lockscreen-layer">
				<div class="lockscreen-layer2">
					<div class="lockscreen-logo">
						<img src="<?php echo base_url('resources/images/logo_white.png') ?>" alt="User Image"><br>
						<a href="#"><b>Treasury</b>PORTAL</a>
					</div>
					<div class="alert alert-warning text-center">
						<b>Login Failed!</b>
						 Password is incorrect.
					</div>
					<div class="lockscreen-name">
						<?php echo $fullname; ?>
					</div>
					<div class="lockscreen-item">
						<div class="lockscreen-image">
							<img src="<?php echo base_url($image) ?>" alt="User Image">
						</div>
						<form id="login-form" class="lockscreen-credentials">
							<div class="input-group">
								<input id="p_username" type="hidden" class="form-control" value="<?php echo $username; ?>">
								<input id="p_password" autofocus type="password" class="form-control" placeholder="Password">
								<div class="input-group-btn">
									<button type="submit" class="btn"><i class="fa fa-arrow-right text-muted"></i></button>
								</div>
							</div>
						</form>
					</div>
					
					<div class="help-block text-center">
						Enter your password to retrieve your session
					</div>
					<div class="or_sign_in text-center">
						<a href="index">Or sign in as a different user</a>
					</div>
					<div class="lockscreen-footer text-center">
						Management Information System<br>
						© 2017. All Rights Reserved.
					</div>
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
								window.location.href = '<?php echo base_url($last_link);?>';
							}
						}
					});
				});
			});
		</script>
	</body>
</html>
