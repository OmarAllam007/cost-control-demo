<?php
namespace App\Support;

use App\Behaviors\CostAttributes;
use App\CostShadow;

class CostShadowCalculator
{
    /** @var CostShadow */
    protected $costShadow;

    /** @var array */
    protected $calculated = [];

    /** @var array */
    protected $appends = [];

    use CostAttributes;

    function __construct(CostShadow $costShadow, $keepRemaining = true)
    {
        $this->costShadow = $costShadow;
        $this->setCalculated($keepRemaining);
    }

    function update()
    {
        $this->costShadow->fill($this->toArray());
        $this->costShadow->save();

        return $this->costShadow;
    }

    protected function setCalculated($keepRemaining)
    {
        $budgetAttributes = $this->costShadow->budget->getAttributes();
        $merged = [];
        if ($keepRemaining) {
            $merged = [
                'remaining_qty' => $this->costShadow->remaining_qty,
                'remaining_unit_price' => $this->costShadow->remaining_unit_price,
                'allowable_ev_cost' => $this->costShadow->allowable_ev_cost,
            ];
        }
        $this->calculated = array_merge($budgetAttributes, $merged);
    }

    public function toArray()
    {
        $attributes = [];
        $fields = $this->appendFields();
        foreach ($fields as $field) {
            $attributes[$field] = $this->getAttribute($field);
        }

        return $attributes;
    }

    function __get($name)
    {
        return $this->getAttribute($name);
    }

    protected function getAttribute($name)
    {
        if (isset($this->calculated[$name])) {
            return $this->calculated[$name];
        }

        $methodName = 'get' . studly_case($name) . 'Attribute';
        if (method_exists($this, $methodName)) {
            return call_user_func([$this, $methodName]);
        }

        return $this->costShadow->getAttribute($name);
    }
}