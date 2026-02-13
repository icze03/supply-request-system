

<?php $__env->startSection('content'); ?>
<div class="min-h-[70vh] flex items-center justify-center px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        <div class="bg-white shadow-xl rounded-lg p-8">
            <div class="text-center mb-8">
                <div class="mx-auto h-16 w-16 bg-indigo-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="h-10 w-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">Manager Verification</h2>
                <p class="mt-2 text-sm text-gray-600">Enter your department passcode to access approval functions</p>
            </div>

            <div id="errorMessage" class="hidden mb-4 p-4 bg-red-50 border border-red-200 rounded-md">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-sm text-red-800" id="errorText"></p>
                </div>
            </div>

            <form id="passcodeForm" class="space-y-6">
                <?php echo csrf_field(); ?>
                <div>
                    <label for="passcode" class="block text-sm font-medium text-gray-700 mb-2">
                        Department Passcode
                    </label>
                    <input 
                        type="password" 
                        id="passcode" 
                        name="passcode" 
                        maxlength="6"
                        class="w-full px-4 py-3 text-center text-2xl tracking-widest border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                        placeholder="••••••"
                        autocomplete="off"
                        required
                    >
                    <p class="mt-2 text-xs text-gray-500 text-center">Enter your 6-digit department passcode</p>
                </div>

                <div>
                    <button 
                        type="submit" 
                        id="submitBtn"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:bg-gray-400 disabled:cursor-not-allowed"
                    >
                        <span id="btnText">Verify Passcode</span>
                        <svg id="btnLoader" class="hidden animate-spin ml-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-6 border-t border-gray-200">
                <p class="text-xs text-gray-500 text-center">
                    <svg class="inline h-4 w-4 text-gray-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Department: <strong><?php echo e(auth()->user()->department->name); ?></strong> (<?php echo e(auth()->user()->department->code); ?>)
                </p>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.getElementById('passcodeForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const passcode = document.getElementById('passcode').value;
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const btnLoader = document.getElementById('btnLoader');
    const errorMessage = document.getElementById('errorMessage');
    
    // Validation
    if (passcode.length !== 6) {
        showError('Passcode must be 6 digits');
        return;
    }
    
    // Disable button and show loader
    submitBtn.disabled = true;
    btnText.classList.add('hidden');
    btnLoader.classList.remove('hidden');
    errorMessage.classList.add('hidden');
    
    try {
        const response = await fetch('<?php echo e(route("manager.verify.check")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ passcode })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Show success and redirect
            btnText.textContent = 'Access Granted!';
            btnText.classList.remove('hidden');
            btnLoader.classList.add('hidden');
            submitBtn.className = 'w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600';
            
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 500);
        } else {
            showError(data.message || 'Invalid passcode. Please try again.');
            submitBtn.disabled = false;
            btnText.classList.remove('hidden');
            btnLoader.classList.add('hidden');
            document.getElementById('passcode').value = '';
            document.getElementById('passcode').focus();
        }
    } catch (error) {
        showError('An error occurred. Please try again.');
        submitBtn.disabled = false;
        btnText.classList.remove('hidden');
        btnLoader.classList.add('hidden');
    }
});

function showError(message) {
    const errorMessage = document.getElementById('errorMessage');
    const errorText = document.getElementById('errorText');
    
    errorText.textContent = message;
    errorMessage.classList.remove('hidden');
    
    // Shake animation
    errorMessage.classList.add('animate-shake');
    setTimeout(() => {
        errorMessage.classList.remove('animate-shake');
    }, 500);
}

// Auto-focus on passcode input
document.getElementById('passcode').focus();

// Only allow numbers
document.getElementById('passcode').addEventListener('input', function(e) {
    this.value = this.value.replace(/[^0-9]/g, '');
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\supply-request-system\resources\views/manager/verify-passcode.blade.php ENDPATH**/ ?>