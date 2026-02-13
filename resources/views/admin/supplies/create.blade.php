@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Add New Supply</h1>
        <p class="mt-1 text-sm text-gray-600">Create a new item in the supply catalog</p>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
       <form action="{{ route('admin.supplies.store') }}" method="POST">
    @csrf
    
    <!-- Name -->
    <div class="mb-4">
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
            Supply Name <span class="text-red-600">*</span>
        </label>
        <input 
            type="text" 
            name="name" 
            id="name" 
            value="{{ old('name') }}"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            required
        >
        @error('name')
            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Category -->
    <div class="mb-4">
    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">
        Category <span class="text-red-600">*</span>
    </label>

    <select 
        name="category" 
        id="category"
        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        required
    >
        <option value="">Select Category</option>
        <option value="Computer Supplies" {{ old('category') == 'Computer Supplies' ? 'selected' : '' }}>
            Computer Supplies
        </option>
        <option value="Janitorial Supplies" {{ old('category') == 'Janitorial Supplies' ? 'selected' : '' }}>
            Janitorial Supplies
        </option>
        <option value="Office & Store Supplies" {{ old('category') == 'Office & Store Supplies' ? 'selected' : '' }}>
            Office & Store Supplies
        </option>
        <option value="Other Materials & supplies" {{ old('category') == 'Other Materials & supplies' ? 'selected' : '' }}>
            Other Materials & supplies
        </option>
    </select>

    @error('category')
        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
    @enderror
</div>


    <!-- Unit -->
    <div class="mb-4">
        <label for="unit" class="block text-sm font-medium text-gray-700 mb-1">
            Unit <span class="text-red-600">*</span>
        </label>
        <select 
            name="unit" 
            id="unit" 
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            required
        >
            <option value="">Select Unit</option>
            <option value="PCS" {{ old('unit') == 'PCS' ? 'selected' : '' }}>Pieces (PCS)</option>
            <option value="BOX" {{ old('unit') == 'BOX' ? 'selected' : '' }}>Box</option>
            <option value="PACK" {{ old('unit') == 'PACK' ? 'selected' : '' }}>Pack</option>
            <option value="REAM" {{ old('unit') == 'REAM' ? 'selected' : '' }}>Ream</option>
            <option value="SET" {{ old('unit') == 'SET' ? 'selected' : '' }}>Set</option>
        </select>
        @error('unit')
            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Description -->
    <div class="mb-4">
        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
        <textarea 
            name="description" 
            id="description" 
            rows="3"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        >{{ old('description') }}</textarea>
    </div>

    <!-- Stock Quantity -->
    <div class="mb-4">
        <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-1">
            Current Stock <span class="text-red-600">*</span>
        </label>
        <input 
            type="number" 
            name="stock_quantity" 
            id="stock_quantity" 
            min="0"
            value="{{ old('stock_quantity', 0) }}"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            required
        >
        @error('stock_quantity')
            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Minimum Stock -->
    <div class="mb-4">
        <label for="minimum_stock" class="block text-sm font-medium text-gray-700 mb-1">
            Minimum Stock Alert <span class="text-red-600">*</span>
        </label>
        <input 
            type="number" 
            name="minimum_stock" 
            id="minimum_stock" 
            min="0"
            value="{{ old('minimum_stock', 10) }}"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            required
        >
        @error('minimum_stock')
            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Active Status -->
    <div class="mb-6">
        <label class="flex items-center">
            <input 
                type="checkbox" 
                name="is_active" 
                value="1"
                {{ old('is_active', true) ? 'checked' : '' }}
                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
            <span class="ml-2 text-sm text-gray-900">Active (visible to employees)</span>
        </label>
    </div>

    <!-- Buttons -->
    <div class="flex justify-end gap-3">
        <a href="{{ route('admin.supplies.index') }}" class="px-6 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
            Cancel
        </a>
        <button type="submit" class="px-6 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700">
             Create Supply
        </button>
    </div>
</form>
    </div>
</div>


@push('scripts')
<script>
document.getElementById('category').addEventListener('change', function() {
    const customInput = document.getElementById('customCategory');
    if (this.value === 'custom') {
        customInput.classList.remove('hidden');
        customInput.required = true;
        customInput.name = 'category';
        this.name = '';
    } else {
        customInput.classList.add('hidden');
        customInput.required = false;
        customInput.name = '';
        this.name = 'category';
    }
});
</script>
@endpush
@endsection