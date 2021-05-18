<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="https://unpkg.com/@coreui/coreui/dist/css/coreui.min.css">
    <link rel="stylesheet" href="https://unpkg.com/@coreui/icons@2.0.0-beta.3/css/all.min.css">


</head>
<body class="c-app flex-row align-items-center">

    <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4">

                <div class="card-body">
                    <h1>Login</h1>
                    <p class="text-muted">Sign In to your account</p>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                              <span class="input-group-text">
                               <i class="cil-user"></i>
                              </span>
                            </div>

                            <input id="email" type="email"  placeholder="Email"  class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                        </div>

                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror

                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                              <span class="input-group-text">
                                    <i class="cil-lock-locked"></i>
                              </span>
                            </div>

                            <input id="password" type="password" placeholder="Password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                        </div>

                        @error('password')
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                        </span>
                        @enderror

                        <div class="row">
                            <div class="col-6">
                                <button class="btn btn-primary px-4" type="submit">Login</button>
                            </div>

                            @if (Route::has('password.request'))
                                <div class="col-6 text-right">
                                    <a href="{{ route('password.request') }}" class="btn btn-link">Forgot Your Password?</a>
                                </div>

                            @endif


                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>

<script src="https://unpkg.com/@coreui/coreui/dist/js/coreui.bundle.min.js"></script>

</div>
</body>
</html>
