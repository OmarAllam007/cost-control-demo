<?php

namespace App;

use App\Behaviors\HasOptions;
use App\Behaviors\Tree;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Productivity extends Model
{
    use SoftDeletes, Tree, HasOptions;

    protected $fillable = ['csi_category_id', 'description',
        'unit', 'crew_structure', 'crew_hours', 'crew_equip', 'daily_output',
        'man_hours', 'equip_hours', 'reduction_factor', 'after_reduction', 'source', 'code'];

    protected $dates = ['created_at', 'updated_at'];

    public static function options()
    {
        return static::orderBy('code')->pluck('code', 'id')->prepend('Select Reference', '');
    }

    public function category()
    {
        return $this->belongsTo(CsiCategory::class, 'csi_category_id');
    }

    public function units()
    {
        return $this->belongsTo(Unit::class, 'unit');
    }

    public function productivityAfterReduction()
    {

        return $this->after_reduction = (1 - $this->reduction_factor) * $this->daily_output;
    }

    public function getAfterReductionAttribute()
    {

        return $this->daily_output * $this->reduction_factor;
    }

    function scopeFilter(Builder $query, $term = '')
    {
        $query->take(20)
            ->orderBy('code');

        if (trim($term)) {
            $query->where('code', 'like', "%{$term}%");
        }
    }


    function morphToJSON()
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'daily_output' => $this->daily_output,
            'reduction' => $this->reduction_factor,
            'after_reduction' => $this->after_reduction
        ];
    }

    function scopeVersion(Builder $query, $project_id, $productivity)
    {
        $query->where('productivity_id', $productivity)
            ->where('project_id', $project_id);
    }

    function scopeBasic(Builder $query)
    {
        $query->where(function(Builder $query) {
           $query->where('project_id', 0)
               ->orWhereNull('project_id');
        });
    }
}