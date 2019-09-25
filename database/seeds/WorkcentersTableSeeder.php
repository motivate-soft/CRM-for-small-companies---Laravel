<?php

use Illuminate\Database\Seeder;

class WorkcentersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Models\Workcenter::class, 40)->create();
    }
}
