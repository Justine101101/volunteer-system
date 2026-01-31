<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin or superadmin user (they are treated as equivalent)
        $superadmin = User::whereIn('role', ['superadmin', 'admin'])->first();
        
        if (!$superadmin) {
            $superadmin = User::first();
        }

        $events = [
            [
                'title' => 'Community Health Fair',
                'description' => 'Join us for a comprehensive health fair providing free medical check-ups, dental services, and health education to the community. We need volunteers to help with registration, guiding patients, and distributing health materials.',
                'date' => Carbon::now()->addDays(7),
                'time' => '08:00',
                'location' => 'Baguio City Health Center',
                'created_by' => $superadmin->id,
            ],
            [
                'title' => 'Environmental Cleanup Drive',
                'description' => 'Help us keep our beautiful Cordillera region clean! We will be cleaning up Burnham Park and surrounding areas. Bring your own gloves and water bottles. Trash bags and tools will be provided.',
                'date' => Carbon::now()->addDays(14),
                'time' => '07:00',
                'location' => 'Burnham Park, Baguio City',
                'created_by' => $superadmin->id,
            ],
            [
                'title' => 'Senior Citizens Feeding Program',
                'description' => 'Volunteer to serve meals and provide companionship to our elderly community members. This is a monthly program that brings joy and nutrition to senior citizens in need.',
                'date' => Carbon::now()->addDays(21),
                'time' => '10:00',
                'location' => 'Senior Citizens Center, La Trinidad',
                'created_by' => $superadmin->id,
            ],
            [
                'title' => 'Children\'s Reading Program',
                'description' => 'Help children develop their reading skills and love for books. Volunteers will read stories, help with homework, and organize educational activities for kids aged 5-12.',
                'date' => Carbon::now()->addDays(28),
                'time' => '14:00',
                'location' => 'Baguio City Library',
                'created_by' => $superadmin->id,
            ],
            [
                'title' => 'Disaster Preparedness Workshop',
                'description' => 'Learn and teach essential disaster preparedness skills to community members. Topics include emergency response, first aid, and evacuation procedures. Training materials will be provided.',
                'date' => Carbon::now()->addDays(35),
                'time' => '09:00',
                'location' => 'Cordillera Regional Office',
                'created_by' => $superadmin->id,
            ],
            [
                'title' => 'Mountain Trail Maintenance',
                'description' => 'Help maintain hiking trails in the Cordillera mountains. This includes clearing debris, marking paths, and ensuring safety for hikers. Bring appropriate outdoor gear and water.',
                'date' => Carbon::now()->addDays(42),
                'time' => '06:00',
                'location' => 'Mount Pulag Trail Head',
                'created_by' => $superadmin->id,
            ],
        ];

        foreach ($events as $event) {
            Event::create($event);
        }
    }
}
