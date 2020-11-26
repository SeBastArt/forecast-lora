<?php

use App\User;
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
        User::create([
            'username' => 'SebastArt',
            'name' => 'Sebastian Schüler',
            'email' => 'sebastian.schueler1@gmx.de',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', //password
            'roles' => ['ROLE_ADMIN'],
            'status' => 'active',
        ]);
        factory(App\User::class, 1)->create();
        factory(App\Company::class, 3)->create();
        factory(App\Facility::class, 10)->create();
        factory(App\Node::class, 20)->create();
        factory(App\Field::class, 40)->create();
        factory(App\City::class, 1)->create();
        factory(App\NodeData::class, 2000)->create();
        factory(App\FieldData::class, 2000)->create();
    }
}
