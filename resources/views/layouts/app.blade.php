<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
<div class="min-h-screen">

    <!-- Navigation -->
    <nav class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">

                    <!-- Logo -->
                    <div class="shrink-0 flex items-center">
                        <a href="{{ route('dashboard') }}" class="text-xl font-bold text-gray-800">
                            Supply Request System
                        </a>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">

                        @php $user = auth()->user(); @endphp

                        {{-- Dashboard — visible to all roles --}}
                        <a href="{{ route('dashboard') }}"
                           class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('dashboard') || request()->routeIs('*.dashboard') ? 'border-indigo-400' : 'border-transparent' }} text-sm font-medium leading-5 text-gray-900 hover:border-gray-300 transition duration-150 ease-in-out">
                            Dashboard
                        </a>

                        {{-- ── SUPER ADMIN LINKS ─────────────────────────────── --}}
                        @if($user->isSuperAdmin())
                        <a href="{{ route('super_admin.role_permissions.index') }}"
                        class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('super_admin.role_permissions*') ? 'border-purple-500' : 'border-transparent' }} text-sm font-medium leading-5 text-purple-700 hover:border-purple-300 transition duration-150 ease-in-out">
                        Role Permissions
                        </a>
                        <a href="{{ route('admin.audit-logs.index') }}"
                        class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.audit-logs*') ? 'border-indigo-400' : 'border-transparent' }} text-sm font-medium leading-5 text-gray-900 hover:border-gray-300 transition duration-150 ease-in-out">
                            Audit Trail

                        </a>
                        <a href="{{ route('admin.users.index') }}"
                        class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.users.*') ? 'border-indigo-400' : 'border-transparent' }} text-sm font-medium leading-5 text-gray-900 hover:border-gray-300 transition duration-150 ease-in-out">
                            Users
                        </a>
                        <a href="{{ route('admin.departments.index') }}"
                        class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.departments.*') ? 'border-indigo-400' : 'border-transparent' }} text-sm font-medium leading-5 text-gray-900 hover:border-gray-300 transition duration-150 ease-in-out">
                            Departments
                        </a>
                        @endif

                        {{-- ── EMPLOYEE LINKS ────────────────────────────────── --}}
                        @if($user->isEmployee())
                            @if($user->hasPermission('catalog'))
                                <a href="{{ route('employee.catalog') }}"
                                   class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('employee.catalog') ? 'border-indigo-400' : 'border-transparent' }} text-sm font-medium leading-5 text-gray-900 hover:border-gray-300 transition duration-150 ease-in-out">
                                    Catalog
                                </a>
                            @endif
                            @if($user->hasPermission('my_requests'))
                                <a href="{{ route('employee.requests.index') }}"
                                   class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('employee.requests.*') ? 'border-indigo-400' : 'border-transparent' }} text-sm font-medium leading-5 text-gray-900 hover:border-gray-300 transition duration-150 ease-in-out">
                                    My Requests
                                </a>
                            @endif
                        @endif

                        {{-- ── MANAGER LINKS ─────────────────────────────────── --}}
                        @if($user->isManager())
                            @if($user->hasPermission('approvals'))
                                <a href="{{ route('manager.approvals.index') }}"
                                   class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('manager.*') ? 'border-indigo-400' : 'border-transparent' }} text-sm font-medium leading-5 text-gray-900 hover:border-gray-300 transition duration-150 ease-in-out">
                                    Approvals
                                </a>
                            @endif
                        @endif
                        {{-- ── HR MANAGER LINKS ──────────────────────────────── --}}
@if($user->isHrManager())
    @if($user->hasPermission('users'))
        <a href="{{ route('admin.users.index') }}"
           class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.users.*') ? 'border-indigo-400' : 'border-transparent' }} text-sm font-medium leading-5 text-gray-900 hover:border-gray-300 transition duration-150 ease-in-out">
            Users
        </a>
    @endif
    @if($user->hasPermission('departments'))
        <a href="{{ route('admin.departments.index') }}"
           class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.departments.*') ? 'border-indigo-400' : 'border-transparent' }} text-sm font-medium leading-5 text-gray-900 hover:border-gray-300 transition duration-150 ease-in-out">
            Departments
        </a>
    @endif
    @if($user->hasPermission('audit_trail'))
        <a href="{{ route('admin.audit-logs.index') }}"
           class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.audit-logs*') ? 'border-indigo-400' : 'border-transparent' }} text-sm font-medium leading-5 text-gray-900 hover:border-gray-300 transition duration-150 ease-in-out">
            Audit Trail
        </a>
    @endif
@endif

                        {{-- ── ADMIN LINKS ───────────────────────────────────── --}}
                        @if($user->isAdmin())
                            @if($user->hasPermission('releases'))
                                <a href="{{ route('admin.releases.index') }}"
                                   class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.releases.*') ? 'border-indigo-400' : 'border-transparent' }} text-sm font-medium leading-5 text-gray-900 hover:border-gray-300 transition duration-150 ease-in-out">
                                    Releases
                                </a>
                            @endif
                            @if($user->hasPermission('supplies'))
                                <a href="{{ route('admin.supplies.index') }}"
                                   class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.supplies.*') ? 'border-indigo-400' : 'border-transparent' }} text-sm font-medium leading-5 text-gray-900 hover:border-gray-300 transition duration-150 ease-in-out">
                                    Supplies
                                </a>
                            @endif
                            @if($user->hasPermission('low_stock'))
                                <a href="{{ route('admin.low-stock.index') }}"
                                   class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.low-stock.*') ? 'border-indigo-400' : 'border-transparent' }} text-sm font-medium leading-5 text-gray-900 hover:border-gray-300 transition duration-150 ease-in-out">
                                    Low Stock
                                </a>
                            @endif
                            @if($user->hasPermission('audit_trail'))
                                <a href="{{ route('admin.audit-logs.index') }}"
                                   class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.audit-logs*') ? 'border-indigo-400' : 'border-transparent' }} text-sm font-medium leading-5 text-gray-900 hover:border-gray-300 transition duration-150 ease-in-out">
                                    Audit Trail
                                </a>
                            @endif
                            @if($user->hasPermission('users'))
                                <a href="{{ route('admin.users.index') }}"
                                   class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.users.*') ? 'border-indigo-400' : 'border-transparent' }} text-sm font-medium leading-5 text-gray-900 hover:border-gray-300 transition duration-150 ease-in-out">
                                    Users
                                </a>
                            @endif
                            @if($user->hasPermission('departments'))
                                <a href="{{ route('admin.departments.index') }}"
                                   class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.departments.*') ? 'border-indigo-400' : 'border-transparent' }} text-sm font-medium leading-5 text-gray-900 hover:border-gray-300 transition duration-150 ease-in-out">
                                    Departments
                                </a>
                            @endif
                        @endif

                    </div>
                </div>

                <!-- User Info & Logout -->
                <div class="hidden sm:flex sm:items-center sm:ml-6">
                    <div class="ml-3 relative">
                        <div class="flex items-center space-x-3">
                            <div class="text-right">
                                <div class="font-medium text-sm text-gray-800">{{ auth()->user()->name }}</div>
                                <div class="font-medium text-xs text-gray-500">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold {{ auth()->user()->getRoleBadgeColor() }}">
                                        {{ auth()->user()->getRoleLabel() }}
                                    </span>
                                    @if(auth()->user()->department)
                                        <span class="ml-1">{{ auth()->user()->department->code }}</span>
                                    @endif
                                </div>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-sm text-gray-700 hover:text-gray-900">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('success') }}
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Page Content -->
    <main class="py-6">
        @yield('content')
    </main>
</div>

@stack('scripts')

<!-- Footer -->
<footer class="bg-gradient-to-r from-gray-800 via-gray-900 to-gray-800 text-white py-3 mt-auto border-t border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-2">
            <div class="text-center md:text-left">
                <h3 class="text-sm font-bold mb-0.5 text-blue-400">Supply Request Management System</h3>
                <p class="text-xs text-gray-400">supply tracking and approval workflow</p>
            </div>
            <div class="text-center">
                <h4 class="text-xs font-semibold mb-1 text-gray-300">Built With</h4>
                <div class="flex flex-wrap justify-center gap-1">
                    <span class="px-1.5 py-0.5 bg-red-600 bg-opacity-20 border border-red-500 rounded text-xs font-mono">Laravel 12</span>
                    <span class="px-1.5 py-0.5 bg-blue-600 bg-opacity-20 border border-blue-500 rounded text-xs font-mono">PHP 8.3</span>
                    <span class="px-1.5 py-0.5 bg-cyan-600 bg-opacity-20 border border-cyan-500 rounded text-xs font-mono">Tailwind CSS</span>
                    <span class="px-1.5 py-0.5 bg-orange-600 bg-opacity-20 border border-orange-500 rounded text-xs font-mono">MySQL</span>
                </div>
            </div>
            <div class="text-center md:text-right">
                <h4 class="text-xs font-semibold mb-1 text-gray-300">Developed By</h4>
                <p class="text-sm font-bold text-blue-400">Klein Imperio</p>
            </div>
        </div>
        <div class="border-t border-gray-700 pt-2">
            <div class="flex flex-col md:flex-row justify-between items-center text-xs text-gray-400">
                <p>© {{ date('Y') }} Supply Requisition System.</p>
                <p class="mt-0.5 md:mt-0">Version 1.0.0 | Last Updated: {{ date('F Y') }}</p>
            </div>
        </div>
    </div>
</footer>
</body>
</html>
