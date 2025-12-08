<x-guest-layout>
    <x-jet-authentication-card>
        <x-slot name="logo">
            <x-jet-authentication-card-logo />
        </x-slot>

        <x-jet-validation-errors class="mb-4" />

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <div class="mb-4 text-sm text-gray-600">
            <p>تم إرسال رمز التحقق إلى بريدك الإلكتروني: <strong>{{ $email }}</strong></p>
        </div>

        <form method="POST" action="{{ route('login.verify-code') }}" id="verifyCodeForm">
            @csrf

            <input type="hidden" name="email" value="{{ $email }}" />

            <div>
                <x-jet-label value="{{ __('words.verify_code') }}" />
                <x-jet-input 
                    dir="ltr" 
                    class="block mt-1 w-full" 
                    type="text" 
                    name="code" 
                    id="code"
                    maxlength="6"
                    pattern="[0-9]*"
                    inputmode="numeric"
                    required 
                    autofocus 
                    autocomplete="one-time-code"
                />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-jet-button type="submit" class="ltr:ml-4 rtl:mr-4 tracking-normal">
                    {{ __('words.login') }}
                </x-jet-button>
            </div>
        </form>

        <div class="mt-4 text-center">
            <form method="POST" action="{{ route('login.2fa-code') }}">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}" />
                <button type="submit" class="text-sm text-blue-600 hover:text-blue-800 underline">
                    إعادة إرسال رمز التحقق
                </button>
            </form>
        </div>
    </x-jet-authentication-card>
</x-guest-layout>
