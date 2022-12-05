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

            //if ($trainee = Trainee::withTrashed()->where('identity_number', $row[0])->first()) {
            if ($trainee = Trainee::withTrashed()->where('name', $row[1])->first()) {
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
}
