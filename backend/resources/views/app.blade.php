<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale()==='en' ? 'ltr' : 'rtl' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
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
            @if (auth()->user()->logrocketEnabled())
                <meta name="logrocket-enabled" content="{{ auth()->user()->logrocketEnabled() }}">
                <meta name="logrocket-id" content="{{ auth()->user()->logrocketId() }}">
                <meta name="logrocket-id-extra" content='{!! auth()->user()->logrocketIdExtra() !!}'>
            @endif
            <meta name="user-permissions" content='{!! json_encode(auth()->user()->getAllPermissions()->pluck('name')->values()->toArray()) !!}'>
        @endif

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Styles -->
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        @if (request()->routeIs('back.chat.*'))
            <link rel="manifest" href="/chat.webmanifest">
            <meta name="theme-color" content="#16a34a">
            <meta name="apple-mobile-web-app-capable" content="yes">
            <meta name="mobile-web-app-capable" content="yes">
        @endif

        <link rel="stylesheet" href="{{ mix('css/app.css') }}">

        @routes
        <script>
            // Ensure route helper is available
            if (typeof window.route === 'undefined') {
                window.route = function(name, params) {
                    return '#';
                };
            }
        </script>

        <!-- Scripts -->
        <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
        <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
        <script>
            // Ensure ionicons are loaded properly
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof window.customElements === 'undefined') {
                    console.warn('Custom elements not supported, ionicons may not work properly');
                }
            });
        </script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.26.0/moment.min.js" defer></script>
        <script src="{{ mix('js/manifest.js') }}" defer></script>
        <script src="{{ mix('js/vendor.js') }}" defer></script>
        <script src="{{ mix('js/app.js') }}" defer></script>
    </head>
    <body class="font-sans antialiased">
        @if (app('impersonate')->isImpersonating())
            <div class="w-full bg-blue-600 text-center p-5 text-white relative" style="z-index: 9999;">
                {{ __('words.you-are-currently-logged-in-as') }}: {{ optional(\App\Models\User::find(app('impersonate')->getImpersonatorId()))->name }}
                <br/>
                <a class="mt-5 block underline" href="{{ route('impersonate.leave') }}">{{ __('words.to-go-back-to-your-account-click-here') }}</a>
            </div>
        @endif
        @inertia
        
        <!-- CSRF Token Auto-Refresh Script -->
        <script>
            // Auto-refresh CSRF token every 30 minutes
            setInterval(function() {
                fetch('/csrf-token')
                    .then(response => response.json())
                    .then(data => {
                        // Update all CSRF tokens on the page
                        document.querySelectorAll('input[name="_token"]').forEach(function(input) {
                            input.value = data.token;
                        });
                        
                        // Update meta tag
                        const metaTag = document.querySelector('meta[name="csrf-token"]');
                        if (metaTag) {
                            metaTag.setAttribute('content', data.token);
                        }
                        
                        // Update Axios default header if using
                        if (window.axios) {
                            window.axios.defaults.headers.common['X-CSRF-TOKEN'] = data.token;
                        }
                        
                        // CSRF token refreshed successfully
                    })
                    .catch(error => {
                        // CSRF token refresh failed
                    });
            }, 30 * 60 * 1000); // Every 30 minutes
        </script>
    </body>
</html>
