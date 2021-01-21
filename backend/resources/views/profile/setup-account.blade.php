<x-guest-layout>
    <x-jet-authentication-card>
        <x-slot name="logo">
            <x-jet-authentication-card-logo />
        </x-slot>

        <x-jet-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('setup-account.update') }}">
            @csrf

            <div><h1 class="text-2xl text-center my-5 font-bold">{{ __('words.welcome-please-specify-a-password') }}!</h1></div>

            <hr class="border-1 mb-5">

            <div class="mt-4">
                <x-jet-label value="{{ __('words.password') }}" />
                <x-jet-input class="block mt-1 w-full" type="password" name="password" :value="old('password')" required />
            </div>

            <div class="flex items-center mt-4">
                <x-jet-button class="mx-12 w-full flex items-center justify-center">
                    {{ __('words.next') }}
                </x-jet-button>
            </div>
        </form>
    </x-jet-authentication-card>
</x-guest-layout>
