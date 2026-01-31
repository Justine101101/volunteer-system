<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MemberController extends Controller
{
    public function __construct()
    {
        // Only require auth for create, edit, update, destroy
        $this->middleware('auth')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        $members = Member::orderBy('order')->orderBy('name')->get();
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

        Member::create([
            'name' => $request->name,
            'role' => $request->role,
            'photo_url' => $photoUrl,
            'order' => $request->order ?? 0,
        ]);

        return redirect()->route('members.index')->with('success', 'Member created successfully!');
    }

    public function edit(Member $member)
    {
        return view('members.edit', compact('member'));
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

        $member->update([
            'name' => $request->name,
            'role' => $request->role,
            'photo_url' => $photoUrl,
            'order' => $request->order ?? $member->order ?? 0,
        ]);

        return redirect()->route('members.index')->with('success', 'Member updated successfully!');
    }

    public function destroy(Member $member)
    {
        // Delete photo if exists
        if ($member->photo_url) {
            $oldPath = str_replace('/storage/', '', $member->photo_url);
            Storage::disk('public')->delete($oldPath);
        }
        
        $member->delete();
        
        return redirect()->route('members.index')->with('success', 'Member deleted successfully!');
    }
}
