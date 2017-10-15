<?php

namespace App\Console\Commands;

use App\BreakDownResourceShadow;
use App\Productivity;
use Illuminate\Console\Command;

class FixProjectProductivity extends Command
{
    protected $signature = 'fix-project-productivity';

    protected $description = 'Fix project productivity';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $ids = collect(\DB::select('select DISTINCT sh.productivity_id from break_down_resource_shadows sh join productivities p on (sh.productivity_id = p.id) where p.project_id is null'));

        $bar = $this->output->createProgressBar($ids->count());
        Productivity::find($ids->pluck('productivity_id')->toArray())->each(function($prod) use ($bar) {
            $attributes = $prod->getAttributes();
            unset($attributes['id'], $attributes['created_at'], $attributes['updated_at']);
            $attributes['productivity_id'] = $prod->id;

            $project_ids = BreakDownResourceShadow::where('productivity_id', $prod->id)->selectRaw('distinct project_id')->get()->pluck('project_id');

            foreach ($project_ids as $project_id) {
                $new_prod = Productivity::where(['project_id' => $project_id, 'productivity_id' => $prod->id])->first();
                if (!$new_prod) {
                    $attributes['project_id'] = $project_id;
                    $new_prod = Productivity::create($attributes);
                }

                BreakDownResourceShadow::where(['project_id' => $project_id, 'productivity_id' => $prod->id])
                    ->update(['productivity_id' => $new_prod->id]);
            }

            $bar->advance();
        });

        $bar->finish();
    }
}
