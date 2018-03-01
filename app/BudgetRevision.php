<?php

namespace App;

use App\Behaviors\RecordsUser;
use App\Jobs\CreateRevisionForProject;
use App\Revision\RevisionBoq;
use App\Revision\RevisionBreakdownResourceShadow;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;

/**
 * @property mixed project_id
 * @property Project project
 */
class BudgetRevision extends Model
{
    use RecordsUser;

    protected $appends = ['url', 'user', 'created_date'];

    protected $chached_eac_value;

    protected $fillable = ['global_period_id'];

    function project()
    {
        return $this->belongsTo(Project::class);
    }

    function global_period()
    {
        return $this->belongsTo(GlobalPeriod::class);
    }

    function created_by_user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function boot()
    {
        parent::boot();

        self::created(function (self $rev) {
            dispatch(new CreateRevisionForProject($rev));
        });

        self::saving(function(self $rev) {
            if (!$rev->exists) {
                $lastRevNum = self::where('project_id', $rev->project_id)->max('rev_num');
                $rev->rev_num = $lastRevNum + 1;
                $rev->original_contract_amount = $rev->project->project_contract_signed_value;
                $rev->change_order_amount = $rev->project->change_order_amount;
            }

            $rev->name = $rev->global_period->name . '_Rev.' . sprintf('%02d', $rev->rev_num);
        });

        self::deleted(function ($revision) {
            $tables = [
                'revision_boqs', 'revision_breakdown_resource_shadows', 'revision_breakdown_resources',
                'revision_breakdowns', 'revision_productivities', 'revision_qty_surveys', 'revision_resources'
            ];

            foreach ($tables as $table) {
                \DB::table($table)->where('revision_id', $revision->id)->delete();
            }
        });
    }

    /**
     * @return Collection
     */
    public function statsByDiscipline()
    {
        return RevisionBreakdownResourceShadow::join('std_activities as a', 'activity_id', '=', 'a.id')
            ->groupBy('a.discipline')->orderBy('a.discipline')->selectRaw('a.discipline as discipline, sum(budget_cost) as cost')
            ->where('revision_id', $this->id)
            ->get()->keyBy('discipline');
    }

    protected function getUserAttribute()
    {
        if ($this->is_automatic) {
            return 'System';
        }

        return $this->created_by_user->name ?? '';
    }

    protected function getUrlAttribute()
    {
        return $this->url();
    }

    protected function getCreatedDateAttribute()
    {
        return $this->created_at->format('d/m/Y') ?? '';
    }

    /*public function getRevisedContractAmount()
    {
        return $this->original_contract_amount + $this->change_order_amount;
    }*/

    function url()
    {
        return url("/project/{$this->project_id}/revisions/{$this->id}");
    }

    function scopeMinRevisions(Builder $query)
    {
        return $query->select('project_id')->selectRaw('min(id) as id')->groupBy('project_id');
    }

    function scopeMaxRevisions(Builder $query)
    {
        return $query->select('project_id')->selectRaw('max(id) as id')->groupBy('project_id');
    }

    function getBudgetCostAttribute()
    {
        if (isset($this->cached_budget_cost)) {
            return $this->cached_budget_cost;
        }

        return $this->cached_budget_cost = RevisionBreakdownResourceShadow::where('revision_id', $this->id)->sum('budget_cost');
    }

    function getEacContractAmountAttribute()
    {
        if (!is_null($this->chached_eac_value)) {
            return $this->chached_eac_value;
        }

        return $this->chached_eac_value = \DB::table('revision_boqs as boq')->where('boq.project_id', $this->project_id)
            ->where('boq.revision_id', $this->id)
            ->join('revision_qty_surveys as qs', function(JoinClause $on){
//                $on->on('boq.id', '=', 'qs.boq_id');
                $on->on('boq.wbs_id', '=', 'qs.wbs_level_id');
                $on->on('boq.cost_account', '=', 'qs.cost_account');
            })
            ->selectRaw('sum(boq.price_ur * qs.eng_qty) as revised_boq')
            ->value('revised_boq');
    }

    function getPlannedProfitAmountAttribute()
    {
        return $this->eac_contract_amount - $this->budget_cost;
    }

    function getPlannedProfitabilityIndexAttribute()
    {
        if (!$this->eac_contract_amount) {
            return 0;
        }

        return $this->planned_profit_amount * 100 / $this->eac_contract_amount;
    }
}
