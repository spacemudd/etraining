<x-guest-layout>
    <x-jet-authentication-card>
        <x-slot name="logo">
            <x-jet-authentication-card-logo />
        </x-slot>

        <x-jet-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register.instructors.store') }}">
            @csrf

            <div><h1 class="text-2xl text-center my-5 font-bold">{{ __('words.welcome') }}!</h1></div>

            <div
               class="flex py-5 px-10 rounded-lg text-2xl justify-center">
                <img src="{{ asset('/img/teacher.svg') }}" class="w-8 ml-5">
                {{ __('words.fill-your-application') }}
            </div>

            <hr class="border-1 mb-5">

            <div>
                <x-jet-label value="{{ __('words.full-name') }}" />
                <x-jet-input class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            </div>

            <div class="mt-4">
                <x-jet-label value="{{ __('words.identity_number') }}" />
                <x-jet-input class="block mt-1 w-full" type="text" name="identity_number" :value="old('identity_number')" required />
            </div>

            <div class="mt-4">
                <x-jet-label value="{{ __('words.email') }}" />
                <x-jet-input class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
            </div>

            <div class="mt-4">
                <x-jet-label value="{{ __('words.password') }}" />
                <x-jet-input class="block mt-1 w-full" type="password" name="password" :value="old('password')" required />
            </div>

            <div class="mt-4">
                <x-jet-label value="{{ __('words.phone') }}" />
                <x-jet-input class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" required />
            </div>

            <div class="mt-4">
                <x-jet-label value="{{ __('words.city') }}" />
                <div class="relative">
                    <select class="block appearance-none w-full border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                            name="city_id"
                            id="city_id">
                        <option value=""></option>
                        @foreach (\App\Models\City::orderBy('name')->get() as $level)
                            <option value="{{ $level->id }}">
                                @if (app()->getLocale() === 'ar')
                                    {{ $level->name_ar }}
                                @else
                                    {{ $level->name_en }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <x-jet-label value="{{ __('words.provided_courses') }}" />
                <x-jet-input class="block mt-1 w-full" type="text" name="provided_courses" :value="old('provided_courses')" required />
            </div>

            <div class="mt-4">
                <x-jet-label value="{{ __('words.twitter_link') }}" />
                <x-jet-input class="block mt-1 w-full" type="text" name="twitter_link" :value="old('twitter_link')" />
            </div>

            <div class="flex items-center mt-4">


                <x-jet-button class="mx-12 w-full flex items-center justify-center">
                    {{ __('words.next') }}
                </x-jet-button>
            </div>
        </form>
    </x-jet-authentication-card>
</x-guest-layout>
