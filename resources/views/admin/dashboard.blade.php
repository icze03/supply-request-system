@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
        <p class="mt-1 text-sm text-gray-600">System overview and pending releases</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Supplies</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $totalSupplies }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Active Supplies</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $activeSupplies }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Pending Release</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $pendingApproval }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Released Today</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $releasedToday }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    @if($pendingApproval > 0)
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <svg class="h-6 w-6 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div>
                    <h3 class="text-sm font-medium text-yellow-900">{{ $pendingApproval }} request(s) awaiting release</h3>
                    <p class="text-sm text-yellow-700">These requests have been approved by managers and need final release</p>
                </div>
            </div>
            <a href="{{ route('admin.releases.index') }}" class="px-4 py-2 bg-yellow-600 text-white text-sm font-medium rounded-md hover:bg-yellow-700">
                Process Releases
            </a>
        </div>
    </div>
    @endif

    <!-- Department Stats (collapsible) -->
    <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
        <button
            type="button"
            onclick="toggleDeptStats()"
            class="w-full px-6 py-4 border-b border-gray-200 flex items-center justify-between hover:bg-gray-50 transition-colors focus:outline-none"
        >
            <h2 class="text-lg font-semibold text-gray-900">Department Statistics</h2>
            <svg id="deptStatsChevron" class="h-5 w-5 text-gray-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        <div id="deptStatsBody" style="overflow:hidden; transition: max-height 0.3s ease, padding 0.3s ease;">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($departments as $dept)
                        <button
                            type="button"
                            onclick="openDeptModal({{ $dept->id }}, '{{ addslashes($dept->name) }}', '{{ $dept->code }}')"
                            class="border border-gray-200 rounded-lg p-4 hover:border-indigo-400 hover:shadow-md hover:bg-indigo-50 transition-all text-left group focus:outline-none focus:ring-2 focus:ring-indigo-400"
                        >
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="font-semibold text-gray-900 group-hover:text-indigo-700 transition-colors">{{ $dept->name }}</h3>
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-1 text-xs font-semibold rounded bg-gray-100 text-gray-800 group-hover:bg-indigo-100 group-hover:text-indigo-800 transition-colors">{{ $dept->code }}</span>
                                    <svg class="w-4 h-4 text-gray-300 group-hover:text-indigo-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-2xl font-bold text-indigo-600">{{ $dept->supply_requests_count }}</p>
                            <p class="text-xs text-gray-500 group-hover:text-indigo-500 transition-colors">Total requests released — click to view</p>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Releases Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-900">Pending Releases</h2>
            <a href="{{ route('admin.releases.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approved By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pendingRequests as $request)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $request->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $request->user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded bg-blue-100 text-blue-800">
                                    {{ $request->department->code }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $request->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $request->managerApprover->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $request->items->count() }} item(s)
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.releases.show', $request->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Review</a>
                                <button onclick="quickRelease({{ $request->id }})" class="text-green-600 hover:text-green-900">Release</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">
                                No pending releases at this time.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ── Department Requests Modal ── --}}
<div id="deptModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col">

        {{-- Modal Header --}}
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between shrink-0">
            <div>
                <h3 class="text-lg font-bold text-gray-900" id="deptModalTitle">Department Requests</h3>
                <p class="text-sm text-gray-500 mt-0.5" id="deptModalSubtitle">Released supply requests</p>
            </div>
            <button onclick="closeDeptModal()" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Modal Body --}}
        <div class="flex-1 overflow-y-auto px-6 py-4">

            {{-- Loading state --}}
            <div id="deptModalLoading" class="flex items-center justify-center py-16">
                <svg class="animate-spin h-8 w-8 text-indigo-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span class="ml-3 text-sm text-gray-500">Loading requests…</span>
            </div>

            {{-- Content --}}
            <div id="deptModalContent" class="hidden">

                {{-- Sort control --}}
                <div class="flex items-center justify-between mb-4">
                    <p id="deptModalCount" class="text-sm text-gray-500"></p>
                    <select id="deptSortOrder" onchange="loadDeptPage(1)"
                        class="text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="desc">Newest first</option>
                        <option value="asc">Oldest first</option>
                    </select>
                </div>

                {{-- Table --}}
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Serial No.</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Employee</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Type</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Budget</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Released</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody id="deptModalTableBody" class="bg-white divide-y divide-gray-100">
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div id="deptModalPagination" class="mt-4 flex items-center justify-between">
                    <p id="deptPaginationInfo" class="text-xs text-gray-500"></p>
                    <div id="deptPaginationButtons" class="flex items-center gap-1"></div>
                </div>

            </div>

            {{-- Empty state --}}
            <div id="deptModalEmpty" class="hidden text-center py-16">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="mt-3 text-sm font-semibold text-gray-700">No released requests</h3>
                <p class="mt-1 text-sm text-gray-400">This department has no released supply requests yet.</p>
            </div>

        </div>

        {{-- Modal Footer --}}
        <div class="px-6 py-3 border-t border-gray-100 bg-gray-50 flex justify-end shrink-0">
            <button onclick="closeDeptModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100">
                Close
            </button>
        </div>

    </div>
</div>

@push('scripts')
<script>
// ── Department Stats toggle ──────────────────────────────────────────
let deptOpen = false;

function toggleDeptStats() {
    const body    = document.getElementById('deptStatsBody');
    const chevron = document.getElementById('deptStatsChevron');
    if (deptOpen) {
        body.style.maxHeight = '0px';
        chevron.style.transform = 'rotate(-90deg)';
    } else {
        body.style.maxHeight = body.scrollHeight + 'px';
        chevron.style.transform = 'rotate(0deg)';
    }
    deptOpen = !deptOpen;
}

document.addEventListener('DOMContentLoaded', function () {
    const body    = document.getElementById('deptStatsBody');
    const chevron = document.getElementById('deptStatsChevron');
    body.style.maxHeight = '0px';
    chevron.style.transform = 'rotate(-90deg)';
});

// ── Department Modal ─────────────────────────────────────────────────
let currentDeptId   = null;
let currentDeptName = '';
let currentPage     = 1;

function openDeptModal(deptId, deptName, deptCode) {
    currentDeptId   = deptId;
    currentDeptName = deptName;
    currentPage     = 1;

    document.getElementById('deptModalTitle').textContent    = deptName;
    document.getElementById('deptModalSubtitle').textContent = deptCode + ' — Released supply requests';
    document.getElementById('deptSortOrder').value           = 'desc';

    document.getElementById('deptModal').classList.remove('hidden');
    document.getElementById('deptModal').classList.add('flex');
    document.body.style.overflow = 'hidden';

    loadDeptPage(1);
}

function closeDeptModal() {
    document.getElementById('deptModal').classList.add('hidden');
    document.getElementById('deptModal').classList.remove('flex');
    document.body.style.overflow = '';
    currentDeptId = null;
}

// Close on backdrop click
document.getElementById('deptModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeptModal();
});

async function loadDeptPage(page) {
    currentPage       = page;
    const sortOrder   = document.getElementById('deptSortOrder').value;

    // Show loading
    document.getElementById('deptModalLoading').classList.remove('hidden');
    document.getElementById('deptModalContent').classList.add('hidden');
    document.getElementById('deptModalEmpty').classList.add('hidden');

    try {
        const res  = await fetch(
            `/admin/dashboard/department/${currentDeptId}/requests?page=${page}&sort=${sortOrder}`,
            { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } }
        );
        const data = await res.json();

        document.getElementById('deptModalLoading').classList.add('hidden');

        if (!data.success || data.total === 0) {
            document.getElementById('deptModalEmpty').classList.remove('hidden');
            return;
        }

        // Populate table
        const tbody = document.getElementById('deptModalTableBody');
        tbody.innerHTML = data.requests.map(req => `
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-mono font-semibold text-indigo-600 text-xs whitespace-nowrap">
                    ${req.serial_number ?? '—'}
                    ${req.ro_number ? `<div class="text-xs font-sans text-gray-400">RO: ${req.ro_number}</div>` : ''}
                </td>
                <td class="px-4 py-3">
                    <div class="text-sm font-medium text-gray-900">${req.user_name}</div>
                    <div class="text-xs text-gray-500">${req.user_email}</div>
                </td>
                <td class="px-4 py-3 text-center">
                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full ${req.request_type === 'special' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'}">
                        ${req.request_type.charAt(0).toUpperCase() + req.request_type.slice(1)}
                    </span>
                </td>
                <td class="px-4 py-3 text-center">
                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full ${req.budget_type === 'budgeted' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800'}">
                        ${req.budget_type === 'budgeted' ? 'Budgeted' : 'Not Budgeted'}
                    </span>
                </td>
                <td class="px-4 py-3 text-sm text-gray-700 whitespace-nowrap">
                    ${req.released_at}
                    <div class="text-xs text-gray-400">${req.released_by ?? '—'}</div>
                </td>
                <td class="px-4 py-3 text-center">
                    <a href="/admin/voucher/${req.id}" target="_blank"
                       class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white text-xs font-semibold rounded-lg hover:bg-indigo-700 transition">
                        Voucher
                    </a>
                </td>
            </tr>
        `).join('');

        // Pagination info
        document.getElementById('deptModalCount').textContent =
            `${data.total} released request${data.total !== 1 ? 's' : ''} found`;
        document.getElementById('deptPaginationInfo').textContent =
            `Showing ${data.from}–${data.to} of ${data.total}`;

        // Pagination buttons
        const btnContainer = document.getElementById('deptPaginationButtons');
        btnContainer.innerHTML = '';

        // Prev
        const prevBtn = document.createElement('button');
        prevBtn.textContent = '← Prev';
        prevBtn.className = `px-3 py-1.5 text-xs font-medium rounded-md border ${
            data.current_page <= 1
                ? 'border-gray-200 text-gray-300 cursor-not-allowed'
                : 'border-gray-300 text-gray-700 hover:bg-gray-50 cursor-pointer'
        }`;
        prevBtn.disabled = data.current_page <= 1;
        if (data.current_page > 1) prevBtn.onclick = () => loadDeptPage(data.current_page - 1);
        btnContainer.appendChild(prevBtn);

        // Page numbers (show up to 5 around current)
        const startPage = Math.max(1, data.current_page - 2);
        const endPage   = Math.min(data.last_page, data.current_page + 2);

        for (let p = startPage; p <= endPage; p++) {
            const btn = document.createElement('button');
            btn.textContent = p;
            btn.className = `px-3 py-1.5 text-xs font-medium rounded-md border ${
                p === data.current_page
                    ? 'bg-indigo-600 border-indigo-600 text-white'
                    : 'border-gray-300 text-gray-700 hover:bg-gray-50 cursor-pointer'
            }`;
            if (p !== data.current_page) btn.onclick = () => loadDeptPage(p);
            btnContainer.appendChild(btn);
        }

        // Next
        const nextBtn = document.createElement('button');
        nextBtn.textContent = 'Next →';
        nextBtn.className = `px-3 py-1.5 text-xs font-medium rounded-md border ${
            data.current_page >= data.last_page
                ? 'border-gray-200 text-gray-300 cursor-not-allowed'
                : 'border-gray-300 text-gray-700 hover:bg-gray-50 cursor-pointer'
        }`;
        nextBtn.disabled = data.current_page >= data.last_page;
        if (data.current_page < data.last_page) nextBtn.onclick = () => loadDeptPage(data.current_page + 1);
        btnContainer.appendChild(nextBtn);

        document.getElementById('deptModalContent').classList.remove('hidden');

    } catch (e) {
        document.getElementById('deptModalLoading').classList.add('hidden');
        document.getElementById('deptModalEmpty').classList.remove('hidden');
        console.error('Failed to load department requests', e);
    }
}

// ── Quick release ────────────────────────────────────────────────────
async function quickRelease(requestId) {
    if (!confirm('Release this request and generate serial number?')) return;

    try {
        const response = await fetch(`/admin/releases/${requestId}/release`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ notes: 'Quick release from dashboard' })
        });

        const data = await response.json();

        if (data.success) {
            alert(`Request released! Serial: ${data.serial_number}`);
            location.reload();
        } else {
            alert('Failed to release request');
        }
    } catch (error) {
        alert('An error occurred. Please try again.');
    }
}
</script>
@endpush
@endsection