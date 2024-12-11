@extends('webplatform.shared.layout')

@section('title', 'Club')

@section('content')
    <style>
        body {
            background-color: #f4f4f4;
        }

        p {
            margin-bottom: 0;
        }

        .image-container {
            position: relative;
            /* Ensure the container is positioned relative for the overlay */
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            /* Match the width of the image */
            height: 150px;
            /* Match the height of the image */
            overflow: hidden;
            /* Prevent overlay from exceeding image bounds */
        }

        .image-container img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            transition: transform 0.3s ease-in-out;
        }

        .image-container .image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
            color: white;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            font-weight: bold;
            text-decoration: none;
        }

        .image-container:hover .image-overlay {
            opacity: 1;
        }

        .club-logo {
            max-height: 100%;
            /* Ensure the logo fits within the container */
            max-width: 100%;
            /* Ensure the logo fits within the container */
            object-fit: contain;
            /* Prevent distortion */
        }

        .details-card {
            overflow: hidden;
        }

        .card-title {
            height: 48px;
            margin-bottom: 0.5rem;
        }
    </style>

    <h1 class="fw-bold">Clubs</h1>
    <hr>
    <div class="container-fluid">
        <form action="{{ route('iclub.club.page') }}" method="GET">
            <div class="row g-3">
                <div class="col-md-4 col-12">
                    <div class="input-group">
                        <input type="text" name="keyword" class="form-control" placeholder="Search for clubs"
                            value="{{ request('keyword') }}">
                        <button type="submit" class="btn btn-outline-secondary"><i class="bi bi-search"></i></button>
                    </div>
                </div>
                @role('user')
                    <div class="col-md-3 col-12">
                        <select name="join_status" class="form-select" onchange="this.form.submit()">
                            <option value="all" {{ request('join_status') == 'all' ? 'selected' : '' }}>All Clubs</option>
                            <option value="joined" {{ request('join_status') == 'joined' ? 'selected' : '' }}>
                                Clubs I've Joined
                            </option>
                            <option value="available" {{ request('join_status') == 'available' ? 'selected' : '' }}>
                                Available Clubs
                            </option>
                        </select>
                    </div>
                @endrole
                <div class="col-md-3 col-12">
                    <select name="category_id" class="form-select" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @role('admin')
                    <div class="col-md-2 col-12">
                        <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#createClubModal">
                            Create Club <i class="bi bi-plus-circle text-white"></i>
                        </button>
                    </div>
                @endrole
            </div>
        </form>

        {{-- Clubs Section --}}
        @php
            $joinStatus = request('join_status', 'all');
        @endphp

        {{-- Joined Clubs Section --}}
        @role('user')
            @if ($joinStatus === 'all' || $joinStatus === 'joined')
                <div id="joinedClubsSection" class="mt-3">
                    <h2 class="fw-bold">Joined Clubs</h2>
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 row-cols-xxl-5 mt-3">
                        @php
                            $joinedClubs = $clubs->filter(function ($club) {
                                return $club->members->contains('user_id', auth()->id());
                            });
                        @endphp

                        @if ($joinedClubs->isEmpty())
                            <p>No joined clubs found.</p>
                        @else
                            @foreach ($joinedClubs as $club)
                                <div class="col mb-4">
                                    <div class="card details-card shadow" style="min-height: 375px">
                                        <div class="image-container">
                                            <a target="_blank" href="{{ route('club.details', ['id' => $club->id]) }}">
                                                <img src="data:image/jpeg;base64,{{ base64_encode($club->logo) }}"
                                                    class="card-img-top img-fluid" alt="{{ $club->name }}"
                                                    style="max-width: 150px; height: 150px; object-fit: contain;">
                                                <div class="image-overlay">View Details</div>
                                            </a>
                                        </div>
                                        <div class="card-body border-top">
                                            <h5 class="card-title">{{ $club->name }}</h5>
                                            <p class="card-text text-muted">{{ $club->club_category->name }}</p>
                                            <p class="card-text text-muted">Total Members: {{ $club->members_count ?? '0' }}
                                            </p>

                                            @if ($club->memberApprovalRequired)
                                                <span class="badge bg-warning text-dark" data-bs-toggle="tooltip"
                                                    title="Your membership request will need to be approved by the club manager.">
                                                    Approval Required
                                                </span>
                                            @else
                                                <span class="badge bg-success" data-bs-toggle="tooltip"
                                                    title="Your membership request will be approved automatically.">
                                                    No Approval Needed
                                                </span>
                                            @endif
                                        </div>
                                        @role('user')
                                            <div class="card-footer">
                                                <div class="d-grid gap-2 mt-1">
                                                    @php
                                                        $isApproved = $club->hasUserJoined(auth()->user());
                                                    @endphp

                                                    @if ($isApproved == 0)
                                                        <!-- User is registered but not approved (Pending) -->
                                                        <button class="btn btn-secondary btn-sm disabled" type="button"
                                                            data-club-id="{{ $club->id }}">Pending</button>
                                                    @elseif ($isApproved == 1)
                                                        <!-- User is registered and approved -->
                                                        <button class="btn btn-success btn-sm disabled" type="button"
                                                            data-club-id="{{ $club->id }}">Joined</button>
                                                    @else
                                                        <!-- User is not registered -->
                                                        <button class="btn btn-primary btn-sm register-button" type="button"
                                                            data-club-id="{{ $club->id }}">Register</button>
                                                    @endif
                                                </div>
                                            </div>
                                        @endrole
                                        @role('admin')
                                            <div class="card-footer">
                                                <div class="d-grid gap-2 mt-1">
                                                    <button class="btn btn-danger btn-sm delete-club-button" type="button"
                                                        data-club-id="{{ $club->id }}">
                                                        Delete Club
                                                    </button>
                                                </div>
                                            </div>
                                        @endrole
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            @endif
        @endrole

        {{-- Available Clubs Section --}}
        @if ($joinStatus === 'all' || $joinStatus === 'available')
            <div id="availableClubsSection" class="mt-3">
                <h2 class="fw-bold">Available Clubs</h2>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 row-cols-xxl-5 mt-3">
                    @php
                        $availableClubs = $clubs->filter(function ($club) {
                            return !$club->members->contains('user_id', auth()->id());
                        });
                    @endphp

                    @if ($availableClubs->isEmpty())
                        <p>No available clubs found.</p>
                    @else
                        @foreach ($availableClubs as $club)
                            <div class="col mb-4">
                                <div class="card details-card shadow" style="min-height: 375px">
                                    <div class="image-container">
                                        <a target="_blank" href="{{ route('club.details', ['id' => $club->id]) }}">
                                            <img src="data:image/jpeg;base64,{{ base64_encode($club->logo) }}"
                                                class="card-img-top img-fluid" alt="{{ $club->name }}"
                                                style="max-width: 150px; height: 150px; object-fit: contain;">
                                            <div class="image-overlay">View Details</div>
                                        </a>
                                    </div>
                                    <div class="card-body border-top">
                                        <h5 class="card-title">{{ $club->name }}</h5>
                                        <p class="card-text text-muted">{{ $club->club_category->name }}</p>
                                        <p class="card-text text-muted">Total Members: {{ $club->members_count ?? '0' }}
                                        </p>

                                        @if ($club->memberApprovalRequired)
                                            <span class="badge bg-warning text-dark" data-bs-toggle="tooltip"
                                                title="Your membership request will need to be approved by the club manager.">
                                                Approval Required
                                            </span>
                                        @else
                                            <span class="badge bg-success" data-bs-toggle="tooltip"
                                                title="Your membership request will be approved automatically.">
                                                No Approval Needed
                                            </span>
                                        @endif
                                    </div>
                                    @role('user')
                                        <div class="card-footer">
                                            <div class="d-grid gap-2 mt-1">
                                                @if ($club->hasUserJoined(auth()->user()))
                                                    <!-- If the user is joined and approved -->
                                                    <button class="btn btn-success btn-sm disabled" type="button"
                                                        data-club-id="{{ $club->id }}">
                                                        Joined
                                                    </button>
                                                @elseif ($club->hasUserJoined(auth()->user()) === false)
                                                    <!-- If the user is registered but not approved (pending) -->
                                                    <button class="btn btn-secondary btn-sm disabled" type="button"
                                                        data-club-id="{{ $club->id }}">
                                                        Pending Approval
                                                    </button>
                                                @else
                                                    <!-- Otherwise, show the "Register" button -->
                                                    <button class="btn btn-primary btn-sm register-button" type="button"
                                                        data-club-id="{{ $club->id }}">
                                                        Register
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @endrole

                                    @role('admin')
                                        <div class="card-footer">
                                            <div class="d-grid gap-2 mt-1">
                                                <button class="btn btn-danger btn-sm delete-club-button" type="button"
                                                    data-club-id="{{ $club->id }}">
                                                    Delete Club
                                                </button>
                                            </div>
                                        </div>
                                    @endrole
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Create Club Modal -->
    <div class="modal fade" id="createClubModal" tabindex="-1" aria-labelledby="createClubModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createClubModalLabel">Create Club</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="createClubForm" class="needs-validation" novalidate>
                        @csrf
                        <div class="mb-3">
                            <label for="clubName" class="form-label">Club Name</label>
                            <input type="text" class="form-control" id="clubName" name="name" required>
                            <div class="invalid-feedback">Please enter a valid club name.</div>
                        </div>
                        <div class="mb-3">
                            <label for="clubCategory" class="form-label">Category</label>
                            <select class="form-select" id="clubCategory" name="category_id" required>
                                <option value="" selected disabled>Select a category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Please select a valid category.</div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Create Club</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const registerButtons = document.querySelectorAll('.register-button');
            const deleteButtons = document.querySelectorAll('.delete-club-button');

            // Register button logic
            registerButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const clubId = this.getAttribute('data-club-id');
                    fetch("{{ route('iclub.club.register') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({
                                club_id: clubId
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'You have successfully applied to join the club!',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => location.reload());
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Joining the club failed: ' + data.message,
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'An error occurred while joining the club.',
                            });
                        });
                });
            });

            // Create Club logic
            document.getElementById('createClubForm').addEventListener('submit', function(event) {
                event.preventDefault();

                // Check form validity
                if (this.checkValidity()) {
                    createClub(this); // Pass the form element to the function
                } else {
                    this.classList.add('was-validated'); // Apply validation feedback

                    // Find the first invalid field
                    const firstInvalidField = this.querySelector(':invalid');
                    if (firstInvalidField) {
                        // Focus on the first invalid field
                        firstInvalidField.focus();

                        // Smoothly scroll to the first invalid field
                        firstInvalidField.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                }
            });


            // Delete Club logic
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const clubId = this.getAttribute('data-club-id');
                    deleteClub(clubId);
                });
            });

            // Reset validation feedback and form fields when modals are hidden
            $('#addClubModal').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
                $(this).find('form').removeClass('was-validated');
            });

            // Function to create a club
            function createClub(form) {
                const formData = new FormData(form);

                fetch("{{ route('iclub.club.create') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'An error occurred while creating the club.'
                        });
                    });
            }

            // Function to delete a club
            function deleteClub(clubId) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This action will permanently delete the club. Ensure no manager account is associated with this club.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/iclub/club/${clubId}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted!',
                                        text: data.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => location.reload());
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: data.message
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'An error occurred while deleting the club.'
                                });
                            });
                    }
                });
            }
        });
    </script>

@endsection
