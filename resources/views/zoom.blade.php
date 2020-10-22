<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
        <meta name="logrocket-id" content="{{ auth()->user()->logrocketId() }}">
        <meta name="logrocket-id-extra" content='{!! auth()->user()->logrocketIdExtra() !!}'>
        <meta name="user-permissions" content='{!! json_encode(collect(optional(auth()->user()->roles()->first())->getAllPermissions())->pluck('name')->toArray()) !!}'>
    @endif

    <title>{{ config('app.name', 'Laravel') }}</title>

    @routes

    <link rel="stylesheet" href="{{ asset('node_modules/@zoomus/websdk/dist/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('node_modules/@zoomus/websdk/dist/css/react-select.css') }}">

    <!-- Scripts -->
    <script src="https://unpkg.com/ionicons@5.2.3/dist/ionicons.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.26.0/moment.min.js" defer></script>
    <script src="{{ mix('js/app.js') }}" defer></script>
</head>
<body class="font-sans antialiased">
    @inertia
</body>
</html>
