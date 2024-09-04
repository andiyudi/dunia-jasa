<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - {{ config('app.name', 'Laravel') }}</title>
    <link rel="stylesheet" href="{{ asset ('') }}cms/assets/css/bootstrap.css">

    <link rel="shortcut icon" href="{{ asset ('') }}cms/assets/images/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset ('') }}cms/assets/css/app.css">
</head>

<body>
    <div id="auth">

<div class="container">
    <div class="row">
        <div class="col-md-5 col-sm-12 mx-auto">
            <div class="card py-4">
                <div class="card-body">
                    <div class="text-center mb-5">
                        <img src="{{ asset ('') }}cms/assets/images/favicon.svg" height="48" class='mb-4'>
                        <h3>Forgot Password</h3>
                        <p>Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.</p>
                    </div>
                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" class="form-control" name="email">
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
