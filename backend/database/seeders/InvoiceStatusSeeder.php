<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvoiceStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('invoice_status')->insert([
            'created_at' => now(),
            'updated_at' => now(),
            'label' => 'Unpaid',
            'status' => 0,
        ]);

        DB::table('invoice_status')->insert([
            'created_at' => now(),
            'updated_at' => now(),
            'label' => 'Paid',
            'status' => 1,
        ]);


        DB::table('invoice_status')->insert([
            'created_at' => now(),
            'updated_at' => now(),
            'label' => 'Audit required',
            'status' => 2,
        ]);

        DB::table('invoice_status')->insert([
            'created_at' => now(),
            'updated_at' => now(),
            'label' => 'Receipt rejected',
            'status' => 3,
        ]);

        DB::table('invoice_status')->insert([
            'created_at' => now(),
            'updated_at' => now(),
            'label' => 'Finance audit required',
            'status' => 4,
        ]);
    }
}
