<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['name' => 'admin_utama', 'label' => 'Admin Utama'],
            ['name' => 'admin_biasa', 'label' => 'Admin Biasa'],
            ['name' => 'member', 'label' => 'Member'],
        ] as $role) {
            Role::firstOrCreate(['name' => $role['name']], ['label' => $role['label']]);
        }
    }
}
