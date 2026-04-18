<?php

namespace Tests\Feature\Events;

use App\Models\User;
use App\Services\DatabaseQueryService;
use App\Services\SupabaseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventShowVolunteerCountTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_event_show_uses_privileged_event_fetch_and_displays_registration_counts(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $eventId = '11111111-1111-1111-1111-111111111111';

        $this->mock(SupabaseService::class);

        $this->mock(DatabaseQueryService::class, function ($mock) use ($eventId) {
            $mock->shouldReceive('getEventByIdWithRegistrationsPrivileged')
                ->once()
                ->with($eventId)
                ->andReturn([
                    'id' => $eventId,
                    'title' => 'Test Event',
                    'description' => 'Test Description',
                    'event_date' => '2030-01-01',
                    'event_time' => '10:00:00',
                    'event_end_time' => '12:00:00',
                    'location' => 'Test Location',
                    'event_status' => 'active',
                    'registrations' => [
                        [
                            'id' => 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
                            'registration_status' => 'approved',
                            'created_at' => '2030-01-01T00:00:00Z',
                            'user' => ['name' => 'Approved User', 'email' => 'approved@example.com'],
                        ],
                        [
                            'id' => 'bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb',
                            'registration_status' => 'pending',
                            'created_at' => '2030-01-01T00:00:00Z',
                            'user' => ['name' => 'Pending User', 'email' => 'pending@example.com'],
                        ],
                    ],
                ]);

            $mock->shouldReceive('getEventById')
                ->never();

            $mock->shouldReceive('getApprovedRegistrationCountForEventPrivileged')
                ->once()
                ->with($eventId)
                ->andReturn(1);

            // Used by EventController@show to resolve the current user's Supabase ID
            $mock->shouldReceive('getUserByEmail')
                ->andReturn(['id' => 'cccccccc-cccc-cccc-cccc-cccccccccccc']);
        });

        $response = $this->actingAs($admin)->get("/events/{$eventId}");

        $response->assertOk();
        $response->assertSee('Registered Volunteers (2)');
        $response->assertSee('Approved: 1');
    }
}

