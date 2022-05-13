<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    
    @include('inc/main/head')
        @section('headSection')
            @show

</head>
    <body class="hold-transition login-page">
    <!-- Site wrapper -->
        <div class="wrapper">

            <div class="content-section">
                
                <!-- Content Wrapper. Contains page content -->
                <div class="content-wrapper">
                    <div class="login-box">
                        <div class="login-logo">
                          <a href="#"><b>Lara</b>SetUp</a>
                        </div>
                        <!-- /.login-logo -->
                        <div class="card">
                          <div class="card-body login-card-body">
                            <p class="login-box-msg">Sign in to start your session</p>
                      
                            
                            <form method="POST" action="{{ route('login') }}">
                                @csrf

                                <div class="form-group row">
                                    <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                                    <div class="col-md-6">
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                                    <div class="col-md-6">
                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-6 offset-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                            <label class="form-check-label" for="remember">
                                                {{ __('Remember Me') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row mb-0">
                                    <div class="col-md-8 offset-md-4">
                                        <button type="submit" class="btn btn-primary">
                                            {{ __('Login') }}
                                        </button>

                                        @if (Route::has('password.request'))
                                            <a class="btn btn-link" href="{{ route('password.request') }}">
                                                {{ __('Forgot Your Password?') }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </form>
                      
                      
                            <p class="mb-1">
                              <a href="forgot-password.html">I forgot my password</a>
                            </p>
                            <p class="mb-0">
                              <a href="register.html" class="text-center">Register a new membership</a>
                            </p>
                          </div>
                          <!-- /.login-card-body -->
                        </div>
                      </div>
                
                    

                    
                </div>
                <!-- /.content-wrapper -->
                    
            </div>

        </div>

    </body>
</html>
