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
                    <select name="event_status" class="form-select" onchange="this.form.submit()">
                        <option value="all" {{ request('event_status') == 'all' ? 'selected' : '' }}>All Events</option>
                        <option value="upcoming" {{ request('event_status') == 'upcoming' ? 'selected' : '' }}>Upcoming
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
                <div class="col-md-2 col-12"></div>
            </div>
        </form>
        <div class="row-cols-5 mt-4">
            @if ($events->isEmpty())
                <p>No events found.</p>
            @else
                @foreach ($events as $event)
                    <div class="col">
                        <div class="card">
                            <img src="data:image/jpeg;base64,{{ base64_encode($event->poster) }}" class="card-img-top"
                                alt="{{ $event->name }}">
                            <div class="card-body">
                                <h5 class="card-title">{{ $event->name }}</h5>
                                <p class="card-text">Organized by: {{ $event->club->name }}</p>
                                <p class="card-text"><small
                                        class="text-muted">{{ \Carbon\Carbon::parse($event->date)->format('D, d M Y') }} at
                                        {{ \Carbon\Carbon::parse($event->start_time)->format('h:i A') }}</small></p>
                                <p class="card-text"><small class="text-muted">Location: {{ $event->location }}</small></p>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
@endsection
