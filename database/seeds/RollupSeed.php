<?php

use Illuminate\Database\Seeder;

class RollupSeed extends Seeder
{
    protected $now;
    public function run()
    {
        $this->now = date('Y-m-d H:i:s');

        rescue(function() {
            $insertId = \App\Resources::insertGetId([
                'id' => 0, 'name' => 'Rollup Resource', 'resource_code' => 'O.0',
                'resource_type_id' => 0, 'rate' => 0, 'waste' => 0, 'unit' => 3,
                'reference' => 0, 'business_partner_id' => 0,
                'created_at' => $this->now, 'updated_at' => $this->now, 'created_by' => 2, 'updated_by' => 2
            ]);

            dd(\Db::statement("UPDATE resources SET id = '0' WHERE id = $insertId"));
        });

        rescue(function() {
            $insertId = \App\BreakdownTemplate::insertGetId([
                'id' => 0, 'name' => 'Rollup template', 'std_activity_id' => 0, 'Code' => '0',
                'created_at' => $this->now, 'updated_at' => $this->now, 'created_by' => 2, 'updated_by' => 2
            ]);

            \Db::update('UPDATE breakdown_templates SET id = 0 WHERE id = ?', [$insertId]);
        });

        rescue(function() {
            $insertId = \App\StdActivityResource::insertGetId([
                'id' => 0, 'template_id' => 0, 'resource_id' => 0, 'equation' => '$v', 'remarks' => 'Rollup',
                'created_at' => $this->now, 'updated_at' => $this->now, 'created_by' => 2, 'updated_by' => 2
            ]);

            \Db::update('UPDATE std_activity_resources SET id = 0 WHERE id = ?', [$insertId]);
        });
    }
}
