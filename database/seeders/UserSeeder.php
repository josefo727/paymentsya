<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
		$user = User::factory()
			->create([
				'name' => 'Admin',
				'email' => 'josefo727@gmail.com'
			]);

        $rol = Role::query()->first()->name;

        $user->syncRoles([$rol]);
    }
}
