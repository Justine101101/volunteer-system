<?php

return [
    'url' => env('SUPABASE_URL'),
    'anon_key' => env('SUPABASE_ANON_KEY'),
    'service_role_key' => env('SUPABASE_SERVICE_ROLE_KEY'),
    'bucket_name' => env('SUPABASE_BUCKET_NAME', 'volunteer-portal'),
    'verify_ssl' => env('SUPABASE_VERIFY_SSL', true),
];