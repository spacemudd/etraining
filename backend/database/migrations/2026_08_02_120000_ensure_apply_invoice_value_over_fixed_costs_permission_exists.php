<?php

declare(strict_types=1);

use App\Models\Permission;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Permission::firstOrCreate([
            'name' => 'apply-invoice-value-over-fixed-costs',
            'guard_name' => 'web',
        ]);
    }

    public function down(): void
    {
        // Permission may be assigned to roles; do not delete on rollback.
    }
};
