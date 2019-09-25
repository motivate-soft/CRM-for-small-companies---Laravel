<?php

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
        factory(App\User::class, 50)->create()->each(function ($u) {
            if($u->role == 'company') {
                $u->company()->save(factory(App\Models\Company::class)->make(['user_id' => $u->id]));
            }
            if($u->role == 'employee') {
                $u->employee()->save(factory(App\Models\Employee::class)->make(['user_id' => $u->id]));
            }
        });
    }
}
