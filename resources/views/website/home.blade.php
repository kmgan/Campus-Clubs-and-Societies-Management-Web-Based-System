<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sunway Clubs and Societies</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <style>
        /* Custom styles */
        .rounded-top-left-1 {
            border-top-left-radius: 10rem;
        }

        .rounded-top-right-1 {
            border-top-right-radius: 10rem;
        }

        .club-category-bg {
            background-color: #a7c7d9;
        }

        .dark-blue-bg {
            background-color: #003572;
        }

        p {
            margin-bottom: 0;
        }

        .fs-4 {
            font-family: "Inter" !important;
        }

        .nav-link {
            color: black !important;
            transition: color 0.2s ease-in-out;
            /* Smooth color transition */
        }

        .nav-link:hover {
            color: grey !important;
        }

        html {
            scroll-padding-top: 83px;
        }

        @media (max-width: 767.98px) {
            html {
                scroll-padding-top: 200px;
                /* Adjusted for smaller screens like mobile */
            }
        }

        a {
            color: inherit;
            /* Inherit the color from the parent element */
            text-decoration: none;
            /* Remove default underline */
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg sticky-top bg-white">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="/images/sunway-logo-full.png" height= "57px" width= "140px">
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
                        <a class="nav-link fs-5 px-3" href="#clubs">Clubs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fs-5 px-3" href="#events">Events</a>
                    </li>
                </ul>
                <a href="{{ route('login') }}" target="_blank" class="btn btn-dark btn-lg rounded-pill"><span
                        style="font-style: italic">iClub </span>Log In</a>
            </div>
        </div>
    </nav>
    <div class="text-white"
        style="background-image: url('/images/background.png'); background-size: cover; background-position: center;">
        <div class="bg-dark bg-opacity-50 d-flex flex-column justify-content-end align-items-start"
            style="height: 450px;">
            <h1 class="display-1 fw-bold mb-0 px-5 mx-md-5">SUNWAY CLUBS AND SOCIETIES</h1>
        </div>
    </div>

    <!-- About Us Section -->
    <div class="container-fluid p-0 text-bg-dark col-12 border-0" id="about">
        <div class="p-5 mx-md-5">
            <h1 class="fw-bold">About Us</h1>
            <p>Explore, Connect, and Thrive! With over 100 clubs and societies, Sunway offers a vibrant community where
                you can pursue your passions, develop new skills, and make lifelong friends. From tech and arts to
                sports and culture, there's something for everyone. Join us and be part of the Sunway spirit!</p>
        </div>

        <!-- Clubs and Societies Section -->
        <div class="container-fluid rounded-top-right-1 text-bg-light col-12 border-0 p-0" id="clubs">
            <div class="p-5 mx-md-5">
                <h1 class="fw-bold">Our Clubs and Societies</h1>
                <form id="searchForm" method="GET" action="{{ route('home') }}">
                    <div class="row">
                        <div class="col-5">
                            <p>Search by keyword</p>
                        </div>
                        <div class="col-5">
                            <p>Search by category</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-5">
                            <div class="input-group">
                                <input type="text" id="keyword" name="keyword" class="form-control form-control-sm"
                                    placeholder="Keywords" value="{{ request('keyword') }}">
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="input-group">
                                <select id="category" name="category_id" class="form-select form-select-sm">
                                    <option value="All categories"
                                        {{ request('category_id') == 'All categories' ? 'selected' : '' }}>
                                        All categories
                                    </option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-2">
                            <button type="submit"
                                class="btn btn-sm btn-outline-warning rounded-pill fw-bold">Search</button>
                        </div>
                    </div>
                </form>
                <br>
                <!-- Render Clubs -->
                <div id="clubsList">
                    @if ($clubs->isEmpty())
                        <p>No clubs found.</p>
                    @else
                        @foreach ($clubs as $categoryId => $categoryClubs)
                            <div class="row mb-3">
                                <p>
                                    <span class="border px-4 py-1 fw-bold club-category-bg rounded-3 nowrap">
                                        {{ $categories->firstWhere('id', $categoryId)->name }}
                                    </span>
                                </p>
                            </div>
                            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 row-cols-xxl-5 g-4">
                                @foreach ($categoryClubs as $club)
                                    <div class="col">
                                        <div class="card h-100 text-center bg-transparent px-0">
                                            <a href="{{ route('club.details', ['id' => $club->id]) }}" target="_blank">
                                                <img src="data:image/jpeg;base64,{{ base64_encode($club->logo) }}"
                                                    alt="{{ $club->name }}" class="card-img-top mx-auto"
                                                    style="max-width: 150px; height: 150px; object-fit: contain;">
                                            </a>
                                            <div class="card-body">
                                                <h6 class="card-title">
                                                    <a href="{{ route('club.details', ['id' => $club->id]) }}"
                                                        target="_blank">
                                                        {{ $club->name }} 
                                                    </a>
                                                </h6>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @if (!$loop->last)
                                <hr class="my-4">
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Events Section -->
            <div class="container-fluid rounded-top-left-1 dark-blue-bg text-white col-12 border-0 p-0" id="events">
                <div class="p-5 p-md-5 mx-md-5">
                    <h1 class="fw-bold">Upcoming Events</h1>

                    <!-- Events Happening This Month -->
                    <h3 class="text-white-50">Happening This Month</h3>
                    @if ($thisMonthEvents->isEmpty())
                        <p>No events happening this month.</p>
                    @else
                        <div class="row">
                            @foreach ($thisMonthEvents as $event)
                                <div class="col-md-6 col-lg-4 col-xxl-3 mb-4">
                                    <div class="card border-0 h-100 bg-transparent text-white">
                                        <a href="{{ route('event.details', ['id' => $event->id]) }}" target="_blank">
                                            <img src="data:image/jpeg;base64,{{ base64_encode($event->poster) }}"
                                                alt="{{ $event->name }}" class="card-img-top"
                                                style="height: 350px; object-fit: cover;">
                                        </a>
                                        <div class="card-body">
                                            <p class="card-text text-white-50 fw-bold">{{ $event->club->name }}</p>
                                            <a href="{{ route('event.details', ['id' => $event->id]) }}"
                                                target="_blank">
                                                <h5 class="card-title">{{ $event->name }} ></h5>
                                            </a>
                                            <p class="card-text">
                                                {{ \Carbon\Carbon::parse($event->date)->format('D | d M Y') }}</p>
                                            <p class="card-text">
                                                <i class="bi bi-geo-alt me-2"></i>{{ $event->location }}
                                            </p>
                                            <p class="card-text">
                                                <i
                                                    class="bi bi-clock me-2"></i>{{ \Carbon\Carbon::parse($event->start_time)->format('h:iA') }}
                                                - {{ \Carbon\Carbon::parse($event->end_time)->format('h:iA') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Future Events -->
                    <h3 class="text-white-50 mt-1">Coming Soon</h3>
                    @if ($futureEvents->isEmpty())
                        <p>No upcoming events.</p>
                    @else
                        <div class="row">
                            @foreach ($futureEvents as $event)
                                <div class="col-md-6 col-lg-4 col-xxl-3 mb-4">
                                    <div class="card h-100 border-0 bg-transparent text-white">
                                        <a href="{{ route('event.details', ['id' => $event->id]) }}" target="_blank">
                                            <img src="data:image/jpeg;base64,{{ base64_encode($event->poster) }}"
                                                alt="{{ $event->name }}" class="card-img-top"
                                                style="height: 350px; object-fit: cover;">
                                        </a>
                                        <div class="card-body">
                                            <p class="card-text text-white-50 fw-bold">{{ $event->club->name }}</p>
                                            <a href="{{ route('event.details', ['id' => $event->id]) }}"
                                                target="_blank">
                                                <h5 class="card-title">{{ $event->name }} ></h5>
                                            </a>
                                            <p class="card-text">
                                                {{ \Carbon\Carbon::parse($event->date)->format('D | d M Y') }}</p>
                                            <p class="card-text">
                                                <i class="bi bi-geo-alt me-2"></i>{{ $event->location }}
                                            </p>
                                            <p class="card-text">
                                                <i
                                                    class="bi bi-clock me-2"></i>{{ \Carbon\Carbon::parse($event->start_time)->format('h:iA') }}
                                                - {{ \Carbon\Carbon::parse($event->end_time)->format('h:iA') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> <!-- jQuery Library -->
    <script>
        $(document).ready(function() {
            $('.nav-link').click(function() {
                if ($('.navbar-toggler').is(':visible')) {
                    $('#navbarTogglerDemo02').collapse('hide');
                }
            });

            // Intercept the form submission
            $('#searchForm').on('submit', function(event) {
                event.preventDefault(); // Prevent the default form submission

                // Get the form action URL and the form data
                const url = $(this).attr('action');
                const formData = $(this).serialize(); // Serialize the form data

                // Perform AJAX request
                $.ajax({
                    url: url,
                    method: 'GET',
                    data: formData,
                    beforeSend: function() {
                        // You can add a loading spinner or message here
                    },
                    success: function(response) {
                        // Update the clubs section with the new content
                        $('#clubsList').html($(response).find('#clubsList').html());
                    },
                    error: function() {
                        alert('Something went wrong. Please try again.');
                    }
                });
            });

            // Trigger form submission on input change
            $('#keyword, #category').on('change input', function() {
                $('#searchForm').submit();
            });
        });
    </script>
</body>

</html>
