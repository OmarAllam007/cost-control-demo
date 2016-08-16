<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResourceType extends Model
{
    protected $fillable = ['name','parent_id','resource_id'];

    protected $dates = ['created_at', 'updated_at'];

    public function resources()
    {
       return $this->hasMany('App\Resources','id','resource_id');
    }

    public function getParent($id)
    {
        $parent_id = ResourceType::where('id',$id)->value('parent_id');
        $parent_name = $this->where('id',$parent_id)->value('name');

        return $parent_name;
    }
    public function getDivision($id)
    {
        $child_id = ResourceType::where('parent_id',$id)->value('id');

        $child_name = $this->where('id',$child_id)->value('name');

        return $child_name;
    }
    public function getDivisions($id)
    {
        $id = ResourceType::where('parent_id',$id)->value('id');

        return $this->getDivision($id);
    }



}