<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use App\Services\DatabaseQueryService;

class ContactController extends Controller
{
    public function __construct(private DatabaseQueryService $queryService)
    {
    }
    public function index()
    {
        return view('contact');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:5000',
        ]);

        $contact = Contact::create([
            'name' => strip_tags($request->name),
            'email' => $request->email,
            'message' => strip_tags($request->message),
        ]);

        // Write-through to Supabase
        $this->queryService->upsertContact([
            'name' => $contact->name,
            'email' => $contact->email,
            'message' => $contact->message,
        ]);

        return redirect()->route('contact')->with('success', 'Thank you for your message! We will get back to you soon.');
    }
}
