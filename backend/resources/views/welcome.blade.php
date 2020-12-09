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

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div><h1 class="text-2xl text-center my-5 text-gray-500 font-bold">{{ __('words.welcome') }}!</h1></div>

            <hr class="border-1 mb-5">

            {{-- Trainee login --}}
            <div>
                <a href="{{ route('register.trainees') }}"
                   class="flex bg-red-500 hover:bg-red-600 text-white py-5 px-10 rounded-lg text-2xl justify-center">
                    <img src="{{ asset('/img/student.svg') }}" class="w-8 ml-5" style="filter:invert(1)">
                    {{ __('words.im-a-trainee') }}
                </a>
            </div>

            <div class="headline-separator py-5 text-center">
                {{ __('words.or') }}
            </div>

            {{-- Instructor login --}}
            <div>
                <a href=""
                   class="flex bg-blue-500 hover:bg-blue-600 text-white py-5 px-10 rounded-lg text-2xl justify-center">
                    <img src="{{ asset('/img/teacher.svg') }}" class="w-8 ml-5" style="filter:invert(1)">
                    {{ __('words.im-an-instructor') }}asd
                </a>
            </div>


            <div class="py-5 text-center">
                <a href="{{ route('login') }}">{{ __('words.have-an-account-?') }} {{ __('words.click-here-to-login') }}</a>
            </div>
        </form>

        <x-slot name="bottomCard">
            <div class="mt-5">
                <ul class="list-disc">
                    <li class="mt-2"><a href="https://app.ptc-ksa.com/terms#support-policy">{{ __('words.support-policy') }}</a></li>
                    <li class="mt-2"><a href="https://app.ptc-ksa.com/terms#attendance-policy">{{ __('words.attendance-policy') }}</a></li>
                    <li class="mt-2"><a href="https://app.ptc-ksa.com/terms#academic-integrity-policy">{{ __('words.academic-integrity-policy') }}</a></li>
                    <li class="mt-2"><a href="https://app.ptc-ksa.com/terms#privacy-policy">{{ __('words.privacy-policy') }}</a></li>
                    <li class="mt-2"><a href="https://app.ptc-ksa.com/terms#intellectual-property-rights">{{ __('words.intellectual-property-rights') }}</a></li>
                </ul>
            </div>
        </x-slot>
    </x-jet-authentication-card>
</x-guest-layout>
