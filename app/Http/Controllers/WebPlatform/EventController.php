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
        $eventStatus = $request->input('event_status');
        $participationStatus = $request->input('participation_status');

        // Base query to get events
        $query = Event::query();

        /** @var \App\Models\User */
        $user = auth()->user();

        // Add condition for club managers to only retrieve events from their club
        if ($user->hasRole('club_manager')) {
            $query->where('club_id', $user->club_id); // Assuming the User model has a 'club_id' field
        }

        $now = Carbon::now();

        if ($eventStatus == 'upcoming') {
            // Include events today that haven't ended yet
            $query->where(function ($query) use ($now) {
                $query->where('date', '>', $now->toDateString())
                    ->orWhere(function ($query) use ($now) {
                        $query->where('date', $now->toDateString())
                            ->where('end_time', '>', $now->toTimeString());
                    });
            })->orderBy('date', 'asc');
        } elseif ($eventStatus == 'completed') {
            // Include events that have ended before today or events that ended earlier today
            $query->where(function ($query) use ($now) {
                $query->where('date', '<', $now->toDateString())
                    ->orWhere(function ($query) use ($now) {
                        $query->where('date', $now->toDateString())
                            ->where('end_time', '<=', $now->toTimeString());
                    });
            })->orderBy('date', 'desc');
        }


        if ($user->hasRole('user')) {
            if ($participationStatus == 'all') {
                // No filtering needed
            } elseif ($participationStatus == 'participating') {
                $query->whereHas('participants', function ($q) {
                    $q->where('user_id', Auth::id());
                });
            } elseif ($participationStatus == 'available') {
                $query->whereDoesntHave('participants', function ($q) {
                    $q->where('user_id', Auth::id());
                });
            }
        }

        $events = $query->withCount(['participants', 'participants as pending_participants_count' => function ($q) {
            $q->where('isApproved', 0);
        }])->get();

        // Return the view with the filtered events
        return view('webplatform.event', compact('events'));
    }


    public function register(Request $request)
    {
        $userId = Auth::id();
        $eventId = $request->input('event_id');

        // Fetch event details to check approval requirement
        $event = DB::table('event')->where('id', $eventId)->first();

        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Event not found.'
            ]);
        }

        // Determine approval status based on the event's `participant_approval_required` field
        $isApproved = $event->participant_approval_required ? 0 : 1;

        // Register the user for the event
        DB::table('event_participant')->insert([
            'user_id' => $userId,
            'event_id' => $eventId,
            'isApproved' => $isApproved,
            'created_at' => now(),
            'updated_at' => now()
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

            // Validation rules
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'poster' => 'required|mimes:jpeg,png,jpg|max:10240',
                'description' => 'required|string',
                'location' => 'required|string',
                'date' => 'required|date',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'max_participants' => 'required|integer|min:0',
                'participant_approval_required' => 'required|in:0,1',
                'theme' => 'required|string|in:Light,Dark,Minimal',
                'background_color' => 'nullable|string',
                'text_color' => 'nullable|string',
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
            $event->max_participants = $request->max_participants;
            $event->participant_approval_required = $request->participant_approval_required;
            $event->club_id = Auth::user()->club_id;
            $event->theme = $request->theme;
            $event->background_color = $request->background_color;
            $event->text_color = $request->text_color;
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
                    'max_participants' => $event->max_participants,
                    'participant_approval_required' => $event->participant_approval_required,
                    'description' => $event->description,
                    'theme' => $event->theme,
                    'poster' => $event->poster ? base64_encode($event->poster) : null,
                    'background_color' => $event->background_color,
                    'text_color' => $event->text_color,
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
            'max_participants' => 'required|integer|min:0',
            'participant_approval_required' => 'required|in:0,1',
            'description' => 'required|string',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
            'theme' => 'required|string',
            'background_color' => 'nullable|string',
            'text_color' => 'nullable|string',
        ]);

        try {
            $event = Event::findOrFail($id);
            $event->name = $request->name;
            $event->location = $request->location;
            $event->date = $request->date;
            $event->start_time = $request->start_time;
            $event->end_time = $request->end_time;
            $event->max_participants = $request->max_participants;
            $event->participant_approval_required = $request->participant_approval_required;
            $event->description = $request->description;
            $event->theme = $request->theme;
            $event->background_color = $request->background_color;
            $event->text_color = $request->text_color;

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
                'event_participant.isPresent', // Fetch the present status
                'event_participant.isApproved'
            )
            ->where('event_participant.event_id', $eventId) // Filter by event_id
            ->where('event_participant.isApproved', 1)
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
            'isPresent' => 'required|in:0,1',
        ]);

        $participant = EventParticipant::find($validated['participant_id']);
        $participant->isPresent = $validated['isPresent'];
        $participant->save();
    }

    public function getPendingParticipant($eventId)
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        // Ensure that the user has the club_manager or admin role
        if ($user->hasRole(['club_manager', 'admin'])) {
            $event = Event::find($eventId);

            if (is_null($event)) {
                return response()->json(['message' => 'Event not found.'], 404);
            }

            // Retrieve pending participants
            $participants = DB::table('event_participant')
                ->join('users', 'event_participant.user_id', '=', 'users.id')
                ->select(
                    'event_participant.id as participant_id',
                    'users.name',
                    'users.student_id',
                    'event_participant.isApproved'
                )
                ->where('event_participant.event_id', $eventId)
                ->where('event_participant.isApproved', 0)
                ->get();

            return response()->json(['participants' => $participants]);
        }

        return response()->json(['message' => 'Unauthorized'], 403);
    }

    public function approveParticipant($id)
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        // Log the user trying to approve and the participant ID
        Log::info("User {$user->id} with role(s): " . implode(', ', $user->getRoleNames()->toArray()) . " is attempting to approve participant ID: {$id}");

        if ($user->hasRole(['club_manager', 'admin'])) {
            // Find the participant using both id and isApproved to ensure it matches the pending state
            $participant = EventParticipant::where('id', $id)->where('isApproved', 0)->first();

            if ($participant) {
                // Log that we found the participant
                Log::info("Participant found: " . $participant->id);

                // Update isApproved field to approve the participant
                $participant->isApproved = 1;
                $participant->save();

                Log::info("Participant {$id} approved successfully by user {$user->id}");

                return response()->json(['message' => 'Participant approved successfully.']);
            }

            // Log if participant not found or already approved
            Log::warning("Participant {$id} not found or already approved.");

            return response()->json(['message' => 'Participant not found or already approved.'], 404);
        }

        // Log unauthorized attempt
        Log::warning("Unauthorized approval attempt by user {$user->id} for participant {$id}");

        return response()->json(['message' => 'Unauthorized'], 403);
    }

    public function rejectParticipant($id)
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        if ($user->hasRole(['club_manager', 'admin'])) {
            $participant = EventParticipant::find($id);

            if ($participant && !$participant->isApproved) {
                $participant->delete();

                return response()->json(['message' => 'Participant rejected successfully.']);
            }

            return response()->json(['message' => 'Participant not found or already processed.'], 404);
        }

        return response()->json(['message' => 'Unauthorized'], 403);
    }
}
