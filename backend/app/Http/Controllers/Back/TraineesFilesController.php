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
                // Log file upload attempt
                \Log::info('File upload attempt', [
                    'trainee_id' => $trainee_id,
                    'has_file' => $request->hasFile('attached_file'),
                    'file_info' => $request->hasFile('attached_file') ? [
                        'original_name' => $request->file('attached_file')->getClientOriginalName(),
                        'size' => $request->file('attached_file')->getSize(),
                        'mime_type' => $request->file('attached_file')->getMimeType(),
                        'extension' => $request->file('attached_file')->getClientOriginalExtension(),
                    ] : null
                ]);

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
                    \Log::error('File not found in request', ['trainee_id' => $trainee_id]);
                    return response()->json(['error' => 'لم يتم العثور على الملف المرفوع'], 400);
                }

                $file = $request->file('attached_file');
                
                // Additional file validation
                if (!$file->isValid()) {
                    \Log::error('Invalid file uploaded', [
                        'trainee_id' => $trainee_id,
                        'error' => $file->getError()
                    ]);
                    return response()->json(['error' => 'الملف المرفوع غير صالح'], 400);
                }

                $media = $trainee->addMediaFromRequest('attached_file')
                    ->usingFileName($file->hashName())
                    ->toMediaCollection('general_files');
                
                \Log::info('File uploaded successfully', [
                    'trainee_id' => $trainee_id,
                    'media_id' => $media->id,
                    'file_name' => $media->file_name
                ]);
                    
                return response()->json([
                    'success' => true,
                    'message' => 'تم رفع الملف بنجاح',
                    'file' => [
                        'id' => $media->id,
                        'name' => $media->file_name,
                        'size' => $media->size
                    ]
                ]);
                    
            } catch (\Illuminate\Validation\ValidationException $e) {
                \Log::error('Validation error during file upload', [
                    'trainee_id' => $trainee_id,
                    'errors' => $e->errors()
                ]);
                return response()->json([
                    'error' => 'خطأ في التحقق من الملف',
                    'errors' => $e->errors()
                ], 422);
            } catch (\Exception $e) {
                \Log::error('Exception during file upload', [
                    'trainee_id' => $trainee_id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json([
                    'error' => 'حدث خطأ أثناء رفع الملف: ' . $e->getMessage()
                ], 500);
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
}
