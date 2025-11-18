<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale()==='en' ? 'ltr' : 'rtl' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('words.page-not-found') }} - {{ config('app.name') }}</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    
    <style>
        .error-animation {
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
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
        <div class="max-w-md w-full space-y-8 text-center">
            <div>
                <!-- 404 Error Icon -->
                <div class="mx-auto h-24 w-24 text-red-500 error-animation">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                
                <!-- Error Code -->
                <h1 class="mt-6 text-6xl font-extrabold text-gray-900">
                    404
                </h1>
                
                <!-- Error Title -->
                <h2 class="mt-4 text-3xl font-bold text-gray-900">
                    {{ __('words.page-not-found') }}
                </h2>
                
                <!-- Error Message -->
                <p class="mt-4 text-lg text-gray-600">
                    {{ __('words.the-page-you-are-looking-for-could-not-be-found') }}
                </p>
                
                <!-- Helpful Instructions -->
                <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <p class="text-sm text-blue-800">
                        {{ __('words.please-check-the-url-and-try-again') }}
                    </p>
                </div>
                
                <!-- Current URL Display -->
                <div class="mt-4 p-3 bg-gray-100 rounded-lg">
                    <p class="text-sm text-gray-600 font-mono break-all">
                        {{ request()->url() }}
                    </p>
                </div>
                
                <!-- Action Buttons -->
                <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
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
</body>
</html> 