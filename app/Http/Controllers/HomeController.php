<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Event;

class HomeController extends Controller
{
    public function index()
    {
        $members = Member::take(6)->get();
        $events = Event::where('date', '>=', now())
            ->orderBy('date', 'asc')
            ->take(5)
            ->get();
        return view('home', compact('members', 'events'));
    }
}
