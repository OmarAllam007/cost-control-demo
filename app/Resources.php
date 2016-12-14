<?php

namespace App;

use App\Behaviors\CachesQueries;
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
            'type' => $this->types->name,
            'unit' => isset($this->units->type) ? $this->units->type : '',
            'rate' => $this->rate,
            'root_type' => $this->types->root->name,
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

    public function updateBreakdownResurces()
    {
        if ($this->project_id) {
            $breakdown_resources = BreakdownResource::whereHas('breakdown', function ($q) {
                $q->where('project_id', $this->project_id);
            })->where('resource_id', $this->resource_id)
                ->get();


            foreach ($breakdown_resources as $breakdown_resource) {
                $formatter = new BreakdownResourceFormatter($breakdown_resource);
                BreakDownResourceShadow::where('breakdown_resource_id', $breakdown_resource->id)
                    ->update($formatter->toArray());
            }

        }
    }

    protected static function boot()
    {
        parent::boot();
        static::updated(function ($resource) {
            $breakdown_resources = BreakdownResource::where('resource_id', $resource->id)->get();
            foreach ($breakdown_resources as $breakdown_resource) {
                $formatter = new BreakdownResourceFormatter($breakdown_resource);
                BreakDownResourceShadow::where('breakdown_resource_id', $breakdown_resource->id)
                    ->update($formatter->toArray());

            }
        });


    }

    function scopeMaterial(Builder $query)
    {
        $ids = ResourceType::where('parent_id', 0)
            ->where('name', 'like', '%material%')->first()
            ->getChildrenIds();

        return $query->whereIn('resource_type_id', $ids);
    }
}