<?php
/**
 * Created by PhpStorm.
 * User: omar.garana
 * Date: 11/1/2016
 * Time: 9:16 AM
 */

namespace App\Jobs;


use App\CsiCategory;
use App\Productivity;

class CacheCsiCategoryTree extends Job
{
    public function handle()
    {
        $csi_categories = CsiCategory::tree()->get()->sortBy('name');
        foreach ($csi_categories as $category) {
            $levelTree = $this->buildTree($category);
            $tree[] = $levelTree;
        }
        return $tree;
    }

    protected function buildTree(CsiCategory $category)
    {
        $tree = ['id'=>$category->id,'name'=>$category->name,'children'=>[],'productivities'=>[]];
        if($category->children->count())
        {
            $tree['children'] = $category->children->map(function(CsiCategory $childLevel){
                return $this->buildTree($childLevel);
            });
        }
        if($category->productivity->count())
        {
            $tree['productivities'] = $category->productivity->map(function (Productivity $productivity){
                return ['id'=>$productivity->id,'description'=>$productivity->description,'csi_code'=>$productivity->csi_code,'crew_structure'=>$productivity->crew_structure,
                'unit'=>$productivity->units->type,
                    'daily_output'=>$productivity->daily_output,
                ];
            });
        }
        return $tree;
    }
}