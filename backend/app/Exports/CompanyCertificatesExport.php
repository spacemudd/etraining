<?php

namespace App\Exports;

use App\Models\Back\Trainee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class CompanyCertificatesExport implements FromCollection, WithHeadings, WithStyles, WithEvents
{
    protected $companyId;
    protected $maxCertificatesCount = 0;
    protected $traineeStatuses = [];

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

        // حساب الحد الأقصى لعدد الشهادات (يجب أن يكون محسوباً قبل headings و styles)
        $this->calculateMaxCertificatesCount($trainees);

        // حفظ حالة كل متدرب (محذوف أو موقوف)
        foreach ($trainees as $trainee) {
            $isDeletedOrSuspended = !is_null($trainee->deleted_at) || !is_null($trainee->suspended_at);
            $this->traineeStatuses[$trainee->identity_number] = $isDeletedOrSuspended;
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
        // التأكد من حساب maxCertificatesCount إذا لم يكن محسوباً
        if ($this->maxCertificatesCount == 0) {
            $trainees = Trainee::withTrashed()
                ->where('company_id', $this->companyId)
                ->with(['custom_certificates', 'uk_certificates' => function($q) {
                    $q->where('status', 'sent');
                }])
                ->get();
            $this->calculateMaxCertificatesCount($trainees);
        }

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

    public function styles(Worksheet $sheet)
    {
        // التأكد من حساب maxCertificatesCount إذا لم يكن محسوباً
        if ($this->maxCertificatesCount == 0) {
            $trainees = Trainee::withTrashed()
                ->where('company_id', $this->companyId)
                ->with(['custom_certificates', 'uk_certificates' => function($q) {
                    $q->where('status', 'sent');
                }])
                ->get();
            $this->calculateMaxCertificatesCount($trainees);
        }

        $totalColumns = 3 + $this->maxCertificatesCount; // 3 أعمدة أساسية + أعمدة الشهادات
        $lastColumn = $this->getColumnLetter($totalColumns);
        
        return [
            // Header row styling
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'], // لون أزرق للهيدر
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            // Content rows styling
            'A:' . $lastColumn => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'font' => [
                    'name' => 'Arial',
                    'size' => 11,
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // التأكد من حساب maxCertificatesCount إذا لم يكن محسوباً
                if ($this->maxCertificatesCount == 0) {
                    $trainees = Trainee::withTrashed()
                        ->where('company_id', $this->companyId)
                        ->with(['custom_certificates', 'uk_certificates' => function($q) {
                            $q->where('status', 'sent');
                        }])
                        ->get();
                    $this->calculateMaxCertificatesCount($trainees);
                }
                
                // حفظ حالة المتدربات إذا لم تكن محفوظة بالفعل
                if (empty($this->traineeStatuses)) {
                    $trainees = Trainee::withTrashed()
                        ->where('company_id', $this->companyId)
                        ->get();
                    foreach ($trainees as $trainee) {
                        $isDeletedOrSuspended = !is_null($trainee->deleted_at) || !is_null($trainee->suspended_at);
                        $this->traineeStatuses[$trainee->identity_number] = $isDeletedOrSuspended;
                    }
                }
                
                // Set RTL for Arabic
                $sheet->setRightToLeft(true);
                
                // Auto-size all columns
                $totalColumns = 3 + $this->maxCertificatesCount;
                for ($i = 1; $i <= $totalColumns; $i++) {
                    $columnLetter = $this->getColumnLetter($i);
                    $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
                }
                
                // Set row height for header
                $sheet->getRowDimension(1)->setRowHeight(25);
                
                // Wrap text for all cells
                $lastColumn = $this->getColumnLetter($totalColumns);
                $sheet->getStyle('A1:' . $lastColumn . $sheet->getHighestRow())
                    ->getAlignment()
                    ->setWrapText(true);
                
                // تطبيق تلوين خلفية أحمر فاتح على صفوف المتدربات المحذوفة أو الموقوفة
                $highestRow = $sheet->getHighestRow();
                for ($row = 2; $row <= $highestRow; $row++) {
                    // قراءة identity_number من العمود A
                    $identityNumber = $sheet->getCell('A' . $row)->getValue();
                    
                    // التحقق من وجود المتدرب في قائمة المحذوفين/الموقوفين
                    if (isset($this->traineeStatuses[$identityNumber]) && $this->traineeStatuses[$identityNumber]) {
                        // تطبيق تلوين خلفية أحمر فاتح على كامل الصف
                        $sheet->getStyle('A' . $row . ':' . $lastColumn . $row)
                            ->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('FFE6E6'); // لون أحمر فاتح
                    }
                }
            },
        ];
    }

    /**
     * Calculate maximum certificates count
     */
    protected function calculateMaxCertificatesCount($trainees)
    {
        $this->maxCertificatesCount = 0;
        foreach ($trainees as $trainee) {
            $count = $trainee->custom_certificates->count() + $trainee->uk_certificates->count();
            $this->maxCertificatesCount = max($this->maxCertificatesCount, $count);
        }
    }

    /**
     * Convert column number to letter (1 = A, 2 = B, etc.)
     */
    protected function getColumnLetter($columnNumber)
    {
        $letter = '';
        while ($columnNumber > 0) {
            $columnNumber--;
            $letter = chr(65 + ($columnNumber % 26)) . $letter;
            $columnNumber = intval($columnNumber / 26);
        }
        return $letter;
    }
}

