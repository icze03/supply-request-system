@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Release Management</h1>
        <p class="mt-1 text-sm text-gray-600">Review approved requests and dispatch supplies</p>
    </div>

    <!-- Tabs -->
    <div class="border-b border-gray-200 mb-6">
        <nav class="-mb-px flex space-x-8">
            <button onclick="switchTab('pending')" id="pendingTab"
                class="border-b-2 border-indigo-500 py-4 px-1 text-sm font-medium text-indigo-600">
                Pending Release ({{ $pendingReleases->total() }})
            </button>
            <button onclick="switchTab('today')" id="todayTab"
                class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                Released Today ({{ $releasedToday->total() }})
            </button>
            <button onclick="switchTab('history')" id="historyTab"
                class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                Release History ({{ $releaseHistory->total() }})
            </button>
        </nav>
    </div>

    {{-- ═══════════════ PENDING TAB ═══════════════ --}}
    <div id="pendingContent">
        @if($pendingReleases->isNotEmpty())
            <div class="space-y-4">
                @foreach($pendingReleases as $req)
                    @php
                        $isPartial   = $req->items->contains(fn($i) => ($i->released_quantity ?? 0) > 0);
                        $borderColor = $isPartial ? 'border-blue-500' : 'border-yellow-400';
                    @endphp
                    <div class="bg-white shadow rounded-lg overflow-hidden border-l-4 {{ $borderColor }}">
                        <div class="p-6">

                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">SR #{{ $req->sr_number }}</h3>
                                    <p class="text-sm text-gray-500 mt-0.5">Submitted {{ $req->created_at->format('M d, Y h:i A') }}</p>
                                </div>
                                <div class="flex flex-col items-end gap-1">
                                    @if($isPartial)
                                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-800 border border-blue-300">
                                            Partially Released
                                        </span>
                                        @if($req->serial_number)
                                            <span class="text-xs font-mono text-blue-600">{{ $req->serial_number }}</span>
                                        @endif
                                    @else
                                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-yellow-50 text-yellow-800 border border-yellow-300">
                                            Awaiting Release
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-5 pb-5 border-b border-gray-100">
                                <div>
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Employee</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $req->user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $req->user->email }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Department</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $req->department->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $req->department->code }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Request Type</p>
                                    <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full
                                        {{ $req->request_type === 'special' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $req->request_type === 'special' ? 'Special' : 'Standard' }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Budget</p>
                                    <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full
                                        {{ $req->budget_type === 'budgeted' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                                        {{ $req->budget_type === 'budgeted' ? 'Budgeted' : 'Not Budgeted' }}
                                    </span>
                                </div>
                            </div>

                            @if($req->request_type === 'standard')
                                <div class="mb-5">
                                    <p class="text-sm font-semibold text-gray-700 mb-2">
                                        Items ({{ $req->items->count() }})
                                        @if($isPartial)
                                            <span class="font-normal text-xs text-blue-500 ml-1">— showing remaining quantities</span>
                                        @endif
                                    </p>
                                    <div class="bg-gray-50 rounded-lg divide-y divide-gray-100">
                                        @foreach($req->items->take(4) as $item)
                                            @php
                                                $released  = $item->released_quantity  ?? 0;
                                                $remaining = $item->remaining_quantity ?? $item->quantity;
                                                $original  = $item->original_quantity  ?? $item->quantity;
                                            @endphp
                                            <div class="flex items-center justify-between px-4 py-2">
                                                <span class="text-sm text-gray-800">{{ $item->item_name }}</span>
                                                <div class="flex items-center gap-3">
                                                    @if($released > 0 && $remaining > 0)
                                                        <span class="text-xs text-blue-600 font-medium">{{ $released }}/{{ $original }} released</span>
                                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-orange-100 text-orange-700">{{ $remaining }} pending</span>
                                                    @elseif($released > 0 && $remaining == 0)
                                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-700">✓ Done</span>
                                                    @else
                                                        <span class="text-sm font-semibold text-gray-700">{{ $remaining }} <span class="font-normal text-gray-400">{{ $item->supply->unit ?? 'pcs' }}</span></span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                        @if($req->items->count() > 4)
                                            <div class="px-4 py-2">
                                                <p class="text-xs text-gray-400 italic">+{{ $req->items->count() - 4 }} more — see full details</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="mb-5">
                                    <p class="text-sm font-semibold text-gray-700 mb-2">Special Request Description</p>
                                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-3">
                                        <p class="text-sm text-gray-800">{{ $req->special_item_description }}</p>
                                    </div>
                                </div>
                            @endif

                            <div class="flex items-center gap-2 text-sm bg-green-50 border border-green-200 rounded-lg px-4 py-2 mb-5">
                                <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="font-semibold text-green-800">{{ $req->managerApprover->name ?? 'Manager' }}</span>
                                <span class="text-gray-400">·</span>
                                <span class="text-xs text-gray-500">{{ $req->manager_approved_at->format('M d, Y h:i A') }}</span>
                            </div>

                            <div class="flex flex-wrap gap-3">
                                <a href="{{ route('admin.releases.show', $req->id) }}"
                                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                                    View Details
                                </a>
                                <button onclick="releaseRequest({{ $req->id }})"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-green-700 rounded-lg text-sm font-semibold text-white hover:bg-green-700 transition">
                                    {{ $isPartial ? 'Release Remaining' : 'Release Now' }}
                                </button>
                                @if($req->serial_number)
                                    <a href="{{ route('admin.voucher', $req->id) }}" target="_blank"
                                       class="inline-flex items-center px-4 py-2 bg-indigo-50 border border-indigo-300 rounded-lg text-sm font-semibold text-indigo-700 hover:bg-indigo-100 transition">
                                        View Voucher
                                    </a>
                                @endif
                                <button onclick="rejectRequest({{ $req->id }})"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-red-700 rounded-lg text-sm font-semibold text-white hover:bg-red-700 transition ml-auto">
                                    Reject
                                </button>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-6">{{ $pendingReleases->links() }}</div>
        @else
            <div class="bg-white shadow rounded-lg p-16 text-center">
                <svg class="mx-auto h-14 w-14 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="mt-4 text-base font-semibold text-gray-700">No pending releases</h3>
                <p class="mt-1 text-sm text-gray-400">All approved requests have been processed.</p>
            </div>
        @endif
    </div>

    {{-- ═══════════════ RELEASED TODAY TAB ═══════════════ --}}
    <div id="todayContent" class="hidden">
        @if($releasedToday->count() > 0)
            <div class="bg-white shadow rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Serial / Round</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">SR Number</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Employee</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Department</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Items Released</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Qty Released</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Type</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Time</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Released By</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($releasedToday as $tx)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <p class="text-sm font-mono font-semibold text-indigo-600">{{ $tx->serial_number }}</p>
                                    <p class="text-xs text-gray-400">Round {{ $tx->round }}</p>
                                    @if($tx->ro_number)
                                        <p class="text-xs text-gray-400">RO: {{ $tx->ro_number }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('admin.releases.show', $tx->supply_request_id) }}"
                                       class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                                        SR #{{ $tx->supplyRequest->sr_number }}
                                    </a>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $tx->supplyRequest->user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $tx->supplyRequest->user->email }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-900">{{ $tx->supplyRequest->department->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $tx->supplyRequest->department->code }}</div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="text-sm font-bold text-gray-800">{{ $tx->items_fully_released_this_round }}</span>
                                    <span class="text-xs text-gray-400"> / {{ $tx->total_items_in_request }}</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="text-sm font-bold text-green-700">
                                        {{ collect($tx->items_snapshot)->sum('qty_released') }}
                                    </span>
                                    <span class="text-xs text-gray-400"> units</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($tx->is_final_release)
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">✓ Final</span>
                                    @else
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">◑ Partial</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-gray-700">
                                    {{ $tx->created_at->format('h:i A') }}
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-gray-700">
                                    {{ $tx->releasedBy->name ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.voucher', $tx->supply_request_id) }}" target="_blank"
                                           class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white text-xs font-semibold rounded-lg hover:bg-indigo-700 transition">
                                            Voucher
                                        </a>
                                        <button onclick="deleteTransaction({{ $tx->id }}, '{{ $tx->serial_number }}', {{ $tx->round }})"
                                            class="inline-flex items-center px-3 py-1.5 bg-red-500 text-white text-xs font-semibold rounded-lg hover:bg-red-600 transition">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-6">{{ $releasedToday->links() }}</div>
        @else
            <div class="bg-white shadow rounded-lg p-16 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="mt-3 text-base font-semibold text-gray-700">No releases today</h3>
                <p class="mt-1 text-sm text-gray-400">No supplies have been dispatched today.</p>
            </div>
        @endif
    </div>

    {{-- ═══════════════ HISTORY TAB ═══════════════ --}}
    <div id="historyContent" class="hidden">
        @if($releaseHistory->count() > 0)
            <div class="bg-white shadow rounded-lg p-4 mb-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Search Serial</label>
                        <input type="text" id="searchSR" placeholder="e.g. HR-20240224-0001"
                            class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            onkeyup="filterHistory()">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Department</label>
                        <select id="filterDept" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500" onchange="filterHistory()">
                            <option value="">All Departments</option>
                            @foreach(\App\Models\Department::all() as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Release Type</label>
                        <select id="filterType" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500" onchange="filterHistory()">
                            <option value="">All Types</option>
                            <option value="final">Final Only</option>
                            <option value="partial">Partial Only</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Date Range</label>
                        <select id="filterDate" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500" onchange="filterHistory()">
                            <option value="">All Time</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="year">This Year</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Serial / Round</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase">SR Number</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Employee</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Department</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Qty Released</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Type</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Released By</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="historyTableBody">
                        @foreach($releaseHistory as $tx)
                            <tr class="hover:bg-gray-50"
                                data-sr="{{ strtolower($tx->serial_number ?? '') }}"
                                data-dept="{{ $tx->supplyRequest->department_id }}"
                                data-date="{{ $tx->created_at->format('Y-m-d') }}"
                                data-type="{{ $tx->is_final_release ? 'final' : 'partial' }}">
                                <td class="px-3 py-3">
                                    <p class="text-sm font-mono font-semibold text-indigo-600">{{ $tx->serial_number }}</p>
                                    <p class="text-xs text-gray-400">Round {{ $tx->round }}</p>
                                    @if($tx->ro_number)
                                        <p class="text-xs text-gray-400">RO: {{ $tx->ro_number }}</p>
                                    @endif
                                </td>
                                <td class="px-3 py-3">
                                    <a href="{{ route('admin.releases.show', $tx->supply_request_id) }}"
                                       class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                                        SR #{{ $tx->supplyRequest->sr_number }}
                                    </a>
                                </td>
                                <td class="px-3 py-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $tx->supplyRequest->user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $tx->supplyRequest->user->email }}</div>
                                </td>
                                <td class="px-3 py-3">
                                    <div class="text-sm text-gray-900">{{ $tx->supplyRequest->department->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $tx->supplyRequest->department->code }}</div>
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <span class="text-sm font-bold text-green-700">{{ collect($tx->items_snapshot)->sum('qty_released') }}</span>
                                    <span class="text-xs text-gray-400"> units</span>
                                </td>
                                <td class="px-3 py-3 text-center">
                                    @if($tx->is_final_release)
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">✓ Final</span>
                                    @else
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">◑ Partial</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-sm text-gray-700">
                                    {{ $tx->created_at->format('M d, Y') }}
                                    <div class="text-xs text-gray-400">{{ $tx->created_at->format('h:i A') }}</div>
                                </td>
                                <td class="px-3 py-3 text-sm text-gray-700">
                                    {{ $tx->releasedBy->name ?? '—' }}
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.voucher', $tx->supply_request_id) }}" target="_blank"
                                           class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white text-xs font-semibold rounded-lg hover:bg-indigo-700 transition">
                                            Voucher
                                        </a>
                                        <button onclick="deleteTransaction({{ $tx->id }}, '{{ $tx->serial_number }}', {{ $tx->round }})"
                                            class="inline-flex items-center px-3 py-1.5 bg-red-500 text-white text-xs font-semibold rounded-lg hover:bg-red-600 transition">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-6">{{ $releaseHistory->links() }}</div>
        @else
            <div class="bg-white shadow rounded-lg p-16 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="mt-3 text-base font-semibold text-gray-700">No release history yet</h3>
                <p class="mt-1 text-sm text-gray-400">Released transactions will appear here.</p>
            </div>
        @endif
    </div>

</div>

{{-- ═══════════════ REJECT MODAL ═══════════════ --}}
<div id="rejectModal"
     style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center; padding:1rem;">
    <div style="background:#fff; border-radius:0.75rem; max-width:28rem; width:100%; padding:1.5rem; margin:auto; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
        <h3 style="font-size:1.125rem; font-weight:600; color:#111827; margin-bottom:0.25rem;">Reject Request</h3>
        <p style="font-size:0.875rem; color:#6B7280; margin-bottom:1rem;">This will mark the request as rejected. Please provide a reason.</p>
        <input type="hidden" id="rejectRequestId">
        <div style="margin-bottom:1rem;">
            <label style="display:block; font-size:0.875rem; font-weight:500; color:#374151; margin-bottom:0.25rem;">
                Reason <span style="color:#EF4444;">*</span>
            </label>
            <textarea id="rejectNotes" rows="3"
                style="width:100%; border:1px solid #D1D5DB; border-radius:0.375rem; padding:0.5rem 0.75rem; font-size:0.875rem; resize:vertical; box-sizing:border-box;"
                placeholder="Explain why this request is being rejected..."></textarea>
            <p id="rejectError" style="display:none; font-size:0.75rem; color:#DC2626; margin-top:0.25rem;">Please enter a reason before rejecting.</p>
        </div>
        <div style="display:flex; justify-content:flex-end; gap:0.75rem;">
            <button id="rejectCancelBtn"
                style="padding:0.5rem 1rem; border:1px solid #D1D5DB; border-radius:0.375rem; font-size:0.875rem; font-weight:500; color:#374151; cursor:pointer; background:#fff;">
                Cancel
            </button>
            <button id="rejectConfirmBtn"
                style="padding:0.5rem 1rem; background:#DC2626; color:#fff; border-radius:0.375rem; font-size:0.875rem; font-weight:600; cursor:pointer; border:none;">
                Confirm Rejection
            </button>
        </div>
    </div>
</div>

{{-- ═══════════════ RELEASE SUCCESS MODAL ═══════════════ --}}
<div id="successModal"
     style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.55); z-index:9999; align-items:center; justify-content:center; padding:1rem;">
    <div style="background:#fff; border-radius:0.75rem; max-width:26rem; width:100%; padding:2rem; margin:auto; box-shadow:0 25px 60px rgba(0,0,0,0.3); text-align:center;">

        {{-- Icon --}}
        <div id="successIcon" style="width:4rem; height:4rem; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 1.25rem;">
            <svg style="width:2rem; height:2rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        {{-- Title --}}
        <h3 id="successTitle" style="font-size:1.25rem; font-weight:700; color:#111827; margin-bottom:0.25rem;"></h3>

        {{-- Serial --}}
        <p style="font-size:0.75rem; color:#6B7280; margin-bottom:0.25rem; margin-top:0.75rem;">Serial Number</p>
        <p id="successSerial" style="font-size:1.375rem; font-family:monospace; font-weight:700; color:#4F46E5; margin-bottom:0.5rem;"></p>

        {{-- RO Number (optional) --}}
        <p id="successRO" style="display:none; font-size:0.75rem; color:#6B7280; margin-bottom:0.75rem;"></p>

        {{-- Status note --}}
        <div id="successNote" style="border-radius:0.5rem; padding:0.625rem 0.875rem; font-size:0.75rem; margin-bottom:1.5rem; border:1px solid; text-align:left; line-height:1.5;"></div>

        {{-- Actions --}}
        <div style="display:flex; gap:0.625rem; justify-content:center;">
            <a id="successVoucherBtn" href="#" target="_blank"
               style="padding:0.5rem 1.25rem; background:#4F46E5; color:#fff; border-radius:0.5rem; font-size:0.875rem; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:0.375rem;">
                <svg style="width:1rem; height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                View Voucher
            </a>
            <button id="successDoneBtn"
                style="padding:0.5rem 1.25rem; background:#fff; color:#374151; border:1px solid #D1D5DB; border-radius:0.5rem; font-size:0.875rem; font-weight:500; cursor:pointer;">
                Done
            </button>
        </div>

    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('rejectCancelBtn').addEventListener('click', closeRejectModal);
    document.getElementById('rejectConfirmBtn').addEventListener('click', confirmReject);
    document.getElementById('rejectModal').addEventListener('click', function(e) {
        if (e.target === this) closeRejectModal();
    });
    document.getElementById('successModal').addEventListener('click', function(e) {
        if (e.target === this) closeSuccessModal();
    });
    document.getElementById('successDoneBtn').addEventListener('click', function() {
        closeSuccessModal();
        window.location.reload();
    });
});

/* ─── Tabs ──────────────────────────────────────────────────────── */
function switchTab(tab) {
    ['pending','today','history'].forEach(t => {
        const btn  = document.getElementById(t + 'Tab');
        const body = document.getElementById(t + 'Content');
        if (t === tab) {
            btn.className = 'border-b-2 border-indigo-500 py-4 px-1 text-sm font-medium text-indigo-600';
            body.classList.remove('hidden');
        } else {
            btn.className = 'border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300';
            body.classList.add('hidden');
        }
    });
}

/* ─── History filter ────────────────────────────────────────────── */
function filterHistory() {
    const search = document.getElementById('searchSR').value.toLowerCase();
    const dept   = document.getElementById('filterDept').value;
    const type   = document.getElementById('filterType').value;
    const range  = document.getElementById('filterDate').value;
    const today  = new Date(); today.setHours(0,0,0,0);

    document.querySelectorAll('#historyTableBody tr').forEach(row => {
        const srMatch   = !search || row.dataset.sr.includes(search);
        const deptMatch = !dept   || row.dataset.dept === dept;
        const typeMatch = !type   || row.dataset.type === type;
        let dateMatch   = true;
        if (range) {
            const d    = new Date(row.dataset.date); d.setHours(0,0,0,0);
            const diff = Math.floor((today - d) / 86400000);
            if (range === 'today' && diff !== 0) dateMatch = false;
            if (range === 'week'  && diff > 7)   dateMatch = false;
            if (range === 'month' && diff > 30)  dateMatch = false;
            if (range === 'year'  && diff > 365) dateMatch = false;
        }
        row.style.display = (srMatch && deptMatch && typeMatch && dateMatch) ? '' : 'none';
    });
}

/* ─── Success modal ─────────────────────────────────────────────── */
function showSuccessModal(data) {
    const partial = data.allocation_type === 'partial';

    // Icon colours
    const icon = document.getElementById('successIcon');
    icon.style.background = partial ? '#DBEAFE' : '#DCFCE7';
    icon.querySelector('svg').style.color = partial ? '#2563EB' : '#16A34A';

    document.getElementById('successTitle').textContent   = partial ? 'Partial Release Saved' : 'Release Successful!';
    document.getElementById('successSerial').textContent  = data.serial_number;

    // RO number
    const roEl = document.getElementById('successRO');
    if (data.ro_number) {
        roEl.textContent  = 'RO: ' + data.ro_number;
        roEl.style.display = 'block';
    } else {
        roEl.style.display = 'none';
    }

    // Status note
    const note = document.getElementById('successNote');
    if (partial) {
        note.style.background   = '#EFF6FF';
        note.style.borderColor  = '#BFDBFE';
        note.style.color        = '#1D4ED8';
        note.innerHTML = 'Items not fully allocated remain in <strong>Pending Release</strong> and can be released when stock is replenished.';
    } else {
        note.style.background  = '#F0FDF4';
        note.style.borderColor = '#BBF7D0';
        note.style.color       = '#15803D';
        note.innerHTML = 'All items have been fully released. The request is now <strong>closed</strong>.';
    }

    // Voucher button
    document.getElementById('successVoucherBtn').href = data.voucher_url;

    // Done button label
    document.getElementById('successDoneBtn').textContent = partial ? 'Back to Pending' : 'Done';

    document.getElementById('successModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeSuccessModal() {
    document.getElementById('successModal').style.display = 'none';
    document.body.style.overflow = '';
}

/* ─── Reject modal ──────────────────────────────────────────────── */
function rejectRequest(id) {
    document.getElementById('rejectRequestId').value     = id;
    document.getElementById('rejectNotes').value         = '';
    document.getElementById('rejectError').style.display = 'none';
    document.getElementById('rejectModal').style.display = 'flex';
    setTimeout(() => document.getElementById('rejectNotes').focus(), 50);
}

function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
}

async function confirmReject() {
    const id    = document.getElementById('rejectRequestId').value;
    const notes = document.getElementById('rejectNotes').value.trim();
    const btn   = document.getElementById('rejectConfirmBtn');
    const err   = document.getElementById('rejectError');

    if (!notes) { err.style.display = 'block'; return; }
    err.style.display  = 'none';
    btn.disabled       = true;
    btn.textContent    = 'Rejecting…';

    try {
        const res  = await fetch(`/admin/releases/${id}/reject`, {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ notes })
        });
        const data = await res.json();
        if (data.success) { closeRejectModal(); window.location.reload(); }
        else { alert('Error: ' + (data.message || 'Could not reject.')); btn.disabled = false; btn.textContent = 'Confirm Rejection'; }
    } catch(e) {
        alert('A network error occurred.');
        btn.disabled    = false;
        btn.textContent = 'Confirm Rejection';
    }
}

/* ─── Release modal ─────────────────────────────────────────────── */
async function releaseRequest(id) {
    let requestData;
    try {
        const res = await fetch(`/admin/releases/${id}/details`);
        requestData = await res.json();
    } catch(e) { alert('Failed to load request details.'); return; }
    if (!requestData.success) { alert('Error loading request details.'); return; }

    const req        = requestData.request;
    const isStandard = req.request_type === 'standard';

    let itemsHtml = '';
    if (isStandard) {
        itemsHtml = `<div class="mb-5">
            <p class="text-sm font-semibold text-gray-700 mb-3">Allocate quantities to release:</p>
            <div class="space-y-3 max-h-72 overflow-y-auto pr-1">`;

        req.items.forEach(item => {
            const pending    = item.quantity;
            const stock      = item.supply?.stock_quantity ?? 0;
            const maxAlloc   = Math.min(pending, stock);
            const released   = item.released_quantity || 0;
            const original   = item.original_quantity || pending;
            const stockColor = stock >= pending ? 'text-green-600' : stock > 0 ? 'text-yellow-600' : 'text-red-600';

            itemsHtml += `
            <div class="border border-gray-200 rounded-lg p-3 bg-gray-50">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <p class="text-sm font-semibold text-gray-900">${item.item_name}</p>
                        <p class="text-xs text-gray-400 font-mono">${item.item_code}</p>
                        ${released > 0 ? `<p class="text-xs text-blue-600 mt-0.5">Previously released: <strong>${released}</strong> of ${original}</p>` : ''}
                    </div>
                    <div class="text-right shrink-0 ml-3">
                        <p class="text-xs text-gray-500">Pending: <span class="font-bold text-gray-800">${pending}</span></p>
                        <p class="text-xs ${stockColor}">In stock: <span class="font-bold">${stock}</span></p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <label class="text-xs font-medium text-gray-600 shrink-0">Release qty:</label>
                    <input type="number"
                        class="allocation-input flex-1 px-3 py-1.5 border ${stock > 0 ? 'border-gray-300' : 'border-red-300 bg-red-50'} rounded-md text-sm"
                        data-item-id="${item.id}" data-pending="${pending}" data-stock="${stock}"
                        value="${maxAlloc}" min="0" max="${maxAlloc}"
                        ${stock === 0 ? 'disabled placeholder="Out of stock"' : ''} />
                    <span class="text-xs text-gray-400 shrink-0">${item.supply?.unit ?? 'pcs'}</span>
                </div>
                ${stock === 0
                    ? '<p class="text-xs text-red-500 mt-1">⚠ Out of stock — stays in pending</p>'
                    : stock < pending
                        ? '<p class="text-xs text-yellow-600 mt-1">⚠ Stock less than pending — remainder stays in pending</p>'
                        : ''}
            </div>`;
        });
        itemsHtml += `</div></div>`;
    }

    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4';
    modal.style.zIndex = '9998'; // below success modal (9999)
    modal.innerHTML = `
        <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[92vh] flex flex-col">
            <div class="px-6 py-4 border-b border-gray-200 shrink-0">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Release Supplies</h3>
                        <p class="text-sm text-gray-500 mt-0.5">SR #${req.sr_number} — ${req.department.name}</p>
                    </div>
                    <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600 ml-4 text-xl leading-none">✕</button>
                </div>
                <p class="mt-2 text-xs text-blue-600 bg-blue-50 border border-blue-200 rounded px-3 py-1.5">
                    Items with qty less than pending will remain in <strong>Pending Release</strong>.
                </p>
            </div>
            <div class="px-6 py-4 overflow-y-auto flex-1">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">RO Number <span class="text-gray-400 text-xs">(optional)</span></label>
                    <input type="text" id="roNumber" placeholder="e.g. RO-2024-001" maxlength="100"
                        class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"/>
                </div>
                ${itemsHtml}
                ${isStandard ? '<div id="allocSummary" class="rounded-lg border px-4 py-3 mb-4 text-sm hidden"></div>' : ''}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes <span class="text-gray-400 text-xs">(optional)</span></label>
                    <textarea id="releaseNotes" rows="3" placeholder="Any notes about this release..."
                        class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end gap-3 shrink-0">
                <button onclick="this.closest('.fixed').remove()"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100">
                    Cancel
                </button>
                <button id="confirmReleaseBtn"
                    class="px-6 py-2 bg-green-600 rounded-lg text-sm font-semibold text-white hover:bg-green-700 flex items-center gap-2">
                    ✓ Confirm Release
                </button>
            </div>
        </div>`;
    document.body.appendChild(modal);

    if (isStandard) {
        const inputs  = modal.querySelectorAll('.allocation-input');
        const summary = document.getElementById('allocSummary');

        function refreshSummary() {
            const all  = Array.from(inputs);
            const full = all.filter(i => (parseInt(i.value)||0) === parseInt(i.dataset.pending)).length;
            const rem  = all.length - full;
            summary.classList.remove('hidden','bg-green-50','border-green-200','text-green-800','bg-yellow-50','border-yellow-200','text-yellow-800');
            if (rem === 0) {
                summary.classList.add('bg-green-50','border-green-200','text-green-800');
                summary.innerHTML = `✓ All <strong>${all.length}</strong> items fully allocated — request will be <strong>fully closed</strong>.`;
            } else {
                summary.classList.add('bg-yellow-50','border-yellow-200','text-yellow-800');
                summary.innerHTML = `<strong>${full}/${all.length}</strong> fully allocated. <strong>${rem}</strong> item(s) will remain in Pending.`;
            }
        }
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                const max = parseInt(this.dataset.stock);
                let v = parseInt(this.value) || 0;
                if (v > max) { v = max; this.value = max; }
                if (v < 0)  { v = 0;   this.value = 0;   }
                refreshSummary();
            });
        });
        refreshSummary();
    }

    document.getElementById('confirmReleaseBtn').onclick = async function() {
        const roNumber    = document.getElementById('roNumber').value.trim();
        const notes       = document.getElementById('releaseNotes').value.trim();
        const allocations = [];

        if (isStandard) {
            modal.querySelectorAll('.allocation-input').forEach(inp => {
                allocations.push({ item_id: parseInt(inp.dataset.itemId), allocated_qty: parseInt(inp.value) || 0 });
            });
        }

        // Close release modal, show spinner
        modal.remove();

        const spinner = document.createElement('div');
        spinner.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:9998;display:flex;align-items:center;justify-content:center;';
        spinner.innerHTML = `<div style="background:#fff;border-radius:0.75rem;padding:2rem 2.5rem;display:flex;align-items:center;gap:1rem;box-shadow:0 20px 60px rgba(0,0,0,0.3);">
            <svg style="width:1.5rem;height:1.5rem;animation:spin 1s linear infinite;color:#4F46E5;" fill="none" viewBox="0 0 24 24">
                <style>@keyframes spin{to{transform:rotate(360deg)}}</style>
                <circle style="opacity:0.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path style="opacity:0.75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <span style="font-size:0.875rem;font-weight:500;color:#1F2937;">Processing release…</span>
        </div>`;
        document.body.appendChild(spinner);

        try {
            const res  = await fetch(`/admin/releases/${id}/release`, {
                method: 'POST',
                headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ ro_number: roNumber||null, notes: notes||null, allocations: allocations.length ? allocations : null })
            });
            const data = await res.json();
            spinner.remove();

            if (data.success) {
                showSuccessModal(data);
            } else {
                alert('Error: ' + (data.message || 'Release failed.'));
            }
        } catch(e) {
            spinner.remove();
            alert('A network error occurred. Please try again.');
        }
    };
}

/* ─── Delete transaction ────────────────────────────────────────── */
async function deleteTransaction(txId, serial, round) {
    if (!confirm(`Delete Round ${round} transaction (${serial})?\n\nThis removes the transaction record only. Stock and quantities are NOT reversed.\n\nThis cannot be undone.`)) return;
    try {
        const res  = await fetch(`/admin/releases/transactions/${txId}`, {
            method: 'DELETE',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        });
        const data = await res.json();
        if (data.success) {
            document.querySelector(`button[onclick*="deleteTransaction(${txId},"]`)?.closest('tr')?.remove();
        } else {
            alert('Error: ' + (data.message || 'Could not delete.'));
        }
    } catch(e) { alert('A network error occurred.'); }
}
</script>
@endpush
@endsection