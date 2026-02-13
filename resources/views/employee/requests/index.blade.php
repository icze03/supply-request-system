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

    <!-- Requests Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purpose</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serial</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($requests as $request)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $request->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="capitalize">{{ $request->request_type }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                            {{ $request->purpose }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $request->items->count() }} item(s)
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $request->getStatusBadgeColor() }}">
                                {{ $request->getStatusLabel() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                            {{ $request->serial_number ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('employee.requests.show', $request->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                            @if($request->isPending())
                                <button onclick="cancelRequest({{ $request->id }})" class="text-red-600 hover:text-red-900">Cancel</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500">
                            No requests found. <a href="{{ route('employee.catalog') }}" class="text-indigo-600 hover:text-indigo-900">Create your first request</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($requests->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $requests->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
async function cancelRequest(id) {
    if (!confirm('Are you sure you want to cancel this request?')) {
        return;
    }
    
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
</script>
@endpush
@endsection