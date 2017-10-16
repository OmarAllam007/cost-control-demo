<?php

namespace App\Console\Commands;

use App\BreakDownResourceShadow;
use App\Productivity;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class FixProjectProductivity extends Command
{
    protected $signature = 'fix-project-productivity';

    protected $description = 'Fix project productivity';
    /** @var Collection */
    protected $productivityCache;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $query = BreakDownResourceShadow::whereNotNull('productivity_id')->where('productivity_id', '!=', 0)->with('productivity');
        $this->bar = $this->output->createProgressBar($query->count());


        $query->chunk(10000, function($shadows) {
            \DB::beginTransaction();

            $shadows->each(function ($shadow) {
                if (!$shadow->productivity->project_id) {
                    $projectProductivity = Productivity::where('productivity_id', $shadow->productivity->id)->where('project_id', $shadow->project_id)->first();
                    if (!$projectProductivity) {
                        $attributes = $shadow->productivity->getAttributes();
                        unset($attributes['id']);
                        $attributes['project_id'] = $shadow->project_id;
                        $attributes['productivity_id'] = $shadow->productivity->id;
                        $attributes['created_at'] = $shadow->created_at->format('Y-m-d H:i:s');
                        $attributes['updated_at'] = $shadow->updated_at->format('Y-m-d H:i:s');
                        $attributes['created_by'] = $shadow->created_by;
                        $attributes['updated_by'] = $shadow->updated_by;

                        $newId = Productivity::insertGetId($attributes);

                        $shadow->productivity_id = $newId;
                        $shadow->save();
                    } else {
                        $shadow->productivity_id = $projectProductivity->id;
                        $shadow->save();
                    }
                }
                $this->bar->advance();
            });

            \DB::commit();
        });





//        $ids = collect(\DB::select('select DISTINCT sh.productivity_id from break_down_resource_shadows sh join productivities p on (sh.productivity_id = p.id) where p.project_id is null'));
//
//        $bar = $this->output->createProgressBar($ids->count());
//        Productivity::find($ids->pluck('productivity_id')->toArray())->each(function($prod) use ($bar) {
//            $attributes = $prod->getAttributes();
//            unset($attributes['id'], $attributes['created_at'], $attributes['updated_at']);
//            $attributes['productivity_id'] = $prod->id;
//
//            $project_ids = BreakDownResourceShadow::where('productivity_id', $prod->id)->selectRaw('distinct project_id')->get()->pluck('project_id');
//
//            foreach ($project_ids as $project_id) {
//                $new_prod = Productivity::where(['project_id' => $project_id, 'productivity_id' => $prod->id])->first();
//                if (!$new_prod) {
//                    $attributes['project_id'] = $project_id;
//                    $new_prod = Productivity::create($attributes);
//                }
//
//                BreakDownResourceShadow::where(['project_id' => $project_id, 'productivity_id' => $prod->id])
//                    ->update(['productivity_id' => $new_prod->id]);
//            }
//
//            $bar->advance();
//        });
//
//        $bar->finish();
    }
}
