<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Services\DatabaseQueryService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function __construct(private DatabaseQueryService $queryService)
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Display a paginated list of audit logs.
     */
    public function index(Request $request)
    {
        $page = max(1, (int) $request->get('page', 1));
        $perPage = 25;
        $filters = [
            'action' => (string) $request->get('action', ''),
            'resource_type' => (string) $request->get('resource_type', ''),
        ];

        // Prefer Supabase logs for cloud/shared visibility.
        $rows = $this->queryService->getAuditLogsPrivileged($page, $perPage, $filters);
        if (is_array($rows) && !isset($rows['error'])) {
            $total = $this->queryService->countAuditLogsPrivileged($filters);

            $items = collect($rows)->map(function ($row) {
                $row = is_array($row) ? $row : [];
                $supabaseUser = isset($row['user']) && is_array($row['user']) ? $row['user'] : null;
                $email = $supabaseUser['email'] ?? ($row['user_email'] ?? null);
                $name = $supabaseUser['name'] ?? null;

                $obj = (object) [
                    'created_at' => !empty($row['created_at']) ? \Carbon\Carbon::parse((string) $row['created_at']) : null,
                    'action' => $row['action'] ?? '',
                    'resource_type' => $row['resource_type'] ?? '',
                    'resource_id' => $row['resource_id'] ?? null,
                    'payload' => $row['payload'] ?? null,
                ];

                $obj->user = null;
                if (!empty($name) || !empty($email)) {
                    $obj->user = (object) [
                        'name' => $name ?: 'User',
                        'email' => $email,
                    ];
                }

                return $obj;
            });

            $logs = new LengthAwarePaginator(
                $items,
                $total,
                $perPage,
                $page,
                [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]
            );

            return view('admin.audit-logs.index', ['logs' => $logs]);
        }

        // Fallback: local table query (legacy/offline mode).
        $query = AuditLog::with('user')->orderByDesc('created_at');
        if ($filters['action'] !== '') {
            $query->where('action', $filters['action']);
        }
        if ($filters['resource_type'] !== '') {
            $query->where('resource_type', $filters['resource_type']);
        }

        $logs = $query->paginate($perPage)->withQueryString();

        return view('admin.audit-logs.index', [
            'logs' => $logs,
        ]);
    }
}

