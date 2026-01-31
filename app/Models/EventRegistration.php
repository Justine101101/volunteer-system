<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_id',
        'status',
    ];

    /**
     * Get the user who registered.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the event being registered for.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
