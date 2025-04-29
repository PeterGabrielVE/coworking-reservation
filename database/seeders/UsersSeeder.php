<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        // Asegurar que el rol "admin" exista
        $role = Role::firstOrCreate(['name' => 'admin']);

        // Crear usuarios administradores
        $admins = [
            ['name' => 'Admin1', 'email' => 'admin1@example.com', 'password' => bcrypt('password')],
            ['name' => 'Admin2', 'email' => 'admin2@example.com', 'password' => bcrypt('password')],
        ];

        foreach ($admins as $admin) {
            $user = User::create($admin);
            $user->assignRole($role);
        }
    }
}
