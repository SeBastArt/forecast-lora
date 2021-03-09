<?php

use App\Models\City;
use App\Models\Company;
use App\Models\Facility;
use App\Models\Field;
use App\Models\Node;
use App\Models\NodeData;
use App\Models\User;
use App\Models\Preset;
use Database\Factories\CompanyUserFactory;
use \Database\Seeders;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user1 = User::create([
            'username' => 'SebastArt',
            'name' => 'Sebastian Schüler',
            'email' => 'sebastian.schueler1@gmx.de',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', //password //Hash::make($input['password']) 
            'roles' => ['ROLE_ADMIN'],
            'status' => 'active',
            'language' => 1,
            'phone' => '+49 176 470 143 09',
            'address' => 'Stauffenbergallee 9a, Dresden',
            'country' => 'Germany'
        ]);
    }
}
