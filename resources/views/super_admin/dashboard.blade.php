@extends('layouts.app')

@section('content')

{{-- Google Fonts: Sora (display) + DM Sans (body) --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap" rel="stylesheet">

<style>
    :root {
        --font-display: 'Sora', sans-serif;
        --font-body:    'DM Sans', sans-serif;

        --ink-900: #0f172a;
        --ink-700: #334155;
        --ink-500: #64748b;
        --ink-300: #cbd5e1;
        --ink-100: #f1f5f9;
        --ink-50:  #f8fafc;

        --indigo-vivid: #4f46e5;
        --indigo-soft:  #eef2ff;
        --indigo-mid:   #818cf8;

        --surface: #ffffff;
        --border:  #e2e8f0;

        --radius-card: 18px;
        --radius-pill: 999px;
        --shadow-card: 0 1px 3px 0 rgba(15,23,42,.06), 0 1px 2px -1px rgba(15,23,42,.04);
        --shadow-hero: 0 4px 32px 0 rgba(15,23,42,.22);
    }

    /* ── Base ─────────────────────────────────────────── */
    #sa-dash * { font-family: var(--font-body); box-sizing: border-box; }

    /* ── Hero ────────────────────────────────────────── */
    #sa-dash .hero {
        background: #ffffff;
        border-bottom: 1px solid #e2e8f0;
        padding: 36px 40px 40px;
        position: relative;
        overflow: hidden;
    }
    #sa-dash .hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(100deg, #f8fafc 0%, #f1f5f9 100%);
        pointer-events: none;
    }

    #sa-dash .hero-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 32px;
    }
    #sa-dash .hero-identity {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    #sa-dash .hero-avatar {
        width: 52px; height: 52px;
        border-radius: 14px;
        background: #eef2ff;
        border: 1px solid #c7d2fe;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    #sa-dash .hero-avatar svg { width: 24px; height: 24px; color: #4f46e5; }

    #sa-dash .hero-label {
        font-family: var(--font-body);
        font-size: 11px;
        font-weight: 500;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #94a3b8;
        margin-bottom: 2px;
    }
    #sa-dash .hero-name {
        font-family: var(--font-display);
        font-size: 22px;
        font-weight: 700;
        color: #0f172a;
        letter-spacing: -.4px;
        line-height: 1.15;
    }
    #sa-dash .hero-meta {
        font-size: 12px;
        color: #8e99a8;
        margin-top: 3px;
    }

    #sa-dash .hero-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: var(--indigo-vivid);
        color: #fff;
        font-family: var(--font-display);
        font-size: 13px;
        font-weight: 600;
        border-radius: 12px;
        text-decoration: none;
        transition: background .15s, transform .1s;
        box-shadow: 0 4px 18px rgba(79,70,229,.4);
        white-space: nowrap;
        flex-shrink: 0;
    }
    #sa-dash .hero-btn:hover { background: #4338ca; transform: translateY(-1px); }
    #sa-dash .hero-btn svg { width: 15px; height: 15px; }

    /* ── Stat Pills ───────────────────────────────────── */
    #sa-dash .stat-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
    }
    @media (max-width: 680px) {
        #sa-dash .stat-grid { grid-template-columns: repeat(2, 1fr); }
    }
    #sa-dash .stat-pill {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 16px 18px;
        box-shadow: 0 1px 3px rgba(15,23,42,.06);
        display: flex;
        align-items: center;
        gap: 12px;
    }
    #sa-dash .stat-icon {
        width: 36px; height: 36px;
        border-radius: 10px;
        background: #eef2ff;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    #sa-dash .stat-icon svg { width: 16px; height: 16px; color: #4f46e5; }
    #sa-dash .stat-value {
        font-family: var(--font-display);
        font-size: 26px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
        letter-spacing: -.5px;
    }
    #sa-dash .stat-label {
        font-size: 11px;
        font-weight: 500;
        color: #64748b;
        margin-top: 3px;
        letter-spacing: .03em;
    }

    /* ── Page body ────────────────────────────────────── */
    #sa-dash .page-body {
        max-width: 1280px;
        margin: 0 auto;
        padding: 28px 24px 48px;
    }

    #sa-dash .layout-grid {
        display: grid;
        grid-template-columns: 1fr 360px;
        gap: 22px;
        align-items: start;
    }
    @media (max-width: 1024px) {
        #sa-dash .layout-grid { grid-template-columns: 1fr; }
    }

    #sa-dash .col-main   { display: flex; flex-direction: column; gap: 22px; }
    #sa-dash .col-side   { display: flex; flex-direction: column; gap: 22px; }

    /* ── Alert ───────────────────────────────────────── */
    #sa-dash .alert-low {
        display: flex;
        align-items: center;
        gap: 12px;
        background: #fffbeb;
        border: 1px solid #fcd34d;
        border-radius: 14px;
        padding: 14px 18px;
    }
    #sa-dash .alert-low svg { width: 18px; height: 18px; color: #d97706; flex-shrink: 0; }
    #sa-dash .alert-low p {
        font-size: 13px;
        font-weight: 500;
        color: #92400e;
        flex: 1;
    }
    #sa-dash .alert-low a {
        font-size: 12px;
        font-weight: 700;
        color: #b45309;
        text-decoration: none;
        white-space: nowrap;
    }
    #sa-dash .alert-low a:hover { text-decoration: underline; }

    /* ── Card base ────────────────────────────────────── */
    #sa-dash .card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-card);
        box-shadow: var(--shadow-card);
        overflow: hidden;
    }
    #sa-dash .card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 22px;
        border-bottom: 1px solid var(--border);
    }
    #sa-dash .card-title {
        font-family: var(--font-display);
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .07em;
        text-transform: uppercase;
        color: var(--ink-700);
    }
    #sa-dash .card-link {
        font-size: 12px;
        font-weight: 600;
        color: var(--indigo-vivid);
        text-decoration: none;
        transition: color .15s;
    }
    #sa-dash .card-link:hover { color: #3730a3; }

    /* ── Role Breakdown ───────────────────────────────── */
    #sa-dash .role-row {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 13px 22px;
        border-bottom: 1px solid var(--ink-50);
        transition: background .12s;
    }
    #sa-dash .role-row:last-child { border-bottom: 0; }
    #sa-dash .role-row:hover { background: var(--ink-50); }

    #sa-dash .role-pill {
        font-family: var(--font-display);
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: var(--radius-pill);
        ring: 1px solid;
        white-space: nowrap;
        min-width: 88px;
        text-align: center;
        flex-shrink: 0;
    }
    #sa-dash .bar-track {
        flex: 1;
        background: var(--ink-100);
        border-radius: var(--radius-pill);
        height: 6px;
        overflow: hidden;
    }
    #sa-dash .bar-fill {
        height: 6px;
        border-radius: var(--radius-pill);
        transition: width .7s cubic-bezier(.4,0,.2,1);
    }
    #sa-dash .role-count {
        font-family: var(--font-display);
        font-size: 14px;
        font-weight: 700;
        color: var(--ink-900);
        min-width: 20px;
        text-align: right;
        flex-shrink: 0;
    }
    #sa-dash .role-pct {
        font-size: 11px;
        color: var(--ink-300);
        min-width: 32px;
        text-align: right;
        flex-shrink: 0;
    }

    /* ── Permission Coverage ──────────────────────────── */
    #sa-dash .perm-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
        padding: 20px 22px;
        border-bottom: 1px solid var(--border);
    }
    #sa-dash .perm-bar-label {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        margin-bottom: 8px;
    }
    #sa-dash .perm-role-name {
        font-family: var(--font-display);
        font-size: 12px;
        font-weight: 600;
    }
    #sa-dash .perm-fraction {
        font-size: 11px;
        color: var(--ink-500);
    }
    #sa-dash .perm-track {
        height: 6px;
        background: var(--ink-100);
        border-radius: var(--radius-pill);
        overflow: hidden;
    }
    #sa-dash .perm-fill {
        height: 6px;
        border-radius: var(--radius-pill);
    }

    /* ── Matrix table ─────────────────────────────────── */
    #sa-dash .matrix-wrap { padding: 20px 22px; }
    #sa-dash .matrix-label {
        font-size: 11px;
        font-weight: 600;
        letter-spacing: .07em;
        text-transform: uppercase;
        color: var(--ink-300);
        margin-bottom: 12px;
    }
    #sa-dash .matrix-table { width: 100%; border-collapse: collapse; font-size: 12px; }
    #sa-dash .matrix-table th {
        font-family: var(--font-display);
        font-weight: 600;
        font-size: 11px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--border);
        text-align: center;
    }
    #sa-dash .matrix-table th:first-child { text-align: left; }
    #sa-dash .matrix-table td {
        padding: 9px 6px;
        border-bottom: 1px solid var(--ink-50);
        text-align: center;
        vertical-align: middle;
    }
    #sa-dash .matrix-table td:first-child {
        text-align: left;
        font-weight: 500;
        color: var(--ink-700);
        padding-left: 0;
        padding-right: 16px;
    }
    #sa-dash .matrix-table tr:last-child td { border-bottom: 0; }
    #sa-dash .matrix-table tr:hover td { background: var(--ink-50); }

    #sa-dash .check-yes {
        display: inline-flex; align-items: center; justify-content: center;
        width: 20px; height: 20px;
        background: #d1fae5;
        border-radius: 50%;
    }
    #sa-dash .check-yes svg { width: 11px; height: 11px; color: #059669; }
    #sa-dash .check-no {
        display: inline-flex; align-items: center; justify-content: center;
        width: 20px; height: 20px;
        background: var(--ink-100);
        border-radius: 50%;
    }
    #sa-dash .check-no svg { width: 11px; height: 11px; color: var(--ink-300); }

    #sa-dash .matrix-legend {
        font-size: 11px;
        color: var(--ink-300);
        margin-top: 12px;
    }

    /* ── Sidebar cards ────────────────────────────────── */
    #sa-dash .req-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px;
        border-radius: 12px;
        margin-bottom: 8px;
    }
    #sa-dash .req-row:last-child { margin-bottom: 0; }
    #sa-dash .req-name {
        font-size: 13px;
        font-weight: 500;
        color: var(--ink-700);
    }
    #sa-dash .req-val {
        font-family: var(--font-display);
        font-size: 18px;
        font-weight: 700;
    }
    #sa-dash .req-wrap { padding: 16px 18px; }

    /* Configure Roles */
    #sa-dash .role-link {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 13px 20px;
        border-bottom: 1px solid var(--ink-50);
        text-decoration: none;
        transition: background .12s;
    }
    #sa-dash .role-link:last-child { border-bottom: 0; }
    #sa-dash .role-link:hover { background: var(--ink-50); }
    #sa-dash .role-link-left { display: flex; align-items: center; gap: 10px; }
    #sa-dash .role-dot { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }
    #sa-dash .role-link-name {
        font-size: 13px;
        font-weight: 500;
        color: var(--ink-700);
        transition: color .12s;
    }
    #sa-dash .role-link:hover .role-link-name { color: var(--ink-900); }
    #sa-dash .role-link-right { display: flex; align-items: center; gap: 8px; }
    #sa-dash .role-link-frac { font-size: 11px; font-weight: 500; color: var(--ink-500); }
    #sa-dash .role-link-arrow { width: 14px; height: 14px; color: var(--ink-300); transition: color .12s; }
    #sa-dash .role-link:hover .role-link-arrow { color: var(--indigo-vivid); }

    /* Activity */
    #sa-dash .activity-row {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 13px 20px;
        border-bottom: 1px solid var(--ink-50);
        transition: background .12s;
    }
    #sa-dash .activity-row:last-child { border-bottom: 0; }
    #sa-dash .activity-row:hover { background: var(--ink-50); }
    #sa-dash .activity-dot {
        width: 8px; height: 8px; border-radius: 50%;
        margin-top: 5px; flex-shrink: 0;
    }
    #sa-dash .activity-desc {
        font-size: 12.5px;
        font-weight: 500;
        color: var(--ink-700);
        line-height: 1.45;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 220px;
    }
    #sa-dash .activity-meta {
        font-size: 11px;
        color: var(--ink-500);
        margin-top: 2px;
    }

    /* New Users */
    #sa-dash .user-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 20px;
        border-bottom: 1px solid var(--ink-50);
        transition: background .12s;
    }
    #sa-dash .user-row:last-child { border-bottom: 0; }
    #sa-dash .user-row:hover { background: var(--ink-50); }
    #sa-dash .user-avatar {
        width: 34px; height: 34px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-family: var(--font-display);
        font-size: 13px;
        font-weight: 700;
        flex-shrink: 0;
    }
    #sa-dash .user-name {
        font-size: 13px;
        font-weight: 600;
        color: var(--ink-900);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    #sa-dash .user-role-label {
        font-size: 11px;
        color: var(--ink-500);
    }
    #sa-dash .user-date {
        font-size: 11px;
        color: var(--ink-300);
        white-space: nowrap;
        margin-left: auto;
    }

    #sa-dash .empty-state {
        padding: 32px 20px;
        text-align: center;
        font-size: 12px;
        color: var(--ink-300);
    }
</style>

<div id="sa-dash" class="min-h-screen" style="background: #f1f5f9;">

    {{-- ══ HERO ══════════════════════════════════════════════════════════ --}}
    <div class="hero">
        <div style="max-width:1280px; margin:0 auto; position:relative; z-index:1;">

            {{-- Top row --}}
            <div class="hero-top">
                <div class="hero-identity">
                    <div class="hero-avatar">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="hero-label">Welcome back</p>
                        <h1 class="hero-name">{{ auth()->user()->name }}</h1>
                        <p class="hero-meta">{{ now()->format('l, F j, Y') }} &nbsp;·&nbsp; Super Administrator</p>
                    </div>
                </div>

                <a href="{{ route('super_admin.role_permissions.index') }}" class="hero-btn">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    Manage Permissions
                </a>
            </div>

            {{-- Stat pills --}}
            @php
            $heroStats = [
                ['label' => 'Total Users',    'value' => $totalUsers,       'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                ['label' => 'Departments',    'value' => $totalDepartments, 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                ['label' => 'Total Requests', 'value' => $totalRequests,    'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                ['label' => 'Supply Items',   'value' => $totalSupplies,    'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
            ];
            @endphp

            <div class="stat-grid">
                @foreach($heroStats as $s)
                <div class="stat-pill">
                    <div class="stat-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $s['icon'] }}"/>
                        </svg>
                    </div>
                    <div>
                        <p class="stat-value">{{ $s['value'] }}</p>
                        <p class="stat-label">{{ $s['label'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>

        </div>
    </div>

    {{-- ══ PAGE BODY ══════════════════════════════════════════════════════ --}}
    <div class="page-body">
        <div class="layout-grid">

            {{-- ── MAIN (left/centre) ────────────────────────────────────── --}}
            <div class="col-main">

                {{-- Low-stock alert --}}
                @if($lowStockCount > 0)
                <div class="alert-low">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <p>
                        <strong>{{ $lowStockCount }}</strong> {{ Str::plural('item', $lowStockCount) }} below minimum stock level.
                    </p>
                    <a href="{{ route('admin.low-stock.index') }}">View →</a>
                </div>
                @endif

                {{-- Role Breakdown --}}
                @php
                $roleDefs = [
                    'super_admin' => ['label'=>'Super Admin','bar'=>'#a855f7','pill'=>'background:#faf5ff;color:#7e22ce;outline:1px solid #e9d5ff;'],
                    'admin'       => ['label'=>'Admin',      'bar'=>'#ef4444','pill'=>'background:#fff1f2;color:#b91c1c;outline:1px solid #fecdd3;'],
                    'manager'     => ['label'=>'Manager',    'bar'=>'#3b82f6','pill'=>'background:#eff6ff;color:#1d4ed8;outline:1px solid #bfdbfe;'],
                    'hr_manager'  => ['label'=>'HR Manager', 'bar'=>'#f97316','pill'=>'background:#fff7ed;color:#c2410c;outline:1px solid #fed7aa;'],
                    'employee'    => ['label'=>'Employee',   'bar'=>'#22c55e','pill'=>'background:#f0fdf4;color:#15803d;outline:1px solid #bbf7d0;'],
                ];
                @endphp

                <div class="card">
                    <div class="card-head">
                        <span class="card-title">User Role Breakdown</span>
                        <a href="{{ route('admin.users.index') }}" class="card-link">Manage Users →</a>
                    </div>

                    @foreach($roleDefs as $rk => $rd)
                    @php
                        $count = $roleStats[$rk] ?? 0;
                        $pct   = $totalUsers > 0 ? round(($count / $totalUsers) * 100) : 0;
                    @endphp
                    <div class="role-row">
                        <span class="role-pill" style="{{ $rd['pill'] }}">{{ $rd['label'] }}</span>
                        <div class="bar-track">
                            <div class="bar-fill" style="width:{{ $count > 0 ? max($pct, 3) : 0 }}%; background:{{ $rd['bar'] }};"></div>
                        </div>
                        <span class="role-count">{{ $count }}</span>
                        <span class="role-pct">{{ $pct }}%</span>
                    </div>
                    @endforeach
                </div>

                {{-- Permission Coverage --}}
                @php
                $permColors = [
                    'admin'      => ['bar'=>'#ef4444','text'=>'#dc2626'],
                    'manager'    => ['bar'=>'#3b82f6','text'=>'#2563eb'],
                    'hr_manager' => ['bar'=>'#f97316','text'=>'#ea580c'],
                    'employee'   => ['bar'=>'#22c55e','text'=>'#16a34a'],
                ];
                @endphp

                <div class="card">
                    <div class="card-head">
                        <span class="card-title">Permission Coverage</span>
                        <a href="{{ route('super_admin.role_permissions.index') }}" class="card-link">Configure →</a>
                    </div>

                    <div class="perm-grid">
                        @foreach($roles as $roleKey => $roleLabel)
                        @php
                            $granted  = $rolePermissionCounts[$roleKey] ?? 0;
                            $pct      = $totalPermissions > 0 ? round(($granted / $totalPermissions) * 100) : 0;
                            $c        = $permColors[$roleKey] ?? ['bar'=>'#94a3b8','text'=>'#64748b'];
                        @endphp
                        <div>
                            <div class="perm-bar-label">
                                <span class="perm-role-name" style="color:{{ $c['text'] }};">{{ $roleLabel }}</span>
                                <span class="perm-fraction">{{ $granted }}/{{ $totalPermissions }}</span>
                            </div>
                            <div class="perm-track">
                                <div class="perm-fill" style="width:{{ $granted > 0 ? max($pct, 4) : 0 }}%; background:{{ $c['bar'] }};"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Matrix --}}
                    <div class="matrix-wrap">
                        <p class="matrix-label">Page Access Matrix</p>
                        <table class="matrix-table">
                            <thead>
                                <tr>
                                    <th style="text-align:left; color:#94a3b8;">Page</th>
                                    @foreach($roles as $rk => $rl)
                                    @php
                                    $thColor = match($rk) {
                                        'admin'      => '#ef4444',
                                        'manager'    => '#3b82f6',
                                        'hr_manager' => '#f97316',
                                        default      => '#22c55e',
                                    };
                                    @endphp
                                    <th style="color:{{ $thColor }};">
                                        {{ $rk === 'hr_manager' ? 'HR' : ucfirst($rk)[0] }}
                                    </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($permissions as $perm)
                                <tr>
                                    <td>{{ $perm->name }}</td>
                                    @foreach($roles as $rk => $rl)
                                    @php $has = \App\Models\RolePermission::where('role',$rk)->where('permission_id',$perm->id)->exists(); @endphp
                                    <td>
                                        @if($has)
                                        <span class="check-yes">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </span>
                                        @else
                                        <span class="check-no">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </span>
                                        @endif
                                    </td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <p class="matrix-legend">A = Admin &nbsp;·&nbsp; M = Manager &nbsp;·&nbsp; HR = HR Manager &nbsp;·&nbsp; E = Employee</p>
                    </div>
                </div>

            </div>{{-- end col-main --}}

            {{-- ── SIDEBAR (right) ───────────────────────────────────────── --}}
            <div class="col-side">

                {{-- Request Status --}}
                <div class="card">
                    <div class="card-head">
                        <span class="card-title">Request Status</span>
                    </div>
                    @php
                    $reqStats = [
                        ['label' => 'Total Requests',  'value' => $totalRequests,   'bg' => '#f8fafc', 'val_color' => '#0f172a'],
                        ['label' => 'Pending',          'value' => $pendingRequests, 'bg' => '#fffbeb', 'val_color' => '#b45309'],
                        ['label' => 'Released Today',   'value' => $releasedToday,  'bg' => '#f0fdf4', 'val_color' => '#15803d'],
                    ];
                    @endphp
                    <div class="req-wrap">
                        @foreach($reqStats as $rs)
                        <div class="req-row" style="background:{{ $rs['bg'] }};">
                            <span class="req-name">{{ $rs['label'] }}</span>
                            <span class="req-val" style="color:{{ $rs['val_color'] }};">{{ $rs['value'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Configure Roles --}}
                <div class="card">
                    <div class="card-head">
                        <span class="card-title">Configure Roles</span>
                    </div>
                    @php
                    $dotColors = [
                        'admin'      => '#f87171',
                        'manager'    => '#60a5fa',
                        'hr_manager' => '#fb923c',
                        'employee'   => '#4ade80',
                    ];
                    @endphp
                    @foreach($roles as $roleKey => $roleLabel)
                    @php $granted = $rolePermissionCounts[$roleKey] ?? 0; @endphp
                    <a href="{{ route('super_admin.role_permissions.index', ['role' => $roleKey]) }}" class="role-link">
                        <div class="role-link-left">
                            <span class="role-dot" style="background:{{ $dotColors[$roleKey] ?? '#94a3b8' }};"></span>
                            <span class="role-link-name">{{ $roleLabel }}</span>
                        </div>
                        <div class="role-link-right">
                            <span class="role-link-frac">{{ $granted }}/{{ $totalPermissions }}</span>
                            <svg class="role-link-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </a>
                    @endforeach
                </div>

                {{-- Recent Activity --}}
                <div class="card">
                    <div class="card-head">
                        <span class="card-title">Recent Activity</span>
                        <a href="{{ route('admin.audit-logs.index') }}" class="card-link">All logs →</a>
                    </div>
                    @forelse($recentLogs as $log)
                    @php
                    $dotColor = match(true) {
                        str_contains($log->action, 'created')  => '#60a5fa',
                        str_contains($log->action, 'approved') => '#34d399',
                        str_contains($log->action, 'rejected') => '#f87171',
                        str_contains($log->action, 'released') => '#818cf8',
                        str_contains($log->action, 'deleted')  => '#fca5a5',
                        str_contains($log->action, 'updated')  => '#c084fc',
                        default => '#cbd5e1',
                    };
                    @endphp
                    <div class="activity-row">
                        <span class="activity-dot" style="background:{{ $dotColor }};"></span>
                        <div style="flex:1; min-width:0;">
                            <p class="activity-desc">{{ $log->description ?? $log->action_name }}</p>
                            <p class="activity-meta">
                                {{ $log->user?->name ?? 'System' }} &nbsp;·&nbsp; {{ $log->created_at?->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                    @empty
                    <div class="empty-state">No activity yet.</div>
                    @endforelse
                </div>

                {{-- New Users --}}
                @php
                $userBadge = [
                    'admin'      => 'background:#fff1f2;color:#b91c1c;',
                    'manager'    => 'background:#eff6ff;color:#1d4ed8;',
                    'hr_manager' => 'background:#fff7ed;color:#c2410c;',
                    'employee'   => 'background:#f0fdf4;color:#15803d;',
                    'super_admin'=> 'background:#faf5ff;color:#7e22ce;',
                ];
                @endphp
                <div class="card">
                    <div class="card-head">
                        <span class="card-title">New Users</span>
                        <a href="{{ route('admin.users.index') }}" class="card-link">All users →</a>
                    </div>
                    @forelse($recentUsers as $usr)
                    <div class="user-row">
                        <div class="user-avatar" style="{{ $userBadge[$usr->role] ?? 'background:#f1f5f9;color:#475569;' }}">
                            {{ strtoupper(substr($usr->name, 0, 1)) }}
                        </div>
                        <div style="flex:1; min-width:0;">
                            <p class="user-name">{{ $usr->name }}</p>
                            <p class="user-role-label">{{ $usr->getRoleLabel() }}</p>
                        </div>
                        <span class="user-date">{{ $usr->created_at->format('M d') }}</span>
                    </div>
                    @empty
                    <div class="empty-state">No users found.</div>
                    @endforelse
                </div>

            </div>{{-- end col-side --}}
        </div>
    </div>
</div>

@endsection