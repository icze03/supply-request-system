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

 <!-- Ultra Compact Footer -->
<footer class="bg-gradient-to-r from-gray-800 via-gray-900 to-gray-800 text-white py-3 mt-auto border-t border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Main Footer Content -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-2">
            <!-- Developer Info -->
            <div class="text-center md:text-left">
                <h3 class="text-sm font-bold mb-0.5 text-blue-400">Supply Request Management System</h3>
                <p class="text-xs text-gray-400">
                    supply tracking and approval workflow
                </p>
            </div>

            <!-- Tech Stack -->
            <div class="text-center">
                <h4 class="text-xs font-semibold mb-1 text-gray-300">Built With</h4>
                <div class="flex flex-wrap justify-center gap-1">
                    <span class="px-1.5 py-0.5 bg-red-600 bg-opacity-20 border border-red-500 rounded text-xs font-mono">Laravel 12</span>
                    <span class="px-1.5 py-0.5 bg-blue-600 bg-opacity-20 border border-blue-500 rounded text-xs font-mono">PHP 8.3</span>
                    <span class="px-1.5 py-0.5 bg-cyan-600 bg-opacity-20 border border-cyan-500 rounded text-xs font-mono">Tailwind CSS</span>
                    <span class="px-1.5 py-0.5 bg-orange-600 bg-opacity-20 border border-orange-500 rounded text-xs font-mono">MySQL</span>
                </div>
            </div>

            <!-- Developer Credit -->
            <div class="text-center md:text-right">
                <h4 class="text-xs font-semibold mb-1 text-gray-300">Developed By</h4>
                <p class="text-sm font-bold text-blue-400">Klein Imperio</p>
            </div>
        </div>

        <!-- Divider -->
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