@extends('webplatform.shared.layout')

@section('title', 'Event')

@section('content')
    <style>
        body {
            background-color: #f4f4f4;
        }

        .form-control,
        .input-group-text,
        .form-select {
            border-color: black !important;
        }

        p {
            margin-bottom: 0;
        }

        .event-card {
            overflow: hidden;
        }

        .event-image-container {
            position: relative;
        }

        .event-image-container img {
            width: 100%;
            height: 250px;
            transition: transform 0.3s ease-in-out;
        }

        .event-image-container .image-overlay {
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

        .event-image-container:hover .image-overlay {
            opacity: 1;
        }

        .table-striped>tbody>tr:nth-child(odd)>td,
        .table-striped>tbody>tr:nth-child(odd)>th {
            background-color: white;
            box-shadow: none !important;
        }

        .table-striped>tbody>tr:nth-child(even)>td,
        .table-striped>tbody>tr:nth-child(even)>th {
            background-color: #ECF7FF;
        }

        table.dataTable.display th {
            background-color: #006DEB;
            font-size: 1.25rem !important;
            font-weight: 600;
        }

        .modal-content {
            background-color: #f4f4f4 !important;
        }
    </style>
    <h1 class="fw-bold">Events</h1>

    <hr>

    <div class="container-fluid">
        <form action="{{ route('iclub.event.page') }}" method="GET">
            <div class="row g-3">
                <div class="col-md-4 col-12">
                    <div class="input-group">
                        <input type="text" name="keyword" class="form-control" placeholder="Search your events"
                            value="{{ request('keyword') }}">
                        <button type="submit" class="btn btn-outline-secondary"><i class="bi bi-search"></i></button>
                    </div>
                </div>
                <div class="col-md-3 col-12">
                    <select name="event_status" id="eventStatusFilter" class="form-select">
                        <option value="all" {{ request('event_status') == 'all' ? 'selected' : '' }}>All Events</option>
                        <option selected value="upcoming" {{ request('event_status') == 'upcoming' ? 'selected' : '' }}>
                            Upcoming
                            Events</option>
                        <option value="completed" {{ request('event_status') == 'completed' ? 'selected' : '' }}>Completed
                            Events</option>
                    </select>
                </div>
                @role('user')
                    <div class="col-md-3 col-12">
                        <select name="participation_status" class="form-select" onchange="this.form.submit()">
                            <option value="all" {{ request('participation_status') == 'all' ? 'selected' : '' }}>All Events
                            </option>
                            <option selected value="participating"
                                {{ request('participation_status') == 'participating' ? 'selected' : '' }}>Events I'm
                                Participating In</option>
                            <option value="available" {{ request('participation_status') == 'available' ? 'selected' : '' }}>
                                Available Events</option>
                        </select>
                    </div>
                @endrole
                @role('club_manager')
                    <div class="col-md-2 col-12">
                        <button class="btn btn-primary" type="button" data-bs-toggle="modal"
                            data-bs-target="#createEventModal">
                            Create Event <i class="bi bi-plus-circle text-white"></i>
                        </button>
                    </div>
                @endrole
            </div>
        </form>

        {{-- Separate Events into Upcoming and Completed --}}
        @php
            $ongoingEvents = $events->filter(function ($event) {
                return \Carbon\Carbon::parse($event->date)->isToday() ||
                    \Carbon\Carbon::parse($event->date)->isFuture();
            });
            $completedEvents = $events->filter(function ($event) {
                return \Carbon\Carbon::parse($event->date)->isPast();
            });
        @endphp

        {{-- Upcoming Events Section --}}
        <div id="ongoingEventsSection" class="mt-3">
            <h2 class="fw-bold">Upcoming Events</h2>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-5 mt-3">
                @if ($ongoingEvents->isEmpty())
                    <p>No upcoming events found.</p>
                @else
                    @foreach ($ongoingEvents as $event)
                        <div class="col mb-4">
                            <div class="card event-card shadow">
                                <div class="event-image-container">
                                    <a target="_blank" href="{{ route('event.details', ['id' => $event->id]) }}">
                                        <img src="data:image/jpeg;base64,{{ base64_encode($event->poster) }}"
                                            class="card-img-top" alt="{{ $event->name }}">
                                        <div class="image-overlay">
                                            View Details
                                        </div>
                                    </a>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">{{ $event->name }}</h5>
                                    <p class="card-text">Organized by: {{ $event->club->name }}</p>
                                    <p class="card-text">
                                        <small
                                            class="text-muted">{{ \Carbon\Carbon::parse($event->date)->format('D, d M Y') }}
                                            at {{ \Carbon\Carbon::parse($event->start_time)->format('h:i A') }}</small>
                                    </p>
                                    <p class="card-text">
                                        <small class="text-muted">Location: {{ $event->location }}</small>
                                    </p>
                                </div>
                                @role('user')
                                    <div class="card-footer">
                                        <div class="d-grid gap-2 mt-1">
                                            @if ($event->isUserRegistered(auth()->user()))
                                                <button class="btn btn-danger btn-sm cancel-registration-button" type="button"
                                                    data-event-id="{{ $event->id }}">Cancel Registration</button>
                                            @else
                                                <button class="btn btn-primary btn-sm register-button" type="button"
                                                    data-event-id="{{ $event->id }}">Register</button>
                                            @endif
                                        </div>
                                    </div>
                                @endrole
                                @role('club_manager')
                                    <div class="card-footer">
                                        <div class="d-flex justify-content-between">
                                            <button class="btn btn-sm btn-primary flex-fill me-2 edit-event-button"
                                                type="button" data-bs-toggle="modal" data-bs-target="#editEventModal"
                                                data-event-id="{{ $event->id }}">Edit</button>
                                            <button class="btn btn-sm btn-primary flex-fill me-2 attendance-button"
                                                type="button" data-event-id="{{ $event->id }}">Attendance</button>
                                            <button class="btn btn-sm btn-danger flex-fill cancel-event-button" type="button"
                                                data-event-id="{{ $event->id }}">Cancel</button>
                                        </div>
                                    </div>
                                @endrole
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        {{-- Completed Events Section --}}
        <div id="completedEventsSection" class="mt-3">
            <h2 class="fw-bold">Completed Events</h2>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-5 mt-3">
                @if ($completedEvents->isEmpty())
                    <p>No completed events found.</p>
                @else
                    @foreach ($completedEvents as $event)
                        <div class="col mb-4">
                            <div class="card event-card shadow">
                                <div class="event-image-container">
                                    <a target="_blank" href="{{ route('event.details', ['id' => $event->id]) }}">
                                        <img src="data:image/jpeg;base64,{{ base64_encode($event->poster) }}"
                                            class="card-img-top" alt="{{ $event->name }}">
                                        <div class="image-overlay">
                                            View Details
                                        </div>
                                    </a>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">{{ $event->name }}</h5>
                                    <p class="card-text">Organized by: {{ $event->club->name }}</p>
                                    <p class="card-text">
                                        <small
                                            class="text-muted">{{ \Carbon\Carbon::parse($event->date)->format('D, d M Y') }}
                                            at {{ \Carbon\Carbon::parse($event->start_time)->format('h:i A') }}</small>
                                    </p>
                                    <p class="card-text">
                                        <small class="text-muted">Location: {{ $event->location }}</small>
                                    </p>
                                </div>
                                @role('user')
                                    <div class="card-footer">
                                        <div class="d-grid gap-2 mt-1">
                                            @if ($event->isUserRegistered(auth()->user()))
                                                <button class="btn btn-danger btn-sm cancel-registration-button" type="button"
                                                    data-event-id="{{ $event->id }}">Cancel Registration</button>
                                            @else
                                                <button class="btn btn-primary btn-sm register-button" type="button"
                                                    data-event-id="{{ $event->id }}">Register</button>
                                            @endif
                                        </div>
                                    </div>
                                @endrole
                                @role('club_manager')
                                    <div class="card-footer">
                                        <div class="d-flex justify-content-between">
                                            <button class="btn btn-sm btn-primary flex-fill me-2 edit-event-button"
                                                type="button" data-bs-toggle="modal" data-bs-target="#editEventModal"
                                                data-event-id="{{ $event->id }}">Edit</button>
                                            <button class="btn btn-sm btn-primary flex-fill me-2 attendance-button"
                                                type="button" data-event-id="{{ $event->id }}">Attendance</button>
                                            <button class="btn btn-sm btn-danger flex-fill cancel-event-button" type="button"
                                                data-event-id="{{ $event->id }}">Cancel</button>
                                        </div>
                                    </div>
                                @endrole
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <!-- Create Event Modal -->
    <div class="modal fade" id="createEventModal" tabindex="-1" aria-labelledby="createEventModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createEventModalLabel">Create Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="createEventForm" class="needs-validation" enctype="multipart/form-data" novalidate>
                        @csrf
                        <div class="mb-3">
                            <label for="eventName" class="form-label">Event Name</label>
                            <input type="text" class="form-control" id="eventName" name="name" required>
                            <div class="invalid-feedback">Please enter a valid event name.</div>
                        </div>
                        <div class="mb-3">
                            <label for="poster" class="form-label">Poster</label>
                            <input type="file" class="form-control" id="poster" name="poster" accept="image/*"
                                required>
                            <div class="mt-3">
                                <img id="posterPreview" src="" alt="Poster Preview"
                                    style="display: none; max-width: 100%; height: auto; border-radius: 10px;">
                            </div>
                            <div class="invalid-feedback">Please upload a valid image.</div>
                        </div>
                        <div class="mb-3">
                            <label for="eventDescription" class="form-label">Event Description</label>
                            <textarea class="form-control" id="eventDescription" name="description" rows="4" required></textarea>
                            <div class="invalid-feedback">Please enter a valid event description.</div>
                        </div>
                        <div class="mb-3">
                            <label for="eventLocation" class="form-label">Location</label>
                            <input type="text" class="form-control" id="eventLocation" name="location" required>
                            <div class="invalid-feedback">Please enter a valid location.</div>
                        </div>
                        <div class="mb-3">
                            <label for="eventDate" class="form-label">Event Date</label>
                            <input type="date" class="form-control" id="eventDate" name="date"
                                value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="startTime" class="form-label">Start Time</label>
                            <select class="form-select" id="startTime" name="start_time" required>
                                @for ($h = 0; $h < 24; $h++)
                                    @foreach (['00', '30'] as $m)
                                        <option value="{{ sprintf('%02d:%02d', $h, $m) }}"
                                            {{ \Carbon\Carbon::now()->minute < 30 ? (sprintf('%02d:%02d', $h, $m) == \Carbon\Carbon::now()->format('H:00') ? 'selected' : '') : (sprintf('%02d:%02d', $h, '30') == \Carbon\Carbon::now()->format('H:30') ? 'selected' : '') }}>
                                            {{ sprintf('%02d:%02d', $h, $m) }}
                                        </option>
                                    @endforeach
                                @endfor
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="endTime" class="form-label">End Time</label>
                            <select class="form-select" id="endTime" name="end_time" required>
                                @php
                                    // Get the current time and determine the next 30-minute interval
                                    $start_time =
                                        \Carbon\Carbon::now()->minute < 30
                                            ? \Carbon\Carbon::now()->startOfHour()
                                            : \Carbon\Carbon::now()->startOfHour()->addMinutes(30);
                                    // Add one hour to the start time
                                    $default_end_time = $start_time->copy()->addHour();
                                @endphp

                                @for ($h = 0; $h < 24; $h++)
                                    @foreach (['00', '30'] as $m)
                                        <option value="{{ sprintf('%02d:%02d', $h, $m) }}"
                                            {{ sprintf('%02d:%02d', $h, $m) == $default_end_time->format('H:i') ? 'selected' : '' }}>
                                            {{ sprintf('%02d:%02d', $h, $m) }}
                                        </option>
                                    @endforeach
                                @endfor
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="eventTheme" class="form-label">Theme</label>
                            <select class="form-select" id="eventTheme" name="theme" required>
                                <option value="Light">Light</option>
                                <option value="Dark">Dark</option>
                            </select>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Create Event</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Event Modal -->
    <div class="modal fade" id="editEventModal" tabindex="-1" aria-labelledby="editEventModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editEventModalLabel">Edit Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editEventForm" class="needs-validation" enctype="multipart/form-data" novalidate>
                        @csrf
                        <div class="mb-3">
                            <label for="editEventName" class="form-label">Event Name</label>
                            <input type="text" class="form-control" id="editEventName" name="name" required>
                            <div class="invalid-feedback">Please enter a valid event name.</div>
                        </div>
                        <div class="mb-3">
                            <label for="editPoster" class="form-label">Poster</label>
                            <input type="file" class="form-control" id="editPoster" name="poster" accept="image/*">
                            <div class="mt-3">
                                <img id="editPosterPreview" src="" alt="Poster Preview"
                                    style="display: none; max-width: 100%; height: auto; border-radius: 10px;">
                            </div>
                            <div class="invalid-feedback">Please upload a valid image.</div>
                        </div>
                        <div class="mb-3">
                            <label for="editEventDescription" class="form-label">Event Description</label>
                            <textarea class="form-control" id="editEventDescription" name="description" rows="4" required></textarea>
                            <div class="invalid-feedback">Please enter a valid event description.</div>
                        </div>
                        <div class="mb-3">
                            <label for="editEventLocation" class="form-label">Location</label>
                            <input type="text" class="form-control" id="editEventLocation" name="location" required>
                            <div class="invalid-feedback">Please enter a valid location.</div>
                        </div>
                        <div class="mb-3">
                            <label for="editEventDate" class="form-label">Event Date</label>
                            <input type="date" class="form-control" id="editEventDate" name="date" required>
                        </div>
                        <div class="mb-3">
                            <label for="editStartTime" class="form-label">Start Time</label>
                            <select class="form-select" id="editStartTime" name="start_time" required>
                                @for ($h = 0; $h < 24; $h++)
                                    @foreach (['00', '30'] as $m)
                                        <option value="{{ sprintf('%02d:%02d', $h, $m) }}">
                                            {{ sprintf('%02d:%02d', $h, $m) }}
                                        </option>
                                    @endforeach
                                @endfor
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editEndTime" class="form-label">End Time</label>
                            <select class="form-select" id="editEndTime" name="end_time" required>
                                @for ($h = 0; $h < 24; $h++)
                                    @foreach (['00', '30'] as $m)
                                        <option value="{{ sprintf('%02d:%02d', $h, $m) }}">
                                            {{ sprintf('%02d:%02d', $h, $m) }}
                                        </option>
                                    @endforeach
                                @endfor
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editEventTheme" class="form-label">Theme</label>
                            <select class="form-select" id="editEventTheme" name="theme" required>
                                <option value="Light">Light</option>
                                <option value="Dark">Dark</option>
                            </select>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="eventAttendanceModal" tabindex="-1" aria-labelledby="eventAttendanceModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-sm-down modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventAttendanceModalLabel">Attendance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="table-responsive">
                                <table id="attendanceTable" class="display compact table table-striped"
                                    style="width:100%">
                                    <thead>
                                        <th class="text-white">Name</th>
                                        <th class="text-white">Present</th>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Register Button Handling
            const registerButtons = document.querySelectorAll('.register-button');
            const cancelRegistrationButtons = document.querySelectorAll('.cancel-registration-button');
            const cancelEventButtons = document.querySelectorAll('.cancel-event-button');
            const eventStatusFilter = document.getElementById('eventStatusFilter');
            const ongoingEventsSection = document.getElementById('ongoingEventsSection');
            const completedEventsSection = document.getElementById('completedEventsSection');

            eventStatusFilter.addEventListener('change', () => {
                const selectedValue = eventStatusFilter.value;

                if (selectedValue === 'all') {
                    ongoingEventsSection.style.display = 'block';
                    completedEventsSection.style.display = 'block';
                } else if (selectedValue === 'upcoming') {
                    ongoingEventsSection.style.display = 'block';
                    completedEventsSection.style.display = 'none';
                } else if (selectedValue === 'completed') {
                    ongoingEventsSection.style.display = 'none';
                    completedEventsSection.style.display = 'block';
                }
            });

            // Trigger the filter logic on page load to apply the current selection
            eventStatusFilter.dispatchEvent(new Event('change'));

            registerButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const eventId = this.getAttribute('data-event-id');
                    fetch("{{ route('iclub.event.register') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({
                                event_id: eventId
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'You have been successfully registered for the event!',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => location.reload());
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Registration failed: ' + data.message,
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'An error occurred while registering for the event.',
                            });
                        });
                });
            });

            document.querySelectorAll('.edit-event-button').forEach(button => {
                button.addEventListener('click', function() {
                    const eventId = this.getAttribute('data-event-id'); // Get event ID from button
                    populateEditForm(eventId); // Call function with event ID
                });
            });

            cancelRegistrationButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const eventId = this.getAttribute('data-event-id');
                    fetch("{{ route('iclub.event.unregister') }}", {
                            method: "DELETE",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({
                                event_id: eventId
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Cancelled!',
                                    text: 'You have successfully cancelled your registration.',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => location.reload());
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Failed to cancel registration: ' + data
                                        .message,
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'An error occurred while cancelling the registration.',
                            });
                        });
                });
            });

            cancelEventButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const eventId = this.getAttribute('data-event-id');
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This action will delete the event permanently!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(`{{ url('/iclub/event') }}/${eventId}/cancel`, {
                                    method: "DELETE",
                                    headers: {
                                        "Content-Type": "application/json",
                                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Deleted!',
                                            text: 'The event has been deleted.',
                                            timer: 2000,
                                            showConfirmButton: false
                                        }).then(() => location.reload());
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Oops...',
                                            text: data.message,
                                        });
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: 'An error occurred while deleting the event.',
                                    });
                                });
                        }
                    });
                });
            });

            // Add event listener for create event form submission with validation
            document.getElementById('createEventForm').addEventListener('submit', function(event) {
                event.preventDefault(); // Prevent the default form submission

                if (this.checkValidity()) {
                    createEvent(); // Only call createEvent if the form is valid
                } else {

                    this.classList.add('was-validated'); // Show validation feedback
                }
            });

            document.getElementById('editEventForm').addEventListener('submit', function(event) {
                event.preventDefault();

                // If form is valid, proceed with submission immediately
                if (this.checkValidity()) {
                    const eventId = this.getAttribute('data-event-id');
                    editEvent(eventId);
                }

                // Add validation feedback
                this.classList.add('was-validated');
            });

            // Reset validation feedback and form fields when modals are hidden
            $('#addMemberModal, #editMemberModal, #createEventModal, #editEventModal').on('hidden.bs.modal',
                function() {
                    $(this).find('form')[0].reset(); // Reset form fields
                    $(this).find('form').removeClass('was-validated'); // Remove validation styles
                });

            // Poster Preview Handling
            const posterInput = document.getElementById('poster');
            const posterPreview = document.getElementById('posterPreview');
            const editPosterInput = document.getElementById('editPoster');
            const editPosterPreview = document.getElementById('editPosterPreview');

            posterInput.addEventListener('change', function() {
                const file = posterInput.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        posterPreview.src = e.target.result;
                        posterPreview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                } else {
                    posterPreview.src = '';
                    posterPreview.style.display = 'none';
                }
            });

            editPosterInput.addEventListener('change', function() {
                const file = editPosterInput.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        editPosterPreview.src = e.target.result;
                        editPosterPreview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                } else {
                    editPosterPreview.src = '';
                    editPosterPreview.style.display = 'none';
                }
            });

            tinymce.init({
                selector: '#eventDescription, #editEventDescription',
                license_key: 'gpl',
                plugins: 'lists link image table code',
                toolbar: 'h1 h2 | undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
                menubar: false,
            });

            function createEvent() {
                const formData = new FormData(document.getElementById('createEventForm'));
                formData.append('description', tinymce.get('eventDescription').getContent());

                $.ajax({
                    url: "{{ route('iclub.event.create') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false, // Important for FormData
                    success: function(response) {
                        $('#createEventModal').modal('hide'); // Close modal
                        Swal.fire({
                            title: 'Success!',
                            text: 'Event created successfully.',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        location.reload(); // Refresh the page or list
                    },
                    error: function(err) {
                        handleAjaxError(err);
                    }
                });
            }

            function editEvent(eventId) {
                const formData = new FormData(document.getElementById('editEventForm'));
                formData.append('_method', 'PUT');

                // Add TinyMCE content
                const description = tinymce.get('editEventDescription').getContent();
                formData.append('description', description);

                // Log the data being sent (for debugging)
                for (let pair of formData.entries()) {
                    console.log(pair[0] + ': ' + pair[1]);
                }

                $.ajax({
                    url: `/iclub/event/${eventId}`,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#editEventModal').modal('hide');
                        Swal.fire({
                            title: 'Success!',
                            text: 'Event updated successfully.',
                            icon: 'success',
                            allowOutsideClick: false, // Prevents closing by clicking outside
                            allowEscapeKey: false, // Prevents closing via the ESC key
                            showConfirmButton: true // Displays an "OK" button
                        }).then(() => {
                            // Reload the page only after the user clicks the "OK" button
                            location.reload();
                        });
                    },
                    error: function(err) {
                        console.log('Error response:', err.responseJSON); // Add this line
                        handleAjaxError(err);
                    }
                });
            }

            document.querySelectorAll('.attendance-button').forEach(button => {
                button.addEventListener('click', function() {
                    // Get the event ID from the button's data attribute
                    const eventId = this.getAttribute('data-event-id');

                    // Set up the DataTable with the event ID (if needed)
                    setupAttendanceTable(eventId);

                    // Open the modal
                    const attendanceModal = new bootstrap.Modal(document.getElementById(
                        'eventAttendanceModal'));
                    attendanceModal.show();
                });
            });

            // Event listener for checkboxes
            $('#attendanceTable').on('change', '.attendance-checkbox', function() {
                const participantId = $(this).data('participant-id');
                const isPresent = $(this).is(':checked') ? 1 : 0;

                // Send attendance status to the server
                $.ajax({
                    url: "{{ route('iclub.eventparticipants.update') }}",
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    data: {
                        participant_id: participantId,
                        present: isPresent
                    },
                    success: function(response) {

                    },
                    error: function(error) {

                    }
                });
            });

            function handleAjaxError(err) {
                if (err.status === 422) {
                    const errors = err.responseJSON.errors;
                    console.log('Validation errors:', errors); // Add this line to see detailed errors
                    let errorMessages = '';
                    for (let field in errors) {
                        if (errors.hasOwnProperty(field)) {
                            errorMessages += `${field}: ${errors[field].join(', ')}\n`;
                        }
                    }
                    Swal.fire('Validation Error!', errorMessages, 'error');
                } else {
                    console.log('Full error:', err); // Add this line
                    Swal.fire('Error!', 'An unexpected error occurred.', 'error');
                }
            }
        });

        function populateEditForm(eventId) {
            fetch(`/iclub/event/${eventId}`)
                .then(response => response.json())
                .then(responseData => {
                    if (responseData.success) {
                        const event = responseData.data;

                        // Populate form fields
                        document.getElementById('editEventName').value = event.name;
                        document.getElementById('editEventLocation').value = event.location;
                        document.getElementById('editEventDate').value = event.date;
                        document.getElementById('editStartTime').value = event.start_time;
                        document.getElementById('editEndTime').value = event.end_time;
                        document.getElementById('editEventTheme').value = event.theme;

                        // TinyMCE content population
                        const editor = tinymce.get('editEventDescription');
                        if (editor) {
                            editor.setContent(event.description || '');
                        }

                        // Poster preview
                        const posterPreview = document.getElementById('editPosterPreview');
                        if (event.poster) {
                            posterPreview.src = `data:image/jpeg;base64,${event.poster}`;
                            posterPreview.style.display = 'block';
                        } else {
                            posterPreview.src = '';
                            posterPreview.style.display = 'none';
                        }

                        // Set the event ID on the form for later use
                        const editEventForm = document.getElementById('editEventForm');
                        editEventForm.setAttribute('data-event-id', eventId);
                    } else {
                        Swal.fire('Error!', 'Failed to fetch event data.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error fetching event data:', error);
                    Swal.fire('Error!', 'Failed to fetch event details.', 'error');
                });
        }

        // Function to initialize or refresh the DataTable
        function setupAttendanceTable(eventId) {
            // Destroy any existing instance of DataTable to reinitialize it
            const table = $('#attendanceTable').DataTable();
            table.destroy();

            // Reinitialize DataTable with updated AJAX data
            $('#attendanceTable').DataTable({
                "processing": true,
                "responsive": true,
                "ajax": {
                    "url": "{{ route('iclub.eventparticipants.data') }}",
                    "data": {
                        "event_id": eventId // Pass the event ID to the server
                    }
                },
                "columns": [{
                        "data": "name"
                    },
                    {
                        "data": "present",
                        "orderable": false,
                        "render": function(data, type, row) {
                            return `
                        <input type="checkbox" class="attendance-checkbox" 
                               data-participant-id="${row.participant_id}" 
                               ${data ? 'checked' : ''}>
                    `;
                        },
                    }
                ],
                "order": [
                    [0, "asc"]
                ]
            });
        }
    </script>

@endsection
