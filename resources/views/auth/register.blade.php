<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up - {{ config('app.name', 'Laravel') }}</title>
    <link rel="stylesheet" href="{{ asset ('') }}cms/assets/css/bootstrap.css">

    <link rel="shortcut icon" href="{{ asset ('') }}cms/assets/images/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset ('') }}cms/assets/css/app.css">
</head>

<body>
    <div id="auth">

<div class="container">
    <div class="row">
        <div class="col-md-7 col-sm-12 mx-auto">
            <div class="card pt-4">
                <div class="card-body">
                    <div class="text-center mb-5">
                        <img src="{{ asset ('') }}cms/assets/images/favicon.svg" height="48" class='mb-4'>
                        <h3>Sign Up</h3>
                        <p>Please fill the form to register.</p>
                    </div>
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="firstname">First Name</label>
                                    <input type="text" id="firstname" class="form-control"  name="firstname">
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="lastname">Last Name</label>
                                    <input type="text" id="lastname" class="form-control" name="lastname">
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" id="email" class="form-control" name="email">
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="phone">Telephone/Whatsapp</label>
                                    <input type="text" id="phone" class="form-control" name="phone">
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" id="password" class="form-control" name="password">
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="password_confirmation">Confirm Password</label>
                                    <input type="password" id="password_confirmation" class="form-control" name="password_confirmation">
                                </div>
                            </div>
                        </diV>
                            <a href="{{ route('login') }}">Have an account? Login</a>
                        <div class="clearfix">
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
