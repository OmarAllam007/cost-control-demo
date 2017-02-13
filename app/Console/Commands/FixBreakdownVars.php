<?php

namespace App\Console\Commands;

use App\Breakdown;
use App\BreakdownVariable;
use Carbon\Carbon;
use Illuminate\Console\Command;

class FixBreakdownVars extends Command
{
    protected $signature = 'fix-vars';

    protected $description = 'Add breakdown variables according to std activity variables';

    public function handle()
    {
        $breakdowns = Breakdown::with('std_activity.variables')->whereRaw('std_activity_id in (select std_activity_id from std_activity_variables)')
            ->whereRaw('id not in (select breakdown_id from breakdown_variables)')
            ->whereRaw('project_id in (select id from projects where deleted_at is null)')->get();

        $bar = $this->output->createProgressBar($breakdowns->count());
        $bar->setBarWidth(50);
        $now = Carbon::now();
        BreakdownVariable::unguard();
        $breakdowns->each(function(Breakdown $breakdown) use ($bar, $now) {
            if (!$breakdown->qty_survey || $breakdown->qty_survey->variables()->exists()) {
                $bar->advance();
                return true;
            }

            $variables = $breakdown->std_activity->variables;
            $breakdownVariables = [];
            foreach ($variables as $variable) {
                $breakdownVariables[] = [
                    'breakdown_id' => $breakdown->id,
                    'qty_survey_id' => $breakdown->qty_survey->id,
                    'name' => $variable->label,
                    'value' => 0,
                    'display_order' => $variable->display_order,
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            }

            BreakdownVariable::insert($breakdownVariables);
            $bar->advance();
        });

        $bar->finish();
    }
}
