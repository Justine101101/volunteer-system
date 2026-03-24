<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\DatabaseQueryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct(private DatabaseQueryService $queryService)
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        $stats = [
            'total_users' => User::count(),
            'total_admins' => User::whereIn('role', ['admin', 'president'])->count(),
            'total_volunteers' => User::where('role', 'volunteer')->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Display the specified user (profile/details page).
     */
    public function show(User $user)
    {
        // Try to resolve this user in Supabase by email to show event participation
        $participation = [];
        if ($user->email) {
            $supabaseUser = $this->queryService->getUserByEmail($user->email);
            $supabaseUserId = is_array($supabaseUser) ? ($supabaseUser['id'] ?? null) : null;

            if ($supabaseUserId) {
                $regs = $this->queryService->getEventRegistrationsForUser($supabaseUserId);

                if (is_array($regs) && !isset($regs['error'])) {
                    foreach ($regs as $reg) {
                        $eventId = $reg['event_id'] ?? null;
                        if (!$eventId) {
                            continue;
                        }

                        // Fetch event details for display (small list per user, acceptable to fetch individually)
                        $event = $this->queryService->getEventByIdPrivileged($eventId);
                        // Supabase client may wrap result inside a "data" key; normalize it
                        if (is_array($event) && isset($event['data']) && is_array($event['data'])) {
                            $event = $event['data'];
                        }
                        // Normalize possible [0 => record] shape into a single record
                        if (is_array($event) && isset($event[0]) && is_array($event[0])) {
                            $event = $event[0];
                        }
                        // Skip if the event lookup failed (prevents "Untitled event" spam)
                        if (!is_array($event) || isset($event['error'])) {
                            continue;
                        }
                        $participation[] = [
                            'registration_id' => $reg['id'] ?? null,
                            'event_id' => $eventId,
                            'title' => is_array($event) ? ($event['title'] ?? 'Untitled event') : 'Untitled event',
                            'event_date' => is_array($event) && isset($event['event_date']) ? $event['event_date'] : null,
                            'registration_status' => strtolower($reg['registration_status'] ?? 'pending'),
                            'created_at' => $reg['created_at'] ?? null,
                        ];
                    }
                }
            }
        }

        return view('admin.users.show', [
            'user' => $user,
            'participation' => $participation,
        ]);
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', Rule::in(['admin', 'president', 'volunteer'])],
            'notification_pref' => ['nullable', 'boolean'],
            'dark_mode' => ['nullable', 'boolean'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'notification_pref' => $validated['notification_pref'] ?? true,
            'dark_mode' => $validated['dark_mode'] ?? false,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', Rule::in(['admin', 'president', 'volunteer'])],
            'notification_pref' => ['nullable', 'boolean'],
            'dark_mode' => ['nullable', 'boolean'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->notification_pref = $validated['notification_pref'] ?? $user->notification_pref;
        $user->dark_mode = $validated['dark_mode'] ?? $user->dark_mode;

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}

