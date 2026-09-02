@extends('uploaded-courses.layout')

@section('title', $title . ' | جسارة')

@section('content')
    <section class="mx-auto flex min-h-[calc(100vh-5rem)] max-w-lg items-center px-4 py-12 sm:px-6">
        <div class="w-full overflow-hidden rounded-[2rem] border border-jasarah/10 bg-white shadow-xl shadow-jasarah/10">
            <div class="bg-jasarah px-6 py-8 text-white">
                <p class="text-sm font-bold text-white/80">دورة محمية</p>
                <h1 class="mt-2 text-2xl font-extrabold leading-10 sm:text-3xl">{{ $title }}</h1>
                <p class="mt-3 text-sm leading-7 text-white/85">أدخل كلمة المرور للوصول إلى محتوى الدورة.</p>
            </div>

            <form method="POST" action="{{ route('uploaded-courses.unlock', ['id' => $courseId]) }}" class="space-y-5 p-6 sm:p-8">
                @csrf

                <div>
                    <label for="password" class="mb-2 block text-sm font-bold text-jasarah-soft">كلمة المرور</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        autofocus
                        autocomplete="current-password"
                        class="w-full rounded-2xl border border-jasarah/15 bg-jasarah-page px-4 py-3 text-base font-medium text-jasarah-ink outline-none ring-jasarah/30 transition focus:border-jasarah focus:ring-2"
                        placeholder="••••••••"
                    >
                    @error('password')
                        <p class="mt-2 text-sm font-bold text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="inline-flex w-full items-center justify-center rounded-full bg-jasarah px-6 py-3 text-base font-extrabold text-white shadow-lg shadow-jasarah/25 transition hover:bg-jasarah-dark"
                >
                    دخول الدورة
                </button>
            </form>
        </div>
    </section>
@endsection
