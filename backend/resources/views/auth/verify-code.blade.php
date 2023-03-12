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

        <form method="POST" action="{{ route('login.verify-code') }}">
            @csrf

            <x-jet-input type="hidden" name="email" value="{{ $email }}" />

            <div>
                <x-jet-label value="{{ __('words.verify_code') }}" />
                <x-jet-input dir="ltr" class="block mt-1 w-full" type="text" name="code" required autofocus />
            </div>

            <div class="flex items-center justify-end mt-4">

                <x-jet-button class="ltr:ml-4 rtl:mr-4 tracking-normal">
                    {{ __('words.login') }}
                </x-jet-button>
                {{--<x-jet-button class="ltr:ml-4 rtl:mr-4 tracking-normal">--}}
                {{--    {{ __('words.send_again') }}--}}
                {{--</x-jet-button>--}}
            </div>
        </form>
    </x-jet-authentication-card>
</x-guest-layout>
