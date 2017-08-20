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
        Resources::flushEventListeners();
        MasterShadow::flushEventListeners();
        BreakDownResourceShadow::flushEventListeners();

        $this->cleanTypeName();
        $this->cleanResourceName();

        $this->removeDuplicates();

        $this->updateBudgetShadow();

        $this->updateMasterShadow();

        $this->output->success('Data cleaning done');
    }

    protected function cleanChildren($node)
    {
        $subtypes = $node->children()->groupBy('name')->selectRaw('name, count(*) as c')->get();
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

    protected function cleanTypeName()
    {
        $this->info('Initiate cleaning resource types');
        $this->output->newLine();

        $bar = $this->output->createProgressBar(ResourceType::count());
        ResourceType::all()->each(function (ResourceType $type) use ($bar) {
            $name = trim(preg_replace('/^[^[:print:]]+/', '+', $type->name));
            $name = trim(preg_replace('/[^[:print:]]+$/', '+', $name));
            $type->name = trim($name, " +\t\n\r\0\x0B");
            $type->save();

            $bar->advance();
        });
        $bar->finish();
        $this->output->newLine();
        $this->output->newLine();
    }

    /**
     * @return \Symfony\Component\Console\Helper\ProgressBar
     */
    protected function cleanResourceName()
    {
        $this->info('Initiate cleaning resources');
        $this->output->newLine();
        $bar = $this->output->createProgressBar(Resources::count());
        Resources::all()->each(function (Resources $r) use ($bar) {
            $r->name = trim(preg_replace('/[^[:print:]]/u', '', $r->name));
            $r->save();
            $bar->advance();
        });
        $bar->finish();
        $this->output->newLine(2);
    }

    protected function updateMasterShadow()
    {
        $this->info('Updating master shadow');
        $this->output->newLine();

        $query = MasterShadow::whereNotIn('resource_type_id', function ($q) {
            $q->from('resource_types')->where('parent_id', 0)->select('id');
        })->with('resource');
        $bar = $this->output->createProgressBar($query->count());
        $resources = $query->get();
        foreach ($resources as $resource) {
            $baseResource = $resource->resource;
            $resource->resource_name = $baseResource->name;
            $resource->resource_divs = $this->getResourceDivisions($baseResource);
            $resource->resource_type_id = $baseResource->types->root->id;
            $resource->save();
            $bar->advance();
        }
        $bar->finish();
        $this->output->newLine(2);
    }

    protected function updateBudgetShadow()
    {
        $this->info('Updating budget shadow');
        $this->output->newLine();

        $query = BreakDownResourceShadow::whereNotIn('resource_type_id', function ($q) {
            $q->from('resource_types')->where('parent_id', 0)->select('id');
        })->with('resource');
        $bar = $this->output->createProgressBar($query->count());
        $resources = $query->get();
        foreach ($resources as $resource) {
            $baseResource = $resource->resource;
            $resource->resource_type = $baseResource->types->root->name;
            $resource->resource_name = $baseResource->name;
            $resource->resource_type_id = $baseResource->types->root->id;
            $resource->save();
            $bar->advance();
        }
        $bar->finish();
        $this->output->newLine();
        $this->output->newLine();
    }

    protected function removeDuplicates()
    {
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
    }
}
