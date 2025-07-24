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
                <button type="submit" id="password-login-btn" class="w-full px-4 py-2 bg-blue-600 text-white rounded mt-4">الدخول باستخدام كلمة المرور</button>
            </div>

            <div class="mt-4" id="otp-section" style="display:none;">
                <form id="otp-request-form" method="POST" action="{{ route('login.2fa-code') }}">
                    @csrf
                    <input type="hidden" name="email" id="otp-email" value="{{ old('email') }}">
                    <button type="button" id="request-otp-btn" class="w-full px-4 py-2 bg-green-500 text-white rounded mt-2">طلب رمز OTP</button>
                    <div id="otp-input-group" style="display:none;">
                        <x-jet-label value="رمز التحقق" />
                        <x-jet-input class="block mt-1 w-full" type="text" name="otp" id="otp-input" />
                        <button type="submit" id="otp-login-btn" class="w-full px-4 py-2 bg-green-600 text-white rounded mt-4">تسجيل الدخول برمز التحقق</button>
                    </div>
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

        <div class="mt-4" id="login-options" style="display:none; flex-direction: column; gap: 0.5rem;">
            <button type="button" id="show-password-btn" class="w-full px-4 py-2 bg-blue-600 text-white rounded mb-2">الدخول باستخدام كلمة المرور</button>
            <form id="magic-link-form" method="POST" action="{{ route('login.magic-link.send') }}">
                @csrf
                <input type="hidden" name="email" id="magic-link-email" value="{{ old('email') }}">
                <button type="submit" class="w-full px-4 py-2 bg-yellow-300 text-black font-bold shadow-lg rounded">استخدم الرابط السريع للدخول 🪄</button>
            </form>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const emailField = document.getElementById('email');
            const passwordSection = document.getElementById('password-section');
            const loginOptions = document.getElementById('login-options');
            const rememberSection = document.getElementById('remember-section');
            const showPasswordBtn = document.getElementById('show-password-btn');
            const loginForm = document.getElementById('loginForm');
            const magicLinkForm = document.getElementById('magic-link-form');
            const magicLinkEmail = document.getElementById('magic-link-email');
            const passwordLoginBtn = document.getElementById('password-login-btn');
            const otpSection = document.getElementById('otp-section');
            const otpEmail = document.getElementById('otp-email');
            const otpRequestForm = document.getElementById('otp-request-form');
            const requestOtpBtn = document.getElementById('request-otp-btn');
            const otpInputGroup = document.getElementById('otp-input-group');
            const otpInput = document.getElementById('otp-input');
            const otpLoginBtn = document.getElementById('otp-login-btn');

            function showOptions() {
                const email = emailField.value.trim();
                if (email !== '') {
                    if (email.endsWith('@hadaf-hq.com')) {
                        otpSection.style.display = 'block';
                        loginOptions.style.display = 'none';
                        passwordSection.style.display = 'none';
                        rememberSection.style.display = 'none';
                        passwordLoginBtn.style.display = 'none';
                    } else {
                        loginOptions.style.display = 'flex';
                        passwordSection.style.display = 'none';
                        rememberSection.style.display = 'none';
                        passwordLoginBtn.style.display = 'none';
                        otpSection.style.display = 'none';
                    }
                } else {
                    loginOptions.style.display = 'none';
                    passwordSection.style.display = 'none';
                    rememberSection.style.display = 'none';
                    passwordLoginBtn.style.display = 'none';
                    otpSection.style.display = 'none';
                }
            }

            emailField.addEventListener('input', function() {
                magicLinkEmail.value = this.value;
                otpEmail.value = this.value;
                showOptions();
            });
            showOptions();

            showPasswordBtn.addEventListener('click', function() {
                passwordSection.style.display = 'block';
                rememberSection.style.display = 'block';
                loginOptions.style.display = 'none';
                passwordLoginBtn.style.display = 'block';
                otpSection.style.display = 'none';
                document.getElementById('password').focus();
            });

            loginForm.addEventListener('submit', function(e) {
                // Optionally, show a loading spinner or disable the button
            });

            magicLinkForm.addEventListener('submit', function(e) {
                // Optionally, show a loading spinner or disable the button
            });

            if (otpRequestForm && requestOtpBtn) {
                requestOtpBtn.addEventListener('click', function() {
                    otpInputGroup.style.display = 'block';
                    otpInput.required = true;
                    requestOtpBtn.style.display = 'none';
                    otpRequestForm.submit();
                });
            }
        });
        </script>
    </x-jet-authentication-card>
</x-guest-layout>
