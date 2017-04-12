<?php

namespace App;

use App\Behaviors\CachesQueries;
use App\Behaviors\HasChangeLog;
use App\Behaviors\HasOptions;
use App\Behaviors\Overridable;
use App\Behaviors\Tree;
use App\Formatters\BreakdownResourceFormatter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resources extends Model
{
    use SoftDeletes, HasOptions, Tree, Overridable;
    use HasChangeLog;

    protected $table = 'resources';

    protected $fillable = [
        'resource_code', 'name', 'rate', 'unit', 'waste', 'business_partner_id', 'resource_type_id', 'reference', 'project_id', 'resource_id'
    ];

    protected $dates = ['created_at', 'updated_at'];

    public function types()
    {
        return $this->belongsTo(ResourceType::class, 'resource_type_id');
    }

    function codes()
    {
        return $this->hasMany(ResourceCode::class, 'resource_id');
    }

    function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function parteners()
    {
        return $this->belongsTo(BusinessPartner::class, 'business_partner_id');
    }

    public function units()
    {
        return $this->belongsTo(Unit::class, 'unit');
    }

    function breakdown_resource()
    {
        return $this->belongsToMany(BreakdownResource::class);
    }

    function breakdown_shadow()
    {
        return $this->belongsToMany(BreakDownResourceShadow::class);
    }

    function scopeFilter(Builder $query, $term = '')
    {
        $query->with(['units', 'types'])
            ->take(20)
            ->orderBy('name');

        if (trim($term)) {
            $query->where('name', 'like', "%{$term}%");
        }
    }

    function scopeBasic(Builder $query)
    {
    }

    function morphToJSON()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->types->name ?? '',
            'unit' => $this->units->type ?? '',
            'rate' => $this->rate,
            'root_type' => $this->types? $this->types->root->name : '',
            'resource_type_id' => $this->types?  $this->types->root->id : 0,
            'waste' => $this->waste,
            'resource_code' => $this->resource_code,
        ];
    }

    public static function checkFixImport($data)
    {
        $errors = [];

        foreach ($data['units'] as $unit => $unit_id) {
            if (!$unit_id) {
                $errors[$unit] = $unit;
            }
        }

        return $errors;
    }

    function syncCodes($codes)
    {
        $codeIds = [0];

        if ($codes) {
            foreach ($codes as $code) {
                $dbCode = $this->codes()->updateOrCreate($code);
                $codeIds[] = $dbCode->id;
            }
        }

        $this->codes()->whereNotIn('id', $codeIds)->delete();
    }

    public function updateBreakdownResources()
    {
        if ($this->project_id) {
            $breakdown_resources = BreakdownResource::whereHas('breakdown', function ($q) {
                $q->where('project_id', $this->project_id);
            })->where('resource_id', $this->id)->get();

            /** @var BreakdownResource $breakdown_resource */
            foreach ($breakdown_resources as $breakdown_resource) {
                $breakdown_resource->resource_waste = $this->waste;
                if ($breakdown_resource->isDirty()) {
                    $breakdown_resource->update();
                } else {
                    $breakdown_resource->updateShadow();
                }
            }
        }
    }

    function scopeMaterial(Builder $query)
    {
        $ids = ResourceType::where('parent_id', 0)
            ->where('name', 'like', '%material%')->first()
            ->getChildrenIds();

        return $query->whereIn('resource_type_id', $ids);
    }

    public function generateResourceCode()
    {
        $rootName = substr($this->types->root->name, strpos($this->types->root->name, '.') + 1, 1);

        $names = explode('»', $this->types->path);
        $code = [];
        $code [] = $rootName;
        //if Labors get by letter else by number
        if ($rootName != 'L') {
            foreach ($names as $key => $name) {
                if ($key == 0) {
                    continue;
                }

                $name = trim($name);
                $divname = substr($name, 0, strpos($this->types->root->name, '.'));
                $code [] = $divname;

            }
        } else {
            foreach ($names as $key => $name) {
                if ($key == 0) {
                    continue;
                }
                $name = trim($name);
                $divname = substr($name, strpos($this->types->root->name, '.') + 1, 1);
                $code [] = $divname;

            }
        }

        $resourceNumber = Resources::where('resource_type_id', $this->types->id)->count();
        $resourceNumber++;
        $code[] = $resourceNumber <= 10 ? '0' . $resourceNumber : $resourceNumber;
        $finalCode = implode('.', $code);

        $this->resource_code = $finalCode;
    }

    function scopeForProject(Builder $query, Project $project)
    {
        $query->where('project_id', $project->id);
    }

}