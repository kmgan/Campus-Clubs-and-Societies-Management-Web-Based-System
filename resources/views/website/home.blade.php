<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sunway Clubs and Societies</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
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
    </style>
</head>

<body>

    <!-- Hero Section -->
    <div class="text-white" style="background-image: url('/images/background.png'); background-size: cover; background-position: center;">
        <div class="bg-dark bg-opacity-50 d-flex flex-column justify-content-end align-items-start" style="height: 450px;">
            <h1 class="display-1 fw-bold mb-0 px-5 mx-md-5">SUNWAY CLUBS AND SOCIETIES</h1>
        </div>
    </div>

    <!-- About Us Section -->
    <div class="container-fluid p-0 text-bg-dark col-12 border-0">
        <div class="p-5 mx-md-5">
            <h1 class="fw-bold">About Us</h1>
            <p>Explore, Connect, and Thrive! With over 100 clubs and societies, Sunway offers a vibrant community where
                you can pursue your passions, develop new skills, and make lifelong friends. From tech and arts to
                sports and culture, there's something for everyone. Join us and be part of the Sunway spirit!</p>
        </div>

        <!-- Clubs and Societies Section -->
        <div class="container-fluid rounded-top-right-1 text-bg-light col-12 border-0 p-0">
            <div class="p-5 mx-md-5">
                <h1 class="fw-bold">Our Clubs and Societies</h1>

                <!-- Search Form -->
                <form method="GET" action="{{ route('home') }}">
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
                                <input type="text" name="keyword" class="form-control form-control-sm"
                                    placeholder="Keywords" value="{{ request('keyword') }}">
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="input-group">
                                <select name="category_name" class="form-select form-select-sm">
                                    <option value="All categories"
                                        {{ request('category_name') == 'All categories' ? 'selected' : '' }}>All categories</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->name }}"
                                            {{ request('category_name') == $category->name ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-2">
                            <button type="submit" class="btn btn-sm btn-outline-warning rounded-pill fw-bold">Search</button>
                        </div>
                    </div>
                </form>

                <!-- Render Clubs -->
                <br>
                @if ($clubs->isEmpty())
                    <p>No clubs found.</p>
                @else
                    @foreach ($clubs as $category => $categoryClubs)
                        <div class="row">
                            <p><span class="border px-4 py-1 fw-bold club-category-bg rounded-3 nowrap">{{ $category }}</span></p>
                        </div>

                        <div class="row">
                            @foreach ($categoryClubs as $club)
                                <div class="col-md-4 my-4">
                                    <div class="border p-3 text-center">
                                        <img src="data:image/jpeg;base64,{{ base64_encode($club->logo) }}" alt="{{ $club->name }}" class="img-fluid"
                                            style="max-width: 150px; height: 150px; object-fit: contain;">
                                        <div class="fw-bold mt-2">{{ $club->name }} ></div>
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

            <!-- Events Section -->
            <div class="container-fluid rounded-top-left-1 dark-blue-bg text-white col-12 border-0 p-0">
                <div class="p-5 p-md-5 mx-md-5">
                    <h1 class="fw-bold">Upcoming Events</h1>

                    <!-- Events Happening This Month -->
                    <h3 class="text-white-50">Happening This Month</h3>
                    @if ($thisMonthEvents->isEmpty())
                        <p>No events happening this month.</p>
                    @else
                        <div class="row">
                            @foreach ($thisMonthEvents as $event)
                                <div class="col-md-4 mb-4">
                                    <div class="overflow-hidden">
                                        <div class="position-relative">
                                            <img src="data:image/jpeg;base64,{{ base64_encode($event->poster) }}" alt="{{ $event->name }}" class="img-fluid">
                                            <span class="position-absolute top-0 start-0 m-3 badge bg-warning text-dark">{{ \Carbon\Carbon::parse($event->date)->format('D | d M Y') }}</span>
                                        </div>
                                        <p>{{ $event->club->name }}</p>
                                        <p class="fw-bold">{{ $event->name }} ></p>
                                        <p><small>{{ $event->description }}</small></p>
                                        <p class="fw-bold"><i class="bi bi-geo-alt me-2"></i>{{ $event->location }}</p>
                                        <p class="fw-bold"><i class="bi bi-clock me-2"></i>{{ \Carbon\Carbon::parse($event->start_time)->format('h:iA') }} - {{ \Carbon\Carbon::parse($event->end_time)->format('h:iA') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Future Events -->
                    <h3 class="text-white-50 mt-5">Coming Soon</h3>
                    @if ($futureEvents->isEmpty())
                        <p>No upcoming events.</p>
                    @else
                        <div class="row">
                            @foreach ($futureEvents as $event)
                                <div class="col-md-4 mb-4">
                                    <div class="overflow-hidden">
                                        <div class="position-relative">
                                            <img src="data:image/jpeg;base64,{{ base64_encode($event->poster) }}" alt="{{ $event->name }}" class="img-fluid">
                                            <span class="position-absolute top-0 start-0 m-3 badge bg-warning text-dark">{{ \Carbon\Carbon::parse($event->date)->format('D | d M Y') }}</span>
                                        </div>
                                        <p>{{ $event->club->name }}</p>
                                        <p class="fw-bold">{{ $event->name }} ></p>
                                        <p><small>{{ $event->description }}</small></p>
                                        <p class="fw-bold"><i class="bi bi-geo-alt me-2"></i>{{ $event->location }}</p>
                                        <p class="fw-bold"><i class="bi bi-clock me-2"></i>{{ \Carbon\Carbon::parse($event->start_time)->format('h:iA') }} - {{ \Carbon\Carbon::parse($event->end_time)->format('h:iA') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
