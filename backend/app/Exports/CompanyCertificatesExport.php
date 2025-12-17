<?php

namespace App\Exports;

use App\Models\Back\Trainee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CompanyCertificatesExport implements FromCollection, WithHeadings
{
    protected $companyId;
    protected $maxCertificatesCount = 0;

    public function __construct($companyId)
    {
        $this->companyId = $companyId;
    }

    public function collection()
    {
        // جلب المتدربين بناءً على company_id مع الشهادات
        $trainees = Trainee::withTrashed()
            ->where('company_id', $this->companyId)
            ->with([
                'custom_certificates' => function($q) {
                    $q->orderBy('created_at', 'desc');
                },
                'uk_certificates' => function($q) {
                    $q->where('status', 'sent')
                      ->with(['ukCertificate.course'])
                      ->orderBy('sent_at', 'desc');
                }
            ])
            ->get();

        // حساب الحد الأقصى لعدد الشهادات
        $this->maxCertificatesCount = 0;
        foreach ($trainees as $trainee) {
            $count = $trainee->custom_certificates->count() + $trainee->uk_certificates->count();
            $this->maxCertificatesCount = max($this->maxCertificatesCount, $count);
        }

        return $trainees->map(function ($trainee) {
            $certificates = [];
            
            // جمع الشهادات المخصصة
            foreach ($trainee->custom_certificates as $cert) {
                $certificates[] = $cert->title;
            }
            
            // جمع شهادات UK
            foreach ($trainee->uk_certificates as $cert) {
                if ($cert->ukCertificate && $cert->ukCertificate->course) {
                    $certificates[] = $cert->ukCertificate->course->name_ar ?? 'غير محدد';
                }
            }
            
            $certificatesCount = count($certificates);
            
            // إنشاء الصف
            $row = [
                'identity_number' => $trainee->identity_number,
                'name' => $trainee->name,
                'certificates_count' => $certificatesCount,
            ];
            
            // إضافة أسماء الشهادات كأعمدة
            for ($i = 0; $i < $this->maxCertificatesCount; $i++) {
                $row['certificate_' . $i] = $certificates[$i] ?? '';
            }
            
            return $row;
        });
    }

    public function headings(): array
    {
        $headings = [
            'الهوية',
            'الإسم',
            'عدد الشهادات المستلمة',
        ];
        
        // إضافة عناوين الأعمدة للشهادات
        for ($i = 1; $i <= $this->maxCertificatesCount; $i++) {
            $headings[] = 'شهادة ' . $i;
        }
        
        return $headings;
    }
}

