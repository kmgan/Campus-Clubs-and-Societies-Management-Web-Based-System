<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title></title>

    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
    <script src="/js/tinymce.min.js"></script>

    <!-- Custom CSS -->
    <style>
        .sidebar {
            background-color: #003572;
            justify-content: start;
        }

        /* Sidebar Styles */
        .navbar-fixed-left {
            width: 13rem;
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
        }

        .navbar-expand-md .navbar-nav .nav-link i {
            padding-right: 0.5rem;
        }

        .navbar-toggler {
            border: none;
        }

        .nav-link {
            color: #eeeeee;
            font-size: 1.25rem;
        }

        .nav-link i {
            color: white;
            padding-right: 1rem;
        }

        .nav-link:hover {
            color: white;
            text-decoration: underline;
        }

        .navbar-nav .nav-item .nav-link.active {
            position: relative;
            color: #ffffff;
            /* Active link text color */
        }

        .navbar-nav .nav-item .nav-link.active::before {
            content: '';
            position: absolute;
            left: -10px;
            /* Adjust this value based on your design */
            top: 0;
            bottom: 0;
            width: 5px;
            /* Width of the white rectangle */
            background-color: #ffffff;
            /* Color of the rectangle */
            border-radius: 0 4px 4px 0;
            /* Rounded corners to make it look good */
        }

        /* Styling for the navbar brand to make it stand out */
        .custom-brand {
            font-size: 2.5rem;
            /* Larger font size for emphasis */
            font-weight: bold;
            /* Make the brand bold */
            color: #ffffff;
            /* White color for contrast against the dark background */
            padding: 1rem 0;
            /* Padding to create space around the brand */
            text-transform: uppercase;
            /* Make the text uppercase to stand out */
            text-align: center;
            /* Center-align the brand text for better visual balance */
            font-family: Arial, Helvetica, sans-serif;
        }

        .custom-brand:hover {
            color: white;
        }

        /* Main Content Styles */
        .content {
            margin-left: 13rem;
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

            .custom-brand {
                padding: 0;
            }

            .modal {
                height: 100%;
                width: 100%;
            }
        }

        .navbar-toggler:focus {
            outline: 2px solid white;
            box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.2);
        }

        .nav-link {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .navbar-brand:focus{
            color: white;
        }
    </style>
</head>

<body>
    <!-- Fixed Sidebar Navbar for Medium and Larger Screens -->
    <nav class="navbar sidebar navbar-fixed-left ps-3 d-none d-md-flex flex-column">
        <!-- Navbar Brand -->
        <a class="navbar-brand custom-brand" href="{{ route('home') }}" target="_blank">iClub</a>
        <ul class="navbar-nav flex-column w-100">
            <!-- Navigation Items -->
            <li class="nav-item">
                <a class="nav-link"
                    href="#" style="pointer-events: none;">
                    {{ Auth::user()->name ?? 'Profile' }}
                </a>
            </li>
            <li class="nav-item">
                @hasanyrole('admin|user')
                    <a class="nav-link {{ request()->routeIs('iclub.club.page') ? 'active' : '' }}"
                        href="{{ route('iclub.club.page') }}"><i class="bi bi-people-fill"></i>Clubs</a>
                @endrole
            </li>
            <li class="nav-item">
                @hasrole('club_manager')
                    <a class="nav-link {{ request()->routeIs('iclub.webContent.page') ? 'active' : '' }}"
                        href="{{ route('iclub.webContent.page') }}"><i class="bi bi-pencil-square"></i>Web Content</a>
                @endrole
            </li>
            <li class="nav-item">
                @hasrole('club_manager')
                    <a class="nav-link {{ request()->routeIs('iclub.clubMember.page') ? 'active' : '' }}"
                        href="{{ route('iclub.clubMember.page') }}"><i class="bi bi-people-fill"></i>Members</a>
                @endrole
            </li>
            <li class="nav-item">
                @hasrole('admin')
                    <a class="nav-link {{ request()->routeIs('iclub.user.page') ? 'active' : '' }}"
                        href="{{ route('iclub.user.page') }}"><i class="bi bi-person-circle"></i></i>Users</a>
                @endrole
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('iclub.event.page') ? 'active' : '' }}"
                    href="{{ route('iclub.event.page') }}"><i class="bi bi-calendar-event"></i>Events</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-right"></i>Sign Out
                </a>
            </li>
        </ul>
    </nav>

    <!-- Collapsible Top Navbar for Small Screens -->
    <nav class="navbar sidebar navbar-expand-md d-md-none">
        <div class="container-fluid">
            <div class="d-flex justify-content-start w-100">
                <!-- Navbar Toggle Button on the left -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mobileNavbar"
                    aria-controls="mobileNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <!-- Centered Navbar Brand -->
                <a class="navbar-brand custom-brand ms-1" href="{{ route('home') }}" target="_blank">iClub</a>
            </div>
            <div class="collapse navbar-collapse" id="mobileNavbar">
                <ul class="navbar-nav">
                    <!-- Navigation Items (same as sidebar) -->
                    <li class="nav-item">
                        <a class="nav-link"
                            href="#" style="pointer-events: none;">
                            {{ Auth::user()->name ?? 'Profile' }}
                        </a>
                    </li>
                    <li class="nav-item">
                        @hasanyrole('admin|user')
                            <a class="nav-link {{ request()->routeIs('iclub.club.page') ? 'active' : '' }}"
                                href="{{ route('iclub.club.page') }}"><i class="bi bi-people-fill"></i>Clubs</a>
                        @endrole
                    </li>
                    <li class="nav-item">
                        @hasrole('club_manager')
                            <a class="nav-link {{ request()->routeIs('iclub.webContent.page') ? 'active' : '' }}"
                                href="{{ route('iclub.webContent.page') }}"><i class="bi bi-pencil-square"></i>Web
                                Content</a>
                        @endrole
                    </li>
                    <li class="nav-item">
                        @hasrole('club_manager')
                            <a class="nav-link {{ request()->routeIs('iclub.clubMember.page') ? 'active' : '' }}"
                                href="{{ route('iclub.clubMember.page') }}"><i class="bi bi-people-fill"></i>Members</a>
                        @endrole
                    </li>
                    <li class="nav-item">
                        @hasrole('admin')
                            <a class="nav-link {{ request()->routeIs('iclub.user.page') ? 'active' : '' }}"
                                href="{{ route('iclub.user.page') }}"><i class="bi bi-person-circle"></i></i>Users</a>
                        @endrole
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('iclub.event.page') ? 'active' : '' }}"
                            href="{{ route('iclub.event.page') }}"><i class="bi bi-calendar-event"></i>Events</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="bi bi-box-arrow-right"></i>Sign Out
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>


    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <!-- Main Content Area -->
    <div class="content">
        @yield('content')
    </div>

</body>

</html>
