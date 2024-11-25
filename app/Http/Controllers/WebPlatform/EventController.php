<?php

namespace App\Http\Controllers\WebPlatform;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventParticipant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    public function showEventPage(Request $request)
    {
        // Get filters from request
        $eventStatus = $request->input('event_status', 'upcoming');
        $participationStatus = $request->input('participation_status', 'all');

        // Base query to get events
        $query = Event::query();

        /** @var \App\Models\User */
        $user = auth()->user();

        // Add condition for club managers to only retrieve events from their club
        if ($user->hasRole('club_manager')) {
            $query->where('club_id', $user->club_id); // Assuming the User model has a 'club_id' field
        }

        // Filter by event status (upcoming, completed, all)
        if ($eventStatus == 'upcoming') {
            $query->where('date', '>=', Carbon::now())->orderBy('date', 'asc');
        } elseif ($eventStatus == 'completed') {
            $query->where('date', '<', Carbon::now())->orderBy('date', 'desc');
        }

        // Filter by participation status (participating, available, all)
        if ($user->hasRole('user')) {
            if ($participationStatus == 'participating') {
                $query->whereHas('participants', function ($q) {
                    $q->where('user_id', Auth::id());
                });
            } elseif ($participationStatus == 'available') {
                $query->whereDoesntHave('participants', function ($q) {
                    $q->where('user_id', Auth::id());
                });
            }
        }

        // Get the filtered events
        $events = $query->get();

        // Return the view with the filtered events
        return view('webplatform.event', compact('events'));
    }


    public function register(Request $request)
    {
        $userId = Auth::id();
        $eventId = $request->input('event_id');

        // Check if the user is already registered
        $existingRegistration = DB::table('event_participant')
            ->where('user_id', $userId)
            ->where('event_id', $eventId)
            ->first();

        if ($existingRegistration) {
            return response()->json([
                'success' => false,
                'message' => 'You are already registered for this event.'
            ]);
        }

        // Register the user for the event
        DB::table('event_participant')->insert([
            'user_id' => $userId,
            'event_id' => $eventId,
            'created_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registration successful!'
        ]);
    }

    public function unregister(Request $request)
    {
        $eventId = $request->event_id;
        $userId = Auth::id();

        // Find and delete the user's event registration
        $registration = EventParticipant::where('event_id', $eventId)
            ->where('user_id', $userId)
            ->first();

        if ($registration) {
            $registration->delete();
            return response()->json(['success' => true, 'message' => 'Successfully cancelled registration.']);
        }

        return response()->json(['success' => false, 'message' => 'Registration not found.']);
    }

    public function cancelEvent($id)
    {
        try {
            // Retrieve the event
            $event = Event::findOrFail($id);
            // Delete the event
            $event->delete();

            return response()->json(['success' => true, 'message' => 'Event successfully deleted.']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Event not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while deleting the event.'], 500);
        }
    }

    public function createEvent(Request $request)
    {
        try {

            // Log the request data
            Log::info('Incoming request for event creation', $request->all());

            // Validation rules
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'poster' => 'required|mimes:jpeg,png,jpg,gif|max:2048',
                'description' => 'required|string',
                'location' => 'required|string',
                'date' => 'required|date',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'theme' => 'required|string|in:Light,Dark'
            ]);

            // Create new Event model instance and save it
            $event = new Event();
            $event->name = $request->name;
            $event->poster = file_get_contents($request->file('poster')->getRealPath()); // Store image as base64 or binary
            $event->description = $request->description;
            $event->location = $request->location;
            $event->date = $request->date;
            $event->start_time = $request->start_time;
            $event->end_time = $request->end_time;
            $event->club_id = Auth::user()->club_id;
            $event->theme = $request->theme;
            $event->save();

            Log::info('Event created successfully', ['event_id' => $event->id]);

            return response()->json(['message' => 'Event created successfully']);
        } catch (\Exception $e) {
            // Log the error details for internal server error
            Log::error('Error occurred while creating event', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['message' => 'An error occurred while creating the event'], 500);
        }
    }

    public function getEvent($id)
    {
        try {
            $event = Event::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'name' => $event->name,
                    'location' => $event->location,
                    'date' => $event->date->format('Y-m-d'),
                    'start_time' => Carbon::parse($event->start_time)->format('H:i'),
                    'end_time' => Carbon::parse($event->end_time)->format('H:i'),
                    'description' => $event->description,
                    'theme' => $event->theme,
                    'poster' => $event->poster ? base64_encode($event->poster) : null,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching event:', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Event not found.'], 404);
        }
    }

    public function updateEvent(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'description' => 'required|string',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'theme' => 'required|string',
        ]);

        try {
            $event = Event::findOrFail($id);
            $event->name = $request->name;
            $event->location = $request->location;
            $event->date = $request->date;
            $event->start_time = $request->start_time;
            $event->end_time = $request->end_time;
            $event->description = $request->description;
            $event->theme = $request->theme;

            if ($request->hasFile('poster')) {
                $event->poster = file_get_contents($request->file('poster')->getRealPath());
            }

            $event->save();

            return response()->json(['success' => true, 'message' => 'Event updated successfully.']);
        } catch (\Exception $e) {
            Log::error('Error updating event:', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to update event.'], 500);
        }
    }

    public function getEventParticipant(Request $request)
    {
        $eventId = $request->input('event_id'); // Get event_id from the request

        // Validate the event_id to ensure it's provided
        if (!$eventId) {
            return response()->json([
                'error' => 'Event ID is required.'
            ], 400);
        }

        // Retrieve participants with user details
        $participants = DB::table('event_participant')
            ->join('users', 'event_participant.user_id', '=', 'users.id') // Join with users table
            ->select(
                'event_participant.id as participant_id',
                'users.name',  // Fetch the user's name
                'event_participant.present' // Fetch the present status
            )
            ->where('event_participant.event_id', $eventId) // Filter by event_id
            ->get();

        // Return the data in DataTables-compatible format
        return response()->json([
            'data' => $participants
        ]);
    }

    public function updateEventParticipant(Request $request)
    {
        $validated = $request->validate([
            'participant_id' => 'required|exists:event_participant,id',
            'present' => 'required|in:0,1',
        ]);

        $participant = EventParticipant::find($validated['participant_id']);
        $participant->present = $validated['present'];
        $participant->save();
    }
}
