<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
        <meta name="logrocket-id" content="{{ auth()->user()->logrocketId() }}">
        <meta name="logrocket-id-extra" content='{!! auth()->user()->logrocketIdExtra() !!}'>
    @endif

    <title>{{ config('app.name') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <!-- Scripts -->
    <script src="https://unpkg.com/ionicons@5.2.3/dist/ionicons.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.26.0/moment.min.js" defer></script>
</head>
<body class="font-sans antialiased  bg-gray-50 dark:bg-gray-900">
<div class="flex h-screen">
    <div class="flex flex-col flex-1 w-full mb-10">
        <div class="container mx-auto p-4">
            <div class="mx-auto">
                <h1 class="text-3xl mb-8 font-bold" id="requirements">{{ __('words.system-requirements') }}</h1>
                <p>تتوافق منصة PTC مع أحدث وأبرز برامج التصفح مثل إنترنت إكسبلورر وفايرفوكس وقوقل كروم وغيرها.</p>
            </div>
        </div>
    </div>
</div>
</body>
</html>
