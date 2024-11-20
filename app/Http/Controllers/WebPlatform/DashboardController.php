<?php

namespace App\Http\Controllers\WebPlatform;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClubMember;

class DashboardController extends Controller
{
    public function showDashboardPage()
    {
        return view('webplatform.dashboard');
    }


}
