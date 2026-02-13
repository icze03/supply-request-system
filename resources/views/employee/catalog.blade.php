@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Supply Catalog</h1>
        <p class="mt-1 text-sm text-gray-600">Browse and request supplies for your department</p>
    </div>

<!-- Filters -->
<div class="bg-white shadow rounded-lg p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
            <input type="text" id="searchInput" placeholder="Search supplies..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
            <select id="categoryFilter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category }}">{{ $category }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end space-x-2">
            <!-- Custom Request Button -->
            <button onclick="openSpecialRequestModal()" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Custom Request
            </button>
            <!-- Cart Button -->
            <button onclick="toggleCartModal()" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                request list (<span id="cartCount">0</span>)
            </button>
        </div>
    </div>
</div>

    <!-- Supply Grid -->
    <div id="supplyGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($supplies as $supply)
            <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow duration-300 supply-card" data-category="{{ $supply->category }}" data-name="{{ strtolower($supply->name) }}">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $supply->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $supply->item_code }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $supply->category }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">{{ $supply->description }}</p>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Unit: {{ $supply->unit }}</span>
                        <button onclick="addToCart({{ $supply->id }}, '{{ $supply->name }}', '{{ $supply->item_code }}', '{{ $supply->unit }}')" class="px-4 py-2 bg-blue-500 text-white text-sm rounded hover:bg-blue-600">
                            Add to list
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Cart Modal -->
<div id="cartModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] flex flex-col">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900">Request Listing</h2>
            <button onclick="toggleCartModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Modal Body - Scrollable Cart Items -->
        <div id="cartItems" class="flex-1 overflow-y-auto px-6 py-4">
            <p class="text-center text-gray-500 mt-8">Your list is empty</p>
        </div>

        <!-- Modal Footer -->
        <div class="border-t border-gray-200 px-6 py-4">
            <!-- Budget Type -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Budget Type *</label>
                <select id="budgetType" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="budgeted">Budgeted</option>
                    <option value="not_budgeted">Not Budgeted</option>
                </select>
            </div>

            <!-- Purpose -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Purpose *</label>
                <textarea id="requestPurpose" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Describe the purpose of this request..."></textarea>
            </div>

            <!-- Submit Button -->
            <button onclick="submitRequest()" id="submitBtn" class="w-full px-4 py-3 bg-indigo-600 text-white font-semibold rounded-md hover:bg-indigo-700 disabled:bg-gray-300 disabled:cursor-not-allowed">
                Submit Request
            </button>
        </div>
    </div>
</div>

<!-- Special Request Modal -->
<div id="specialRequestModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900">Custom Supply Request</h2>
            <button onclick="closeSpecialRequestModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <p class="text-sm text-gray-600 mb-6">Request supplies that are not in the catalog. Please provide detailed information.</p>

        <form id="specialRequestForm">
            <div class="space-y-4">
                <!-- Budget Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Budget Type *</label>
                    <select id="special_budget_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="budgeted">Budgeted</option>
                        <option value="not_budgeted">Not Budgeted</option>
                    </select>
                </div>

                <!-- Item Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Item Description *</label>
                    <textarea
                        id="special_item_description"
                        rows="4"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Describe the item(s) you need in detail..."
                        required
                    ></textarea>
                    <p class="text-xs text-gray-500 mt-1">Be as specific as possible (brand, quantity, specifications, etc.)</p>
                </div>

                <!-- Purpose -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Purpose *</label>
                    <textarea
                        id="special_purpose"
                        rows="3"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Why do you need this item?"
                        required
                    ></textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="closeSpecialRequestModal()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-md text-sm font-medium hover:bg-purple-700">
                    Submit Custom Request
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let cart = [];

// Toggle Cart Modal
function toggleCartModal() {
    const modal = document.getElementById('cartModal');
    modal.classList.toggle('hidden');
}

// Close modal when clicking outside
document.getElementById('cartModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        toggleCartModal();
    }
});

function addToCart(id, name, code, unit) {
    const existing = cart.find(item => item.supply_id === id);

    if (existing) {
        existing.quantity++;
    } else {
        cart.push({
            supply_id: id,
            name: name,
            code: code,
            unit: unit,
            quantity: 1
        });
    }

    updateCart();

    // Show toast notification
    showToast('Added to list!', 'success');
}

function removeFromCart(id) {
    cart = cart.filter(item => item.supply_id !== id);
    updateCart();
}

function updateQuantity(id, change) {
    const item = cart.find(item => item.supply_id === id);
    if (item) {
        item.quantity += change;
        if (item.quantity <= 0) {
            removeFromCart(id);
        } else {
            updateCart();
        }
    }
}

function updateCart() {
    const cartCount = document.getElementById('cartCount');
    const cartItems = document.getElementById('cartItems');
    const submitBtn = document.getElementById('submitBtn');

    cartCount.textContent = cart.reduce((sum, item) => sum + item.quantity, 0);

    if (cart.length === 0) {
        cartItems.innerHTML = '<p class="text-center text-gray-500 mt-8">Your list is empty</p>';
        if (submitBtn) submitBtn.disabled = true;
    } else {
        if (submitBtn) submitBtn.disabled = false;
        cartItems.innerHTML = cart.map(item => `
            <div class="mb-4 p-4 border border-gray-200 rounded-lg">
                <div class="flex justify-between items-start mb-2">
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900">${item.name}</h4>
                        <p class="text-sm text-gray-500">${item.code}</p>
                    </div>
                    <button onclick="removeFromCart(${item.supply_id})" class="text-red-600 hover:text-red-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Unit: ${item.unit}</span>
                    <div class="flex items-center space-x-2">
                        <button onclick="updateQuantity(${item.supply_id}, -1)" class="px-2 py-1 bg-gray-200 rounded hover:bg-gray-300">-</button>
                        <span class="px-3 py-1 bg-gray-100 rounded">${item.quantity}</span>
                        <button onclick="updateQuantity(${item.supply_id}, 1)" class="px-2 py-1 bg-gray-200 rounded hover:bg-gray-300">+</button>
                    </div>
                </div>
            </div>
        `).join('');
    }
}

// Special Request Modal Functions
function openSpecialRequestModal() {
    document.getElementById('specialRequestModal').classList.remove('hidden');
}

function closeSpecialRequestModal() {
    document.getElementById('specialRequestModal').classList.add('hidden');
    const form = document.getElementById('specialRequestForm');
    if (form) form.reset();
}

// Close special modal when clicking outside
document.getElementById('specialRequestModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeSpecialRequestModal();
    }
});

const specialForm = document.getElementById('specialRequestForm');
if (specialForm) {
    specialForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = {
            budget_type: document.getElementById('special_budget_type').value,
            item_description: document.getElementById('special_item_description').value.trim(),
            purpose: document.getElementById('special_purpose').value.trim()
        };

        if (!formData.item_description || !formData.purpose) {
            showToast('Please fill in all required fields', 'error');
            return;
        }

        try {
            const response = await fetch('{{ route("employee.requests.special") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(formData)
            });

            const data = await response.json();

            if (data.success) {
                showToast('Custom request submitted successfully!', 'success');
                closeSpecialRequestModal();
                setTimeout(() => {
                    window.location.href = '{{ route("employee.requests.index") }}';
                }, 1000);
            } else {
                showToast('Failed to submit request', 'error');
            }
        } catch (error) {
            showToast('An error occurred. Please try again.', 'error');
        }
    });
}

async function submitRequest() {
    const purpose = document.getElementById('requestPurpose').value.trim();
    const budgetType = document.getElementById('budgetType').value;

    if (!purpose) {
        showToast('Please provide a purpose for this request', 'error');
        return;
    }

    if (cart.length === 0) {
        showToast('Your cart is empty', 'error');
        return;
    }

    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Submitting...';

    try {
        const response = await fetch('{{ route("employee.requests.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                purpose: purpose,
                budget_type: budgetType,
                items: cart
            })
        });

        const data = await response.json();

        if (data.success) {
            showToast('Request submitted successfully!', 'success');
            cart = [];
            updateCart();
            document.getElementById('requestPurpose').value = '';
            toggleCartModal();

            // Redirect to requests page after 1 second
            setTimeout(() => {
                window.location.href = '{{ route("employee.requests.index") }}';
            }, 1000);
        } else {
            showToast('Failed to submit request', 'error');
        }
    } catch (error) {
        showToast('An error occurred. Please try again.', 'error');
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Submit Request';
    }
}

// Search and filter functionality
document.getElementById('searchInput').addEventListener('input', filterSupplies);
document.getElementById('categoryFilter').addEventListener('change', filterSupplies);

function filterSupplies() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const category = document.getElementById('categoryFilter').value;
    const cards = document.querySelectorAll('.supply-card');    

    cards.forEach(card => {
        const name = card.dataset.name;
        const cardCategory = card.dataset.category;

        const matchesSearch = name.includes(search);
        const matchesCategory = !category || cardCategory === category;

        if (matchesSearch && matchesCategory) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Toast notification
function showToast(message, type = 'info') {
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-blue-500'
    };

    const toast = document.createElement('div');
    toast.className = `fixed bottom-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-opacity duration-300`;
    toast.textContent = message;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
</script>
@endpush
@endsection