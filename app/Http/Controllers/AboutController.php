<?php

namespace App\Http\Controllers;

use App\Services\DatabaseQueryService;

class AboutController extends Controller
{
    private const OFFICER_ROLES = [
        'President',
        'First Vice President',
        'Second Vice President',
        'Secretary',
        'Treasurer',
    ];

    public function __construct(private DatabaseQueryService $queryService)
    {
    }

    public function index()
    {
        $officers = $this->queryService->getMembersCollection(1, 1000)
            ->whereIn('role', self::OFFICER_ROLES)
            ->sortBy('order')
            ->values();

        return view('about', compact('officers'));
    }
}
