<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
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
        
        if (isset($result['error'])) {
            Log::error('Failed to fetch members: ' . $result['error']);
            $members = [];
        } else {
            $members = is_array($result) ? $result : [];
            // Transform Supabase response to match expected format
            $members = array_map(function($member) {
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

        // Single Source of Truth: Write only to Supabase
        $result = $this->queryService->upsertMember([
            'name' => $request->name,
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
    public function edit(Member $member)
    {
        // Find the Supabase member by name
        $supabaseMember = $this->queryService->findMemberByName($member->name);

        if (!$supabaseMember) {
            // Fallback: try to get by ID if it's a UUID
            if (is_string($member->id) && strlen($member->id) > 10) {
                $supabaseMember = $this->queryService->getMemberById($member->id);
            }
        }

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
        ];

        return view('members.edit', ['member' => $memberData]);
    }

    public function update(Request $request, Member $member)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'order' => 'nullable|integer|min:0',
        ]);

        $photoUrl = $member->photo_url;
        
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($member->photo_url) {
                $oldPath = str_replace('/storage/', '', $member->photo_url);
                Storage::disk('public')->delete($oldPath);
            }
            
            // Upload new photo
            $photo = $request->file('photo');
            $filename = Str::slug($request->name) . '-' . time() . '.' . $photo->getClientOriginalExtension();
            $path = $photo->storeAs('members', $filename, 'public');
            $photoUrl = Storage::url($path);
        }

        // Get Supabase member ID - try to find by email or name
        // Since IDs don't match, we'll find by matching fields
        $members = $this->queryService->getMembers(1, 100, [
            'search' => $member->name ?? $member->email ?? '',
        ]);
        
        $supabaseMemberId = null;
        if (is_array($members) && !isset($members['error'])) {
            foreach ($members as $m) {
                if (isset($m['name']) && $m['name'] === $member->name) {
                    $supabaseMemberId = $m['id'] ?? null;
                    break;
                }
            }
        }

        if (!$supabaseMemberId) {
            Log::warning('Could not find Supabase member to update for local member ID: ' . $member->id);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Member not found in database. Please refresh and try again.');
        }

        // Single Source of Truth: Update only in Supabase
        $result = $this->queryService->updateMember($supabaseMemberId, [
            'name' => $request->name,
            'role' => $request->role,
            'photo_url' => $photoUrl,
            'order' => $request->order ?? $member->order ?? 0,
        ]);

        if (isset($result['error'])) {
            Log::error('Failed to update member in Supabase: ' . ($result['error'] ?? 'Unknown error'));
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update member. Please try again.');
        }

        return redirect()->route('members.index')->with('success', 'Member updated successfully!');
    }

    public function destroy(Member $member)
    {
        // Get Supabase member ID - try to find by email or name
        $members = $this->queryService->getMembers(1, 100, [
            'search' => $member->name ?? $member->email ?? '',
        ]);
        
        $supabaseMemberId = null;
        if (is_array($members) && !isset($members['error'])) {
            foreach ($members as $m) {
                if (isset($m['name']) && $m['name'] === $member->name) {
                    $supabaseMemberId = $m['id'] ?? null;
                    break;
                }
            }
        }

        // Delete photo if exists
        if ($member->photo_url) {
            $oldPath = str_replace('/storage/', '', $member->photo_url);
            Storage::disk('public')->delete($oldPath);
        }
        
        // Single Source of Truth: Delete only from Supabase
        if ($supabaseMemberId) {
            $result = $this->queryService->deleteMember($supabaseMemberId);
            if (isset($result['error'])) {
                Log::error('Failed to delete member from Supabase: ' . ($result['error'] ?? 'Unknown error'));
                return redirect()->back()->with('error', 'Failed to delete member. Please try again.');
            }
        } else {
            Log::warning('Could not find Supabase member to delete for local member ID: ' . $member->id);
        }
        
        return redirect()->route('members.index')->with('success', 'Member deleted successfully!');
    }
}
