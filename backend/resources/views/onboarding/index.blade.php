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

            <h1 class="text-xl text-center pt-10 pb-5">{{ __('words.hi-there') }}</h1>

            <hr class="py-5">

            <div>
                <a href="" class="text-2xl block bg-red-500 p-3 text-white rounded-lg flex justify-center items-center hover:bg-red-600">
                    <img src="{{ asset('/img/student.svg') }}" class="w-6" style="filter:invert(1)" alt="">
                    <span class="mx-4">{{ __('words.im-a-trainee') }}</span>
                </a>
            </div>

            <div class="mt-10">
                <a href="" class="text-2xl block bg-blue-500 p-3 text-white rounded-lg flex justify-center items-center hover:bg-blue-600">
                    <img src="{{ asset('/img/teacher.svg') }}" class="w-6" style="filter:invert(1)" alt="">
                    <span class="mx-4">{{ __('words.im-an-instructor') }}</span>
                </a>
            </div>

        </form>
    </x-jet-authentication-card>
</x-guest-layout>
