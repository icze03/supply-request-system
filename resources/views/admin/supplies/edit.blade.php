@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Edit Supply</h1>
        <p class="mt-1 text-sm text-gray-600">Update supply information</p>
        <p class="mt-1 text-xs text-gray-500">Item Code: <span class="font-mono font-semibold">{{ $supply->item_code }}</span></p>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form action="{{ route('admin.supplies.update', $supply->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Item Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        value="{{ old('name', $supply->name) }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-300 @enderror" 
                        required
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="category" 
                        id="category" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('category') border-red-300 @enderror"
                        required
                    >
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ old('category', $supply->category) == $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Unit -->
                <div>
                    <label for="unit" class="block text-sm font-medium text-gray-700 mb-2">
                        Unit of Measurement <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="unit" 
                        id="unit" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('unit') border-red-300 @enderror"
                        required
                    >
                        <option value="pcs" {{ old('unit', $supply->unit) == 'pcs' ? 'selected' : '' }}>Pieces (pcs)</option>
                        <option value="box" {{ old('unit', $supply->unit) == 'box' ? 'selected' : '' }}>Box</option>
                        <option value="pack" {{ old('unit', $supply->unit) == 'pack' ? 'selected' : '' }}>Pack</option>
                        <option value="ream" {{ old('unit', $supply->unit) == 'ream' ? 'selected' : '' }}>Ream</option>
                        <option value="bottle" {{ old('unit', $supply->unit) == 'bottle' ? 'selected' : '' }}>Bottle</option>
                        <option value="roll" {{ old('unit', $supply->unit) == 'roll' ? 'selected' : '' }}>Roll</option>
                        <option value="pad" {{ old('unit', $supply->unit) == 'pad' ? 'selected' : '' }}>Pad</option>
                        <option value="set" {{ old('unit', $supply->unit) == 'set' ? 'selected' : '' }}>Set</option>
                    </select>
                    @error('unit')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea 
                        name="description" 
                        id="description" 
                        rows="4"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('description') border-red-300 @enderror"
                    >{{ old('description', $supply->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Active Status -->
                <div class="flex items-center">
                    <input 
                        type="checkbox" 
                        name="is_active" 
                        id="is_active" 
                        value="1"
                        {{ old('is_active', $supply->is_active) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                    <label for="is_active" class="ml-2 block text-sm text-gray-700">
                        Active (available for requests)
                    </label>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('admin.supplies.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700">
                    Update Supply
                </button>
            </div>
           <div>
    <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-1">
        Current Stock <span class="text-red-600">*</span>
    </label>
    <input 
        type="number" 
        name="stock_quantity" 
        id="stock_quantity" 
        min="0"
        value="{{ old('stock_quantity', $supply->stock_quantity) }}"
        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        required
    >
    @error('stock_quantity')
        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="minimum_stock" class="block text-sm font-medium text-gray-700 mb-1">
        Minimum Stock Alert <span class="text-red-600">*</span>
    </label>
    <input 
        type="number" 
        name="minimum_stock" 
        id="minimum_stock" 
        min="0"
        value="{{ old('minimum_stock', $supply->minimum_stock) }}"
        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        required
    >
    @error('minimum_stock')
        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
    @enderror
</div>
        </form>
    </div>
</div>
@endsection