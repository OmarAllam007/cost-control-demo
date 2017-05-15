<?php

namespace App;

use App\Behaviors\CachesQueries;
use App\Behaviors\HasChangeLog;
use App\Behaviors\HasOptions;
use App\Behaviors\Overridable;
use App\Behaviors\Tree;
use App\Jobs\CacheCsiCategoryTree;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Productivity extends Model
{
    use SoftDeletes, Tree, HasOptions, Overridable;
    use HasChangeLog;

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
        'code',
        'csi_code',
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
            'code' => $this->csi_code,
            'daily_output' => $this->daily_output,
            'reduction' => $this->reduction_factor,
            'after_reduction' => $this->after_reduction,
        ];
    }

    function getJson(Project $project)
    {
//        $projectProductivities = Productivity::where('project_id', $project->id)->get()->keyBy('productivity_id');
        return [
                'id' => $this->id,
                'code' => $this->csi_code,
                'description' => $this->description,
                'crew_structure' => $this->crew_structure,
                'after_reduction' => $this->after_reduction,
                'daily_output' => $this->daily_output,
                'unit' => $this->units->type,
            ];
    }

    public function getManHoursAttribute()
    {
        if (!$this->after_reduction) {
            return 0;
        }

        return round(($this->getCrewManAttribute($this->crew_structure) / $this->after_reduction), 2);
    }

    public function getCrewManAttribute()
    {
        $lines = array_filter(explode("\n", strtolower($this->crew_structure)));

        $man_numbers = [];
        foreach ($lines as $line) {
            $tokens = [];
            preg_match('/([\d.]+)\s?(.*)/', $line, $tokens);
            $key = strtolower(trim($tokens[2]));
            $man_number = trim($tokens[1]);
            $resource = Resources::where('name', 'like', "%$key%")->first();
            if (!$resource) {
                continue;
            }
            $type = $resource->types->root->name;
            $is_labour = stristr($type, '02.LABORS');

            if ($is_labour) {//if(array_search($key,$crew) !== false){
                $man_numbers[] = $man_number * 10;
            }
        }

        return array_sum($man_numbers);
    }

    public function getAfterReductionAttribute()
    {
        return $this->daily_output * $this->reduction_factor;
    }

    public function getEquipHoursAttribute()
    {
        if (!$this->after_reduction) {
            return 0;
        }

        return round(($this->getCrewEquipAttribute($this->crew_structure) / $this->after_reduction), 2);
    }

    public function getCrewEquipAttribute()
    {
        $equip_powers = array_map('strtolower',
            array_column(ProductivityList::where('name', '=', 'Equipment')->get(array('type'))->toArray(), 'type'));
        $equip_numbers = [];
        $lines = preg_split("(\r\n|\n)", strtolower($this->crew_structure));
        foreach ($lines as $line) {
            $tokens = [];
            preg_match('/([\d.]+)\s?(.*)/', $line, $tokens);
            if (empty($tokens)) {
                continue;
            }
            $key = trim($tokens[2]);
            $number = trim($tokens[1]);
            if (array_search($key, $equip_powers)) {//if(array_search($key,$crew) !== false){
                $equip_numbers[] = $number * 10;
            }
        }
        return array_sum($equip_numbers);
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

    public static function boot()
    {
        parent::boot();

        static::created(function () {
            \Cache::forget('csi-tree');
            \Cache::remember('csi-tree', 7 * 24 * 60, function () {
                return dispatch(new CacheCsiCategoryTree());
            });
        });
    }
}