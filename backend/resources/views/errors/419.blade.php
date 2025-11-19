<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale()==='en' ? 'ltr' : 'rtl' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('words.page-expired') }} - {{ config('app.name') }}</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    
    <style>
        .error-animation {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }
        
        .countdown-animation {
            animation: countdown 1s linear;
        }
        
        @keyframes countdown {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
            }
        }
        
        /* Flip arrows in RTL mode */
        [dir="rtl"] .rtl-flip {
            transform: scaleX(-1);
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="max-w-lg w-full space-y-8 text-center">
            <div>
                <!-- 419 Error Icon -->
                <div class="mx-auto h-24 w-24 text-orange-500 error-animation">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 18.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                
                <!-- Error Code -->
                <h1 class="mt-6 text-6xl font-extrabold text-gray-900">
                    419
                </h1>
                
                <!-- Error Title -->
                <h2 class="mt-4 text-3xl font-bold text-gray-900">
                    {{ __('words.page-expired') }}
                </h2>
                
                <!-- Error Message -->
                <p class="mt-4 text-lg text-gray-600">
                    {{ __('words.your-session-has-expired') }}
                </p>
                
                <!-- Detailed Explanation -->
                <div class="mt-6 p-4 bg-orange-50 rounded-lg border border-orange-200">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-orange-800">
                                {{ __('words.session-expired-explanation') }}
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- What happened -->
                <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                    <p class="text-sm text-blue-800">
                        {{ __('words.this-happens-when-you-stay-idle-for-too-long') }}
                    </p>
                </div>
                
                <!-- Auto-refresh countdown -->
                <div class="mt-6 p-4 bg-green-50 rounded-lg border border-green-200">
                    <div class="flex items-center justify-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-800">
                                {{ __('words.auto-refresh-in') }}
                                <span id="countdown" class="font-bold countdown-animation">10</span>
                                {{ __('words.seconds') }}
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
                    <!-- Refresh Button -->
                    <button onclick="window.location.reload()" 
                            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                        <svg class="w-5 h-5 {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        {{ __('words.refresh-page') }}
                    </button>
                    
                    <!-- Go Back Button -->
                    <button onclick="window.history.back()" 
                            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        <svg class="w-5 h-5 {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }} rtl-flip" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        {{ __('words.go-back') ?? 'Go Back' }}
                    </button>
                    
                    <!-- Home Button -->
                    <a href="{{ route('dashboard') }}" 
                       class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        <svg class="w-5 h-5 {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        {{ __('words.go-back-home') }}
                    </a>
                </div>
                
                <!-- Help Section -->
                <div class="mt-8 text-center">
                    <p class="text-sm text-gray-500">
                        {{ __('words.having-trouble-contact-support') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Auto-refresh countdown
        let timeLeft = 10;
        const countdownElement = document.getElementById('countdown');
        
        const countdown = setInterval(() => {
            timeLeft--;
            countdownElement.textContent = timeLeft;
            countdownElement.classList.add('countdown-animation');
            
            setTimeout(() => {
                countdownElement.classList.remove('countdown-animation');
            }, 1000);
            
            if (timeLeft <= 0) {
                clearInterval(countdown);
                window.location.reload();
            }
        }, 1000);
        
        // Stop countdown if user clicks any button
        document.addEventListener('click', function(e) {
            if (e.target.tagName === 'BUTTON' || e.target.tagName === 'A') {
                clearInterval(countdown);
            }
        });
        
        // Handle keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.key === 'F5' || (e.ctrlKey && e.key === 'r')) {
                clearInterval(countdown);
            }
        });
    </script>
</body>
</html> 