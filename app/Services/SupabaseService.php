<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SupabaseService
{
    protected ?string $url;
    protected ?string $apiKey;
    protected ?string $serviceRoleKey;
    protected ?string $table;
    protected bool $verifySsl;

    public function __construct()
    {
        $this->url = config('supabase.url') ? rtrim(config('supabase.url'), '/') : null;
        $this->apiKey = config('supabase.anon_key');
        $this->serviceRoleKey = config('supabase.service_role_key');
        $this->table = null;
        $this->verifySsl = (bool) config('supabase.verify_ssl', true);
    }

    /**
     * Query the database using Supabase REST API
     */
    public function from(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Select data from table
     */
    public function select($columns = '*')
    {
        $response = Http::withOptions(['verify' => $this->verifySsl])->withHeaders([
            'apikey' => $this->apiKey,
            'Authorization' => "Bearer {$this->apiKey}",
        ])->get("{$this->url}/rest/v1/{$this->table}", [
            'select' => $columns,
        ]);

        return $response->json();
    }

    /**
     * Insert data into table
     */
    public function insert(array $data)
    {
        $response = Http::withOptions(['verify' => $this->verifySsl])->withHeaders([
            'apikey' => $this->apiKey,
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
            'Prefer' => 'return=representation',
        ])->post("{$this->url}/rest/v1/{$this->table}", $data);

        return $response->json();
    }

    /**
     * Update data in table
     */
    public function update(array $data, array $conditions = [])
    {
        $query = http_build_query($conditions);
        
        $response = Http::withOptions(['verify' => $this->verifySsl])->withHeaders([
            'apikey' => $this->apiKey,
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
            'Prefer' => 'return=representation',
        ])->patch("{$this->url}/rest/v1/{$this->table}?{$query}", $data);

        return $response->json();
    }

    /**
     * Delete data from table
     */
    public function delete(array $conditions = [])
    {
        $query = http_build_query($conditions);
        
        $response = Http::withOptions(['verify' => $this->verifySsl])->withHeaders([
            'apikey' => $this->apiKey,
            'Authorization' => "Bearer {$this->apiKey}",
            'Prefer' => 'return=representation',
        ])->delete("{$this->url}/rest/v1/{$this->table}?{$query}");

        return $response->json();
    }

    /**
     * Privileged delete with service role key
     */
    public function deletePrivileged(array $conditions = [])
    {
        $query = http_build_query($conditions);

        $response = Http::withOptions(['verify' => $this->verifySsl])->withHeaders([
            'apikey' => $this->serviceRoleKey ?: $this->apiKey,
            'Authorization' => 'Bearer ' . ($this->serviceRoleKey ?: $this->apiKey),
            'Prefer' => 'return=representation',
        ])->delete("{$this->url}/rest/v1/{$this->table}?{$query}");

        return $response->json();
    }

    /**
     * Privileged insert with service role key and optional ON CONFLICT target
     */
    public function insertPrivileged(array $data, ?string $onConflict = null)
    {
        $url = "{$this->url}/rest/v1/{$this->table}";
        if ($onConflict) {
            $url .= '?on_conflict=' . urlencode($onConflict);
        }

        $response = Http::withOptions(['verify' => $this->verifySsl])->withHeaders([
            'apikey' => $this->serviceRoleKey ?: $this->apiKey,
            'Authorization' => 'Bearer ' . ($this->serviceRoleKey ?: $this->apiKey),
            'Content-Type' => 'application/json',
            'Prefer' => 'return=representation, resolution=merge-duplicates',
        ])->post($url, $data);

        return $response->json();
    }

    /**
     * Privileged update with service role key
     */
    public function updatePrivileged(array $data, array $conditions = [])
    {
        $query = http_build_query($conditions);

        $response = Http::withOptions(['verify' => $this->verifySsl])->withHeaders([
            'apikey' => $this->serviceRoleKey ?: $this->apiKey,
            'Authorization' => 'Bearer ' . ($this->serviceRoleKey ?: $this->apiKey),
            'Content-Type' => 'application/json',
            'Prefer' => 'return=representation',
        ])->patch("{$this->url}/rest/v1/{$this->table}?{$query}", $data);

        return $response->json();
    }

    /**
     * Upload file to Supabase Storage
     */
    public function uploadFile(string $bucket, string $path, $file)
    {
        $response = Http::withHeaders([
            'apikey' => $this->serviceRoleKey,
            'Authorization' => "Bearer {$this->serviceRoleKey}",
        ])->withBody($file, 'application/octet-stream')
          ->post("{$this->url}/storage/v1/object/{$bucket}/{$path}");

        return $response->json();
    }

    /**
     * Get public URL for a file
     */
    public function getFileUrl(string $bucket, string $path): string
    {
        return "{$this->url}/storage/v1/object/public/{$bucket}/{$path}";
    }
}