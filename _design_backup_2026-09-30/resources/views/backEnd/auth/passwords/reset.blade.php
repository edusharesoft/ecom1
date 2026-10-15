<!doctype html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<head>
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<link rel="shortcut icon" href="{{asset($generalsetting->favicon)}}" alt="{{$generalsetting->name}}" />
	<title>Reset Password | {{$generalsetting->name}}</title>

	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700">
	<link rel="stylesheet" href="{{asset('public/backEnd/')}}/assets_login/css/vendors.css">
	<link rel="stylesheet" href="{{asset('public/backEnd/')}}/assets_login/css/aiz-core.css">

	<style>
		body { font-size: 12px; }
		.password-wrapper { position: relative; }
		.pw-toggle {
			position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
			background: none; border: none; cursor: pointer;
			color: #aaa; font-size: 14px; padding: 0; line-height: 1;
		}
		.pw-toggle:hover { color: #4e73df; }
		.strength-bar { height: 4px; border-radius: 2px; background: #e9ecef; margin-top: 6px; overflow: hidden; }
		.strength-fill { height: 100%; width: 0; border-radius: 2px; transition: width .3s, background .3s; }
		.strength-text { font-size: 11px; color: #aaa; margin-top: 3px; }
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
									<h1 class="h3 text-primary mb-0">Reset Password</h1>
									<p>Enter your new password below.</p>
								</div>

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

								<form method="POST" action="{{ route('admin.password.update') }}">
									@csrf
									<input type="hidden" name="token" value="{{ $token }}">

									{{-- Email --}}
									<div class="form-group">
										<input id="email" type="email" name="email"
											   value="{{ old('email', $email ?? '') }}"
											   class="form-control @error('email') is-invalid @enderror"
											   placeholder="Email" required autofocus>
										@error('email')
											<span class="invalid-feedback" role="alert">
												<strong>{{ $message }}</strong>
											</span>
										@enderror
									</div>

									{{-- New Password --}}
									<div class="form-group">
										<div class="password-wrapper">
											<input id="password" type="password" name="password"
												   class="form-control @error('password') is-invalid @enderror"
												   placeholder="New Password" required
												   oninput="checkStrength(this.value)">
											<button type="button" class="pw-toggle" onclick="togglePw('password', this)">👁</button>
										</div>
										@error('password')
											<span class="invalid-feedback" role="alert">
												<strong>{{ $message }}</strong>
											</span>
										@enderror
										<div class="strength-bar"><div class="strength-fill" id="strength-fill"></div></div>
										<div class="strength-text" id="strength-text"></div>
									</div>

									{{-- Confirm Password --}}
									<div class="form-group">
										<div class="password-wrapper">
											<input id="password-confirm" type="password" name="password_confirmation"
												   class="form-control"
												   placeholder="Confirm New Password" required>
											<button type="button" class="pw-toggle" onclick="togglePw('password-confirm', this)">👁</button>
										</div>
									</div>

									<button type="submit" class="btn btn-primary btn-lg btn-block">
										Reset Password
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
<script>
function togglePw(id, btn) {
	var inp = document.getElementById(id);
	inp.type = inp.type === 'password' ? 'text' : 'password';
	btn.textContent = inp.type === 'password' ? '👁' : '🙈';
}
function checkStrength(val) {
	var fill  = document.getElementById('strength-fill');
	var text  = document.getElementById('strength-text');
	var score = 0;
	if (val.length >= 8)           score++;
	if (/[A-Z]/.test(val))        score++;
	if (/[0-9]/.test(val))        score++;
	if (/[^A-Za-z0-9]/.test(val)) score++;
	if (!val.length) { fill.style.width = '0'; text.textContent = ''; return; }
	var colors = ['#ef4444','#f97316','#eab308','#22c55e'];
	var labels = ['Weak','Fair','Good','Strong'];
	var widths = ['25%','50%','75%','100%'];
	var i = score - 1;
	fill.style.width      = widths[i] || '25%';
	fill.style.background = colors[i] || '#ef4444';
	text.textContent      = labels[i] || 'Weak';
	text.style.color      = colors[i] || '#ef4444';
}
</script>
</body>
</html>
