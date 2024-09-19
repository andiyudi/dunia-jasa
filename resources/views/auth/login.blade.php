<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in - {{ config('app.name', 'Laravel') }}</title>
    <link rel="stylesheet" href="{{ asset ('') }}cms/assets/css/bootstrap.css">

    <link rel="shortcut icon" href="{{ asset ('') }}cms/assets/images/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset ('') }}cms/assets/css/app.css">
</head>

<body>
    <div id="auth">

<div class="container">
    <div class="row">
        <div class="col-md-5 col-sm-12 mx-auto">
            <div class="card pt-4">
                <div class="card-body">
                    <div class="text-center mb-5">
                        <img src="{{ asset ('') }}cms/assets/images/favicon.svg" height="48" class='mb-4'>
                        <h3>Sign In</h3>
                        <p>Please sign in to continue to Dunia Jasa.</p>
                    </div>
                    <!-- Check for session status (e.g., success message) -->
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <!-- Display validation error messages -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="form-group position-relative has-icon-left">
                            <label for="email">Email</label>
                            <div class="position-relative">
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus>
                                <div class="form-control-icon">
                                    <i data-feather="mail"></i>
                                </div>
                            </div>
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group position-relative has-icon-left">
                            <div class="clearfix">
                                <label for="password">Password</label>
                                <a href="{{ route('password.request') }}" class='float-end'>
                                    <small>Forgot password?</small>
                                </a>
                            </div>
                            <div class="position-relative">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                                <div class="form-control-icon">
                                    <i data-feather="lock"></i>
                                </div>
                            </div>
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class='form-check clearfix my-4'>
                            <div class="checkbox float-start">
                                <input type="checkbox" id="checkbox1" class='form-check-input' >
                                <label for="checkbox1">Remember me</label>
                            </div>
                            <div class="float-end">
                                <a href="{{ route('register') }}">Don't have an account?</a>
                            </div>
                        </div>
                        <div class="clearfix">
                            <a href="{{ route('home') }}" type="button" class="btn btn-secondary me-md-2">Back</a>
                            <button type="submit" class="btn btn-primary float-end">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

    </div>
    <script src="{{ asset ('') }}cms/assets/js/feather-icons/feather.min.js"></script>
    <script src="{{ asset ('') }}cms/assets/js/app.js"></script>

    <script src="{{ asset ('') }}cms/assets/js/main.js"></script>
</body>

</html>
