@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Request Details</h1>
                <p class="mt-1 text-sm text-gray-600">View complete request information</p>
            </div>
            <a href="{{ route('admin.releases.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                ← Back to Releases
            </a>
        </div>
    </div>

    <!-- Request Card -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">SR Number: {{ $request->sr_number }}</h2>
                    <p class="text-sm text-gray-600">Submitted {{ $request->created_at->format('F d, Y h:i A') }}</p>
                </div>
                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $request->getStatusBadgeColor() }}">
                    {{ $request->getStatusLabel() }}
                </span>
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
                @if($request->budget_type)
                    <div>
                        <p class="text-sm font-medium text-gray-500">Budget Type</p>
                        <p class="mt-1">
                            <span class="px-2 py-1 text-xs font-semibold rounded {{ $request->budget_type === 'budgeted' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst(str_replace('_', ' ', $request->budget_type)) }}
                            </span>
                        </p>
                    </div>
                @endif
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500">Purpose</p>
                <p class="mt-1 text-sm text-gray-900">{{ $request->purpose }}</p>
            </div>

            <!-- Items -->
            @if($request->request_type === 'standard')
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-3">Requested Items ({{ $request->items->count() }})</p>
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item Name</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Quantity</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Unit</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($request->items as $item)
                                    <tr>
                                        <td class="px-6 py-4 text-sm font-mono text-gray-900">{{ $item->item_code }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ $item->item_name }}</td>
                                        <td class="px-6 py-4 text-center text-sm font-semibold text-gray-900">{{ $item->quantity }}</td>
                                        <td class="px-6 py-4 text-center text-sm text-gray-500">{{ $item->supply->unit ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
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

            <!-- Approval Trail -->
            @if($request->status !== 'pending')
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-3">Approval Trail</p>
                    <div class="space-y-3">
                        @if($request->manager_approved_at)
                            <div class="border border-green-200 bg-green-50 rounded-md p-4">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <p class="text-sm font-semibold text-green-800">Manager Approved</p>
                                        <p class="text-sm text-gray-900">{{ $request->managerApprover->name }}</p>
                                        <p class="text-xs text-gray-600">{{ $request->manager_approved_at->format('M d, Y h:i A') }}</p>
                                    </div>
                                </div>
                                @if($request->manager_notes)
                                    <p class="text-sm text-gray-700 mt-2">{{ $request->manager_notes }}</p>
                                @endif
                            </div>
                        @endif

                        @if($request->admin_released_at)
                            <div class="border border-indigo-200 bg-indigo-50 rounded-md p-4">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <p class="text-sm font-semibold text-indigo-800">Admin Released</p>
                                        <p class="text-sm text-gray-900">{{ $request->adminReleaser->name }}</p>
                                        <p class="text-xs text-gray-600">{{ $request->admin_released_at->format('M d, Y h:i A') }}</p>
                                    </div>
                                </div>
                                @if($request->serial_number)
                                    <p class="text-sm font-mono font-semibold text-indigo-900 mt-2">Serial: {{ $request->serial_number }}</p>
                                @endif
                                @if($request->admin_notes)
                                    <p class="text-sm text-gray-700 mt-2">{{ $request->admin_notes }}</p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Actions -->
        @if($request->status === 'admin_released')
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                <a href="{{ route('admin.voucher', $request->id) }}" target="_blank" class="px-6 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700">
                    View Voucher
                </a>
            </div>
        @endif
    </div>
</div>
@endsection