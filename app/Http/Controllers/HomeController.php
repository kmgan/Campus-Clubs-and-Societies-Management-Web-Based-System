<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Club;
use Illuminate\Http\Request;
use DateTime;

class HomeController extends Controller
{
    public function index()
    {
        // Fetch all clubs grouped by category
        $clubs = Club::all()->groupBy('category');

        // Get the first day of the next month
        $nextMonth = new DateTime('first day of next month');

        // Fetch events happening this month
        $thisMonthEvents = Event::where('date', '>=', now()->format('Y-m-d'))
                                ->where('date', '<', $nextMonth->format('Y-m-d'))
                                ->orderBy('date', 'asc')
                                ->get();

        // Fetch future events
        $futureEvents = Event::where('date', '>=', $nextMonth->format('Y-m-d'))
                             ->orderBy('date', 'asc')
                             ->get();

        // Pass data to the view
        return view('home', compact('clubs', 'thisMonthEvents', 'futureEvents'));
    }
}
