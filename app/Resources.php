<?php

namespace App;

use App\Behaviors\HasOptions;
use App\Behaviors\Tree;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resources extends Model
{
    use SoftDeletes, HasOptions,Tree;


    protected $table = 'resources';
    protected $fillable = ['resource_code','name','rate','unit','waste','business_partner_id','resource_type_id','reference'];
    protected $dates = ['created_at', 'updated_at'];

    public function types()
    {
        return $this->belongsTo(ResourceType::class,'resource_type_id');

    }
    public function parteners()
    {
        return $this->belongsTo(BusinessPartner::class,'business_partner_id');
    }

    public function units(){
        return $this->belongsTo(Unit::class,'unit');
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

    function morphToJSON()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->types->name,
            'unit' => isset($this->units->type) ? $this->units->type : '',
            'rate' => $this->rate,
            'root_type' => $this->types->root->name
        ];
    }

    function scopeVersion(Builder $query, $project_id, $resource_id)
    {
        $resource = $query->where('resource_id', $resource_id)->where('project_id', $project_id);
    }
}