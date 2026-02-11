<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function __construct()
    {
        // Restrict to superadmins by default; adjust middleware as needed
        $this->middleware(['auth', 'role:superadmin']);
    }

    /**
     * Display a paginated list of audit logs.
     */
    public function index(Request $request)
    {
        $query = AuditLog::with('user')
            ->orderByDesc('created_at');

        // Optional filtering by action or resource_type
        if ($action = $request->get('action')) {
            $query->where('action', $action);
        }

        if ($resourceType = $request->get('resource_type')) {
            $query->where('resource_type', $resourceType);
        }

        $logs = $query->paginate(25)->withQueryString();

        return view('admin.audit-logs.index', [
            'logs' => $logs,
        ]);
    }
}

