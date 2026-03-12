<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;


class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role_id = Role::where('name', 'admin')->firstOrFail()->id;
        User::create([
            'role_id' => $role_id,
            'name' => 'admin',
            'email'=>  'admin@gmail.com',
            'password' => Hash::make('sR12345')
        ]);
    }
}
