<!doctype html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<head>
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<link rel="shortcut icon" href="{{asset($generalsetting->favicon)}}" alt="{{$generalsetting->name}}" />
	<title>Admin Login | {{$generalsetting->name}}</title>

	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700">
	<link rel="stylesheet" href="{{asset('public/backEnd/')}}/assets_login/css/vendors.css">
	<link rel="stylesheet" href="{{asset('public/backEnd/')}}/assets_login/css/aiz-core.css">

	<style>
		body { font-size: 12px; }
	</style>
</head>
<body class="">

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
									<h1 class="h3 text-primary mb-0">Welcome to {{$generalsetting->name}}</h1>
									<p>Login to your account.</p>
								</div>

								@if(session('error'))
									<div class="alert alert-danger alert-dismissible fade show" role="alert">
										<strong>Error!</strong> {{ session('error') }}
										<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
									</div>
								@endif

								@if($errors->any())
									<div class="alert alert-danger alert-dismissible fade show" role="alert">
										<strong>Error!</strong>
										<ul class="mb-0">
											@foreach($errors->all() as $error)
												<li>{{ $error }}</li>
											@endforeach
										</ul>
										<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
									</div>
								@endif

								<form method="POST" action="{{route('login')}}">
									@csrf
									<div class="form-group">
										<input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus placeholder="Email">
										@error('email')
											<span class="invalid-feedback" role="alert">
												<strong>{{ $message }}</strong>
											</span>
										@enderror
									</div>
									<div class="form-group">
										<input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required placeholder="Password">
										@error('password')
											<span class="invalid-feedback" role="alert">
												<strong>{{ $message }}</strong>
											</span>
										@enderror
									</div>
									<div class="row mb-2">
										<div class="col-sm-6">
											<div class="text-left">
												<label class="aiz-checkbox">
													<input type="checkbox" name="remember" id="checkbox-signin" value="1">
													<span>Remember Me</span>
													<span class="aiz-square-check"></span>
												</label>
											</div>
										</div>
										<div class="col-sm-6">
											<div class="text-right">
												<a href="{{ route('admin.password.request') }}" class="text-reset fs-14">Forgot password ?</a>
											</div>
										</div>
									</div>
									<button type="submit" class="btn btn-primary btn-lg btn-block">
										Login
									</button>
								</form>

								@if(isset($demoMode) && $demoMode)
								<div class="mt-4 pt-3 border-top">
									<p class="text-muted small mb-2">ডেমো একাউন্ট</p>
									<div class="d-flex gap-2 align-items-center flex-wrap">
										<input type="text" class="form-control form-control-sm" id="demo-email" value="info@creativedesign.com.bd" readonly style="flex:1;min-width:0;">
										<input type="text" class="form-control form-control-sm" id="demo-password" value="12345678" readonly style="width:100px;">
										<button type="button" class="btn btn-outline-primary btn-sm" onclick="fillDemoCreds()">Use</button>
									</div>
								</div>
								@endif

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
@if(isset($demoMode) && $demoMode)
<script>
function fillDemoCreds() {
	document.getElementById('email').value    = document.getElementById('demo-email').value;
	document.getElementById('password').value = document.getElementById('demo-password').value;
	var btn = document.querySelector('button[onclick="fillDemoCreds()"]');
	if (btn) { btn.textContent = 'Filled!'; setTimeout(function(){ btn.textContent = 'Use'; }, 1200); }
}
</script>
@endif
</body>
</html>
