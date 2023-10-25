<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::query()->create(['guard_name' => 'web', 'name' => 'Super Admin']);
        Role::query()->create(['guard_name' => 'web', 'name' => 'Admin']);
        Role::query()->create(['guard_name' => 'web', 'name' => 'Client']);
    }
}
