<?php

namespace App;

use App\Behaviors\CachesQueries;
use App\Behaviors\HasChangeLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Behaviors\HasOptions;
use App\Behaviors\Tree;

class Boq extends Model
{
    use Tree, HasOptions, CachesQueries;
    use HasChangeLog;

    protected $fillable = [
        'wbs_id', 'item', 'description', 'type', 'unit_id', 'quantity', 'dry_ur', 'price_ur', 'arabic_description'
        , 'kcc_qty', 'subcon', 'materials', 'manpower', 'cost_account', 'item_code', 'division_id', 'project_id',
    ];

    protected $dates = ['created_at', 'updated_at'];

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    function wbs()
    {
        return $this->belongsTo(WbsLevel::class, 'wbs_id');
    }

    public function getDry($subcon, $material, $man_power)
    {
        return $subcon + $material + $man_power;
    }

    public function getAllQuantity($quantity)
    {
        return $quantity * 16;
    }

    public function getDryForBuilding($quantity, $dry)
    {
        return $dry * $quantity;
    }

    public function getPriceForBuilding($price, $quantity)
    {
        return $price * $quantity;
    }

    public function getDryForAllBuilding($quantity, $dry)
    {
        return $dry * $quantity * 16;
    }

    public function getPriceForAllBuilding($quantity, $price)
    {
        return $price * $quantity * 16;
    }

    function boq_levels()
    {
        return $this->belongsTo(BoqDivision::class,'division_id');
    }

    function project(){
        return $this->belongsTo(Project::class,'project_id');
    }

    public static function checkFixImport($data)
    {
        $errors = [];

        foreach ($data['units'] as $unit => $unit_id) {
            if (empty($unit_id)) {
                $errors['units.'.$unit] = $unit;
            }
        }

        foreach ($data['wbs'] as $wbs => $wbs_id) {
            if (empty($wbs_id)) {
                $errors['wbs.'.$wbs] = $wbs;
            }
        }

        return $errors;
    }

    function scopeCostAccountOnWbs(Builder $query, WbsLevel $wbs, $cost_account)
    {
        $wbs_parents = collect($wbs->id);
        $parent = $wbs;
        while ($parent->parent_id) {
            $wbs_parents->push($parent->parent_id);
            $parent = $parent->parent;
        }

        $query->whereIn('wbs_id', $wbs_parents)->where('cost_account', $cost_account);

        return $query;
    }

}