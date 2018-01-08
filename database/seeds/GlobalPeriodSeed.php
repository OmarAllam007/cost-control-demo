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

        \Db::update('UPDATE costcontrol.periods SET global_period_id = 1 WHERE id = 1');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 1 WHERE id = 2');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 2 WHERE id = 3');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 2 WHERE id = 5');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 1 WHERE id = 6');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 5 WHERE id = 7');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 5 WHERE id = 8');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 5 WHERE id = 9');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 1 WHERE id = 10');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 4 WHERE id = 11');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 5 WHERE id = 12');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 9 WHERE id = 13');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 6 WHERE id = 14');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 6 WHERE id = 15');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 6 WHERE id = 16');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 6 WHERE id = 18');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 6 WHERE id = 19');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 6 WHERE id = 20');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 9 WHERE id = 21');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 7 WHERE id = 22');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 8 WHERE id = 23');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 7 WHERE id = 24');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 7 WHERE id = 26');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 7 WHERE id = 27');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 7 WHERE id = 28');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 9 WHERE id = 29');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 9 WHERE id = 30');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 9 WHERE id = 31');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 9 WHERE id = 32');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 9 WHERE id = 33');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 10 WHERE id = 34');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 10 WHERE id = 35');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 9 WHERE id = 36');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 10 WHERE id = 37');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 9 WHERE id = 38');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 9 WHERE id = 39');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 9 WHERE id = 40');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 10 WHERE id = 41');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 10 WHERE id = 42');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 11 WHERE id = 43');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 10 WHERE id = 44');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 11 WHERE id = 45');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 11 WHERE id = 46');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 11 WHERE id = 47');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 11 WHERE id = 48');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 11 WHERE id = 49');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 11 WHERE id = 50');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 11 WHERE id = 51');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 12 WHERE id = 52');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 12 WHERE id = 53');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 12 WHERE id = 54');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 12 WHERE id = 55');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 13 WHERE id = 56');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 12 WHERE id = 57');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 13 WHERE id = 58');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 14 WHERE id = 59');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 12 WHERE id = 60');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 12 WHERE id = 61');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 13 WHERE id = 62');
        \Db::update('UPDATE costcontrol.periods SET global_period_id = 14 WHERE id = 63');
    }
}
