<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $event->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        .img-fluid {
            border-radius: 25px !important;
        }

        p {
            margin-bottom: 0;
        }

        strong {
            display: inline;
        }

        .background-video {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
        }

        .overlay {
            position: relative;
            z-index: 1;
        }

        .card.custom-opacity {
            opacity: 0.3;
        }

        a {
            color: inherit;
            /* Inherit the color from the parent element */
            text-decoration: none;
            /* Remove default underline */
        }
    </style>
</head>

<body
    class="{{ strtolower($event->theme) == 'dark' ? 'text-light' : (strtolower($event->theme) == 'minimal' ? '' : 'text-dark') }}"
    style="background-color: {{ strtolower($event->theme) == 'minimal' ? $event->background_color : '' }}; color: {{ strtolower($event->theme) == 'minimal' ? $event->text_color : '' }};">

    <!-- Background Video, shown only for Light and Dark themes -->
    @if (strtolower($event->theme) == 'light' || strtolower($event->theme) == 'dark')
        <video autoplay loop muted class="background-video">
            <source src="/videos/{{ strtolower($event->theme) }}.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    @endif

    <!-- Back Link -->
    <div class="container-fluid float-start">
        <a href="{{ route('home') }}"
            class="{{ strtolower($event->theme) == 'dark' ? 'text-white' : 'text-dark' }} fs-3"
            style="text-decoration: none; font-weight: bold;">
            &larr; Back
        </a>
    </div>

    <!-- Main Content Container -->
    <div id="eventContainer" class="container p-5 overlay">
        <div class="row justify-content-center">
            <!-- Left Column -->
            <div class="col-12 col-md-4 mx-3 order-1 order-md-1 mb-4 mb-md-0">
                <div class="row">
                    <img src="data:image/jpeg;base64,{{ base64_encode($event->poster) }}" alt="{{ $event->name }}"
                        class="img-fluid shadow-lg px-0" loading="lazy">
                </div>
                <br>
                <div class="row">
                    <h5>Hosted by</h5>
                </div>
                <hr>
                <div class="row">
                    <p class="fw-bold d-flex align-items-center">
                        <img src="data:image/jpeg;base64,{{ base64_encode($event->club->logo) }}"
                            alt="{{ $event->club->name }} Logo" style="max-width: 30px; height: auto;">
                        <span class="ms-2">{{ $event->club->name }}</span>

                        <a href="mailto:{{ $event->club->email }}" class="ms-auto" data-bs-toggle="tooltip"
                            title="{{ $event->club->email }}">
                            <i class="bi bi-envelope fs-4"></i>
                        </a>
                    </p>
                </div>
                <br>
                <div class="row">
                    <div class="row">
                        <p>{{ $event->participants_count }} Going</p>
                    </div>
                </div>
                <hr>
            </div>

            <!-- Right Column -->
            <div class="col-12 col-md-6 mx-3 order-2 order-md-2">
                <div class="row">
                    <h1 class="fw-bold">{{ $event->name }}</h1>
                </div>
                <div class="row mb-2">
                    <div class="col-1 me-3">
                        <i class="bi bi-calendar h1"></i>
                    </div>
                    <div class="col">
                        <div class="row">
                            <p class="fw-bold">{{ \Carbon\Carbon::parse($event->date)->format('l, F d Y') }}</p>
                        </div>
                        <div class="row">
                            <p>{{ \Carbon\Carbon::parse($event->start_time)->format('h:iA') }} -
                                {{ \Carbon\Carbon::parse($event->end_time)->format('h:iA') }}</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-1 me-3">
                        <i class="bi bi-geo-alt h1"></i>
                    </div>
                    <div class="col">
                        <p class="pt-2 fw-bold">{{ $event->location }}</p>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="card px-0 shadow-sm">
                        <div
                            class="card-header {{ strtolower($event->theme) == 'dark' ? 'bg-secondary text-light' : 'bg-light text-dark' }}">
                            Registration
                        </div>
                        <div
                            class="card-body {{ strtolower($event->theme) == 'dark' ? 'bg-dark text-light' : 'bg-light text-dark' }}">
                            <div class="row" id="eventStatus">
                                <h5></h5>
                            </div>
                            <div class="row mb-3">
                                <div class="col">
                                    <p id="countdownTimer"></p>
                                </div>
                            </div>
                            <hr>
                            <p>Welcome! Log in to iClub to register for the event.</p>
                            <br>
                            <div class="d-grid gap-2">
                                <a href="{{ route('login') }}" target="_blank"
                                    class="btn {{ strtolower($event->theme) == 'dark' ? 'btn-light text-dark' : 'btn-dark text-light' }}"><span
                                        style="font-style: italic">iClub </span>Log In</a>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row" id="eventStatus">
                    <h2></h2>
                </div>
                <br>
                <div class="row">
                    <h2>About Event</h2>
                </div>
                <hr>
                <div class="row">
                    <p>{!! $event->description !!}</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> <!-- jQuery Library -->
    <script>
        // JavaScript to change the container class based on screen size
        function adjustContainerClass() {
            const container = document.getElementById('eventContainer');
            if (window.innerWidth < 768) {
                container.classList.remove('container');
                container.classList.add('container-fluid');
            } else {
                container.classList.remove('container-fluid');
                container.classList.add('container');
            }
        }

        const eventDate = new Date(
            '{{ \Carbon\Carbon::parse($event->date)->format('Y-m-d') }}T{{ \Carbon\Carbon::parse($event->start_time)->format('H:i:s') }}'
        ).getTime();
        const eventEndDate = new Date(
            '{{ \Carbon\Carbon::parse($event->date)->format('Y-m-d') }}T{{ \Carbon\Carbon::parse($event->end_time)->format('H:i:s') }}'
        ).getTime();

        const timerInterval = setInterval(function() {
            const now = new Date().getTime();
            const distance = eventDate - now;

            if (now > eventEndDate) {
                clearInterval(timerInterval);
                document.getElementById('countdownTimer').innerHTML = 'Event has ended.';
                document.getElementById('eventStatus').innerHTML = '<h5>Past Event</h5>';
            } else if (now >= eventDate && now <= eventEndDate) {
                document.getElementById('countdownTimer').innerHTML = 'Event is happening now!';
                document.getElementById('eventStatus').innerHTML = '<h5>Current Event</h5>';
            } else {
                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                document.getElementById('countdownTimer').innerHTML =
                    `Event is starting in ${days}d ${hours}h ${minutes}m ${seconds}s`;
                document.getElementById('eventStatus').innerHTML = '<h5>Upcoming Event</h5>';
            }
        }, 1000);

        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();

            // Initial adjustment
            adjustContainerClass();

            // Adjust container on window resize
            window.addEventListener('resize', adjustContainerClass);
        });
    </script>
</body>

</html>
