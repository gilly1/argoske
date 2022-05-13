<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    
    @include('inc/main/head')
        @section('headSection')
            @show

</head>
    <body class="hold-transition login-page" style="background-image: url('{{ asset('images/loginbackgound2.jpg')}}');background-size: cover">
        <div class="login-box">
            <div class="login-logo">
                    <a style="font-weight:500;color:blue;" href="#"><b style="font-weight:400;color:black;"></b>Argos</a>
            </div>

                <!-- /.login-logo -->
                <div class="card">
                    <div class="card-body login-card-body">
                    <p class="login-box-msg">Sign in to start your session</p>
                
                    
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="input-group mb-3">
                                <input id="email" type="email"  placeholder="Email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-envelope"></span>
                                    </div>
                                </div>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                        </div>

                        <div class="input-group mb-3">
                                <input id="password" type="password"  placeholder="Password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                                
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-lock"></span>
                                    </div>
                                </div>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-8">
                                  <div class="icheck-primary">
                                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label for="remember">
                                      Remember Me
                                    </label>
                                  </div>
                                </div>
                                <!-- /.col -->
                                <div class="col-4">
                                  <button type="submit" class="btn btn-primary btn-block">
                                      {{ __('Login') }}
                                  </button>
                                </div>
                                <!-- /.col -->
                            </div>
                    </form>
                
                
                    {{-- <p class="mb-1">
                        <a href="forgot-password.html">I forgot my password</a>
                    </p>
                    <p class="mb-0">
                        <a href="register.html" class="text-center">Register a new membership</a>
                    </p> --}}
                    </div>
                    <!-- /.login-card-body -->
                </div>
            </div>
               

    </body>
</html>
