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

            <div>
                <a href="{{ route('login') }}"
                   class="flex bg-gray-500 hover:bg-gray-600 text-white py-5 px-10 rounded-lg text-2xl justify-center">
                    <img src="{{ asset('/img/login.svg') }}" class="w-8 ml-4" style="filter:invert(1)">
                    {{ __('words.to-login') }}
                </a>
            </div>

            <div class="headline-separator my-5 text-center">
                {{ __('words.to-register') }}
            </div>

            {{-- Trainee login --}}
            <div>
                <a href="{{ route('register.trainees') }}"
                   class="flex bg-red-500 hover:bg-red-600 text-white py-5 px-10 rounded-lg text-2xl justify-center">
                    <img src="{{ asset('/img/student.svg') }}" class="w-8 ml-5" style="filter:invert(1)">
                    {{ __('words.im-a-trainee') }}
                </a>
            </div>

            <a href="/TraineeGuide.pdf" target="_blank" class="mt-5 justify-center flex p-2 border border-gray-500 border-2 gap-2 rounded hover:text-gray-800 hover:border-red-500">
                <svg style="color:black;margin-top:3px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                </svg>
                <span>دليل المستخدم</span>
            </a>

{{--            <div class="headline-separator py-5 text-center">--}}
{{--                {{ __('words.or') }}--}}
{{--            </div>--}}

{{--             Instructor login --}}
{{--            <div>--}}
{{--                <a href="{{ route('register.instructors') }}"--}}
{{--                   class="flex bg-blue-500 hover:bg-blue-600 text-white py-5 px-10 rounded-lg text-2xl justify-center">--}}
{{--                    <img src="{{ asset('/img/teacher.svg') }}" class="w-8 ml-5" style="filter:invert(1)">--}}
{{--                    {{ __('words.im-an-instructor') }}--}}
{{--                </a>--}}
{{--            </div>--}}
        </form>

        <x-slot name="bottomCard">
            <div class="mt-5">
                <ul class="list-disc">
                    <li class="mt-2"><a href="https://app.ptc-ksa.com/requirements">{{ __('words.system-requirements') }}</a></li>
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
