@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Release Management</h1>
        <p class="mt-1 text-sm text-gray-600">Manage approved requests and release supplies</p>
    </div>

    <!-- Tabs -->
    <div class="border-b border-gray-200 mb-6">
        <nav class="-mb-px flex space-x-8">
            <button onclick="switchTab('pending')" id="pendingTab" class="border-b-2 border-indigo-500 py-4 px-1 text-sm font-medium text-indigo-600">
                Pending Release ({{ $pendingReleases->total() }})
            </button>
            <button onclick="switchTab('today')" id="todayTab" class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                Released Today ({{ $releasedToday->total() }})
            </button>
            <button onclick="switchTab('history')" id="historyTab" class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                Release History ({{ $releaseHistory->total() }})
            </button>
        </nav>
    </div>

    <!-- Pending Release Tab - ENHANCED -->
    <div id="pendingContent">
        @if($pendingReleases->isNotEmpty())
            <div class="space-y-4">
                @foreach($pendingReleases as $request)
                    <div class="bg-white shadow-lg rounded-lg overflow-hidden border-l-4 border-yellow-500">
                        <div class="p-6">
                            <!-- Header Row -->
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">SR #{{ $request->sr_number }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">Submitted {{ $request->created_at->format('M d, Y h:i A') }}</p>
                                </div>
                                <span class="px-4 py-2 text-sm font-bold rounded-full bg-yellow-100 text-yellow-800 border border-yellow-300">
                                     Awaiting Release
                                </span>
                            </div>

                            <!-- Info Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-5 pb-5 border-b border-gray-200">
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Employee</p>
                                    <p class="text-sm font-bold text-gray-900">{{ $request->user->name }}</p>
                                    <p class="text-xs text-gray-600">{{ $request->user->email }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Department</p>
                                    <p class="text-sm font-bold text-gray-900">{{ $request->department->name }}</p>
                                    <p class="text-xs text-gray-600">{{ $request->department->code }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Request Type</p>
                                    <span class="inline-flex px-3 py-1 text-xs font-bold rounded-full {{ $request->request_type === 'special' ? 'bg-purple-100 text-purple-800 border border-purple-300' : 'bg-blue-100 text-blue-800 border border-blue-300' }}">
                                        {{ $request->request_type === 'special' ? 'Special' : 'Standard' }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Budget Type</p>
                                    <span class="inline-flex px-3 py-1 text-xs font-bold rounded-full {{ $request->budget_type === 'budgeted' ? 'bg-green-100 text-green-800 border border-green-300' : 'bg-orange-100 text-orange-800 border border-orange-300' }}">
                                        {{ $request->budget_type === 'budgeted' ? '✓ Budgeted' : '⚠ Not Budgeted' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Items Section -->
                            @if($request->request_type === 'standard')
                                <div class="mb-5">
                                    <p class="text-sm font-bold text-gray-700 mb-3">Requested Items ({{ $request->items->count() }})</p>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <div class="space-y-2">
                                            @foreach($request->items->take(5) as $item)
                                                <div class="flex justify-between items-center">
                                                    <span class="text-sm text-gray-900">• {{ $item->item_name }}</span>
                                                    <span class="text-sm font-semibold text-indigo-600">{{ $item->quantity }} {{ $item->supply->unit ?? 'pcs' }}</span>
                                                </div>
                                            @endforeach
                                            @if($request->items->count() > 5)
                                                <p class="text-xs text-gray-500 italic mt-2">+ {{ $request->items->count() - 5 }} more item(s)</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="mb-5">
                                    <p class="text-sm font-bold text-gray-700 mb-2">⭐ Special Request Description</p>
                                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                                        <p class="text-sm text-gray-800">{{ $request->special_item_description }}</p>
                                    </div>
                                </div>
                            @endif

                            <!-- Approval Info -->
                            <div class="flex items-center text-sm text-gray-600 bg-green-50 border border-green-200 rounded-lg p-3 mb-5">
                                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="font-semibold">Approved by {{ $request->managerApprover->name ?? 'Manager' }}</span>
                                <span class="mx-2">•</span>
                                <span>{{ $request->manager_approved_at->format('M d, Y h:i A') }}</span>
                            </div>

                            <!-- Action Buttons -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <a href="{{ route('admin.releases.show', $request->id) }}" 
                                   class="inline-flex items-center justify-center px-5 py-3 bg-gray-100 border-2 border-gray-300 rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-200 transition">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    View Full Details
                                </a>
                                <button onclick="releaseRequest({{ $request->id }})" 
                                        class="inline-flex items-center justify-center px-5 py-3 bg-green-600 border-2 border-green-700 rounded-lg text-sm font-bold text-white hover:bg-green-700 transition">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Release Now
                                </button>
                                <button onclick="rejectRequest({{ $request->id }})" 
                                        class="inline-flex items-center justify-center px-5 py-3 bg-red-600 border-2 border-red-700 rounded-lg text-sm font-bold text-white hover:bg-red-700 transition">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Reject Request
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="mt-6">
                {{ $pendingReleases->links() }}
            </div>
        @else
            <div class="bg-white shadow rounded-lg p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-4 text-lg font-semibold text-gray-900">No pending releases</h3>
                <p class="mt-2 text-sm text-gray-500">All approved requests have been released.</p>
            </div>
        @endif
    </div>

    <!-- Released Today Tab -->
    <div id="todayContent" class="hidden">
        @if($releasedToday->count() > 0)
            <div class="bg-white shadow rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Serial</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dept</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Time</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($releasedToday as $request)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-mono text-indigo-600 font-semibold">
                                    {{ $request->serial_number }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $request->user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $request->user->email }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $request->department->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $request->department->code }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $request->request_type === 'special' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ ucfirst($request->request_type) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 text-center">
                                    {{ $request->admin_released_at->format('h:i A') }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    <a href="{{ route('admin.voucher', $request->id) }}" 
                                       target="_blank"
                                       class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700 transition">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="mt-6">
                {{ $releasedToday->links() }}
            </div>
        @else
            <div class="bg-white shadow rounded-lg p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No releases today</h3>
                <p class="mt-1 text-sm text-gray-500">No supplies have been released today.</p>
            </div>
        @endif
    </div>

    <!-- Release History Tab -->
    <div id="historyContent" class="hidden">
        @if($releaseHistory->count() > 0)
            <!-- Filter/Search -->
            <div class="mb-4 bg-white shadow rounded-lg p-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Search Serial Number</label>
                        <input type="text" id="searchSR" placeholder="Search by serial number..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" onkeyup="filterHistory()">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                        <select id="filterDept" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" onchange="filterHistory()">
                            <option value="">All Departments</option>
                            @foreach(\App\Models\Department::all() as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                        <select id="filterDate" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" onchange="filterHistory()">
                            <option value="">All Time</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="year">This Year</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- History Table -->
            <div class="bg-white shadow rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Serial</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dept</th>
                            <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase">Budget</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">By</th>
                            <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="historyTableBody">
                        @foreach($releaseHistory as $request)
                            <tr class="hover:bg-gray-50" 
                                data-sr="{{ $request->serial_number }}" 
                                data-dept="{{ $request->department_id }}" 
                                data-date="{{ $request->admin_released_at->format('Y-m-d') }}">
                                <td class="px-3 py-3 whitespace-nowrap text-sm font-mono text-indigo-600 font-semibold">
                                    {{ $request->serial_number }}
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $request->user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $request->user->email }}</div>
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $request->department->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $request->department->code }}</div>
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap text-center">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $request->request_type === 'special' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ ucfirst($request->request_type) }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap text-center">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $request->budget_type === 'budgeted' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                                        {{ $request->budget_type === 'budgeted' ? 'Budgeted' : 'Not Budgeted' }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ $request->admin_released_at->format('M d, Y') }}
                                    <div class="text-xs text-gray-400">{{ $request->admin_released_at->format('h:i A') }}</div>
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-900">
                                    {{ $request->adminReleaser->name ?? 'N/A' }}
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <!-- View Voucher Button -->
                                        <a href="{{ route('admin.voucher', $request->id) }}" 
                                           target="_blank"
                                           class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700 transition whitespace-nowrap">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            View
                                        </a>
                                        
                                        <!-- Delete Button -->
                                        <button onclick="deleteHistory({{ $request->id }}, '{{ $request->serial_number }}')" 
                                                class="inline-flex items-center px-3 py-2 bg-red-600 text-white text-xs font-bold rounded-lg hover:bg-red-700 transition whitespace-nowrap">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="mt-6">
                {{ $releaseHistory->links() }}
            </div>
        @else
            <div class="bg-white shadow rounded-lg p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No release history</h3>
                <p class="mt-1 text-sm text-gray-500">No supplies have been released yet.</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function switchTab(tab) {
    const tabs = ['pending', 'today', 'history'];
    const tabButtons = ['pendingTab', 'todayTab', 'historyTab'];
    const contents = ['pendingContent', 'todayContent', 'historyContent'];
    
    tabs.forEach((t, i) => {
        const button = document.getElementById(tabButtons[i]);
        const content = document.getElementById(contents[i]);
        
        if (t === tab) {
            button.className = 'border-b-2 border-indigo-500 py-4 px-1 text-sm font-medium text-indigo-600';
            content.classList.remove('hidden');
        } else {
            button.className = 'border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300';
            content.classList.add('hidden');
        }
    });
}

function filterHistory() {
    const searchSR = document.getElementById('searchSR').value.toLowerCase();
    const filterDept = document.getElementById('filterDept').value;
    const filterDate = document.getElementById('filterDate').value;
    const rows = document.querySelectorAll('#historyTableBody tr');
    
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    rows.forEach(row => {
        const sr = row.dataset.sr.toLowerCase();
        const dept = row.dataset.dept;
        const date = new Date(row.dataset.date);
        date.setHours(0, 0, 0, 0);
        
        let show = true;
        
        if (searchSR && !sr.includes(searchSR)) show = false;
        if (filterDept && dept !== filterDept) show = false;
        
        if (filterDate) {
            const diffDays = Math.floor((today - date) / (1000 * 60 * 60 * 24));
            if (filterDate === 'today' && diffDays !== 0) show = false;
            if (filterDate === 'week' && diffDays > 7) show = false;
            if (filterDate === 'month' && diffDays > 30) show = false;
            if (filterDate === 'year' && diffDays > 365) show = false;
        }
        
        row.style.display = show ? '' : 'none';
    });
}

async function releaseRequest(id) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4';
    modal.innerHTML = `
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Release Supplies</h3>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Release Notes (Optional)</label>
                <textarea id="releaseNotes" rows="4" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Add any notes about this release..."></textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button onclick="this.closest('.fixed').remove()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button id="confirmRelease" class="px-4 py-2 bg-green-600 rounded-md text-sm font-medium text-white hover:bg-green-700">
                    ✓ Confirm Release
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    document.getElementById('releaseNotes').focus();
    
    document.getElementById('confirmRelease').onclick = async function() {
        const notes = document.getElementById('releaseNotes').value.trim();
        modal.remove();
        
        try {
            const response = await fetch(`/admin/releases/${id}/release`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ notes: notes || 'Released without notes' })
            });
            
            const data = await response.json();
            
            if (data.success) {
                const successModal = document.createElement('div');
                successModal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4';
                successModal.innerHTML = `
                    <div class="bg-white rounded-lg max-w-md w-full p-6 text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">✓ Release Successful!</h3>
                        <p class="text-sm text-gray-600 mb-4">Serial Number: <span class="font-mono font-bold text-indigo-600">${data.serial_number}</span></p>
                        <button onclick="window.location.reload()" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700">
                            OK
                        </button>
                    </div>
                `;
                document.body.appendChild(successModal);
            } else {
                alert('Error: ' + (data.message || 'Failed to release request'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        }
    };
}

async function rejectRequest(id) {
    const notes = prompt('Enter rejection reason (required):');
    if (!notes || notes.trim() === '') {
        alert('Rejection reason is required');
        return;
    }
    
    if (!confirm('Are you sure you want to reject this request?')) return;
    
    try {
        const response = await fetch(`/admin/releases/${id}/reject`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ notes: notes })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('✓ Request rejected successfully');
            window.location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to reject request'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    }
}

async function deleteHistory(id, serialNumber) {
    const confirmModal = document.createElement('div');
    confirmModal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4';
    confirmModal.innerHTML = `
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-red-100 rounded-full">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2 text-center">Delete Release History?</h3>
            <p class="text-sm text-gray-600 mb-4 text-center">
                Are you sure you want to delete this release record?<br>
                <span class="font-mono font-bold text-red-600">${serialNumber}</span>
            </p>
            <p class="text-xs text-red-600 mb-4 text-center"> This action cannot be undone!</p>
            <div class="flex justify-end space-x-3">
                <button onclick="this.closest('.fixed').remove()" 
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button id="confirmDelete" 
                        class="px-4 py-2 bg-red-600 rounded-md text-sm font-medium text-white hover:bg-red-700">
                     Delete
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(confirmModal);
    
    document.getElementById('confirmDelete').onclick = async function() {
        confirmModal.remove();
        
        const loadingModal = document.createElement('div');
        loadingModal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4';
        loadingModal.innerHTML = `
            <div class="bg-white rounded-lg p-6">
                <div class="flex items-center space-x-3">
                    <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-sm font-medium text-gray-900">Deleting...</span>
                </div>
            </div>
        `;
        document.body.appendChild(loadingModal);
        
        try {
            const response = await fetch(`/admin/releases/${id}/delete`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            const data = await response.json();
            loadingModal.remove();
            
            if (data.success) {
                const successModal = document.createElement('div');
                successModal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4';
                successModal.innerHTML = `
                    <div class="bg-white rounded-lg max-w-md w-full p-6 text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">✓ Deleted Successfully!</h3>
                        <p class="text-sm text-gray-600 mb-4">Release history has been removed.</p>
                        <button onclick="window.location.reload()" 
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700">
                            OK
                        </button>
                    </div>
                `;
                document.body.appendChild(successModal);
            } else {
                alert('Error: ' + (data.message || 'Failed to delete record'));
            }
        } catch (error) {
            loadingModal.remove();
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        }
    };
}
</script>
@endpush
@endsection