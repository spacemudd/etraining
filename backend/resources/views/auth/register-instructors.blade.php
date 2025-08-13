<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Scripts -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <script src="{{ mix('js/app.js') }}" defer></script>
    
    <style>
        /* Ensure all form inputs have consistent styling */
        .form-input, 
        .form-input[type="text"], 
        .form-input[type="email"], 
        .form-input[type="password"], 
        .form-input[type="date"],
        .form-input[type="tel"] {
            height: 48px !important;
            padding: 12px 16px !important;
            font-size: 14px !important;
            line-height: 1.5 !important;
            border: 1px solid #d1d5db !important;
            border-radius: 0.375rem !important;
            background-color: #ffffff !important;
            color: #374151 !important;
            width: 100% !important;
            transition: all 0.2s ease-in-out !important;
        }
        
        .form-input:focus {
            outline: none !important;
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
        }
        
        .form-input.error {
            border-color: #ef4444 !important;
        }
        
        /* Select dropdowns */
        select {
            height: 48px !important;
            padding: 12px 16px !important;
            font-size: 14px !important;
            line-height: 1.5 !important;
            border: 1px solid #d1d5db !important;
            border-radius: 0.375rem !important;
            background-color: #ffffff !important;
            color: #374151 !important;
            width: 100% !important;
            transition: all 0.2s ease-in-out !important;
            cursor: pointer !important;
        }
        
        select:focus {
            outline: none !important;
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
        }
        
        /* Labels */
        label {
            display: block !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            color: #374151 !important;
            margin-bottom: 0.5rem !important;
        }
        
        /* Form groups */
        .form-group {
            margin-bottom: 1rem !important;
        }
        
        /* Responsive adjustments */
        @media (max-width: 640px) {
            .form-input,
            select {
                font-size: 16px !important; /* Prevents zoom on iOS */
            }
        }
        
        /* Button styling */
        .btn-primary {
            background-color: #3b82f6 !important;
            color: #ffffff !important;
            padding: 12px 24px !important;
            border-radius: 0.375rem !important;
            font-weight: 500 !important;
            transition: all 0.2s ease-in-out !important;
            border: none !important;
            cursor: pointer !important;
        }
        
        .btn-primary:hover {
            background-color: #2563eb !important;
            transform: translateY(-1px) !important;
        }
        
        .btn-primary:focus {
            outline: none !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
        }
    </style>
</head>
<body class="font-sans antialiased">
<x-guest-layout>
    <x-jet-authentication-card>
        <x-slot name="logo">
            <x-jet-authentication-card-logo />
        </x-slot>

        <x-jet-validation-errors class="mb-4" />

        @if (session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @if (session('status'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('status') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('register.instructors.store') }}">
            @csrf

            <div><h1 class="text-2xl text-center my-5 font-bold">{{ __('words.welcome') }}!</h1></div>

            <div
               class="flex py-5 px-10 rounded-lg text-2xl justify-center">
                <img src="{{ asset('/img/teacher.svg') }}" class="w-8 ml-5">
                {{ __('words.fill-your-application') }}
            </div>

            <hr class="border-1 mb-5">

            <div>
                <x-jet-label value="{{ __('words.full-name') }}" />
                <x-jet-input class="block mt-1 w-full h-12 px-4 py-3" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            </div>

            <div class="mt-4">
                <x-jet-label value="{{ __('words.identity_number') }}" />
                <x-jet-input class="block mt-1 w-full h-12 px-4 py-3" type="text" name="identity_number" :value="old('identity_number')" required />
            </div>

            <div class="mt-4">
                <x-jet-label value="{{ __('words.email') }}" />
                <x-jet-input class="block mt-1 w-full h-12 px-4 py-3" type="email" name="email" :value="old('email')" required />
            </div>

            <div class="mt-4">
                <x-jet-label value="{{ __('words.password') }}" />
                <x-jet-input class="block mt-1 w-full h-12 px-4 py-3" type="password" name="password" :value="old('password')" required />
            </div>

            <div class="mt-4">
                <x-jet-label value="{{ __('words.phone') }}" />
                <x-jet-input class="block mt-1 w-full h-12 px-4 py-3" type="text" name="phone" :value="old('phone')" required />
            </div>

            <div class="mt-4">
                <x-jet-label value="{{ __('words.city') }}" />
                <div class="relative">
                    <select class="block appearance-none w-full h-12 px-4 py-3 border border-gray-200 text-gray-700 rounded-md leading-tight focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50"
                            name="city_id"
                            id="city_id">
                        <option value=""></option>
                        @foreach (\App\Models\City::orderBy('name')->get() as $level)
                            <option value="{{ $level->id }}">
                                @if (app()->getLocale() === 'ar')
                                    {{ $level->name_ar }}
                                @else
                                    {{ $level->name_en }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <x-jet-label value="{{ __('words.provided_courses') }}" />
                <x-jet-input class="block mt-1 w-full h-12 px-4 py-3" type="text" name="provided_courses" :value="old('provided_courses')" required />
            </div>

            <div class="mt-4">
                <x-jet-label value="{{ __('words.twitter_link') }}" />
                <x-jet-input class="block mt-1 w-full h-12 px-4 py-3" type="text" name="twitter_link" :value="old('twitter_link')" />
            </div>

            <div class="flex items-center mt-4">
                <x-jet-button class="mx-12 w-full flex items-center justify-center">
                    {{ __('words.next') }}
                </x-jet-button>
            </div>
        </form>
    </x-jet-authentication-card>
</x-guest-layout>
</body>
</html>
