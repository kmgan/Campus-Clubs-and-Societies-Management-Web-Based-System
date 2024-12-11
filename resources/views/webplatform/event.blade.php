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

        .image-container {
            position: relative;
        }

        .image-container img {
            width: 100%;
            height: 250px;
            transition: transform 0.3s ease-in-out;
        }

        .image-container:hover .image-overlay {
            opacity: 1;
        }

        .details-card {
            overflow: hidden;
        }

        .card-title {
            height: 48px;
            margin-bottom: 0.5rem;
        }
    </style>
    <h1 class="fw-bold">Events</h1>

    <hr>

    <div class="container-fluid">
        <form action="{{ route('iclub.event.page') }}" method="GET">
            <div class="row g-3">
                <div class="col-md-4 col-12">
                    <div class="input-group">
                        <input type="text" name="keyword" class="form-control" placeholder="Search for events"
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
                            <option value="participating"
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
            $now = \Carbon\Carbon::now('Asia/Kuala_Lumpur');

            // Debug information
            foreach ($events as $event) {
                $eventDate = \Carbon\Carbon::parse($event->date)->startOfDay();
                \Log::info(
                    "Event {$event->id}: EventDate: {$eventDate}, IsUpcoming: " .
                        ($eventDate->gte($now->startOfDay()) ? 'true' : 'false'),
                );
            }

            $ongoingEvents = $events->filter(function ($event) use ($now) {
                return \Carbon\Carbon::parse($event->date)
                    ->startOfDay()
                    ->gte($now->startOfDay());
            });

            $completedEvents = $events->filter(function ($event) use ($now) {
                return \Carbon\Carbon::parse($event->date)
                    ->startOfDay()
                    ->lt($now->startOfDay());
            });
        @endphp


        {{-- Upcoming Events Section --}}
        <div id="ongoingEventsSection" class="mt-3">
            <h2 class="fw-bold">Upcoming Events</h2>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 row-cols-xxl-5 mt-3">
                @if ($ongoingEvents->isEmpty())
                    <p>No upcoming events found.</p>
                @else
                    @foreach ($ongoingEvents as $event)
                        <div class="col mb-4">
                            <div class="card details-card shadow">
                                <div class="image-container">
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
                                    <p class="card-text">{{ $event->club->name }}</p>
                                    <p class="card-text">
                                        <small
                                            class="text-muted">{{ \Carbon\Carbon::parse($event->date)->format('D, d M Y') }}
                                            at {{ \Carbon\Carbon::parse($event->start_time)->format('h:i A') }}</small>
                                    </p>
                                    <p class="card-text">
                                        <small class="text-muted">{{ $event->location }}</small>
                                    </p>
                                    <p>
                                        <small class="text-muted">{{ $event->participants_count }} going |</small>
                                        <small
                                            class="text-muted">{{ $event->max_participants - $event->participants_count }}
                                            spots left</small>
                                    </p>
                                    @if ($event->participant_approval_required)
                                        <span class="badge bg-warning text-dark" data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="This event requires approval to participate">
                                            Approval Required
                                        </span>
                                    @else
                                        <span class="badge bg-success" data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="This event does not require approval to participate">
                                            No Approval Required
                                        </span>
                                    @endif
                                </div>
                                @role('user')
                                    <div class="card-footer">
                                        <div class="d-grid gap-2 mt-1">
                                            @if (Carbon\Carbon::now()->greaterThanOrEqualTo(
                                                    $event->date->copy()->setTime($event->end_time->hour, $event->end_time->minute, $event->end_time->second)))
                                                <!-- If the event has ended, show "Event Ended" button -->
                                                <button class="btn btn-secondary btn-sm" type="button" disabled>Event
                                                    Ended</button>

                                                <!-- Check if the user is registered -->
                                            @elseif ($event->isUserRegistered(auth()->user()))
                                                @php
                                                    $participant = $event
                                                        ->participants()
                                                        ->where('user_id', auth()->user()->id)
                                                        ->first();
                                                @endphp

                                                <!-- Check if the user is approved or pending approval -->
                                                @if ($participant->isApproved == 1)
                                                    <!-- If user is approved, show "Cancel Registration" button -->
                                                    <button class="btn btn-danger btn-sm cancel-registration-button"
                                                        type="button" data-event-id="{{ $event->id }}">
                                                        Cancel Registration
                                                    </button>
                                                @elseif ($participant->isApproved == 0)
                                                    <!-- If user is pending approval, show "Cancel Registration" and "Pending" label -->
                                                    <button class="btn btn-secondary btn-sm" type="button"
                                                        disabled>Pending</button>
                                                    <button class="btn btn-danger btn-sm cancel-registration-button"
                                                        type="button" data-event-id="{{ $event->id }}">
                                                        Cancel Registration
                                                    </button>
                                                @endif
                                                <!-- Check if the event is full -->
                                            @elseif ($event->participants_count >= $event->max_participants)
                                                <!-- If the event is full, show "Full" button -->
                                                <button class="btn btn-secondary btn-sm" type="button" disabled>Full</button>

                                                <!-- If none of the above, allow the user to register -->
                                            @else
                                                <button class="btn btn-primary btn-sm register-button" type="button"
                                                    data-event-id="{{ $event->id }}">
                                                    Register
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endrole
                                @hasanyrole('club_manager|admin')
                                    <div class="card-footer">
                                        <div class="d-flex justify-content-between">
                                            <button class="btn btn-sm btn-primary flex-fill me-2 edit-event-button"
                                                type="button" data-bs-toggle="modal" data-bs-target="#editEventModal"
                                                data-event-id="{{ $event->id }}">Edit</button>

                                            <div class="position-relative">
                                                <button class="btn btn-sm btn-primary flex-fill me-2 attendance-button"
                                                    type="button" data-event-id="{{ $event->id }}">
                                                    Attendance
                                                    <!-- Display the Badge if there are pending participants -->
                                                    @if ($event->pending_participants_count > 0)
                                                        <span
                                                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                                            style="left: 90% !important;">
                                                            {{ $event->pending_participants_count }}
                                                        </span>
                                                    @endif
                                                </button>
                                            </div>

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
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 row-cols-xxl-5 mt-3">
                @if ($completedEvents->isEmpty())
                    <p>No completed events found.</p>
                @else
                    @foreach ($completedEvents as $event)
                        <div class="col mb-4">
                            <div class="card details-card shadow">
                                <div class="image-container">
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
                                    <p class="card-text">{{ $event->club->name }}</p>
                                    <p class="card-text">
                                        <small
                                            class="text-muted">{{ \Carbon\Carbon::parse($event->date)->format('D, d M Y') }}
                                            at {{ \Carbon\Carbon::parse($event->start_time)->format('h:i A') }}</small>
                                    </p>
                                    <p class="card-text">
                                        <small class="text-muted">{{ $event->location }}</small>
                                    </p>
                                    <p>
                                        <small class="text-muted">{{ $event->participants_count }} going |</small>
                                        <small
                                            class="text-muted">{{ $event->max_participants - $event->participants_count }}
                                            spots left</small>
                                    </p>
                                    @if ($event->participant_approval_required)
                                        <span class="badge bg-warning text-dark" data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="This event requires approval to participate">
                                            Approval Required
                                        </span>
                                    @else
                                        <span class="badge bg-success" data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="This event does not require approval to participate">
                                            No Approval Required
                                        </span>
                                    @endif
                                </div>
                                @role('user')
                                    <div class="card-footer">
                                        <div class="d-grid gap-2 mt-1">
                                            @if (Carbon\Carbon::now()->greaterThanOrEqualTo(
                                                    $event->date->copy()->setTime($event->end_time->hour, $event->end_time->minute, $event->end_time->second)))
                                                <!-- If the event has ended, show "Event Ended" button -->
                                                <button class="btn btn-secondary btn-sm" type="button" disabled>Event
                                                    Ended</button>

                                                <!-- Check if the user is registered -->
                                            @elseif ($event->isUserRegistered(auth()->user()))
                                                @php
                                                    $participant = $event
                                                        ->participants()
                                                        ->where('user_id', auth()->user()->id)
                                                        ->first();
                                                @endphp

                                                <!-- Check if the user is approved or pending approval -->
                                                @if ($participant->isApproved == 1)
                                                    <!-- If user is approved, show "Cancel Registration" button -->
                                                    <button class="btn btn-danger btn-sm cancel-registration-button"
                                                        type="button" data-event-id="{{ $event->id }}">
                                                        Cancel Registration
                                                    </button>
                                                @elseif ($participant->isApproved == 0)
                                                    <!-- If user is pending approval, show "Cancel Registration" and "Pending" label -->
                                                    <button class="btn btn-secondary btn-sm" type="button"
                                                        disabled>Pending</button>
                                                    <button class="btn btn-danger btn-sm cancel-registration-button"
                                                        type="button" data-event-id="{{ $event->id }}">
                                                        Cancel Registration
                                                    </button>
                                                @endif
                                                <!-- Check if the event is full -->
                                            @elseif ($event->participants_count >= $event->max_participants)
                                                <!-- If the event is full, show "Full" button -->
                                                <button class="btn btn-secondary btn-sm" type="button" disabled>Full</button>

                                                <!-- If none of the above, allow the user to register -->
                                            @else
                                                <button class="btn btn-primary btn-sm register-button" type="button"
                                                    data-event-id="{{ $event->id }}">
                                                    Register
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endrole
                                @hasanyrole('club_manager|admin')
                                    <div class="card-footer">
                                        <div class="d-flex justify-content-between">
                                            <button class="btn btn-sm btn-primary flex-fill me-2 edit-event-button"
                                                type="button" data-bs-toggle="modal" data-bs-target="#editEventModal"
                                                data-event-id="{{ $event->id }}">Edit</button>

                                            <div class="position-relative">
                                                <button class="btn btn-sm btn-primary flex-fill me-2 attendance-button"
                                                    type="button" data-event-id="{{ $event->id }}">
                                                    Attendance
                                                    <!-- Display the Badge if there are pending participants -->
                                                    @if ($event->pending_participants_count > 0)
                                                        <span
                                                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                                            style="left: 90% !important;">
                                                            {{ $event->pending_participants_count }}
                                                        </span>
                                                    @endif
                                                </button>
                                            </div>

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
                            <label for="maxParticipants" class="form-label">Max Participants</label>
                            <input type="number" class="form-control" id="maxParticipants" name="max_participants"
                                min="0" required>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch"
                                id="participantApprovalRequired" name="participant_approval_required">
                            <label class="form-check-label" for="participantApprovalRequired">Participant
                                Approval</label>
                        </div>
                        <!-- Theme Selector -->
                        <div class="mb-3">
                            <label for="eventTheme" class="form-label">Theme</label>
                            <select class="form-select" id="eventTheme" name="theme" required>
                                <option value="Light">Light Animated</option>
                                <option value="Dark">Dark Animated</option>
                                <option value="Minimal">Minimal</option>
                            </select>
                        </div>

                        <!-- Color Picker for Minimal theme -->
                        <div class="mb-3" id="addColorPickerContainer">
                            <label for="backgroundColor" class="form-label">Background Color</label>
                            <input type="color" class="form-control" id="backgroundColor" name="background_color"
                                value="#ffffff">
                            <small class="form-text text-muted">Choose if theme is Minimal</small>
                        </div>

                        <!-- Text Color Picker for Minimal theme -->
                        <div class="mb-3" id="addTextColorPickerContainer">
                            <label for="textColor" class="form-label">Text Color</label>
                            <input type="color" class="form-control" id="textColor" name="text_color"
                                value="#000000">
                            <small class="form-text text-muted">Choose if theme is Minimal</small>
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
                            <label for="editMaxParticipants" class="form-label">Max Participants</label>
                            <input type="number" class="form-control" id="editMaxParticipants" name="max_participants"
                                min="0" required>
                        </div>
                        <!-- Participant Approval Switch -->
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch"
                                id="editParticipantApprovalRequired" name="participant_approval_required">
                            <label class="form-check-label" for="editParticipantApprovalRequired">Participant
                                Approval</label>
                        </div>
                        <div class="mb-3">
                            <label for="editEventTheme" class="form-label">Theme</label>
                            <select class="form-select" id="editEventTheme" name="theme" required>
                                <option value="Light">Light Animated</option>
                                <option value="Dark">Dark Animated</option>
                                <option value="Minimal">Minimal</option>
                            </select>
                        </div>

                        <!-- Color Picker for Minimal theme -->
                        <div class="mb-3" id="editColorPickerContainer">
                            <label for="editBackgroundColor" class="form-label">Background Color</label>
                            <input type="color" class="form-control" id="editBackgroundColor" name="background_color"
                                value="#ffffff">
                            <small class="form-text text-muted">Choose if theme is Minimal</small>
                        </div>

                        <!-- Text Color Picker for Minimal theme -->
                        <div class="mb-3" id="editTextColorPickerContainer">
                            <label for="editTextColor" class="form-label">Text Color</label>
                            <input type="color" class="form-control" id="editTextColor" name="text_color"
                                value="#000000">
                            <small class="form-text text-muted">Choose if theme is Minimal</small>
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
                            <div class="col text-end">
                                <!-- Participant Approval Button with Badge -->
                                <div class="dropdown position-relative">
                                    <button class="btn btn-primary mb-3 dropdown-toggle" type="button"
                                        id="participantApprovalButton" data-bs-toggle="dropdown" aria-expanded="false">
                                        Participant Approval
                                        <!-- The Badge to Show Pending Participants Count -->
                                        <span id="pendingParticipantBadge"
                                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                            style="left: 80%; display: none;">
                                            0
                                        </span>
                                    </button>
                                    <ul id="pendingParticipantsList" class="dropdown-menu dropdown-menu-end py-0"
                                        aria-labelledby="participantApprovalButton">
                                        <!-- Pending Participants will be listed here -->
                                    </ul>
                                </div>
                            </div>
                        </div>
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

    <!-- Add this HTML for the loading spinner -->
    <div id="loadingSpinner"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.8); z-index: 9999; display: flex; align-items: center; justify-content: center;">
        <div class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Show the loading spinner
            document.getElementById('loadingSpinner').style.display = 'flex';

            tinymce.init({
                selector: '#eventDescription, #editEventDescription',
                license_key: 'gpl',
                plugins: 'lists link image table code',
                toolbar: 'h1 h2 | undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
                menubar: false,
                setup: function(editor) {
                    editor.on('init', function() {
                        // Hide the loading spinner once TinyMCE is initialized
                        document.getElementById('loadingSpinner').style.display = 'none';
                    });
                }
            });

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

            // Reset validation feedback, form fields, and hide image previews when modals are hidden
            $('#createEventModal, #editEventModal').on('hidden.bs.modal',
                function() {
                    $(this).find('form')[0].reset(); // Reset form fields
                    $(this).find('form').removeClass('was-validated'); // Remove validation styles

                    // Hide image previews
                    $(this).find('img').each(function() {
                        $(this).attr('src', '').hide();
                    });
                });

            // Poster Preview Handling
            const posterInput = document.getElementById('poster');
            const posterPreview = document.getElementById('posterPreview');
            const editPosterInput = document.getElementById('editPoster');
            const editPosterPreview = document.getElementById('editPosterPreview');

            // Common validation for poster images (for both add and edit)
            function handleFileInputChange(inputId, previewId) {
                const fileInput = document.getElementById(inputId);
                const previewImage = document.getElementById(previewId);
                const maxSizeInMB = 10; // 10 MB
                const maxSizeInBytes = maxSizeInMB * 1024 * 1024; // Convert MB to bytes
                const allowedFormats = ['image/jpeg', 'image/png', 'image/jpg'];

                fileInput.addEventListener('change', function(event) {
                    const file = event.target.files[0];

                    if (file) {
                        // Check file size
                        if (file.size > maxSizeInBytes) {
                            // If file is too large, show SweetAlert
                            Swal.fire({
                                icon: 'error',
                                title: 'File too large',
                                text: `The selected file exceeds the ${maxSizeInMB}MB limit.`,
                            });
                            fileInput.value = ''; // Clear the file input
                            previewImage.style.display = 'none'; // Hide the preview
                        }
                        // Check file type
                        else if (!allowedFormats.includes(file.type)) {
                            // If file type is invalid, show SweetAlert
                            Swal.fire({
                                icon: 'error',
                                title: 'Invalid file format',
                                text: 'Please upload a valid image (JPEG, PNG, JPG).',
                            });
                            fileInput.value = ''; // Clear the file input
                            previewImage.style.display = 'none'; // Hide the preview
                        } else {
                            // If file is valid, show the preview
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                previewImage.src = e.target.result;
                                previewImage.style.display = 'block'; // Show the preview
                            };
                            reader.readAsDataURL(file);
                        }
                    } else {
                        // If no file selected, hide the preview
                        previewImage.style.display = 'none';
                    }
                });
            }

            // Initialize the file input and preview handlers for both 'add' and 'edit'
            handleFileInputChange('editPoster', 'editPosterPreview');
            handleFileInputChange('poster', 'posterPreview');


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

            document.addEventListener('focusin', (e) => {
                if (e.target.closest(
                        ".tox-tinymce, .tox-tinymce-aux, .moxman-window, .tam-assetmanager-root") !==
                    null) {
                    e.stopImmediatePropagation();
                }
            });

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

                    // Fetch pending participants directly after opening the modal
                    getPendingParticipant(eventId);
                });
            });

            // Function to fetch pending participants
            function getPendingParticipant(eventId) {
                $.ajax({
                    url: `/iclub/${eventId}/pendingParticipant`, // Your route for fetching pending participants
                    type: "GET",
                    success: function(response) {
                        let participantsList = response.participants;
                        let pendingParticipantsList = $('#pendingParticipantsList');
                        let pendingParticipantBadge = $('#pendingParticipantBadge');

                        // Clear any existing items
                        pendingParticipantsList.empty();

                        // Check if there are any pending participants
                        if (participantsList.length > 0) {
                            // Update the badge with the count of pending participants
                            pendingParticipantBadge.text(participantsList.length);
                            pendingParticipantBadge
                                .show(); // Show the badge if there are pending participants

                            // Loop through the list of pending participants and create list items
                            participantsList.forEach(function(participant) {
                                let listItem = `
                    <li id="participant-${participant.participant_id}" class="dropdown-item d-flex justify-content-between align-items-center">
                        <span>${participant.name} (Student ID: ${participant.student_id})</span>
                        <div>
                            <!-- Approve Button with Tick Icon -->
                            <button class="btn btn-outline-success btn me-2 border-0" title="Approve" onclick="approveParticipant(${participant.participant_id}, event)">
                                <i class="bi bi-check-lg"></i>
                            </button>
                            <!-- Reject Button with Cross Icon -->
                            <button class="btn btn-outline-danger btn border-0" title="Reject" onclick="rejectParticipant(${participant.participant_id}, event)">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </li>
                `;
                                pendingParticipantsList.append(listItem);
                            });
                        } else {
                            // If no pending participants, display a message
                            let noParticipantsItem = `
                    <li class="dropdown-item text-center">
                        No participants waiting for approval.
                    </li>
                `;
                            pendingParticipantsList.append(noParticipantsItem);
                            pendingParticipantBadge.hide();
                        }
                    },
                    error: function(error) {
                        console.error('Error fetching pending participants:', error);
                    }
                });
            }

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
                        isPresent: isPresent
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

        function createEvent() {
            const formData = new FormData(document.getElementById('createEventForm'));
            formData.append('description', tinymce.get('eventDescription').getContent());

            // Convert checkbox value to boolean
            const approvalRequired = document.getElementById('participantApprovalRequired').checked ? '1' : '0';
            formData.append('participant_approval_required', approvalRequired);

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
                    console.log('Error response:', err.responseJSON); // Add this line
                }
            });
        }

        function editEvent(eventId) {
            console.log('editEvent function called with eventId:', eventId);

            const formData = new FormData(document.getElementById('editEventForm'));
            formData.append('_method', 'PUT');

            // Add TinyMCE content
            const description = tinymce.get('editEventDescription').getContent();
            formData.append('description', description);

            const participantApprovalRequired = document.getElementById('editParticipantApprovalRequired').checked ? '1' :
                '0';
            formData.set('participant_approval_required', participantApprovalRequired);

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
                        showConfirmButton: true,
                        confirmButtonText: 'OK',
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                },
                error: function(err) {
                    console.log('Error response:', err.responseJSON);
                    let errorMessage = 'An unexpected error occurred.';

                    // Try to get more specific error message if available
                    if (err.responseJSON && err.responseJSON.message) {
                        errorMessage = err.responseJSON.message;
                    } else if (err.responseJSON && err.responseJSON.error) {
                        errorMessage = err.responseJSON.error;
                    }

                    Swal.fire({
                        title: 'Error!',
                        text: errorMessage,
                        icon: 'error',
                        showConfirmButton: true,
                        confirmButtonText: 'OK',
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    });
                }
            });
        }

        $('#createEventForm').on('submit', function(event) {
            event.preventDefault(); // Prevent default submission behavior

            // Synchronize TinyMCE content with the underlying textarea
            const editor = tinymce.get('eventDescription');
            if (editor) {
                editor.save(); // This ensures the TinyMCE content is written to the textarea
            }

            // Check form validity and proceed
            if (this.checkValidity()) {
                createEvent(); // Call the function to create the event
            } else {
                // Find the first invalid field
                const firstInvalidField = this.querySelector(':invalid');
                if (firstInvalidField) {
                    firstInvalidField.focus(); // Focus on the first invalid field
                    firstInvalidField.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    }); // Smoothly scroll to the field
                }
            }

            this.classList.add('was-validated'); // Add Bootstrap validation feedback
        });

        $('#editEventForm').on('submit', function(event) {
            event.preventDefault(); // Prevent default submission behavior

            // Synchronize TinyMCE content with the underlying textarea
            const editor = tinymce.get('editEventDescription');
            if (editor) {
                editor.save(); // This ensures the TinyMCE content is written to the textarea
            }

            // Check form validity and proceed
            if (this.checkValidity()) {
                const eventId = this.getAttribute('data-event-id');
                editEvent(eventId);
            } else {
                // Find the first invalid field
                const firstInvalidField = this.querySelector(':invalid');
                if (firstInvalidField) {
                    firstInvalidField.focus(); // Focus on the first invalid field
                    firstInvalidField.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    }); // Smoothly scroll to the field
                }
            }

            this.classList.add('was-validated'); // Add validation feedback
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
                        document.getElementById('editMaxParticipants').value = event.max_participants;
                        document.getElementById('editParticipantApprovalRequired').checked = event
                            .participant_approval_required;

                        // Set the background color and text color
                        if (event.background_color) {
                            document.getElementById('editBackgroundColor').value = event.background_color;
                        }
                        if (event.text_color) {
                            document.getElementById('editTextColor').value = event.text_color;
                        }

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

                        handleThemeChange();
                    } else {
                        Swal.fire('Error!', 'Failed to fetch event data.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error fetching event data:', error);
                    Swal.fire('Error!', 'Failed to fetch event details.', 'error');
                });
        }

        const themeSelect = document.getElementById('editEventTheme');
        const backgroundColorPicker = document.getElementById('editBackgroundColor');
        const textColorPicker = document.getElementById('editTextColor');

        // Function to handle showing/hiding the color pickers
        function handleThemeChange() {
            if (themeSelect.value === 'Minimal') {
                editColorPickerContainer.style.display = 'block'; // Show if Minimal is selected
                editTextColorPickerContainer.style.display = 'block';
            } else {
                editColorPickerContainer.style.display = 'none'; // Hide if other theme is selected
                editTextColorPickerContainer.style.display = 'none';
            }
        }

        // Add event listener for theme selection change
        themeSelect.addEventListener('change', handleThemeChange);

        // Initial call to set the correct state when the page loads
        handleThemeChange();

        const addThemeSelect = document.getElementById('eventTheme');
        const addColorPickerContainer = document.getElementById('addColorPickerContainer');
        const addTextColorPickerContainer = document.getElementById('addTextColorPickerContainer');

        // Function to handle showing/hiding the color pickers for the "add" form
        function handleAddThemeChange() {
            if (addThemeSelect.value === 'Minimal') {
                addColorPickerContainer.style.display = 'block'; // Show if Minimal is selected
                addTextColorPickerContainer.style.display = 'block';
            } else {
                addColorPickerContainer.style.display = 'none'; // Hide if other theme is selected
                addTextColorPickerContainer.style.display = 'none';
            }
        }

        // Add event listener for theme selection change in the "add" form
        addThemeSelect.addEventListener('change', handleAddThemeChange);

        // Initial call to set the correct state when the page loads for the "add" form
        handleAddThemeChange();

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
                        "data": "isPresent",
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

        // Function to approve a participant
        function approveParticipant(participantId, event) {
            event.stopPropagation(); // Prevent dropdown from closing

            // Add debug log to verify the ID
            console.log('Approving participant:', participantId);

            // Ensure participantId is valid
            if (!participantId) {
                console.error('Invalid participant ID');
                return;
            }

            $.ajax({
                url: `/iclub/pendingParticipant/${participantId}/approve`,
                type: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    // Remove the approved participant from the pending list in the dropdown
                    $(`#participant-${participantId}`).remove();

                    // Update the badge count
                    let currentCount = parseInt($('#pendingParticipantBadge').text());
                    let newCount = currentCount - 1;

                    if (newCount > 0) {
                        $('#pendingParticipantBadge').text(newCount);
                    } else {
                        // Hide the badge if no pending participants are left
                        $('#pendingParticipantBadge').hide();

                        // Show "No participants waiting for approval" message if list is empty
                        let noParticipantsItem = `
                    <li class="dropdown-item text-center">
                        No participants waiting for approval.
                    </li>
                `;
                        $('#pendingParticipantsList').append(noParticipantsItem);
                    }

                    if ($.fn.DataTable.isDataTable('#attendanceTable')) {
                        $('#attendanceTable').DataTable().ajax.reload(null,
                            false); // Reload table without resetting pagination
                    }

                    // Check if there are any items left in the pending list
                    if ($('#pendingParticipantsList li').length === 0) {
                        let noParticipantsItem = `
                    <li class="dropdown-item text-center">
                        No participants waiting for approval.
                    </li>
                `;
                        $('#pendingParticipantsList').append(noParticipantsItem);
                        $('#pendingParticipantBadge').hide();
                    }
                },
                error: function(err) {
                    console.error('Error:', err);
                    Swal.fire('Error!', 'An error occurred while approving the participant.', 'error');
                }
            });
        }

        // Function to reject a participant
        function rejectParticipant(participantId, event) {
            event.stopPropagation(); // Prevent dropdown from closing

            // Show a confirmation dialog using Swal
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you really want to reject this participant?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, reject it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Proceed with the rejection if confirmed
                    $.ajax({
                        url: `/iclub/pendingParticipant/${participantId}/reject`,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(result) {
                            // Remove the rejected participant from the pending list in the dropdown
                            $(`#participant-${participantId}`).remove();

                            // Update the badge count
                            let currentCount = parseInt($('#pendingParticipantBadge').text());
                            let newCount = currentCount - 1;

                            if (newCount > 0) {
                                $('#pendingParticipantBadge').text(newCount);
                            } else {
                                // Hide the badge if no pending participants are left
                                $('#pendingParticipantBadge').hide();

                                // Show "No participants waiting for approval" message if list is empty
                                let noParticipantsItem = `
                            <li class="dropdown-item text-center">
                                No participants waiting for approval.
                            </li>
                        `;
                                $('#pendingParticipantsList').append(noParticipantsItem);
                            }

                            // Additional check in case all participants are removed
                            if ($('#pendingParticipantsList li').length === 0) {
                                let noParticipantsItem = `
                            <li class="dropdown-item text-center">
                                No participants waiting for approval.
                            </li>
                        `;
                                $('#pendingParticipantsList').append(noParticipantsItem);
                                $('#pendingParticipantBadge').hide();
                            }
                        },
                        error: function(err) {
                            Swal.fire('Error!', 'An error occurred while rejecting the participant.',
                                'error');
                        }
                    });
                }
            });
        }
    </script>

@endsection
