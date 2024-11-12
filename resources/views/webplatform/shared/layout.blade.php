<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        .navbar {
            background-color: #003572;
        }

        /* Sidebar Styles */
        .navbar-fixed-left {
            width: 12.5rem;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml;charset=UTF8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3E%3Cpath stroke='white' stroke-width='3' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
        }

        .navbar-expand-md .navbar-nav .nav-link {
            color: #eeeeee;
            font-weight: 600;
            font-size: 1.4rem;
            padding-left: 1rem;
            /* Adjust padding to align with toggler icon */
        }

        .navbar-expand-md .navbar-nav .nav-link i {
            padding-right: 0.5rem;
        }

        .navbar-toggler {
            border: none;
        }

        .nav-link {
            color: #eeeeee;
            font-weight: 600;
            font-size: 1.4rem;
        }

        .nav-link i {
            color: white;
            padding-right: 1rem;
        }

        .nav-link:hover {
            color: white;
            text-decoration: underline;
        }

        /* Main Content Styles */
        .content {
            margin-left: 12.5rem;
            /* Offset for sidebar */
            padding: 20px;
        }

        /* Responsive Adjustments */
        @media (max-width: 767.98px) {
            .content {
                margin-left: 0;
            }

            .container {
                padding-left: 5px;
            }

            .collapse.navbar-collapse {
                transition: 0.12s ease-in-out;
            }

            .collapse.navbar-collapse.show {
                height: 100vh;
                top: 0;
                bottom: 0;
                left: 0;
                right: 0;
            }
        }

        .navbar-toggler:focus {
            outline: 2px solid white;
            box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.2);
        }
    </style>
</head>

<body>
    <!-- Fixed Sidebar Navbar for Medium and Larger Screens -->
    <nav class="navbar navbar-fixed-left ps-3 align-items-start d-none d-md-flex">
        <ul class="navbar-nav">
            <!-- Navigation Items -->
            <li class="nav-item">
                <a class="nav-link" href="#"><i class="bi bi-columns-gap"></i>Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#"><i class="bi bi-people-fill"></i>Clubs</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#"><i class="bi bi-calendar-event"></i>Events</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#"><i class="bi bi-chat-left-text"></i>Messages</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#"><i class="bi bi-question-circle"></i>Help</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#"><i class="bi bi-box-arrow-right"></i>Sign Out</a>
            </li>
        </ul>
    </nav>

    <!-- Collapsible Top Navbar for Small Screens -->
    <nav class="navbar navbar-expand-md d-md-none">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mobileNavbar"
                aria-controls="mobileNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mobileNavbar">
                <ul class="navbar-nav">
                    <!-- Navigation Items (same as sidebar) -->
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-columns-gap"></i>Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-people-fill"></i>Clubs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-calendar-event"></i>Events</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-chat-left-text"></i>Messages</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-question-circle"></i>Help</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-box-arrow-right"></i>Sign Out</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <div class="content">
        @yield('content')
    </div>

    <!-- Bootstrap JS Bundle with Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
