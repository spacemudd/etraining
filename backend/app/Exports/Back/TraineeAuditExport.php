<?php

namespace App\Exports\Back;

use App\Models\Back\Audit;
use App\Models\Back\Trainee;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TraineeAuditExport implements FromCollection, WithHeadings, WithMapping, WithEvents, WithStyles
{
    protected $userId;
    protected $field;
    protected $weeks;

    public function __construct($userId = null, $field = null, $weeks = 2)
    {
        $this->userId = $userId;
        $this->field = $field;
        $this->weeks = $weeks;
    }

    public function headings(): array
    {
        return [
            'الاسم',
            'الهوية',
            'القيمة القديمة',
            'القيمة الجديدة',
            'تاريخ التعديل',
        ];
    }

    public function map($audit): array
    {
        $trainee = Trainee::withTrashed()->find($audit->auditable_id);
        $traineeName = $trainee ? $trainee->name : 'غير متوفر';
        $traineeIdentity = $trainee ? $trainee->identity_number : 'غير متوفر';

        // Extract old and new values for the specific field
        $oldValue = 'غير متوفر';
        $newValue = 'غير متوفر';

        if ($this->field) {
            // Extract specific field value
            if ($audit->old_values) {
                $oldValues = is_string($audit->old_values) ? json_decode($audit->old_values, true) : $audit->old_values;
                $oldValue = isset($oldValues[$this->field]) ? (is_null($oldValues[$this->field]) ? 'NULL' : $oldValues[$this->field]) : 'غير متوفر';
            }
            if ($audit->new_values) {
                $newValues = is_string($audit->new_values) ? json_decode($audit->new_values, true) : $audit->new_values;
                $newValue = isset($newValues[$this->field]) ? (is_null($newValues[$this->field]) ? 'NULL' : $newValues[$this->field]) : 'غير متوفر';
            }
        } else {
            // If no specific field, show all changes
            if ($audit->old_values) {
                $oldValues = is_string($audit->old_values) ? json_decode($audit->old_values, true) : $audit->old_values;
                $oldValue = $oldValues ? json_encode($oldValues, JSON_UNESCAPED_UNICODE) : 'غير متوفر';
            }
            if ($audit->new_values) {
                $newValues = is_string($audit->new_values) ? json_decode($audit->new_values, true) : $audit->new_values;
                $newValue = $newValues ? json_encode($newValues, JSON_UNESCAPED_UNICODE) : 'غير متوفر';
            }
        }

        return [
            $traineeName,
            $traineeIdentity,
            $oldValue,
            $newValue,
            $audit->created_at ? $audit->created_at->format('Y-m-d H:i:s') : 'غير متوفر',
        ];
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        $query = Audit::where('auditable_type', 'App\Models\Back\Trainee')
            ->where('created_at', '>=', Carbon::now()->subWeeks($this->weeks))
            ->orderByDesc('created_at');

        if ($this->userId) {
            $query->where('user_id', $this->userId);
        }

        if ($this->field) {
            $query->whereRaw("JSON_EXTRACT(old_values, '$.\"$this->field\"') != JSON_EXTRACT(new_values, '$.\"$this->field\"')");
        }

        return $query->get(['id', 'event', 'old_values', 'new_values', 'created_at', 'auditable_id']);
    }

    public function registerEvents(): array
    {
        if (app()->getLocale() === 'ar') {
            return [
                AfterSheet::class => function(AfterSheet $event) {
                    $event->sheet->getDelegate()->setRightToLeft(true);
                },
            ];
        } else {
            return [
                AfterSheet::class => function(AfterSheet $event) {
                    $event->sheet->getDelegate()->setRightToLeft(false);
                },
            ];
        }
    }

    public function styles(Worksheet $sheet)
    {
        return [
            'A:Z' => [
                'font' => [
                    'name' => 'Arial',
                    'size' => 12,
                ]
            ],
        ];
    }
}

