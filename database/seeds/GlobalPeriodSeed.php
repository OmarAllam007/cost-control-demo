<?php

use Illuminate\Database\Seeder;

class GlobalPeriodSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\GlobalPeriod::truncate();

        \App\GlobalPeriod::create(['name' => 'November 2016', 'start_date' => '2016-11-01', 'end_date' => '2016-11-30']);
        \App\GlobalPeriod::create(['name' => 'December 2016', 'start_date' => '2016-12-01', 'end_date' => '2016-12-31']);
        \App\GlobalPeriod::create(['name' => 'January 2017', 'start_date' => '2017-01-01', 'end_date' => '2017-01-31']);
        \App\GlobalPeriod::create(['name' => 'February 2017', 'start_date' => '2017-02-01', 'end_date' => '2017-02-28']);
        \App\GlobalPeriod::create(['name' => 'March 2017', 'start_date' => '2017-03-01', 'end_date' => '2017-03-31']);
        \App\GlobalPeriod::create(['name' => 'April 2017', 'start_date' => '2017-04-01', 'end_date' => '2017-04-30']);
        \App\GlobalPeriod::create(['name' => 'May 2017', 'start_date' => '2017-05-01', 'end_date' => '2017-05-31']);
        \App\GlobalPeriod::create(['name' => 'June 2017', 'start_date' => '2017-06-01', 'end_date' => '2017-06-30']);
        \App\GlobalPeriod::create(['name' => 'July 2017', 'start_date' => '2017-07-01', 'end_date' => '2017-07-31']);
        \App\GlobalPeriod::create(['name' => 'August 2017', 'start_date' => '2017-08-01', 'end_date' => '2017-08-31']);
        \App\GlobalPeriod::create(['name' => 'September 2017', 'start_date' => '2017-09-01', 'end_date' => '2017-09-30']);
        \App\GlobalPeriod::create(['name' => 'October 2017', 'start_date' => '2017-10-01', 'end_date' => '2017-10-31']);
        \App\GlobalPeriod::create(['name' => 'November 2017', 'start_date' => '2017-11-01', 'end_date' => '2017-11-30']);
        \App\GlobalPeriod::create(['name' => 'December 2017', 'start_date' => '2017-12-01', 'end_date' => '2017-12-31']);

        \DB::update('UPDATE costcontrol.periods SET global_period_id = 1 WHERE id = 1');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 1 WHERE id = 2');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 2 WHERE id = 3');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 2 WHERE id = 5');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 1 WHERE id = 6');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 5 WHERE id = 7');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 5 WHERE id = 8');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 5 WHERE id = 9');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 1 WHERE id = 10');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 4 WHERE id = 11');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 5 WHERE id = 12');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 9 WHERE id = 13');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 6 WHERE id = 14');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 6 WHERE id = 15');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 6 WHERE id = 16');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 6 WHERE id = 18');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 6 WHERE id = 19');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 6 WHERE id = 20');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 9 WHERE id = 21');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 7 WHERE id = 22');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 8 WHERE id = 23');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 7 WHERE id = 24');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 7 WHERE id = 26');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 7 WHERE id = 27');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 7 WHERE id = 28');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 9 WHERE id = 29');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 9 WHERE id = 30');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 9 WHERE id = 31');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 9 WHERE id = 32');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 9 WHERE id = 33');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 10 WHERE id = 34');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 10 WHERE id = 35');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 9 WHERE id = 36');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 10 WHERE id = 37');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 9 WHERE id = 38');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 9 WHERE id = 39');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 9 WHERE id = 40');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 10 WHERE id = 41');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 10 WHERE id = 42');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 11 WHERE id = 43');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 10 WHERE id = 44');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 11 WHERE id = 45');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 11 WHERE id = 46');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 11 WHERE id = 47');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 11 WHERE id = 48');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 11 WHERE id = 49');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 11 WHERE id = 50');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 11 WHERE id = 51');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 12 WHERE id = 52');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 12 WHERE id = 53');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 12 WHERE id = 54');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 12 WHERE id = 55');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 13 WHERE id = 56');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 12 WHERE id = 57');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 13 WHERE id = 58');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 14 WHERE id = 59');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 12 WHERE id = 60');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 12 WHERE id = 61');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 13 WHERE id = 62');
        \DB::update('UPDATE costcontrol.periods SET global_period_id = 14 WHERE id = 63');
    }
}
