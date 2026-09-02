@extends('uploaded-courses.layout')

@section('title', $title . ' | جسارة')

@section('header_actions')
    <form method="POST" action="{{ route('uploaded-courses.lock', ['id' => $courseId]) }}">
        @csrf
        <button
            type="submit"
            class="inline-flex items-center justify-center rounded-full border border-jasarah/15 bg-white px-4 py-2 text-sm font-bold text-jasarah shadow-sm transition hover:border-jasarah/40"
        >
            تسجيل الخروج
        </button>
    </form>
@endsection

@section('content')
    @php
        $units = $manifest['units'] ?? [];
        $flatLessons = [];
        foreach ($units as $unitIndex => $unit) {
            foreach (($unit['sessions'] ?? []) as $sessionIndex => $session) {
                foreach (($session['lessons'] ?? []) as $lessonIndex => $lesson) {
                    $flatLessons[] = array_merge($lesson, [
                        'unit_title' => $unit['title'] ?? '',
                        'session_title' => $session['title'] ?? '',
                        'unit_index' => $unitIndex,
                        'session_index' => $sessionIndex,
                        'lesson_index' => $lessonIndex,
                    ]);
                }
            }
        }
        $first = $flatLessons[0] ?? null;
    @endphp

    <section class="bg-jasarah">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 lg:py-10">
            <p class="text-sm font-bold text-white/80">محتوى الدورة</p>
            <h1 class="mt-2 max-w-4xl text-3xl font-extrabold leading-tight text-white sm:text-4xl">{{ $title }}</h1>
            <p class="mt-3 text-sm font-medium text-white/85">
                {{ count($flatLessons) }} درس مرئي · {{ count($units) }} وحدات
            </p>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="grid gap-6 lg:grid-cols-[1.4fr_0.9fr]">
            <div class="overflow-hidden rounded-[2rem] border border-jasarah/10 bg-white shadow-xl shadow-jasarah/10">
                <div class="bg-black">
                    <video
                        id="course-player"
                        class="aspect-video w-full bg-black"
                        controls
                        playsinline
                        preload="metadata"
                        @if($first)
                            src="{{ \App\Http\Controllers\UploadedCoursesController::streamUrl($courseId, $first['path']) }}"
                        @endif
                    >
                        متصفحك لا يدعم تشغيل الفيديو.
                    </video>
                </div>
                <div class="space-y-2 p-5 sm:p-6">
                    <p id="now-playing-unit" class="text-sm font-bold text-jasarah">{{ $first['unit_title'] ?? '' }}</p>
                    <h2 id="now-playing-title" class="text-xl font-extrabold text-jasarah-ink sm:text-2xl">
                        {{ $first['title'] ?? 'اختر درساً من القائمة' }}
                    </h2>
                    <p id="now-playing-session" class="text-sm leading-7 text-jasarah-muted">
                        {{ $first['session_title'] ?? '' }}
                    </p>
                </div>
            </div>

            <aside class="overflow-hidden rounded-[2rem] border border-jasarah/10 bg-white shadow-xl shadow-jasarah/10">
                <div class="border-b border-jasarah/10 bg-jasarah-tint px-5 py-4">
                    <h2 class="text-lg font-extrabold text-jasarah">وحدات الدورة</h2>
                    <p class="mt-1 text-sm text-jasarah-muted">اختر الجلسة ثم شغّل الدرس</p>
                </div>

                <div class="max-h-[70vh] space-y-3 overflow-y-auto p-3 sm:p-4" id="lesson-list">
                    @foreach($units as $unitIndex => $unit)
                        <details class="group rounded-3xl border border-jasarah/10 bg-jasarah-page open:bg-white" @if($unitIndex === 0) open @endif>
                            <summary class="cursor-pointer list-none px-4 py-3 font-extrabold text-jasarah marker:content-none">
                                <div class="flex items-center justify-between gap-3">
                                    <span>{{ $unit['title'] ?? ('الوحدة ' . ($unitIndex + 1)) }}</span>
                                    <span class="rounded-full bg-jasarah/10 px-3 py-1 text-xs font-bold text-jasarah">
                                        {{ collect($unit['sessions'] ?? [])->sum(function ($s) { return count($s['lessons'] ?? []); }) }} دروس
                                    </span>
                                </div>
                            </summary>

                            <div class="space-y-3 px-3 pb-3">
                                @foreach(($unit['sessions'] ?? []) as $sessionIndex => $session)
                                    <div class="rounded-2xl bg-white p-3 ring-1 ring-jasarah/10">
                                        <p class="mb-2 px-1 text-xs font-bold text-jasarah-muted">
                                            {{ $session['title'] ?? ('جلسة ' . ($sessionIndex + 1)) }}
                                        </p>
                                        <div class="space-y-2">
                                            @foreach(($session['lessons'] ?? []) as $lessonIndex => $lesson)
                                                @php
                                                    $isFirst = $unitIndex === 0 && $sessionIndex === 0 && $lessonIndex === 0;
                                                    $streamUrl = \App\Http\Controllers\UploadedCoursesController::streamUrl($courseId, $lesson['path']);
                                                @endphp
                                                <button
                                                    type="button"
                                                    class="lesson-btn w-full rounded-2xl px-3 py-3 text-right transition hover:bg-jasarah-tint {{ $isFirst ? 'lesson-active' : 'bg-jasarah-page' }}"
                                                    data-stream-url="{{ $streamUrl }}"
                                                    data-title="{{ $lesson['title'] }}"
                                                    data-unit="{{ $unit['title'] ?? '' }}"
                                                    data-session="{{ $session['title'] ?? '' }}"
                                                >
                                                    <span class="block text-sm font-extrabold leading-6">{{ $lesson['title'] }}</span>
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </details>
                    @endforeach
                </div>
            </aside>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    (function () {
        var player = document.getElementById('course-player');
        var titleEl = document.getElementById('now-playing-title');
        var unitEl = document.getElementById('now-playing-unit');
        var sessionEl = document.getElementById('now-playing-session');
        var buttons = document.querySelectorAll('.lesson-btn');

        function activate(button) {
            buttons.forEach(function (btn) {
                btn.classList.remove('lesson-active');
                btn.classList.add('bg-jasarah-page');
            });
            button.classList.add('lesson-active');
            button.classList.remove('bg-jasarah-page');

            var url = button.getAttribute('data-stream-url');
            var title = button.getAttribute('data-title') || '';
            var unit = button.getAttribute('data-unit') || '';
            var session = button.getAttribute('data-session') || '';

            titleEl.textContent = title;
            unitEl.textContent = unit;
            sessionEl.textContent = session;

            if (player.getAttribute('src') !== url) {
                player.setAttribute('src', url);
                player.load();
            }
            player.play().catch(function () {});
        }

        buttons.forEach(function (button) {
            button.addEventListener('click', function () {
                activate(button);
            });
        });
    })();
</script>
@endpush
