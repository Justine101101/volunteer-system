<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'date',
        'time',
        'location',
        'photo_url',
        'created_by',
    ];

    protected $with = ['creator'];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            // Store as TIME column; keep as string to avoid invalid datetime casting
            'time' => 'string',
        ];
    }

    /**
     * Get the user who created the event.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the event registrations for this event.
     */
    public function registrations()
    {
        return $this->hasMany(EventRegistration::class);
    }

    /**
     * Get the users registered for this event.
     */
    public function registeredUsers()
    {
        return $this->belongsToMany(User::class, 'event_registrations')
                    ->withPivot('status')
                    ->withTimestamps();
    }
}
