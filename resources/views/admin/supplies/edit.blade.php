@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Edit Supply</h1>
        <p class="mt-1 text-sm text-gray-600">Update supply information</p>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form action="{{ route('admin.supplies.update', $supply->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">

                <!-- Item Code (Editable) -->
                <div>
                    <label for="item_code" class="block text-sm font-medium text-gray-700 mb-2">
                        Item Code <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="item_code"
                        id="item_code"
                        value="{{ old('item_code', $supply->item_code) }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono @error('item_code') border-red-300 @enderror"
                        placeholder="e.g. SUP-001"
                        required
                    >
                    <p class="mt-1 text-xs text-gray-500">Changing the item code may affect existing records that reference it.</p>
                    @error('item_code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

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
                        <option value="pcs"    {{ old('unit', $supply->unit) == 'pcs'    ? 'selected' : '' }}>Pieces (pcs)</option>
                        <option value="box"    {{ old('unit', $supply->unit) == 'box'    ? 'selected' : '' }}>Box</option>
                        <option value="pack"   {{ old('unit', $supply->unit) == 'pack'   ? 'selected' : '' }}>Pack</option>
                        <option value="ream"   {{ old('unit', $supply->unit) == 'ream'   ? 'selected' : '' }}>Ream</option>
                        <option value="bottle" {{ old('unit', $supply->unit) == 'bottle' ? 'selected' : '' }}>Bottle</option>
                        <option value="roll"   {{ old('unit', $supply->unit) == 'roll'   ? 'selected' : '' }}>Roll</option>
                        <option value="pad"    {{ old('unit', $supply->unit) == 'pad'    ? 'selected' : '' }}>Pad</option>
                        <option value="set"    {{ old('unit', $supply->unit) == 'set'    ? 'selected' : '' }}>Set</option>
                    </select>
                    @error('unit')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Stock Fields -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-2">
                            Current Stock <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            name="stock_quantity"
                            id="stock_quantity"
                            min="0"
                            value="{{ old('stock_quantity', $supply->stock_quantity) }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('stock_quantity') border-red-300 @enderror"
                            required
                        >
                        @error('stock_quantity')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="minimum_stock" class="block text-sm font-medium text-gray-700 mb-2">
                            Minimum Stock Alert <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            name="minimum_stock"
                            id="minimum_stock"
                            min="0"
                            value="{{ old('minimum_stock', $supply->minimum_stock) }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('minimum_stock') border-red-300 @enderror"
                            required
                        >
                        @error('minimum_stock')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Budget / Unit Cost (Optional) -->
                <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-800">Budget Tracking (Optional)</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Set a unit cost to track department budget usage when this item is requested.</p>
                        </div>
                        <label class="flex items-center cursor-pointer">
                            <div class="relative">
                                <input
                                    type="checkbox"
                                    id="enable_budget"
                                    name="enable_budget"
                                    value="1"
                                    {{ old('enable_budget', $supply->unit_cost !== null) ? 'checked' : '' }}
                                    class="sr-only"
                                    onchange="toggleBudget(this)"
                                >
                                <div id="toggleBg" class="block w-10 h-6 rounded-full transition-colors duration-200 {{ old('enable_budget', $supply->unit_cost !== null) ? 'bg-indigo-600' : 'bg-gray-300' }}"></div>
                                <div id="toggleDot" class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform duration-200 {{ old('enable_budget', $supply->unit_cost !== null) ? 'translate-x-4' : '' }}"></div>
                            </div>
                            <span class="ml-2 text-sm text-gray-600">Enable</span>
                        </label>
                    </div>

                    <div id="budgetFields" class="{{ old('enable_budget', $supply->unit_cost !== null) ? '' : 'hidden' }}">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="unit_cost" class="block text-sm font-medium text-gray-700 mb-2">
                                    Unit Cost (₱) <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 text-sm">₱</span>
                                    <input
                                        type="number"
                                        name="unit_cost"
                                        id="unit_cost"
                                        min="0"
                                        step="0.01"
                                        value="{{ old('unit_cost', $supply->unit_cost) }}"
                                        class="w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('unit_cost') border-red-300 @enderror"
                                        placeholder="0.00"
                                    >
                                </div>
                                @error('unit_cost')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Budget Deduction</label>
                                <div class="flex items-center h-10 px-3 bg-white border border-gray-200 rounded-md text-sm text-gray-600">
                                    Deducted per unit from department budget on release
                                </div>
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-amber-600">
                             When a request is released, the total cost (unit cost × quantity) will be deducted from the requesting department's allocated budget. Returns will reverse the deduction.
                        </p>
                    </div>
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
        </form>
    </div>
</div>

@push('scripts')
<script>
function toggleBudget(checkbox) {
    const fields = document.getElementById('budgetFields');
    const bg = document.getElementById('toggleBg');
    const dot = document.getElementById('toggleDot');

    if (checkbox.checked) {
        fields.classList.remove('hidden');
        bg.classList.remove('bg-gray-300');
        bg.classList.add('bg-indigo-600');
        dot.classList.add('translate-x-4');
    } else {
        fields.classList.add('hidden');
        bg.classList.add('bg-gray-300');
        bg.classList.remove('bg-indigo-600');
        dot.classList.remove('translate-x-4');
        document.getElementById('unit_cost').value = '';
    }
}
</script>
@endpush
@endsection