<?php

namespace App\Console\Commands;

use App\Boq;
use App\BreakDownResourceShadow;
use App\Survey;
use Illuminate\Console\Command;

class AddBoqAndSurveyToBreakdownShadowProject extends Command
{
    protected $signature = 'shadow:add-boq-survey-project';
    protected $bar;
    protected $boqs;
    protected $survies;
    /**
     * The console command description.
     *
     * @var string
     */

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->bar = $this->output->createProgressBar(BreakDownResourceShadow::where('project_id',41)->count());
        $this->bar->setBarWidth(50);
        $this->boqs = collect();
        $this->survies = collect();

        BreakDownResourceShadow::with('wbs')->where('project_id',41)->chunk(10000, function ($shadows) {
            $shadows->each(function (BreakDownResourceShadow $shadow) {
                $code = $shadow->wbs->code . '#' . $shadow->cost_account;
                if ($this->boqs->has($code) && $this->survies->has($code)) {
                    $boq = $this->boqs->get($code);
                    $survey = $this->survies->get($code);
                } else {
                    $boq = Boq::costAccountOnWbs($shadow->wbs, $shadow->cost_account)->first();
                    $survey = Survey::costAccountOnWbs($shadow->wbs, $shadow->cost_account)->first();
                    $this->boqs->put($code, $boq);
                    $this->boqs->put($code, $survey);
                }

                if ($boq && $survey) {
                    $shadow->boq_id = $boq->id;
                    $shadow->survey_id = $survey->id;
                    $shadow->boq_wbs_id = $boq->wbs_id;
                    $shadow->survey_wbs_id = $survey->wbs_id;
                } else {
                    $shadow->boq_id =0;
                    $shadow->survey_id =0;
                    $shadow->boq_wbs_id =0;
                    $shadow->survey_wbs_id =0;
                }

                $shadow->save();
                $this->bar->advance();
            });
        });
        $this->bar->finish();
    }
}
