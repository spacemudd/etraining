<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $pusherKey = config('broadcasting.connections.pusher.key');
        $pusherHost = config('broadcasting.connections.pusher.options.host');
    @endphp
    @if (filled($pusherKey) && filled($pusherHost))
        <meta name="pusher-key" content="{{ $pusherKey }}">
        <meta name="pusher-cluster" content="{{ config('broadcasting.connections.pusher.options.cluster', 'mt1') }}">
        <meta name="pusher-host" content="{{ $pusherHost }}">
        <meta name="pusher-port" content="{{ config('broadcasting.connections.pusher.options.port', 443) }}">
        <meta name="pusher-scheme" content="{{ config('broadcasting.connections.pusher.options.scheme', 'https') }}">
    @endif
    @auth
        <meta name="logrocket-id" content="{{ auth()->user()->logrocketId() }}">
        <meta name="logrocket-id-extra" content='{!! auth()->user()->logrocketIdExtra() !!}'>
        <meta name="user-permissions" content='{!! json_encode(auth()->user()->getAllPermissions()->pluck('name')->values()->toArray()) !!}'>
    @endif

    <title>{{ config('app.name', 'Laravel') }}</title>

    @routes

    <link rel="stylesheet" href="{{ asset('node_modules/@zoomus/websdk/dist/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('node_modules/@zoomus/websdk/dist/css/react-select.css') }}">

    <!-- Scripts -->
    <script src="https://unpkg.com/ionicons@5.2.3/dist/ionicons.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.26.0/moment.min.js" defer></script>
    <script src="{{ mix('js/manifest.js') }}" defer></script>
    <script src="{{ mix('js/vendor.js') }}" defer></script>
    <script src="{{ mix('js/app.js') }}" defer></script>
</head>
<body class="font-sans antialiased">
    @inertia
    <style type="text/css">
        button {
            font-family: Nunito, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"
        }
    </style>
    @if (auth()->user()->trainee)
        <style type="text/css">
            .send-video-container {
                display: none;
            }
            .join-audio-container {
                display: none;
            }
            .participants-section-container__participants-footer-bottom {
                display: none;
            }
            [aria-label="Share Screen"] {
                display: none;
            }
            /*[aria-label="open the chat pane"] {*/
            /*    display:none;*/
            /*}*/
            /*.join-dialog {*/
            /*    display: none;*/
            /*}*/
            .e2e-encryption-indicator__encrypt-indicator.e2e-encryption-indicator__encrypt-indicator--2 {
                display: none;
            }
        </style>
    @endif
</body>
</html>
