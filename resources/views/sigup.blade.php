<!doctype html>
<html class="fixed">
	<head>

		<!-- Basic -->
		<meta charset="UTF-8">

		<meta name="keywords" content="HTML5 Admin Template" />
		<meta name="description" content="Porto Admin - Responsive HTML5 Template">
		<meta name="author" content="okler.net">

		<!-- Mobile Metas -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

		<!-- Web Fonts  -->
		<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css">

		<!-- Vendor CSS -->
		<link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.css" />
		<link rel="stylesheet" href="vendor/animate/animate.css">

		<link rel="stylesheet" href="vendor/font-awesome/css/all.min.css" />
		<link rel="stylesheet" href="vendor/magnific-popup/magnific-popup.css" />
		<link rel="stylesheet" href="vendor/bootstrap-datepicker/css/bootstrap-datepicker3.css" />

		<!-- Theme CSS -->
		<link rel="stylesheet" href="css/theme.css" />

		<!-- Skin CSS -->
		<link rel="stylesheet" href="css/skins/default.css" />

		<!-- Theme Custom CSS -->
		<link rel="stylesheet" href="css/custom.css">

		<!-- Head Libs -->
		<script src="vendor/modernizr/modernizr.js"></script>

	</head>
	<body>
		<!-- start: page -->
		<section class="body-sign">
			<div class="center-sign">
				<a href="/" class="logo float-left">
					<h1 style="margin: 0;">Task Management</h1>
					{{-- <img src="img/logo.png" height="54" alt="Porto Admin" /> --}}
				</a>

				<div class="panel card-sign">
					<div class="card-title-sign mt-3 text-right">
						<h2 class="title text-uppercase font-weight-bold m-0"><i class="fas fa-user mr-1"></i> Sign Up</h2>
					</div>
					<div class="card-body">
						<form action="{{ route('signuppost') }}" method="post">
							@csrf

							@if (Session::has('error'))
								<div class="alert alert-danger">{{ Session::get('error') }}</div>
							@endif
								@if(Session::has('status') && Session::get('status'))
					<div class="row">
						<div class="col">
							<div class="alert alert-success mt-20">
								Register Created  <strong> Succesfully!</strong>
							</div>
						</div>
					</div>
					@endif          
							<div class="form-group mb-3">
								<label>Name</label>
								<div class="input-group">
									<input name="name" type="text" class="form-control form-control-lg" />
									
								</div>
								@if ($errors->has('name')) 
									<div class="validation-error errorActive">{!! $errors->first('name') !!}</div> 
								@endif

							</div>

							<div class="form-group mb-3">
								<label>Email</label>
								<div class="input-group">
									<input name="email" type="text" class="form-control form-control-lg" />
									
								</div>
								@if ($errors->has('email')) 
									<div class="validation-error errorActive">{!! $errors->first('email') !!}</div> 
								@endif
							</div>

							<div class="form-group mb-3">
								<div class="clearfix">
									<label class="float-left">Password</label>
								</div>
								<div class="input-group">
									<input name="password" type="password" class="form-control form-control-lg" />
								</div>
								@if ($errors->has('password')) 
									<div class="validation-error errorActive">{!! $errors->first('password') !!}</div> 
								@endif
							</div>

							<div class="form-group mb-3">
								<div class="clearfix">
									<label class="float-left">User Type</label>
								</div>
								<div class="input-group">
									<select class="form-control form-control-lg" name="usertype">
										<option value="">Select</option>
										<option value="admin">Admin</option>
										<option value="user">User</option>
										<option value="member">Member</option>
									</select>
								</div>
								@if ($errors->has('usertype')) 
									<div class="validation-error errorActive">{!! $errors->first('usertype') !!}</div> 
								@endif
							</div>
							<div class="row">
								
								<div class="col-sm-4 text-right">
									<button type="submit" style="margin-right: -116px;" class="btn btn-primary mt-2">Register</button>
								</div>
							</div>												

							<p class="text-center">Don't have an account yet? <a href="/login">Sign In!</a></p>

						</form>
					</div>
				</div>

				<p class="text-center text-muted mt-3 mb-3">&copy; Copyright {{ date('Y') }}. All Rights Reserved.</p>
			</div>
		</section>
		<!-- end: page -->

		<!-- Vendor -->
		<script src="vendor/jquery/jquery.js"></script>
		<script src="vendor/jquery-browser-mobile/jquery.browser.mobile.js"></script>
		<script src="vendor/popper/umd/popper.min.js"></script>
		<script src="vendor/bootstrap/js/bootstrap.js"></script>
		<script src="vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
		<script src="vendor/common/common.js"></script>
		<script src="vendor/nanoscroller/nanoscroller.js"></script>
		<script src="vendor/magnific-popup/jquery.magnific-popup.js"></script>
		<script src="vendor/jquery-placeholder/jquery.placeholder.js"></script>
		
		<!-- Theme Base, Components and Settings -->
		<script src="js/theme.js"></script>
		
		<!-- Theme Custom -->
		<script src="js/custom.js"></script>
		
		<!-- Theme Initialization Files -->
		<script src="js/theme.init.js"></script>

	</body>
</html>