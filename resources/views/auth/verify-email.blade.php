<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - {{ config('app.name', 'Laravel') }}</title>
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
                        <h3>Verify Email</h3>
                        <p>Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.</p>
                    </div>
                    <div class="clearfix">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-secondary float-start">
                                    Back
                                </button>
                            </form>
                            <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                                <button type="submit" class="btn btn-primary">Resend Email</button>
                            </form>
                        </div>
                    </div>
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
