<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    // Supabase uses UUIDs for `public.users.id`, but Eloquent defaults to integer keys.
    // Without this, Laravel can cast the UUID to an integer (e.g. "69..."=>69), which
    // then breaks queries with: "invalid input syntax for type uuid".
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'notification_pref',
        'dark_mode',
        'two_factor_enabled',
        'email_verified_at',
        'photo_url',
        'google_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'notification_pref' => 'boolean',
            'dark_mode' => 'boolean',
            'two_factor_enabled' => 'boolean',
        ];
    }

    /**
     * Get the events created by the user.
     */
    public function events()
    {
        return $this->hasMany(Event::class, 'created_by');
    }

    /**
     * Get the event registrations for the user.
     */
    public function eventRegistrations()
    {
        return $this->hasMany(EventRegistration::class);
    }

    /**
     * Get the messages sent by the user.
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Get the messages received by the user.
     */
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /**
     * Check if user is superadmin.
     *
     * @deprecated Super Admin role has been removed. Treat as Admin for backwards compatibility.
     */
    public function isSuperAdmin()
    {
        return $this->isAdmin();
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin()
    {
        // In Supabase we store elevated users as `superadmin`, but middleware treats it as admin.
        return $this->role === 'admin' || $this->role === 'superadmin';
    }

    /**
     * Check if user is admin or superadmin.
     */
    public function isAdminOrSuperAdmin()
    {
        // Superadmin removed; admins and presidents share elevated access.
        return $this->role === 'admin'
            || $this->role === 'superadmin'
            || $this->role === 'president';
    }

    /**
     * Check if user is officer.
     * @deprecated Officer role has been removed. This method always returns false.
     */
    public function isOfficer()
    {
        return false;
    }

    /**
     * Check if user is volunteer.
     */
    public function isVolunteer()
    {
        return $this->role === 'volunteer';
    }

    /**
     * Check if user is president.
     */
    public function isPresident()
    {
        return $this->role === 'president';
    }
}
