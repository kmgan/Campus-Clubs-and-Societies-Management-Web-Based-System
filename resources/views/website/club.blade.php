<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $club->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">

    <style>
        /* CSS for the background image */
        .bg-container {
            background-image: url('{{ asset($club->club_category->bg_img_url ?? 'images/default-background.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            /* Ensures it covers the full height */
            color: white;
            /* Adjust text color for better readability */
        }

        .about-us-img {
            border-top-right-radius: 2rem;
            border-top-left-radius: 2rem;
            border-bottom-left-radius: 4rem;
        }

        .activity-img {
            border-bottom-right-radius: 4rem;
            border-top-left-radius: 2rem;
            border-bottom-left-radius: 2rem;
        }

        .grey-bg {
            background-color: #ccccccd5;
        }

        .rounded-top-left-1 {
            border-top-left-radius: 5rem;
        }

        .rounded-top-right-1 {
            border-top-right-radius: 5rem;
        }

        .url-icon {
            font-size: 2.5rem;
        }

        html {
            scroll-padding-top: 62px;
        }

        @media (max-width: 767.98px) {
            html {
                scroll-padding-top: 200px;
                /* Adjusted for smaller screens like mobile */
            }
        }

        #highlightsCarousel .carousel-inner img {
            max-height: 500px;
            object-fit: contain;
            width: 100%;
        }

        .carousel {
            overflow: hidden
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-dark bg-dark navbar-expand-lg sticky-top">
        <div class="container-fluid">
            <a href="{{ route('home') }}" class="fs-5 px-3 text-white"
                style="text-decoration: none; font-weight: bold;">
                &larr; Back
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link fs-5 px-3" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fs-5 px-3" href="#activities">Activities</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fs-5 px-3" href="#gallery">Gallery</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fs-5 px-3" href="#membership">Membership</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fs-5 px-3" href="#contact">Contact</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container-fluid bg-container">
        @if ($club->logo)
            <div class="d-flex py-1">
                <div class="col text-center">
                    <img src="data:image/jpeg;base64,{{ base64_encode($club->logo) }}" alt="{{ $club->name }} Logo"
                        style="max-width: 135px; height: 135px;">
                </div>
            </div>
        @endif
        <div class="row">
            <div class="col text-center">
                <h1 class="fw-bold text-uppercase">{{ $club->name }}</h1>
            </div>
        </div>
        <div class="container text-bg-dark pt-5 px-0 rounded-top-left-1" id="about">
            <div class="row px-5">
                <h1 class="fw-bold">ABOUT US</h1>
            </div>
            <br>
            <div class="row px-5">
                <div class="col-md-5">
                    <img src="{{ asset($club->about_us_img_url) }}" class="img-fluid about-us-img">
                </div>
                <div class="col-md-7 py-3">
                    @if (!empty($club->description))
                        {!! $club->description !!}
                    @else
                        <p>Club description not available.</p>
                    @endif
                </div>
            </div>
            <br>
            <div class="row justify-content-center pb-5">
                <div class="col-12 col-md-4 text-center">
                    <div class="row">
                        <h2>{{ $memberCount }}</h2>
                    </div>
                    <div class="row">
                        <p>Members</p>
                    </div>
                </div>
                <div class="col-12 col-md-4 text-center">
                    <div class="row">
                        <h2>{{ $eventsOrganized }}</h2>
                    </div>
                    <div class="row">
                        <p>Events organized</p>
                    </div>
                </div>
                <div class="col-12 col-md-4 text-center">
                    <div class="row">
                        <h2>{{ $establishedDate }}</h2>
                    </div>
                    <div class="row">
                        <p>Established</p>
                    </div>
                </div>
            </div>
            <div class="container-fluid text-bg-light pt-5 px-0 rounded-top-right-1" id="activities">
                <div class="row px-5">
                    <h1 class="fw-bold">WHAT WE DO</h1>
                </div>
                <br>
                <div class="row px-5">
                    @foreach ($club->club_activity as $activity)
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card h-100 border-0 text-bg-light">
                                <img src="{{ asset($activity->activity_img_url) }}" alt="{{ $activity->name }}"
                                    class="card-img-top img-fluid activity-img"
                                    style="height: 250px; object-fit: cover;">
                                <div class="card-body">
                                    <h4 class="card-title">{{ $activity->name }}</h4>
                                    <p class="card-text">{{ $activity->description }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="container-fluid text-bg-dark pt-5 px-0 rounded-top-left-1" id="gallery">
                    <div class="row px-5">
                        <h1 class="fw-bold">OUR HIGHLIGHTS</h1>
                    </div>
                    <br>
                    <div class="row pb-5">
                        @if ($club->club_gallery && $club->club_gallery->isNotEmpty())
                            @php
                                $validImages = $club->club_gallery->filter(function ($image) {
                                    return file_exists(public_path($image->gallery_img_url)); // Check if the file exists
                                });
                            @endphp

                            @if ($validImages->isNotEmpty())
                                <div id="highlightsCarousel" class="carousel slide" data-bs-ride="carousel">
                                    <div class="carousel-indicators">
                                        @foreach ($validImages as $key => $image)
                                            <button type="button" data-bs-target="#highlightsCarousel"
                                                data-bs-slide-to="{{ $key }}"
                                                class="{{ $key === 0 ? 'active' : '' }}"
                                                aria-current="{{ $key === 0 ? 'true' : 'false' }}"
                                                aria-label="Slide {{ $key + 1 }}">
                                            </button>
                                        @endforeach
                                    </div>
                                    <div class="carousel-inner">
                                        @foreach ($validImages as $key => $image)
                                            <div class="carousel-item {{ $key === 0 ? 'active' : '' }}">
                                                <img src="{{ asset($image->gallery_img_url) }}" class="d-block w-100"
                                                    alt="Highlight Image">
                                            </div>
                                        @endforeach
                                    </div>
                                    <a class="carousel-control-prev" href="#highlightsCarousel" role="button"
                                        data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Previous</span>
                                    </a>
                                    <a class="carousel-control-next" href="#highlightsCarousel" role="button"
                                        data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Next</span>
                                    </a>
                                </div>
                            @else
                                <p>No valid gallery images available for this club.</p>
                            @endif
                        @else
                            <div class="row px-5 pb-5">
                                <p>No gallery images available for this club.</p>
                            </div>
                        @endif
                    </div>

                    <div class="container-fluid text-bg-light pt-5 px-0 rounded-top-right-1" id="membership">
                        <div class="row px-5">
                            <h1 class="fw-bold">JOIN US</h1>
                        </div>
                        <br>
                        <div class="row px-5 pb-5">
                            @if (!empty($club->join_description))
                                {!! $club->join_description !!}
                            @else
                                <p>
                                    Login to <a href="{{ route('login') }}">iClub</a> to sign up.
                                </p>
                            @endif
                        </div>
                        <div class="container-fluid grey-bg pt-5 px-0 rounded-top-left-1" id="contact">
                            <div class="row px-5">
                                <h1 class="fw-bold">CONTACT US</h1>
                            </div>
                            <div class="row justify-content-center mt-4">
                                <!-- Email -->
                                @if (!empty($club->email))
                                    <div class="col-12 col-md-3 text-center mb-4">
                                        <a href="mailto:{{ $club->email }}" target="_blank">
                                            <i class="bi bi-envelope-fill text-secondary url-icon"></i>
                                        </a>
                                        <p class="mt-2 text-secondary small">{{ $club->email }}</p>
                                    </div>
                                @endif

                                <!-- LinkedIn -->
                                @if (!empty($club->linkedin_url))
                                    <div class="col-12 col-md-3 text-center mb-4">
                                        <a href="{{ $club->linkedin_url }}" target="_blank">
                                            <i class="bi bi-linkedin text-secondary url-icon"></i>
                                        </a>
                                        <p class="mt-2 text-secondary small">{{ $club->linkedin_url }}</p>
                                    </div>
                                @endif

                                <!-- Facebook -->
                                @if (!empty($club->facebook_url))
                                    <div class="col-12 col-md-3 text-center mb-4">
                                        <a href="{{ $club->facebook_url }}" target="_blank">
                                            <i class="bi bi-facebook text-secondary url-icon"></i>
                                        </a>
                                        <p class="mt-2 text-secondary small">{{ $club->facebook_url }}</p>
                                    </div>
                                @endif

                                <!-- Instagram -->
                                @if (!empty($club->instagram_url))
                                    <div class="col-12 col-md-3 text-center mb-4">
                                        <a href="{{ $club->instagram_url }}" target="_blank">
                                            <i class="bi bi-instagram text-secondary url-icon"></i>
                                        </a>
                                        <p class="mt-2 text-secondary small">{{ $club->instagram_url }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> <!-- jQuery Library -->
</body>

</html>
