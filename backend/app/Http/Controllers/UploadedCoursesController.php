<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class UploadedCoursesController extends Controller
{
    public function show(Request $request, int $id)
    {
        app()->setLocale('ar');

        $course = $this->courseConfig($id);
        if ($course === null) {
            abort(404);
        }

        if (!$this->isUnlocked($id)) {
            return view('uploaded-courses.password', [
                'courseId' => $id,
                'title' => $course['title'],
            ]);
        }

        $manifest = $this->loadManifest($id);
        if ($manifest === null) {
            abort(503, 'Course content is not ready yet.');
        }

        return view('uploaded-courses.show', [
            'courseId' => $id,
            'title' => $course['title'],
            'manifest' => $manifest,
        ]);
    }

    public function unlock(Request $request, int $id)
    {
        app()->setLocale('ar');

        $course = $this->courseConfig($id);
        if ($course === null) {
            abort(404);
        }

        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $expected = (string) ($course['password'] ?? '');
        $provided = (string) $request->input('password');

        if ($expected === '' || !hash_equals($expected, $provided)) {
            return redirect()
                ->route('uploaded-courses.show', ['id' => $id])
                ->withErrors(['password' => 'كلمة المرور غير صحيحة'])
                ->withInput();
        }

        $request->session()->put($this->sessionKey($id), true);

        return redirect()->route('uploaded-courses.show', ['id' => $id]);
    }

    public function lock(Request $request, int $id)
    {
        $request->session()->forget($this->sessionKey($id));

        return redirect()->route('uploaded-courses.show', ['id' => $id]);
    }

    public function stream(Request $request, int $id, string $path)
    {
        app()->setLocale('ar');

        if ($this->courseConfig($id) === null) {
            abort(404);
        }

        if (!$this->isUnlocked($id)) {
            abort(403);
        }

        $relativePath = $this->sanitizeRelativePath($path);
        $absolutePath = $this->courseRoot($id) . DIRECTORY_SEPARATOR . $relativePath;

        if (!is_file($absolutePath)) {
            abort(404);
        }

        $realCourseRoot = realpath($this->courseRoot($id));
        $realFile = realpath($absolutePath);

        if ($realCourseRoot === false || $realFile === false || strpos($realFile, $realCourseRoot . DIRECTORY_SEPARATOR) !== 0) {
            abort(404);
        }

        $mime = File::mimeType($absolutePath) ?: 'application/octet-stream';
        $accelPath = '/internal/uploaded-courses/' . $id . '/' . implode('/', array_map('rawurlencode', explode('/', $relativePath)));

        // Hand off to nginx (X-Accel-Redirect) so Range / seeking is handled natively.
        return response('', 200, [
            'Content-Type' => $mime,
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'private, no-store, no-cache, must-revalidate',
            'X-Accel-Redirect' => $accelPath,
            'X-Accel-Buffering' => 'no',
        ]);
    }

    private function courseConfig(int $id): ?array
    {
        $courses = config('uploaded-courses', []);

        return $courses[$id] ?? null;
    }

    private function isUnlocked(int $id): bool
    {
        return (bool) session($this->sessionKey($id), false);
    }

    private function sessionKey(int $id): string
    {
        return "uploaded_course_unlocked.{$id}";
    }

    private function courseRoot(int $id): string
    {
        return storage_path("app/uploaded-courses/{$id}");
    }

    private function loadManifest(int $id): ?array
    {
        $path = $this->courseRoot($id) . DIRECTORY_SEPARATOR . 'manifest.json';

        if (!is_file($path)) {
            return null;
        }

        $decoded = json_decode((string) file_get_contents($path), true);

        return is_array($decoded) ? $decoded : null;
    }

    public static function streamUrl(int $id, string $relativePath): string
    {
        $encoded = implode('/', array_map('rawurlencode', explode('/', $relativePath)));

        return url("/ar/uploaded-courses/{$id}/stream/{$encoded}");
    }

    private function sanitizeRelativePath(string $path): string
    {
        // Allow URL-encoded Arabic path segments from the route
        $path = rawurldecode($path);
        $path = str_replace(['\\', "\0"], ['/', ''], $path);
        $path = ltrim($path, '/');

        $segments = array_values(array_filter(explode('/', $path), static function ($segment) {
            return $segment !== '' && $segment !== '.' && $segment !== '..';
        }));

        if ($segments === []) {
            abort(404);
        }

        return implode('/', $segments);
    }
}
