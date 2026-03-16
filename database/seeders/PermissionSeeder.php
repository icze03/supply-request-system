<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\RolePermission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Define every page in the system ──────────────────────────────
        $pages = [
            // slug            name               route_pattern              url_prefix            sort
            ['dashboard',    'Dashboard',        'dashboard',               '/dashboard',           1],
            ['releases',     'Releases',         'admin.releases.*',        '/admin/releases',      2],
            ['supplies',     'Supplies',         'admin.supplies.*',        '/admin/supplies',      3],
            ['low_stock',    'Low Stock',        'admin.low-stock.*',       '/admin/low-stock',     4],
            ['audit_trail',  'Audit Trail',      'admin.audit-logs*',       '/admin/audit-logs',    5],
            ['users',        'Users',            'admin.users.*',           '/admin/users',         6],
            ['departments',  'Departments',      'admin.departments.*',     '/admin/departments',   7],
            ['approvals',    'Approvals',        'manager.approvals.*',     '/manager/approvals',   8],
            ['catalog',      'Catalog',          'employee.catalog*',       '/employee/catalog',    9],
            ['my_requests',  'My Requests',      'employee.requests.*',     '/employee/requests',  10],
        ];

        foreach ($pages as [$slug, $name, $routePattern, $urlPrefix, $sort]) {
            Permission::updateOrCreate(
                ['slug' => $slug],
                [
                    'name'          => $name,
                    'route_pattern' => $routePattern,
                    'url_prefix'    => $urlPrefix,
                    'sort_order'    => $sort,
                ]
            );
        }

        // ── 2. Default permissions per role ─────────────────────────────────
        //      Super Admin always has everything — no rows needed.
        $defaults = [
            'admin' => [
                'dashboard', 'releases', 'supplies', 'low_stock',
                'audit_trail', 'users', 'departments',
            ],
            'manager' => [
                'dashboard', 'approvals',
            ],
            'hr_manager' => [                    
            'dashboard', 'users', 'departments',
            ],
            'employee' => [
                'dashboard', 'catalog', 'my_requests',
            ],
        ];

        foreach ($defaults as $role => $slugs) {
            // Clear existing so we can re-run seeder safely
            RolePermission::where('role', $role)->delete();

            foreach ($slugs as $slug) {
                $permission = Permission::where('slug', $slug)->first();
                if ($permission) {
                    RolePermission::create([
                        'role'          => $role,
                        'permission_id' => $permission->id,
                    ]);
                }
            }
        }

        $this->command->info('✓ Permissions seeded');
        $this->command->info('  Admin    : dashboard, releases, supplies, low_stock, audit_trail, users, departments');
        $this->command->info('  Manager  : dashboard, approvals');
        $this->command->info('  Employee : dashboard, catalog, my_requests');
    }
}
