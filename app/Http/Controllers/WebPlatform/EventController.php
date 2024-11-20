<?php

namespace App\Http\Controllers\WebPlatform;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventParticipant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function showEventPage(Request $request)
    {
        // Get filters from request
        $eventStatus = $request->input('event_status', 'upcoming');
        $participationStatus = $request->input('participation_status', 'all');

        // Base query to get events
        $query = Event::query();

        // Filter by event status (upcoming, completed, all)
        if ($eventStatus == 'upcoming') {
            $query->where('date', '>=', Carbon::now())->orderBy('date', 'asc');
        } elseif ($eventStatus == 'completed') {
            $query->where('date', '<', Carbon::now())->orderBy('date', 'desc');
        }

        // Filter by participation status (participating, available, all)
        /** @var \App\Models\User */
        $user = auth()->user();  
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
}
