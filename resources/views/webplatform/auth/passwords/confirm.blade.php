<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link href='https://fonts.googleapis.com/css?family=Playfair Display' rel='stylesheet'>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body {
            height: 100vh;
            background-image: url('/images/background.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        h1 {
            font-family: 'Playfair Display';
            font-size: 5rem;
        }

        .card {
            width: 100%;
            max-width: 500px;
            /* Set a reasonable max width */
        }

        .title-container {
            position: absolute;
            top: 20px;
            width: 100%;
            text-align: center;
        }

        @media (max-width: 767.98px) {
            body {
                background-image: none;
                background-color: #f8f9fa;
                /* Light background color for mobile */
            }

            .card {
                max-width: 100%;
                /* Stretch the card to the max width */
                margin: 0 1rem;
                /* Small margin to keep some spacing on the sides */
            }
        }

        a{
            text-decoration: none;
        }
    </style>
</head>

<body>
    <!-- Title Positioned at the Top Center -->
    <div class="title-container">
        <a href="{{ route('login') }}"><h1 class="text-dark fw-bold"><i>iClub</i></h1></a>
    </div>

    <!-- Container for Login Card -->
    <div class="container h-100 d-flex align-items-center justify-content-center">
        <div class="card">
            <div class="card-header">{{ __('Confirm Password') }}</div>

            <div class="card-body">
                {{ __('Please confirm your password before continuing.') }}

                <form method="POST" action="{{ route('password.confirm') }}">
                    @csrf

                    <div class="row mb-3">
                        <label for="password"
                            class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                        <div class="col-md-6">
                            <input id="password" type="password"
                                class="form-control @error('password') is-invalid @enderror" name="password"
                                required autocomplete="current-password">

                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-0">
                        <div class="col-md-8 offset-md-4">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Confirm Password') }}
                            </button>

                            @if (Route::has('password.request'))
                                <a class="btn btn-link" href="{{ route('password.request') }}">
                                    {{ __('Forgot Your Password?') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
