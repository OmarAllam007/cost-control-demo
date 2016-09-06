<?php

namespace App;

use App\Behaviors\HasOptions;
use App\Behaviors\Tree;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Productivity extends Model
{
    use Tree, HasOptions;
    protected $path = [];
    protected $fillable = ['csi_category_id',
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

//    public function divisionParent($id = false)
//    {
//        if (!$id) {
//            $this->path = [];
//            $id = $this->csi_category_id;
//        }
//
//        $div = CsiCategory::find($id);
//        $this->path[] = $div->name;
//
//        if ($div->parent_id != 0) {
//            $this->divisionParent($div->parent_id);
//        }
//        return implode('/',$this->path);
//    }

    public function productivityAfterReduction()
    {

        return $this->after_reduction = (1 - $this->reduction_factor) * $this->daily_output;
    }

    public function getAfterReductionAttribute()
    {

        return $this->daily_output * (1 - $this->reduction_factor);
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
}