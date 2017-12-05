<?php

use App\Report;
use Illuminate\Database\Seeder;

class ReportsSeeder extends Seeder
{
    public function run()
    {
        Report::unguard();

        $now = date('Y-m-d H:i:s');

        //Insert Cost Reports
        \DB::table('reports')->insert([
            ['name' => '', 'description' => '', 'class_name' => 'App\\Reports\\CostControl\\', 'type' => 'Cost Control'],
            ['name' => '', 'description' => '', 'class_name' => 'App\\Reports\\CostControl\\', 'type' => 'Cost Control'],
            ['name' => '', 'description' => '', 'class_name' => 'App\\Reports\\CostControl\\', 'type' => 'Cost Control'],
            ['name' => '', 'description' => '', 'class_name' => 'App\\Reports\\CostControl\\', 'type' => 'Cost Control'],
            ['name' => '', 'description' => '', 'class_name' => 'App\\Reports\\CostControl\\', 'type' => 'Cost Control'],
            ['name' => '', 'description' => '', 'class_name' => 'App\\Reports\\CostControl\\', 'type' => 'Cost Control'],
            ['name' => '', 'description' => '', 'class_name' => 'App\\Reports\\CostControl\\', 'type' => 'Cost Control'],
            ['name' => '', 'description' => '', 'class_name' => 'App\\Reports\\CostControl\\', 'type' => 'Cost Control'],
            ['name' => '', 'description' => '', 'class_name' => 'App\\Reports\\CostControl\\', 'type' => 'Cost Control'],
        ]);

        //Insert Budget Reports
        \DB::table('reports')->insert([
            ['name' => '', 'description' => '', 'class_name' => 'App\\Reports\\Budget\\', 'type' => 'Budget', 'created_at' => $now, 'updated_at' => $now],
            ['name' => '', 'description' => '', 'class_name' => 'App\\Reports\\Budget\\', 'type' => 'Budget', 'created_at' => $now, 'updated_at' => $now],
            ['name' => '', 'description' => '', 'class_name' => 'App\\Reports\\Budget\\', 'type' => 'Budget', 'created_at' => $now, 'updated_at' => $now],
            ['name' => '', 'description' => '', 'class_name' => 'App\\Reports\\Budget\\', 'type' => 'Budget', 'created_at' => $now, 'updated_at' => $now],
            ['name' => '', 'description' => '', 'class_name' => 'App\\Reports\\Budget\\', 'type' => 'Budget', 'created_at' => $now, 'updated_at' => $now],
            ['name' => '', 'description' => '', 'class_name' => 'App\\Reports\\Budget\\', 'type' => 'Budget', 'created_at' => $now, 'updated_at' => $now],
            ['name' => '', 'description' => '', 'class_name' => 'App\\Reports\\Budget\\', 'type' => 'Budget', 'created_at' => $now, 'updated_at' => $now],
            ['name' => '', 'description' => '', 'class_name' => 'App\\Reports\\Budget\\', 'type' => 'Budget', 'created_at' => $now, 'updated_at' => $now],
            ['name' => '', 'description' => '', 'class_name' => 'App\\Reports\\Budget\\', 'type' => 'Budget', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
