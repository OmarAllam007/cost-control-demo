<activity-log inline-template>
<section>
    <div class="loader" v-show="loading">
        <i class="fa fa-refresh fa-spin fa-3x"></i>
    </div>

    <div class="activity-log" v-if="activities|isEmptyObject">
        <article class="panel panel-info" v-for="(activity, resources) in activities">
            <div class="panel-heading">
                <h4 class="panel-title"><a :href="'#activity-' + $index" data-toggle="collapse" v-text="activity"></a>
                </h4>
            </div>

            <table class="table table-striped table-condensed table-bordered collapse" :id="'activity-' + $index">
                <thead>
                <tr>
                    <th>Store Resource</th>
                    <th>Store U.O.M</th>
                    <th>Budget Resource</th>
                    <th>Budget U.O.M</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Cost</th>
                    <th>Date</th>
                    <th>Doc #</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="resource in resources">
                    <td v-text="resource.store_resource_name"></td>
                    <td v-text="resource.store_measure_unit"></td>
                    <td v-text="resource.budget_resource_name"></td>
                    <td v-text="resource.budget_measure_unit"></td>
                    <td v-text="resource.qty"></td>
                    <td v-text="resource.unit_price"></td>
                    <td v-text="resource.cost"></td>
                    <td v-text="resource.action_date"></td>
                    <td v-text="resource.doc_no"></td>
                </tr>
                </tbody>
            </table>
        </article>
    </div>

    <div class="alert alert-info" v-else><i class="fa fa-info-circle"></i> No data found</div>
</section>
</activity-log>