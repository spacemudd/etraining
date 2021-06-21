<?php

namespace App\Imports;

use App\Models\Back\TraineeBlockList;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;

class TraineesBlocklistImport implements ToCollection
{
    /**
     * @param Collection $rows
     * @return \Illuminate\Support\Collection
     * @throws \Throwable
     */
    public function collection(Collection $rows)
    {
        DB::beginTransaction();
        foreach ($rows as $row) {
            if (!isset($row[5])) { // If there's no reason, just skip.
                continue;
            }

            if (isset($row[2]) && ! filter_var(($row[2]), FILTER_VALIDATE_EMAIL)) {
                Log::error('Failed validating for this user: '.json_encode($row));
                continue;
            }

            TraineeBlockList::create([
                'name' => $row[0],
                'identity_number' => $row[1],
                'email' => $row[2],
                'phone' => $row[3],
                'phone_additional' => $row[4],
                'reason' => $row[5],
            ]);
        }
        DB::commit();

        return $rows;
    }
}
