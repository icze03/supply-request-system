<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
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

                            {{-- Dashboard (all roles) --}}
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('dashboard') ? 'border-indigo-400' : 'border-transparent' }} text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out">
                                Dashboard
                            </a>

                            {{-- Employee links --}}
                            @if(auth()->user()->isEmployee())
                                <a href="{{ route('employee.catalog') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('employee.catalog') ? 'border-indigo-400' : 'border-transparent' }} text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out">
                                    Catalog
                                </a>
                                <a href="{{ route('employee.requests.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('employee.requests.*') ? 'border-indigo-400' : 'border-transparent' }} text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out">
                                    My Requests
                                </a>
                            @endif

                            {{-- Manager links --}}
                            @if(auth()->user()->isManager())
                                <a href="{{ route('manager.approvals.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('manager.*') ? 'border-indigo-400' : 'border-transparent' }} text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out">
                                    Approvals
                                </a>
                            @endif

                            {{-- Admin links: Dashboard, Releases, Supplies, Low Stock, Audit Trail, Users, Departments --}}
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.releases.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.releases.*') ? 'border-indigo-400' : 'border-transparent' }} text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out">
                                    Releases
                                </a>
                                <a href="{{ route('admin.supplies.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.supplies.*') ? 'border-indigo-400' : 'border-transparent' }} text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out">
                                    Supplies
                                </a>
                                <a href="{{ route('admin.low-stock.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.low-stock.*') ? 'border-indigo-400' : 'border-transparent' }} text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out">
                                    Low Stock
                                </a>
                                <a href="{{ route('admin.audit-logs.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.audit-logs*') ? 'border-indigo-400' : 'border-transparent' }} text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out">
                                    Audit Trail
                                </a>
                                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.users.*') ? 'border-indigo-400' : 'border-transparent' }} text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out">
                                    Users
                                </a>
                                <a href="{{ route('admin.departments.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.departments.*') ? 'border-indigo-400' : 'border-transparent' }} text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out">
                                    Departments
                                </a>
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
                                            {{ ucfirst(auth()->user()->role) }}
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

        <!-- Page Content -->
        <main class="py-6">
            @yield('content')
        </main>
    </div>

    @stack('scripts')

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-4 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-sm">
                Developed by <span class="font-semibold">Klein Isaac Imperio</span> © {{ date('2026') }}
            </p>
        </div>
    </footer>
</body>
</html>