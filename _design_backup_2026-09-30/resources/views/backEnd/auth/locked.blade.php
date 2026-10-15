<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="utf-8" />
        <title>Locked | MasterEcommerce</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
        <meta content="Coderthemes" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="{{asset('public/backEnd/')}}/assets/images/favicon.ico">

        <!-- Bootstrap css -->
        <link href="{{asset('public/backEnd/')}}/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <!-- App css -->
        <link href="{{asset('public/backEnd/')}}/assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style"/>
        <!-- icons -->
        <link href="{{asset('public/backEnd/')}}/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <!-- Head js -->
        <script src="{{asset('public/backEnd/')}}/assets/js/head.js"></script>

    </head>

    <body class="authentication-bg authentication-bg-pattern">

        <div class="account-pages mt-5 mb-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-4">
                        <div class="card bg-pattern">

                            <div class="card-body p-4">
                                
                                <div class="text-center w-75 m-auto">
                                    <div class="auth-logo">
                                        <a href="index.html" class="logo logo-dark text-center">
                                            <span class="logo-lg">
                                                <img src="{{asset('public/backEnd/')}}/assets/images/logo-dark.png" alt="" height="22">
                                            </span>
                                        </a>
                    
                                        <a href="" class="logo logo-light text-center">
                                            <span class="logo-lg">
                                                <img src="{{asset('public/backEnd/')}}/assets/images/logo-light.png" alt="" height="22">
                                            </span>
                                        </a>
                                    </div>
                                    <p class="text-muted mb-4 mt-3">Your panel is not locked password to access unlocked panel.</p>
                                </div>

                                <form method="POST" action="{{route('unlocked')}}" >
                                    @csrf
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <div class="input-group input-group-merge">
                                            <input type="password" id="password" class="form-control @error('password') is-invalid @enderror" name="password" value="{{ old('password') }}" required autocomplete="password" autofocus placeholder="Enter your password">
                                            <div class="input-group-text" data-password="false">
                                                <span class="password-eye"></span>
                                            </div>
                                            @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="checkbox-signin" value="1" name="remember" checked>
                                            <label class="form-check-label" for="checkbox-signin">Remember me</label>
                                        </div>
                                    </div>

                                    <div class="text-center d-grid">
                                        <button class="btn btn-primary" type="submit"> Unlock </button>
                                    </div>

                                </form>


                            </div> <!-- end card-body -->
                        </div>
                        <!-- end card -->

                    </div> <!-- end col -->
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end page -->

        <!-- Vendor js -->
        <script src="{{asset('public/backEnd/')}}/assets/js/vendor.min.js"></script>

        <!-- App js -->
        <script src="{{asset('public/backEnd/')}}/assets/js/app.min.js"></script>
        
    </body>
</html>