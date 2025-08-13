@props(['disabled' => false])
<div class="w-full">
    <div class="flex items-center">
        <!-- Phone input field -->
        <input 
            type="tel"
            name="{{ $attributes->get('name') }}"
            value="{{ $attributes->get('value') }}"
            {{ $disabled ? 'disabled' : '' }}
            class="form-input block w-full h-12 px-4 py-3 border-r-0 rounded-r-none focus:ring-indigo-500 focus:border-indigo-500"
            placeholder="5X XXX XXXX"
            pattern="[0-9]{9}"
            maxlength="9"
            autocomplete="tel"
        >
        
        <!-- Saudi Arabia flag and country code -->
        <div class="flex-shrink-0 w-20 h-12 bg-gray-50 border border-l-0 border-gray-200 rounded-r-md flex items-center justify-center">
            <div class="flex items-center space-x-2">
                <span class="text-green-600 text-lg">🇸🇦</span>
                <span class="text-sm font-medium text-gray-700">+966</span>
            </div>
        </div>
    </div>
    
    <!-- Help text for Saudi phone format -->
    <p class="mt-1 text-xs text-gray-500">
        مثال: 501234567 (بدون الرقم صفر في البداية)
    </p>
</div>

<style>
/* Ensure the phone input container matches other form fields exactly */
.form-input[type="tel"] {
    height: 48px !important;
    min-height: 48px !important;
    max-height: 48px !important;
    border-radius: 0.375rem 0 0 0.375rem !important;
    border-right: none !important;
    border: 1px solid #d1d5db !important;
    background-color: #ffffff !important;
    color: #374151 !important;
    transition: all 0.2s ease-in-out !important;
    box-sizing: border-box !important;
    font-size: 14px !important;
    line-height: 1.5 !important;
}

/* Country code container styling */
.flex-shrink-0 {
    border-radius: 0 0.375rem 0.375rem 0 !important;
    background-color: #f9fafb !important;
    border: 1px solid #d1d5db !important;
    border-left: none !important;
    height: 48px !important;
    min-height: 48px !important;
    max-height: 48px !important;
}

/* Focus states */
.form-input[type="tel"]:focus {
    outline: none !important;
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
}

.form-input[type="tel"]:focus + .flex-shrink-0 {
    border-color: #3b82f6 !important;
}

/* Error state */
.form-input[type="tel"].error {
    border-color: #ef4444 !important;
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .form-input[type="tel"] {
        font-size: 16px !important; /* Prevents zoom on iOS */
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const phoneInputs = document.querySelectorAll('input[type="tel"]');
    
    phoneInputs.forEach(function(input) {
        // Auto-format phone number as user types
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, ''); // Remove non-digits
            
            // Limit to 9 digits
            if (value.length > 9) {
                value = value.substring(0, 9);
            }
            
            // Update input value
            e.target.value = value;
        });
        
        // Validate on blur
        input.addEventListener('blur', function(e) {
            const value = e.target.value;
            if (value && value.length < 9) {
                e.target.classList.add('error');
                e.target.style.borderColor = '#ef4444';
            } else {
                e.target.classList.remove('error');
                e.target.style.borderColor = '#d1d5db';
            }
        });
        
        // Remove error on input
        input.addEventListener('input', function(e) {
            e.target.classList.remove('error');
            e.target.style.borderColor = '#d1d5db';
        });
    });
});
</script>
