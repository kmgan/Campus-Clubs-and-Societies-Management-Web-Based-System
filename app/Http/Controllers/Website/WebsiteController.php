<?php

namespace App\Http\Controllers\website;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Club;
use App\Models\ClubCategory;
use App\Models\EventParticipant;
use Illuminate\Http\Request;
use DateTime;

class WebsiteController extends Controller
{
    public function index(Request $request)
    {
        // Get clubs with search and category filters
        $clubs = $this->getFilteredClubs($request->input('keyword'), $request->input('category_name'));

        // Fetch categories for the dropdown
        $categories = ClubCategory::all();

        // Fetch events for this month and future events
        [$thisMonthEvents, $futureEvents] = $this->getEvents();

        // Pass data to the view
        return view('website.home', compact('clubs', 'categories', 'thisMonthEvents', 'futureEvents'));
    }

    private function getFilteredClubs($keyword, $categoryName)
    {
        $clubsQuery = Club::query();

        if ($keyword) {
            $clubsQuery->where('name', 'like', '%' . $keyword . '%');
        }

        if ($categoryName && $categoryName !== 'All categories') {
            $clubsQuery->where('category', $categoryName);
        }

        return $clubsQuery->get()->groupBy('category');
    }

    private function getEvents()
    {
        $nextMonth = new DateTime('first day of next month');
        $thisMonthEvents = Event::where('date', '>=', now()->format('Y-m-d'))
            ->where('date', '<', $nextMonth->format('Y-m-d'))
            ->orderBy('date', 'asc')
            ->get();

        $futureEvents = Event::where('date', '>=', $nextMonth->format('Y-m-d'))
            ->orderBy('date', 'asc')
            ->get();

        return [$thisMonthEvents, $futureEvents];
    }

    public function showEventDetails($id)
    {
        $event = Event::withCount('participants')->findOrFail($id);
        return view('website.event', compact('event'));
    }

    public function showClubDetails($id)
    {
        return view('website.club', compact('club'));
    }
}
