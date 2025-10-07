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
                // Log file upload attempt with detailed info
                \Log::info('File upload attempt', [
                    'trainee_id' => $trainee_id,
                    'has_file' => $request->hasFile('attached_file'),
                    'request_size' => $request->header('content-length'),
                    'max_file_size' => ini_get('upload_max_filesize'),
                    'max_post_size' => ini_get('post_max_size'),
                    'file_info' => $request->hasFile('attached_file') ? [
                        'original_name' => $request->file('attached_file')->getClientOriginalName(),
                        'size' => $request->file('attached_file')->getSize(),
                        'mime_type' => $request->file('attached_file')->getMimeType(),
                        'extension' => $request->file('attached_file')->getClientOriginalExtension(),
                        'is_valid' => $request->file('attached_file')->isValid(),
                        'error' => $request->file('attached_file')->getError(),
                        'path' => $request->file('attached_file')->getPath(),
                    ] : null
                ]);

                // More flexible file validation - check extension instead of MIME type
                $validator = \Validator::make($request->all(), [
                    'attached_file' => 'required|file|max:20480', // 20MB max
                ], [
                    'attached_file.required' => 'يجب اختيار ملف للرفع',
                    'attached_file.file' => 'يجب أن يكون الملف صالحاً',
                    'attached_file.max' => 'حجم الملف يجب أن يكون أقل من 20 ميجابايت',
                ]);

                // Custom validation for file extension
                $validator->after(function ($validator) use ($request) {
                    if ($request->hasFile('attached_file')) {
                        $file = $request->file('attached_file');
                        $extension = strtolower($file->getClientOriginalExtension());
                        $allowedExtensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
                        
                        if (!in_array($extension, $allowedExtensions)) {
                            $validator->errors()->add('attached_file', 'يجب أن يكون الملف من نوع: PDF, DOC, DOCX, JPG, JPEG, PNG');
                        }
                    }
                });

                if ($validator->fails()) {
                    \Log::error('File validation failed', [
                        'trainee_id' => $trainee_id,
                        'errors' => $validator->errors()->toArray()
                    ]);
                    throw new \Illuminate\Validation\ValidationException($validator);
                }

                $trainee = Trainee::withTrashed()->findOrFail($trainee_id);
                
                // Check if file exists in request
                if (!$request->hasFile('attached_file')) {
                    // Check if the issue is due to file size limits
                    $contentLength = $request->header('content-length');
                    $maxPostSize = $this->parseSize(ini_get('post_max_size'));
                    $maxUploadSize = $this->parseSize(ini_get('upload_max_filesize'));
                    
                    $errorMessage = 'لم يتم العثور على الملف المرفوع';
                    
                    if ($contentLength && $contentLength > $maxPostSize) {
                        $errorMessage = 'حجم الملف كبير جداً. الحد الأقصى المسموح: ' . ini_get('post_max_size');
                    } elseif ($contentLength && $contentLength > $maxUploadSize) {
                        $errorMessage = 'حجم الملف كبير جداً. الحد الأقصى المسموح: ' . ini_get('upload_max_filesize');
                    }
                    
                    \Log::error('File not found in request', [
                        'trainee_id' => $trainee_id,
                        'content_length' => $contentLength,
                        'max_post_size' => $maxPostSize,
                        'max_upload_size' => $maxUploadSize,
                        'all_files' => $request->allFiles(),
                        'post_data_keys' => array_keys($request->all())
                    ]);
                    
                    return response()->json(['error' => $errorMessage], 413);
                }

                $file = $request->file('attached_file');
                
                // Check for PHP upload errors
                $uploadError = $file->getError();
                if ($uploadError !== UPLOAD_ERR_OK) {
                    $errorMessages = [
                        UPLOAD_ERR_INI_SIZE => 'الملف كبير جداً (تجاوز upload_max_filesize)',
                        UPLOAD_ERR_FORM_SIZE => 'الملف كبير جداً (تجاوز MAX_FILE_SIZE)',
                        UPLOAD_ERR_PARTIAL => 'تم رفع جزء من الملف فقط',
                        UPLOAD_ERR_NO_FILE => 'لم يتم رفع أي ملف',
                        UPLOAD_ERR_NO_TMP_DIR => 'مجلد مؤقت مفقود',
                        UPLOAD_ERR_CANT_WRITE => 'فشل في كتابة الملف على القرص',
                        UPLOAD_ERR_EXTENSION => 'إضافة PHP أوقفت رفع الملف',
                    ];
                    
                    $errorMessage = $errorMessages[$uploadError] ?? 'خطأ غير معروف في رفع الملف';
                    
                    \Log::error('PHP upload error', [
                        'trainee_id' => $trainee_id,
                        'error_code' => $uploadError,
                        'error_message' => $errorMessage,
                        'file_size' => $file->getSize(),
                        'max_upload_size' => ini_get('upload_max_filesize'),
                        'max_post_size' => ini_get('post_max_size')
                    ]);
                    
                    return response()->json(['error' => $errorMessage], 400);
                }
                
                // Additional file validation
                if (!$file->isValid()) {
                    \Log::error('Invalid file uploaded', [
                        'trainee_id' => $trainee_id,
                        'error' => $file->getError(),
                        'file_path' => $file->getPath(),
                        'file_size' => $file->getSize()
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
