<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'en' ? 'ltr' : 'rtl' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('words.payment-gateway-unavailable-title') }}</title>
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-lg bg-white rounded-xl shadow-sm border border-amber-200 p-8 text-center">
        <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 text-amber-700 text-2xl">!</div>
        <h1 class="text-xl font-bold text-gray-800 mb-3">
            {{ __('words.payment-gateway-unavailable-title') }}
        </h1>
        <p class="text-gray-600 leading-7 mb-8">
            {{ __('words.payment-gateway-unavailable-body') }}
        </p>
        <a href="{{ route('dashboard') }}"
           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-md">
            {{ __('words.dashboard') }}
        </a>
    </div>
</body>
</html>
