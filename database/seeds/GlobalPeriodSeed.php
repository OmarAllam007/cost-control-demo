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
    }
}
