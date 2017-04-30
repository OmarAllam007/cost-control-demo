<?php

namespace App\Console\Commands;

use App\Resources;
use App\ResourceType;
use Illuminate\Console\Command;
use Make\Makers\Resource;

class CleanResourceTypes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean-rt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean resource types';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $mainTypeNames = ['equip', 'scaff', 'general', 'lab', 'mat', 'other', 'subcon'];

        foreach ($mainTypeNames as $typeName) {
            $types = ResourceType::parents()->where('name', 'like', "%$typeName%")->orderBy('id')->get();
            $first = $types->first();

            $typeIds = $types->pluck('id');

            ResourceType::whereIn('parent_id', $typeIds)->update(['parent_id' => $first->id]);
            Resources::whereIn('resource_type_id', $typeIds)->update(['resource_type_id' => $first->id]);

            ResourceType::whereIn('id', $typeIds)->where('id', '!=', $first->id)->delete();

            $this->cleanChildren($first);
        }

        /*$bar = $this->output->createProgressBar(ResourceType::count());
        ResourceType::all()->each(function(ResourceType $type) use ($bar) {
            $type->name = trim($type->name);
            $type->save();
            $bar->advance();
        });
        $bar->finish();

        $bar = $this->output->createProgressBar(Resources::count());
        Resources::all()->each(function(Resources $r) use ($bar) {
            $r->name = trim($r->name);
            $r->save();
            $bar->advance();
        });
        $bar->finish();*/

        // $this->output->success('Resources Count: ' .
        //     ResourceType::parents()->get()->keyBy('id')->map(function($type) { return Resources::whereIn('resource_type_id', $type->getChildrenIds())->count(); })->sum());
    }

    protected function cleanChildren($node)
    {
        $subtypes = $node->children()->groupBy('name')->selectRaw('name, count(*) as c')->having('c', '>', 1)->get();
        foreach ($subtypes as $subtype) {
            $types = $node->children()->where('name', $subtype->name)->orderBy('id')->get();
            $first = $types->first();
            $ids = $types->pluck('id');

            ResourceType::whereIn('parent_id', $ids)->update(['parent_id' => $first->id]);
            Resources::whereIn('resource_type_id', $ids)->get()->each(function(Resources $resource) use ($first) {
                $resource->update(['resource_type_id' => $first->id]);
            });

            ResourceType::whereIn('id', $ids)->where('id', '!=', $first->id)->delete();

            $this->cleanChildren($first);
        }
    }
}
