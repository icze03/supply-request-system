@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">My Requests</h1>
            <p class="mt-1 text-sm text-gray-600">Track and manage your supply requests</p>
        </div>
        <a href="{{ route('employee.catalog') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
            New Request
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4 flex items-center">
            <svg class="h-5 w-5 text-green-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-sm text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4 flex items-center">
            <svg class="h-5 w-5 text-red-400 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <p class="text-sm text-red-800">{{ session('error') }}</p>
        </div>
    @endif

    <!-- Requests List -->
    <div class="space-y-4">
        @forelse($requests as $request)
            @php
                $hasPartialRelease = $request->items->contains(fn($i) => ($i->released_quantity ?? 0) > 0 && ($i->remaining_quantity ?? 0) > 0);
                $hasAnyRelease     = $request->items->contains(fn($i) => ($i->released_quantity ?? 0) > 0);
                $isFullyReleased   = $request->status === 'admin_released' && !$hasPartialRelease;
            @endphp

            <div class="bg-white shadow rounded-lg overflow-hidden border-l-4
                @if($hasPartialRelease) border-blue-400
                @elseif($request->status === 'admin_released') border-green-400
                @elseif($request->status === 'manager_approved') border-yellow-400
                @elseif(in_array($request->status, ['admin_rejected', 'manager_rejected'])) border-red-400
                @else border-gray-300
                @endif
            ">
                <div class="p-5">
                    <!-- Header Row -->
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-base font-bold text-gray-900">SR #{{ $request->sr_number }}</h3>
                                <span class="text-xs text-gray-400">•</span>
                                <span class="text-xs text-gray-500">{{ $request->created_at->format('M d, Y') }}</span>
                                <span class="text-xs capitalize px-2 py-0.5 rounded-full
                                    {{ $request->request_type === 'special' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ $request->request_type }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-500 mt-1 max-w-lg truncate">{{ $request->purpose }}</p>
                        </div>

                        <!-- Status Badge -->
                        <div class="flex flex-col items-end gap-1 ml-4 shrink-0">
                            @if($hasPartialRelease)
                                <span class="px-3 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-800 border border-blue-300">
                                    Partially Released
                                </span>
                                @if($request->serial_number)
                                    <span class="text-xs font-mono text-blue-500">{{ $request->serial_number }}</span>
                                @endif
                            @else
                                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $request->getStatusBadgeColor() }}">
                                    {{ $request->getStatusLabel() }}
                                </span>
                                @if($request->serial_number)
                                    <span class="text-xs font-mono text-gray-400">{{ $request->serial_number }}</span>
                                @endif
                            @endif
                        </div>
                    </div>

                    <!-- Items Summary -->
                    @if($request->request_type === 'standard' && $request->items->count())
                        <div class="mb-3">
                            @if($hasPartialRelease || $hasAnyRelease)
                                <!-- Show detailed breakdown when partial release happened -->
                                <div class="bg-gray-50 rounded-lg p-3 space-y-2">
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Items Breakdown</p>
                                    @foreach($request->items as $item)
                                        @php
                                            $released  = $item->released_quantity  ?? 0;
                                            $remaining = $item->remaining_quantity ?? $item->quantity;
                                            $original  = $item->original_quantity  ?? $item->quantity;
                                            $isDone    = $released > 0 && $remaining == 0;
                                            $isPending = $released == 0;
                                            $isPartial = $released > 0 && $remaining > 0;
                                        @endphp
                                        <div class="flex items-center justify-between text-sm">
                                            <div class="flex items-center gap-2">
                                                @if($isDone)
                                                    <span class="text-green-500 text-xs">✓</span>
                                                @elseif($isPartial)
                                                    <span class="text-blue-500 text-xs">●</span>
                                                @else
                                                    <span class="text-gray-300 text-xs">○</span>
                                                @endif
                                                <span class="text-gray-800">{{ $item->item_name }}</span>
                                            </div>
                                            <div class="text-right">
                                                @if($isDone)
                                                    <span class="text-xs text-green-600 font-semibold">All {{ $original }} released</span>
                                                @elseif($isPartial)
                                                    <span class="text-xs text-blue-600 font-semibold">{{ $released }} released</span>
                                                    <span class="text-xs text-orange-600 font-semibold ml-2">{{ $remaining }} pending</span>
                                                @else
                                                    <span class="text-xs text-gray-500">{{ $remaining }} {{ $item->supply->unit ?? 'pcs' }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <!-- Simple summary when no release yet -->
                                <div class="flex flex-wrap gap-2">
                                    @foreach($request->items->take(4) as $item)
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-gray-100 text-gray-700">
                                            {{ $item->item_name }}
                                            <span class="ml-1 font-semibold text-indigo-600">×{{ $item->quantity }}</span>
                                        </span>
                                    @endforeach
                                    @if($request->items->count() > 4)
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-gray-100 text-gray-500">
                                            +{{ $request->items->count() - 4 }} more
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @elseif($request->request_type === 'special')
                        <div class="mb-3">
                            <p class="text-xs text-gray-500 bg-purple-50 border border-purple-100 rounded px-3 py-2 truncate">
                                {{ $request->special_item_description }}
                            </p>
                        </div>
                    @endif

                    <!-- Partial Release Notice -->
                    @if($hasPartialRelease)
                        <div class="mb-3 flex items-start gap-2 bg-blue-50 border border-blue-200 rounded-lg px-3 py-2">
                            <svg class="w-4 h-4 text-blue-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-xs text-blue-700">
                                Some items have been released. The remaining items are still being processed and will be released when stock becomes available.
                            </p>
                        </div>
                    @endif

                    <!-- Footer Row: meta + actions -->
                    <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                        <!-- Left: approval info -->
                        <div class="text-xs text-gray-400 space-x-3">
                            @if($request->manager_approved_at)
                                <span>✓ Approved {{ $request->manager_approved_at->format('M d') }}</span>
                            @endif
                            @if($request->admin_released_at)
                                <span>✓ Released {{ $request->admin_released_at->format('M d') }}</span>
                            @endif
                            @if($request->ro_number)
                                <span class="font-mono">RO: {{ $request->ro_number }}</span>
                            @endif
                        </div>

                        <!-- Right: actions -->
                        <div class="flex items-center gap-2">
                            <a href="{{ route('employee.requests.show', $request->id) }}"
                               class="text-xs text-blue-600 hover:text-blue-900 font-medium">
                                View Details
                            </a>

                            {{-- Cancel: only when pending --}}
                            @if($request->isPending())
                                <span class="text-gray-300">|</span>
                                <button onclick="cancelRequest({{ $request->id }})"
                                        class="text-xs text-red-600 hover:text-red-900 font-medium">
                                    Cancel
                                </button>
                            @endif

                            

                            

                            {{-- View voucher when fully released --}}
                            @if($request->status === 'admin_released' && $request->serial_number && !$hasPartialRelease)
                                <span class="text-gray-300">|</span>
                                <a href="{{ route('employee.requests.voucher', $request->id) }}"
                                target="_blank"
                                class="inline-flex items-center px-2.5 py-1 bg-white-100 text-white-700 border border-white-300 rounded text-xs font-semibold hover:bg-white-200 transition">
                                    Voucher
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white shadow rounded-lg p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No requests found</h3>
                <p class="mt-1 text-sm text-gray-500">
                    <a href="{{ route('employee.catalog') }}" class="text-indigo-600 hover:text-indigo-900">Create your first request</a>
                </p>
            </div>
        @endforelse
    </div>

    @if($requests->hasPages())
        <div class="mt-6">
            {{ $requests->links() }}
        </div>
    @endif
</div>

<!-- Return Request Modal -->
<div id="returnModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-md w-full shadow-xl">
        <div class="p-6">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-orange-100 mr-3">
                    <svg class="h-5 w-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Return Supplies</h3>
                    <p id="returnSerialLabel" class="text-xs text-gray-500 font-mono"></p>
                </div>
            </div>

            <p class="text-sm text-gray-600 mb-4">
                This will submit a <strong>return request</strong> for admin approval. Once approved, the items will be returned to stock and any applicable budget deductions will be reversed.
            </p>

            <div class="mb-4">
                <label for="returnReason" class="block text-sm font-medium text-gray-700 mb-2">
                    Reason for Return <span class="text-red-500">*</span>
                </label>
                <textarea
                    id="returnReason"
                    rows="3"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                    placeholder="e.g. Item no longer needed, wrong item received, excess supplies..."
                ></textarea>
                <p id="returnReasonError" class="mt-1 text-xs text-red-600 hidden">Please provide a reason for the return.</p>
            </div>

            <div class="flex justify-end space-x-3">
                <button onclick="closeReturnModal()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button onclick="submitReturnRequest()" class="px-4 py-2 bg-orange-600 rounded-md text-sm font-medium text-white hover:bg-orange-700">
                    Submit Return Request
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentReturnId = null;

function openReturnModal(id, serial) {
    currentReturnId = id;
    document.getElementById('returnSerialLabel').textContent = 'Serial: ' + serial;
    document.getElementById('returnReason').value = '';
    document.getElementById('returnReasonError').classList.add('hidden');
    document.getElementById('returnModal').classList.remove('hidden');
}

function closeReturnModal() {
    currentReturnId = null;
    document.getElementById('returnModal').classList.add('hidden');
}

async function submitReturnRequest() {
    const reason = document.getElementById('returnReason').value.trim();
    if (!reason) {
        document.getElementById('returnReasonError').classList.remove('hidden');
        return;
    }
    document.getElementById('returnReasonError').classList.add('hidden');

    try {
        const response = await fetch(`/employee/requests/${currentReturnId}/return`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ reason })
        });

        const data = await response.json();
        if (data.success) {
            closeReturnModal();
            location.reload();
        } else {
            alert(data.message || 'Failed to submit return request.');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    }
}

async function cancelRequest(id) {
    if (!confirm('Are you sure you want to cancel this request?')) return;

    try {
        const response = await fetch(`/employee/requests/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const data = await response.json();
        if (data.success) {
            location.reload();
        } else {
            alert('Failed to cancel request');
        }
    } catch (error) {
        alert('An error occurred. Please try again.');
    }
}

document.getElementById('returnModal').addEventListener('click', function(e) {
    if (e.target === this) closeReturnModal();
});
</script>
@endpush
@endsection