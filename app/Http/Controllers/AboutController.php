<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;

class AboutController extends Controller
{
    public function index()
    {
        $officers = Member::whereIn('role', ['President', 'First Vice President', 'Second Vice President', 'Secretary', 'Treasurer'])
            ->orderBy('order')
            ->get();
        return view('about', compact('officers'));
    }
}
