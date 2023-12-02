<?php

namespace Database\Seeders;

use App\Services\RolesService;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function run()
    {
        $service = app()->make(RolesService::class);

        $service->seedPermissions();
    }
}
