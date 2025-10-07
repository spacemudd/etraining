<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\Trainee;
use App\Models\Media;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Str;

class TraineesFilesController extends Controller
{
    public function index($trainee_id)
    {
       
        $trainee = Trainee::withTrashed()->with('general_files')
        ->findOrFail($trainee_id);
        return Inertia::render('Back/Trainees/Files/Index', [
            'trainee' => $trainee,
        ]);
    }

        public function store(Request $request, $trainee_id)
        {
            try {
                // Validate the uploaded file
                $request->validate([
                    'attached_file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB max
                ], [
                    'attached_file.required' => 'يجب اختيار ملف للرفع',
                    'attached_file.file' => 'يجب أن يكون الملف صالحاً',
                    'attached_file.mimes' => 'يجب أن يكون الملف من نوع: PDF, DOC, DOCX, JPG, JPEG, PNG',
                    'attached_file.max' => 'حجم الملف يجب أن يكون أقل من 10 ميجابايت',
                ]);

                $trainee = Trainee::withTrashed()->findOrFail($trainee_id);
                
                // Check if file exists in request
                if (!$request->hasFile('attached_file')) {
                    return redirect()->back()->with('error', 'لم يتم العثور على الملف المرفوع');
                }

                $trainee->addMediaFromRequest('attached_file')
                    ->usingFileName(request()->file('attached_file')->hashName())
                    ->toMediaCollection('general_files');
                    
                return redirect()->route('back.trainees.files.index', $trainee->id)
                    ->with('success', 'تم رفع الملف بنجاح');
                    
            } catch (\Illuminate\Validation\ValidationException $e) {
                return redirect()->back()
                    ->withErrors($e->validator)
                    ->withInput();
            } catch (\Exception $e) {
                return redirect()->back()
                    ->with('error', 'حدث خطأ أثناء رفع الملف: ' . $e->getMessage())
                    ->withInput();
            }
        }

    public function show($trainee_id, $file)
    {
        $media = Media::findOrFail($file);
        if ($media->disk === 's3') {
            return redirect()->to($media->getTemporaryUrl(now()->addMinutes(5), '', [
                //'ResponseContentType' => 'application/octet-stream',
            ]));
        } else {
            return response()->file($media->getPath());
        }
    }

    public function destroy($trainee_id, $file)
    {
        $media = Media::findOrFail($file);
        $media->delete();
        return redirect()->route('back.trainees.files.index', $trainee_id);
    }
    
    /**
     * Parse PHP size string to bytes
     */
    private function parseSize($size) {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
        $size = preg_replace('/[^0-9\.]/', '', $size);
        if ($unit) {
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        } else {
            return round($size);
        }
    }
}
