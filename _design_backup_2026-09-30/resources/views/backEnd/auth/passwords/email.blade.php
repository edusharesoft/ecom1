<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<title>Forgot Password | {{$generalsetting->name}}</title>
	<link rel="shortcut icon" href="{{asset($generalsetting->favicon)}}" alt="{{$generalsetting->name}}" />

	<link rel="stylesheet" href="{{asset('public/backEnd/')}}/assets_login/css/vendors.css">
	<link rel="stylesheet" href="{{asset('public/backEnd/')}}/assets_login/css/aiz-core.css">
</head>
<body>

<div class="aiz-main-wrapper d-flex">
	<div class="flex-grow-1">
		<div class="h-100 bg-cover bg-center py-5 d-flex align-items-center" style="background-image: url({{asset('public/backEnd/')}}/assets_login/img/background.jpg)">
			<div class="container">
				<div class="row">
					<div class="col-lg-6 col-xl-4 mx-auto">
						<div class="card text-left">
							<div class="card-body">
								<div class="mb-5 text-center">
									<img src="{{asset($generalsetting->dark_logo)}}" class="mw-100 mb-4" height="40">
									<h1 class="h3 text-primary mb-0">Forgot Password</h1>
									<p>Enter your email to reset password</p>
								</div>

								@if (session('status'))
									<div class="alert alert-success">
										{{ session('status') }}
									</div>
								@endif

								@if ($errors->any())
									<div class="alert alert-danger">
										{{ $errors->first() }}
									</div>
								@endif

								<form method="POST" action="{{ route('admin.password.email') }}">
									@csrf
									<div class="form-group">
										<input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Enter Email" required autofocus>
										@error('email')
											<span class="invalid-feedback" role="alert">
												<strong>{{ $message }}</strong>
											</span>
										@enderror
									</div>

									<button type="submit" class="btn btn-primary btn-lg btn-block">
										Send Password Reset Link
									</button>
								</form>

								<div class="mt-3 text-center">
									<a href="{{ route('login') }}" class="text-reset fs-14">Back to Login</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="{{asset('public/backEnd/')}}/assets_login/js/vendors.js"></script>
<script src="{{asset('public/backEnd/')}}/assets_login/js/aiz-core.js"></script>
</body>
</html>
