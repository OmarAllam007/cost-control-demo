<?php

use Illuminate\Database\Seeder;

class ModulesSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Module::truncate();

        \App\Module::firstOrCreate(['name' => 'Resources']);
        \App\Module::firstOrCreate(['name' => 'Standard Activities']);
        \App\Module::firstOrCreate(['name' => 'Breakdown Templates']);
        \App\Module::firstOrCreate(['name' => 'Productivity']);
        \App\Module::firstOrCreate(['name' => 'Project']);
    }
}
