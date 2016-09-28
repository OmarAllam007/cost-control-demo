<?php

namespace App;

use App\Behaviors\HasOptions;
use App\Behaviors\Overridable;
use App\Behaviors\Tree;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Productivity extends Model
{
    use SoftDeletes, Tree, HasOptions, Overridable;

    protected $fillable = [
        'csi_category_id',
        'description',
        'unit',
        'crew_structure',
        'crew_hours',
        'crew_equip',
        'daily_output',
        'man_hours',
        'equip_hours',
        'reduction_factor',
        'after_reduction',
        'source',
        'code'
    ];

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
        return $this->after_reduction;
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
            'after_reduction' => $this->after_reduction,
        ];
    }

    public function getCrewManAttribute($crew_structure)
    {
        $man_powers = array_map('strtolower',
            array_column(ProductivityList::where('name', '=', 'Manpower')->get(array('type'))->toArray(), 'type'));
        $man_numbers = [];
        $lines = explode("\n", strtolower($crew_structure));
        foreach ($lines as $line) {
            $tokens = [];
            preg_match('/([\d.]+)\s?(.*)/', $line, $tokens);
            $key = trim($tokens[2]);
            $man_number = trim($tokens[1]);
            if (array_search($key, $man_powers)) {//if(array_search($key,$crew) !== false){
                $man_numbers[] = $man_number * 10;
            }
        }

        return array_sum($man_numbers);
    }

    public function getCrewEquipAttribute($crew_structure)
    {
        $equip_powers = array_map('strtolower',
            array_column(ProductivityList::where('name', '=', 'Equipment')->get(array('type'))->toArray(), 'type'));
        $equip_numbers = [];
        $lines = explode("\n", strtolower($crew_structure));
        foreach ($lines as $line) {
            $tokens = [];
            preg_match('/([\d.]+)\s?(.*)/', $line, $tokens);
            $key = trim($tokens[2]);
            $number = trim($tokens[1]);
            if (array_search($key, $equip_powers)) {//if(array_search($key,$crew) !== false){
                $equip_numbers[] = $number * 10;
            }
        }
        return array_sum($equip_numbers);
    }

    public function getManHoursAttribute()
    {
        return round(($this->getCrewManAttribute($this->crew_structure) / $this->daily_output), 2);
    }

    public function getEquipHoursAttribute()
    {
        return round(($this->getCrewEquipAttribute($this->crew_structure) / $this->daily_output), 2);
    }
}