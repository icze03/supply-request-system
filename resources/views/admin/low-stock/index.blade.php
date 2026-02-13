@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Low Stock Alerts</h1>
        <p class="mt-1 text-sm text-gray-600">Monitor and manage supplies with low inventory</p>
    </div>

    <!-- Critical Alert -->
    @if($criticalStock > 0)
        <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">
                        <strong>Critical:</strong> {{ $criticalStock }} item(s) are out of stock!
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Low Stock Table -->
    @if($lowStockSupplies->count() > 0)
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Current Stock</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Minimum</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Needed</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Unit</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($lowStockSupplies as $supply)
                        <tr class="hover:bg-gray-50 {{ $supply->stock_quantity <= 0 ? 'bg-red-50' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">
                                {{ $supply->item_code }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $supply->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $supply->category }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($supply->stock_quantity <= 0)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        {{ $supply->stock_quantity }} (OUT OF STOCK!)
                                    </span>
                                @elseif($supply->stock_quantity <= $supply->minimum_stock / 2)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                                        {{ $supply->stock_quantity }}
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        {{ $supply->stock_quantity }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                {{ $supply->minimum_stock }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="px-2 py-1 text-xs font-semibold rounded bg-indigo-100 text-indigo-800">
                                    {{ max(0, $supply->minimum_stock - $supply->stock_quantity) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                {{ $supply->unit }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <a href="{{ route('admin.supplies.edit', $supply->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                    Restock
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $lowStockSupplies->links() }}
        </div>
    @else
        <div class="bg-white shadow rounded-lg p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">All supplies are adequately stocked</h3>
            <p class="mt-1 text-sm text-gray-500">No low stock alerts at this time.</p>
        </div>
    @endif
</div>
@endsection