<?php

namespace App\Http\Controllers\website;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Club;
use App\Models\ClubCategory;
use Illuminate\Http\Request;
use DateTime;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Fetch search and category filter input from the request
        $keyword = $request->input('keyword');
        $categoryName = $request->input('category_name'); 

        // Build a query to fetch clubs with optional filtering
        $clubsQuery = Club::query();

        // Apply keyword search if provided
        if ($keyword) {
            $clubsQuery->where('name', 'like', '%' . $keyword . '%');
        }

        // Apply category filter if provided and not 'All categories'
        if ($categoryName && $categoryName !== 'All categories') {
            $clubsQuery->where('category', $categoryName);
        }

        // Fetch clubs grouped by category after applying filters
        $clubs = $clubsQuery->get()->groupBy('category');

        // Fetch all categories for the dropdown
        $categories = ClubCategory::all();

        // Events logic remains the same
        $nextMonth = new DateTime('first day of next month');
        $thisMonthEvents = Event::where('date', '>=', now()->format('Y-m-d'))
                                ->where('date', '<', $nextMonth->format('Y-m-d'))
                                ->orderBy('date', 'asc')
                                ->get();

        $futureEvents = Event::where('date', '>=', $nextMonth->format('Y-m-d'))
                             ->orderBy('date', 'asc')
                             ->get();

        // Pass data to the view
        return view('website.home', compact('clubs', 'categories', 'thisMonthEvents', 'futureEvents', 'keyword', 'categoryName'));
    }
}

