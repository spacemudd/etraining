<x-guest-layout>
    <x-jet-authentication-card>
        <x-slot name="logo">
            <x-jet-authentication-card-logo/>
        </x-slot>

        <x-jet-validation-errors class="mb-4" />

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <form id="loginForm" method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <x-jet-label value="{{ __('words.email') }}" />
                <x-jet-input id="email" dir="ltr" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            </div>

            <div class="mt-4" id="hideFor2Fa">
                <x-jet-label value="{{ __('words.password') }}" />
                <x-jet-input id="password" dir="ltr" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            </div>

            <div class="mt-4">
                <form method="POST" action="{{ route('login.magic-link.send') }}">
                    @csrf
                    <input type="hidden" name="email" id="magic-link-email" value="{{ old('email') }}">
                    <button type="submit" class="text-blue-700 underline text-sm hover:text-blue-900 mt-2">{{ __('Send me a magic link to login') }}</button>
                </form>
            </div>
            <script>
                // Keep magic link email in sync with login email field
                document.getElementById('email').addEventListener('input', function() {
                    document.getElementById('magic-link-email').value = this.value;
                });
            </script>

            <div class="block mt-4">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox" name="remember">
                    <span class="mx-2 text-sm text-gray-600">{{ __('words.remember-me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-lg font-bold text-blue-700 hover:text-blue-900 mr-4 px-3 py-2 border border-blue-300 rounded transition duration-150 ease-in-out bg-blue-50" href="{{ route('password.request') }}">
                        {{ __('words.forget-password') }}
                    </a>
                @endif

                <x-jet-button class="ltr:ml-4 rtl:mr-4 tracking-normal">
                    {{ __('words.login') }}
                </x-jet-button>
            </div>
        </form>

        <script>
            {{-- Add listener to email field --}}
            var emailField = document.getElementById("email");
            emailField.addEventListener('input', function() {
                // if (document.getElementById('email').value.includes('nelc.user@ptc-ksa.net' || 'nelc@ptc-ksa.net')) {
                //     document.getElementById('hideFor2Fa').style.display = 'block';
                //     document.getElementById('password').required = true;
                // }
                if (document.getElementById('email').value.includes('nelc.instructor@ptc-ksa.net')) {
                    document.getElementById('hideFor2Fa').style.display = 'block';
                    document.getElementById('password').required = true;
                }
                else if (document.getElementById('email').value.includes('nelc.user@ptc-ksa.net')) {
                    document.getElementById('hideFor2Fa').style.display = 'block';
                    document.getElementById('password').required = true;
                }
                else if (document.getElementById('email').value.includes('ptc-ksa.net') || document.getElementById('email').value.includes('hadaf-hq.com')) {
                    document.getElementById('hideFor2Fa').style.display = 'none';
                    document.getElementById('password').required = false;
                } else {
                    document.getElementById('hideFor2Fa').style.display = 'block';
                    document.getElementById('password').required = true;
                }
            });
            emailField.dispatchEvent(new Event('input'));

            {{-- Redirect PTC users to 2FA page --}}
            const form = document.getElementById("loginForm");
            form.onsubmit = function() {
                // if (emailField.value.includes('nelc.user@ptc-ksa.net' || 'nelc@ptc-ksa.net')) {
                //     form.action = '/login';
                //     form.submit();
                // }
                if (emailField.value.includes('nelc.instructor@ptc-ksa.net')) {
                    form.action = '/login';
                    form.submit();
                }
                else if (emailField.value.includes('nelc.user@ptc-ksa.net')) {
                    form.action = '/login';
                    form.submit();
                }
                else if (emailField.value.includes('ptc-ksa.net') || emailField.value.includes('hadaf-hq.com')) {
                    form.action = '/login/2fa-code';
                    form.submit();
                } else {
                    form.action = '/login';
                    form.submit();
                }
            };
        </script>
    </x-jet-authentication-card>
</x-guest-layout>
