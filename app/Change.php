<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Change
 * @property-read array original_data
 */
class Change extends Model
{
    const MODEL_ALIASES = [
        'break_down_resource_shadow' => 'Breakdown Resource',
        'breakdown_resource' => 'Breakdown Resource',
        'survey' => 'Qty Survey',
        'boq' => 'BOQ',
        'std_activity_resource' => 'Template resource'
    ];

    protected $fillable = ['model', 'original', 'updated', 'model_id'];

    protected $casts = ['updated' => 'array', 'original' => 'array'];

    function subject()
    {
        return $this->morphTo('subject', 'model', 'model_id');
    }

    function hasChangedFields()
    {
//        dd(array_filter($this->updated), array_filter($this->original_data));
        return array_filter($this->updated) || array_filter($this->original_data);
    }

    function getSimpleModelNameAttribute()
    {
        $modelName = str_replace('App\\', '', $this->model);
        $lowerModel = snake_case($modelName);
        if (isset(self::MODEL_ALIASES[$lowerModel])) {
            return self::MODEL_ALIASES[$lowerModel];
        }

        return $modelName;
    }

    function getOriginalDataAttribute()
    {
        return $this->getAttribute('original');
    }

    function getDescriptorAttribute()
    {
        if (isset($this->original_data['descriptor'])) {
            return $this->original_data['descriptor'];
        }

        return $this->subject->descriptor ?? '';
    }
}
