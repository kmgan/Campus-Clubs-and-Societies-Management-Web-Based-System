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
        $clubs = $this->getFilteredClubs($request->input('keyword'), $request->input('category_id'));

        // Fetch categories for the dropdown
        $categories = ClubCategory::orderBy('name', 'asc')->get(); // Fetch all categories

        // Fetch events for this month and future events
        [$thisMonthEvents, $futureEvents] = $this->getEvents();

        // Pass data to the view
        return view('website.home', compact('clubs', 'categories', 'thisMonthEvents', 'futureEvents'));
    }

    private function getFilteredClubs($keyword, $categoryId)
    {
        $clubsQuery = Club::query();

        // Filter by keyword
        if ($keyword) {
            $clubsQuery->where('name', 'like', '%' . $keyword . '%');
        }

        // Filter by category ID
        if ($categoryId && $categoryId !== 'All categories') {
            $clubsQuery->where('category_id', $categoryId);
        }

        // Always order clubs by name
        $clubsQuery->orderBy('name', 'asc');

        // Get filtered clubs grouped by category_id
        $clubs = $clubsQuery->get()->groupBy('category_id');

        // Get categories sorted by name
        $categories = ClubCategory::orderBy('name', 'asc')->get()->keyBy('id');

        // If a specific category is searched, only include that category
        if ($categoryId && $categoryId !== 'All categories') {
            return collect([$categoryId => $clubs->get($categoryId, collect())]);
        }

        // Only include categories that have clubs after filtering
        return collect($categories->keys()
            ->mapWithKeys(function ($categoryId) use ($clubs) {
                $categoryClubs = $clubs->get($categoryId, collect());
                // Only include the category if it has clubs
                return $categoryClubs->isNotEmpty()
                    ? [$categoryId => $categoryClubs]
                    : [];
            }));
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
        $club = Club::with(['club_gallery', 'members'])->findOrFail($id);

        // Calculate the number of members
        $memberCount = $club->members()->count();

        // Assuming you have a relation 'events' in your Club model
        $eventsOrganized = $club->events()->count();

        // Get the year the club was established (assuming there is a `created_at` or a similar field)
        $establishedDate = $club->created_at->format('m/Y');

        return view('website.club', compact('club', 'memberCount', 'eventsOrganized', 'establishedDate'));
    }
}
