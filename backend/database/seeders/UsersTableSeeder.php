<?php

namespace Database\Seeders;

use App\Actions\Fortify\CreateNewUser;
use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = (new CreateNewUser())->create([
            'name' => 'Admin',
            'email' => 'admin@ptc-ksa.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);
    }
}
