<?php

namespace App\Console\Commands;

use App\BreakDownResourceShadow;
use App\MasterShadow;
use App\Resources;
use App\ResourceType;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
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

    /** @var  Collection */
    protected $cache;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->cache = collect();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Initiate cleaning resource types');
        $this->output->newLine();
        Resources::flushEventListeners();
        $bar = $this->output->createProgressBar(ResourceType::count());
        ResourceType::all()->each(function(ResourceType $type) use ($bar) {
            $type->name = trim($type->name);
            $type->save();
            $bar->advance();
        });
        $bar->finish();
        $this->output->newLine();
        $this->output->newLine();

        $this->info('Initiate cleaning resources');
        $this->output->newLine();
        $bar = $this->output->createProgressBar(Resources::count());
        Resources::all()->each(function(Resources $r) use ($bar) {
            $r->name = trim($r->name);
            $r->save();
            $bar->advance();
        });
        $bar->finish();
        $this->output->newLine();
        $this->output->newLine();

        $this->info('Removing duplicate resource types');
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
        $this->output->newLine(2);

        $this->info('Updating budget shadow');
        $this->output->newLine();
        BreakDownResourceShadow::flushEventListeners();
        $query = BreakDownResourceShadow::whereNotIn('resource_type_id', function($q) {
            $q->from('resource_types')->where('parent_id', 0)->select('id');
        })->with('resource');
        $bar = $this->output->createProgressBar($query->count());
        $resources = $query->get();
        foreach($resources as $resource) {
            $baseResource = $resource->resource;
            $resource->resource_type = $baseResource->types->root->name;
            $resource->resource_name = $baseResource->name;
            $resource->resource_type_id = $baseResource->types->root->id;
            $resource->save();
        }
        $bar->finish();
        $this->output->newLine();
        $this->output->newLine();

        $this->info('Updating master shadow');
        $this->output->newLine();
        MasterShadow::flushEventListeners();
        $query = MasterShadow::whereNotIn('resource_type_id', function($q) {
            $q->from('resource_types')->where('parent_id', 0)->select('id');
        })->with('resource');
        $bar = $this->output->createProgressBar($query->count());
        $resources = $query->get();
        foreach($resources as $resource) {
            $baseResource = $resource->resource;
            $resource->resource_name = $baseResource->name;
            $resource->resource_divs = $this->getResourceDivisions($baseResource);
            $resource->resource_type_id = $baseResource->types->root->id;
            $resource->save();
        }
        $bar->finish();
        $this->output->newLine(2);

        $this->output->success('Data cleaning done');
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

    protected function getResourceDivisions(Resources $resource)
    {
        if (isset($this->cache[$resource->id])) {
            return $this->cache[$resource->id];
        }

        $parent = $division = $resource->types;
        $divisions = [$division->name];
        while ($parent = $parent->parent) {
            $divisions[] = $parent->name;
        }

        return $this->cache[$resource->id] = json_encode(array_reverse($divisions));
    }
}
