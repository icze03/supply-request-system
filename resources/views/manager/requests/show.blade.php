@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Request Details</h1>
                <p class="mt-1 text-sm text-gray-600">View and manage supply request</p>
            </div>
            <a href="{{ route('manager.approvals.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">
                Back to Approvals
            </a>
        </div>
    </div>

    @php
        $hasPartialRelease = $request->items->contains(fn($i) => ($i->released_quantity ?? 0) > 0);
        $isFullyReleased   = $request->status === 'admin_released';
        $isPending         = $request->status === 'pending';
    @endphp

    <!-- Request Card -->
    <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">SR #{{ $request->sr_number }}</h2>
                    <p class="text-sm text-gray-600">Submitted {{ $request->created_at->format('F d, Y h:i A') }}</p>
                    @if($request->serial_number)
                        <p class="text-sm font-mono text-indigo-600 mt-1">Serial: {{ $request->serial_number }}</p>
                    @endif
                </div>
                <div class="text-right">
                    @if($isFullyReleased)
                        <span class="px-3 py-1 text-sm font-bold rounded-full bg-green-100 text-green-800 border border-green-300">
                            Fully Released
                        </span>
                    @elseif($hasPartialRelease)
                        <span class="px-3 py-1 text-sm font-bold rounded-full bg-blue-100 text-blue-800 border border-blue-300">
                            Partially Released
                        </span>
                    @else
                        <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $request->getStatusBadgeColor() }}">
                            {{ $request->getStatusLabel() }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Request Info -->
        <div class="px-6 py-4 space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-500">Requester</p>
                    <p class="mt-1 text-sm text-gray-900">{{ $request->user->name }}</p>
                    <p class="text-xs text-gray-500">{{ $request->user->email }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Department</p>
                    <p class="mt-1 text-sm text-gray-900">{{ $request->department->name }}</p>
                    <p class="text-xs text-gray-500">{{ $request->department->code }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Request Type</p>
                    <p class="mt-1">
                        <span class="px-2 py-1 text-xs font-semibold rounded {{ $request->request_type === 'special' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ ucfirst($request->request_type) }}
                        </span>
                    </p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Budget Type</p>
                    <p class="mt-1">
                        <span class="px-2 py-1 text-xs font-semibold rounded {{ $request->budget_type === 'budgeted' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                            {{ $request->budget_type === 'budgeted' ? 'Budgeted' : 'Not Budgeted' }}
                        </span>
                    </p>
                </div>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500">Purpose</p>
                <p class="mt-1 text-sm text-gray-900">{{ $request->purpose }}</p>
            </div>

            <!-- Items Table -->
            @if($request->request_type === 'standard')
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-sm font-medium text-gray-500">
                            Requested Items ({{ $request->items->count() }})
                            @if($hasPartialRelease)
                                <span class="text-xs text-blue-600 ml-2">— showing release status</span>
                            @endif
                        </p>
                        @if($isPending)
                            <button onclick="enableBulkEdit()" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                                Edit Quantities
                            </button>
                        @endif
                    </div>
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item Name</th>
                                    @if($hasPartialRelease)
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Requested</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Released</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Remaining</th>
                                    @else
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Quantity</th>
                                    @endif
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Unit</th>
                                    @if($isPending)
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase edit-header hidden">Actions</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($request->items as $item)
                                    @php
                                        $released  = $item->released_quantity  ?? 0;
                                        $remaining = $item->remaining_quantity ?? $item->quantity;
                                        $original  = $item->original_quantity  ?? $item->quantity;
                                        $done      = $released > 0 && $remaining == 0;
                                        $partial   = $released > 0 && $remaining > 0;
                                    @endphp
                                    <tr class="{{ $done ? 'bg-green-50' : ($partial ? 'bg-blue-50' : 'bg-white') }} hover:bg-gray-100 transition" 
                                        id="item-row-{{ $item->id }}">
                                        <td class="px-6 py-4 text-sm font-mono text-gray-900">{{ $item->item_code }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ $item->item_name }}</td>
                                        
                                        @if($hasPartialRelease)
                                            <td class="px-6 py-4 text-center text-sm text-gray-600">{{ $original }}</td>
                                            <td class="px-6 py-4 text-center text-sm font-semibold {{ $released > 0 ? 'text-green-700' : 'text-gray-300' }}">
                                                {{ $released > 0 ? $released : '—' }}
                                            </td>
                                            <td class="px-6 py-4 text-center text-sm font-semibold {{ $remaining > 0 ? 'text-orange-600' : 'text-gray-300' }}">
                                                {{ $remaining > 0 ? $remaining : '—' }}
                                            </td>
                                        @else
                                            <td class="px-6 py-4 text-center">
                                                <span class="quantity-display-{{ $item->id }} text-sm font-semibold text-gray-900">
                                                    {{ $item->quantity }}
                                                </span>
                                                @if($isPending)
                                                    <input 
                                                        type="number" 
                                                        class="quantity-edit-{{ $item->id }} hidden w-20 mx-auto text-center rounded-md border-gray-300" 
                                                        value="{{ $item->quantity }}" 
                                                        min="1"
                                                        max="9999">
                                                @endif
                                            </td>
                                        @endif
                                        
                                        <td class="px-6 py-4 text-center text-sm text-gray-500">{{ $item->supply->unit ?? 'N/A' }}</td>
                                        
                                        @if($isPending)
                                            <td class="px-6 py-4 text-center edit-actions hidden">
                                                <div class="flex items-center justify-center gap-2">
                                                    <button 
                                                        onclick="saveQuantity({{ $item->id }})" 
                                                        class="text-green-600 hover:text-green-800 text-sm font-medium">
                                                        Save
                                                    </button>
                                                    <button 
                                                        onclick="cancelEdit({{ $item->id }})" 
                                                        class="text-gray-600 hover:text-gray-800 text-sm font-medium">
                                                        Cancel
                                                    </button>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                            @if($hasPartialRelease)
                                <tfoot class="bg-gray-50 border-t border-gray-200 text-xs font-bold">
                                    <tr>
                                        <td colspan="2" class="px-6 py-3 text-gray-500 uppercase">Totals</td>
                                        <td class="px-6 py-3 text-center text-gray-700">{{ $request->items->sum(fn($i) => $i->original_quantity ?? $i->quantity) }}</td>
                                        <td class="px-6 py-3 text-center text-green-700">{{ $request->items->sum(fn($i) => $i->released_quantity ?? 0) }}</td>
                                        <td class="px-6 py-3 text-center text-orange-600">{{ $request->items->sum(fn($i) => $i->remaining_quantity ?? $i->quantity) }}</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            @else
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-2">Special Item Description</p>
                    <div class="bg-purple-50 border border-purple-200 rounded-md p-4">
                        <p class="text-sm text-gray-900">{{ $request->special_item_description }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Actions -->
        @if($isPending)
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <div class="flex justify-end space-x-3">
                    <button 
                        onclick="rejectRequest()" 
                        class="px-6 py-2 border border-red-300 rounded-md text-sm font-medium text-red-700 bg-white hover:bg-red-50">
                        Reject Request
                    </button>
                    <button 
                        onclick="approveRequest()" 
                        class="px-6 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                        Approve Request
                    </button>
                </div>
            </div>
        @endif
    </div>

    @if($hasPartialRelease)
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-800">
            <strong>Partial Release Status:</strong> This request has been partially released. 
            Remaining items are awaiting stock replenishment or admin action.
        </div>
    @endif
</div>

<!-- Approval/Reject Modal -->
<div id="actionModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4" id="modalTitle"></h3>
        <form id="actionForm">
            <input type="hidden" id="action-type">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Notes <span id="required-indicator"></span></label>
                <textarea 
                    id="action-notes" 
                    rows="4" 
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Add notes..."></textarea>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="closeActionModal()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" id="submitActionBtn" class="px-4 py-2 rounded-md text-sm font-medium text-white">
                    Confirm
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
/* ─── Bulk Edit Mode ────────────────────────────────────────────────── */
function enableBulkEdit() {
    document.querySelectorAll('.edit-header').forEach(el => el.classList.remove('hidden'));
    document.querySelectorAll('.edit-actions').forEach(el => el.classList.remove('hidden'));
    document.querySelectorAll('[class^="quantity-display-"]').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('[class^="quantity-edit-"]').forEach(el => el.classList.remove('hidden'));
}

function cancelEdit(itemId) {
    const display = document.querySelector(`.quantity-display-${itemId}`);
    const edit = document.querySelector(`.quantity-edit-${itemId}`);
    edit.value = display.textContent.trim();
}

async function saveQuantity(itemId) {
    const newQuantity = document.querySelector(`.quantity-edit-${itemId}`).value;
    
    if (newQuantity < 1) {
        alert('Quantity must be at least 1');
        return;
    }
    
    try {
        const response = await fetch(`/manager/requests/{{ $request->id }}/update-item`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                item_id: itemId,
                quantity: newQuantity
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            document.querySelector(`.quantity-display-${itemId}`).textContent = newQuantity;
            alert('✓ Quantity updated successfully');
        } else {
            alert(data.message || 'Failed to update quantity');
        }
    } catch (error) {
        alert('An error occurred. Please try again.');
    }
}

/* ─── Approval/Reject ───────────────────────────────────────────────── */
function approveRequest() {
    document.getElementById('modalTitle').textContent = 'Approve Request';
    document.getElementById('action-type').value = 'approve';
    document.getElementById('required-indicator').textContent = '';
    document.getElementById('action-notes').placeholder = 'Add approval notes (optional)';
    document.getElementById('submitActionBtn').className = 'px-4 py-2 rounded-md text-sm font-medium text-white bg-green-600 hover:bg-green-700';
    document.getElementById('submitActionBtn').textContent = '✓ Approve';
    document.getElementById('actionModal').classList.remove('hidden');
}

function rejectRequest() {
    document.getElementById('modalTitle').textContent = 'Reject Request';
    document.getElementById('action-type').value = 'reject';
    document.getElementById('required-indicator').textContent = '*';
    document.getElementById('action-notes').placeholder = 'Reason for rejection (required)';
    document.getElementById('submitActionBtn').className = 'px-4 py-2 rounded-md text-sm font-medium text-white bg-red-600 hover:bg-red-700';
    document.getElementById('submitActionBtn').textContent = 'Reject';
    document.getElementById('actionModal').classList.remove('hidden');
}

function closeActionModal() {
    document.getElementById('actionModal').classList.add('hidden');
    document.getElementById('actionForm').reset();
}

document.getElementById('actionForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const action = document.getElementById('action-type').value;
    const notes = document.getElementById('action-notes').value.trim();
    
    if (action === 'reject' && !notes) {
        alert('Please provide a reason for rejection');
        return;
    }
    
    try {
        const response = await fetch(`/manager/approvals/{{ $request->id }}/${action}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ notes: notes })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message);
            window.location.href = '{{ route("manager.approvals.index") }}';
        } else {
            alert(data.message || 'Action failed');
        }
    } catch (error) {
        alert('An error occurred. Please try again.');
    }
});
</script>
@endpush
@endsection