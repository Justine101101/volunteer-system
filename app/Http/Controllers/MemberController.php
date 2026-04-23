<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\DatabaseQueryService;
use Illuminate\Support\Facades\Log;

class MemberController extends Controller
{
    public function __construct(private DatabaseQueryService $queryService)
    {
        // Only require auth for create, edit, update, destroy
        $this->middleware('auth')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    /**
     * Display a listing of members.
     * Primary Database: Supabase
     */
    public function index()
    {
        $result = $this->queryService->getMembers(1, 1000); // Get all members

        $hasErrorShape = is_array($result)
            && !array_is_list($result)
            && (isset($result['error']) || isset($result['message']) || isset($result['code']));

        if ($hasErrorShape) {
            $errorMessage = $result['error'] ?? $result['message'] ?? 'Unknown error';
            Log::error('Failed to fetch members: ' . $errorMessage, ['supabase_response' => $result]);
            $members = [];
        } else {
            $members = (is_array($result) && array_is_list($result)) ? $result : [];

            // Transform Supabase response to match expected format
            $members = array_map(function ($member) {
                return (object) [
                    'id' => $member['id'] ?? null,
                    'name' => $member['name'] ?? '',
                    'role' => $member['role'] ?? '',
                    'photo_url' => $member['photo_url'] ?? null,
                    'order' => $member['order'] ?? 0,
                    'email' => $member['email'] ?? null,
                    'phone' => $member['phone'] ?? null,
                ];
            }, $members);
        }
        
        return view('members.index', compact('members'));
    }

    public function create()
    {
        return view('members.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'order' => 'nullable|integer|min:0',
        ]);

        $photoUrl = null;
        
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $filename = Str::slug($request->name) . '-' . time() . '.' . $photo->getClientOriginalExtension();
            $path = $photo->storeAs('members', $filename, 'public');
            $photoUrl = Storage::url($path);
        }

        // Some Supabase schemas require members.email to be non-null/unique.
        // Generate a deterministic placeholder when admin does not provide one.
        $memberEmail = $request->email;
        if (empty($memberEmail)) {
            $memberEmail = Str::slug($request->name) . '-' . now()->timestamp . '@members.local';
        }

        // Single Source of Truth: Write only to Supabase
        $result = $this->queryService->upsertMember([
            'name' => $request->name,
            'email' => $memberEmail,
            'role' => $request->role,
            'photo_url' => $photoUrl,
            'order' => $request->order ?? 0,
        ]);

        if (isset($result['error'])) {
            Log::error('Failed to create member in Supabase: ' . ($result['error'] ?? 'Unknown error'));
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create member. Please try again.');
        }

        return redirect()->route('members.index')->with('success', 'Member created successfully!');
    }

    /**
     * Show the form for editing the specified member.
     * Primary Database: Supabase
     * Note: Route model binding still uses MySQL Member model for compatibility
     */
    public function edit(string $memberId)
    {
        $supabaseMember = $this->queryService->getMemberById($memberId);

        if (!$supabaseMember || isset($supabaseMember['error'])) {
            abort(404, 'Member not found');
        }

        // Transform Supabase response to object for view compatibility
        $memberData = (object) [
            'id' => $supabaseMember['id'] ?? null,
            'name' => $supabaseMember['name'] ?? '',
            'role' => $supabaseMember['role'] ?? '',
            'photo_url' => $supabaseMember['photo_url'] ?? null,
            'order' => $supabaseMember['order'] ?? 0,
            'email' => $supabaseMember['email'] ?? null,
        ];

        return view('members.edit', ['member' => $memberData]);
    }

    public function update(Request $request, string $memberId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'order' => 'nullable|integer|min:0',
        ]);

        $existingMember = $this->queryService->getMemberById($memberId);
        if (!$existingMember || isset($existingMember['error'])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Member not found in database. Please refresh and try again.');
        }

        $photoUrl = $existingMember['photo_url'] ?? null;
        
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($photoUrl) {
                $oldPath = str_replace('/storage/', '', $photoUrl);
                Storage::disk('public')->delete($oldPath);
            }
            
            // Upload new photo
            $photo = $request->file('photo');
            $filename = Str::slug($request->name) . '-' . time() . '.' . $photo->getClientOriginalExtension();
            $path = $photo->storeAs('members', $filename, 'public');
            $photoUrl = Storage::url($path);
        }

        // Single Source of Truth: Update only in Supabase
        $result = $this->queryService->updateMember($memberId, [
            'name' => $request->name,
            'role' => $request->role,
            'photo_url' => $photoUrl,
            'order' => $request->order ?? ($existingMember['order'] ?? 0),
        ]);

        if (isset($result['error'])) {
            Log::error('Failed to update member in Supabase: ' . ($result['error'] ?? 'Unknown error'));
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update member. Please try again.');
        }

        return redirect()->route('members.index')->with('success', 'Member updated successfully!');
    }

    public function destroy(string $memberId)
    {
        $existingMember = $this->queryService->getMemberById($memberId);
        if (!$existingMember || isset($existingMember['error'])) {
            return redirect()->back()->with('error', 'Member not found in database.');
        }

        // Delete photo if exists
        if (!empty($existingMember['photo_url'])) {
            $oldPath = str_replace('/storage/', '', $existingMember['photo_url']);
            Storage::disk('public')->delete($oldPath);
        }
        
        // Single Source of Truth: Delete only from Supabase
        $result = $this->queryService->deleteMember($memberId);
        if (isset($result['error'])) {
            Log::error('Failed to delete member from Supabase: ' . ($result['error'] ?? 'Unknown error'));
            return redirect()->back()->with('error', 'Failed to delete member. Please try again.');
        }
        
        return redirect()->route('members.index')->with('success', 'Member deleted successfully!');
    }
}
