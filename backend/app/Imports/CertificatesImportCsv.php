<?php

namespace App\Imports;

use App\Models\Back\CertificatesImport;
use App\Models\Back\CertificatesImportsRow;
use App\Models\Back\Trainee;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class CertificatesImportCsv implements ToCollection
{
    public $import;

    public $failed_rows;

    public function __construct(CertificatesImport $import)
    {
        $this->import = $import;
    }

    /**
     * @param \Illuminate\Support\Collection $rows
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if ($row[0] === 'رقم الهوية') {
                continue;
            }

            if (($trainee = Trainee::withTrashed()->where('identity_number', $row[0])->first()) || ($trainee = Trainee::withTrashed()->where('identity_number', $this->arabic_numbers($row[0]))->first())) {
                $imported_row = new CertificatesImportsRow([
                    'trainee_id' => $trainee->id,
                    'course_id' => $this->import->course_id,
                ]);
                $this->import->rows()->save($imported_row);
            } else {
                $this->failed_rows[] = $row[0];
            }

            $this->import->processed_count++;
            $this->import->save();
        }

        $this->import->completed_at = now();
        $this->import->failed_rows = $this->failed_rows;
        $this->import->save();
    }

    public function arabic_numbers($input)
    {
        $numbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $arabic_numbers = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];

        return str_replace($numbers, $arabic_numbers, $input);
    }
}
