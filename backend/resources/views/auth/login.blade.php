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

            <div class="mt-4" id="password-section" style="display:none;">
                <x-jet-label value="{{ __('words.password') }}" />
                <x-jet-input id="password" dir="ltr" class="block mt-1 w-full" type="password" name="password" autocomplete="current-password" />
            </div>

            <div class="mt-4" id="login-options" style="display:none; flex-direction: column; gap: 0.5rem;">
                <button type="button" id="show-password-btn" class="w-full px-4 py-2 bg-blue-600 text-white rounded mb-2">الدخول باستخدام كلمة المرور</button>
                <form id="magic-link-form" method="POST" action="{{ route('login.magic-link.send') }}">
                    @csrf
                    <input type="hidden" name="email" id="magic-link-email" value="{{ old('email') }}">
                    <button type="submit" class="w-full px-4 py-2 bg-yellow-400 text-black font-bold border-2 border-yellow-500 shadow-lg rounded">استخدم الرابط السريع للدخول 🪄</button>
                </form>
            </div>

            <div class="block mt-4" id="remember-section" style="display:none;">
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
            </div>
        </form>

        <script>
            const emailField = document.getElementById('email');
            const passwordSection = document.getElementById('password-section');
            const loginOptions = document.getElementById('login-options');
            const rememberSection = document.getElementById('remember-section');
            const showPasswordBtn = document.getElementById('show-password-btn');
            const loginForm = document.getElementById('loginForm');
            const magicLinkForm = document.getElementById('magic-link-form');
            const magicLinkEmail = document.getElementById('magic-link-email');

            function showOptions() {
                if (emailField.value.trim() !== '') {
                    loginOptions.style.display = 'flex';
                    passwordSection.style.display = 'none';
                    rememberSection.style.display = 'none';
                } else {
                    loginOptions.style.display = 'none';
                    passwordSection.style.display = 'none';
                    rememberSection.style.display = 'none';
                }
            }

            emailField.addEventListener('input', function() {
                magicLinkEmail.value = this.value;
                showOptions();
            });
            showOptions();

            showPasswordBtn.addEventListener('click', function() {
                passwordSection.style.display = 'block';
                rememberSection.style.display = 'block';
                loginOptions.style.display = 'none';
                document.getElementById('password').focus();
            });

            magicLinkForm.addEventListener('submit', function(e) {
                // Optionally, show a loading spinner or disable the button
            });
        </script>
    </x-jet-authentication-card>
</x-guest-layout>
