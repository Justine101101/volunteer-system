<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use App\Services\DatabaseQueryService;
use Illuminate\Support\Facades\Log;

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

        // Single Source of Truth: Write only to Supabase
        $result = $this->queryService->upsertContact([
            'name' => strip_tags($request->name),
            'email' => $request->email,
            'message' => strip_tags($request->message),
            'subject' => 'Contact Form Submission',
        ]);

        if (isset($result['error'])) {
            Log::error('Failed to save contact in Supabase: ' . ($result['error'] ?? 'Unknown error'));
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to send message. Please try again.');
        }

        return redirect()->route('contact')->with('success', 'Thank you for your message! We will get back to you soon.');
    }
}
