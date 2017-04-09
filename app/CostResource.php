<?php

namespace App;

use App\Behaviors\CachesQueries;
use Illuminate\Database\Eloquent\Model;

class CostResource extends Model
{
    use CachesQueries;

    protected $fillable = ['resource_id', 'period_id', 'rate', 'project_id'];

    protected $with = ['resource', 'resource.units', 'resource.types'];


    function resource()
    {
        return $this->belongsTo(Resources::class);
    }

    public function jsonFormat()
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'code' => $this->resource->resource_code,
            'type' => $this->resource->types->root->name,
            'type_id' => $this->resource->types->root->id,
            'rate' => $this->rate,
            'measure_unit' => $this->resource->units->type ?? '',
        ];
    }
}
