<template id="QtySurveyTemplate">
    <div class="qty-survey">
        <div class="loader" v-show="loading">
            <i class="fa fa-spinner fa-spin fa-3x"></i>
        </div>

        <section class="filters" id="qty-survey-filters">

        </section>

        <table class="table table-condensed table-striped table-hover table-fixed" v-if="quantities.length">
            <thead>
            <tr>
                <th class="col-xs-2">Cost Account</th>
                <th class="col-xs-3">Description</th>
                <th class="col-xs-2">Budget Quantity</th>
                <th class="col-xs-2">Eng Quantity</th>
                <th class="col-xs-3">Action</th>
            </tr>
            </thead>

            <tbody>
            <tr v-for="quantity in quantities">
                <td class="col-xs-2">@{{ quantity.cost_account}}</td>
                <td class="col-xs-3">@{{ quantity.description}}</td>
                <td class="col-xs-2">@{{ quantity.budget_qty}}</td>
                <td class="col-xs-2">@{{ quantity.eng_qty}}</td>
                <td class="col-xs-3">
                    <form action="/qty-survey/@{{quantity.id}}" method="post" @submit.prevent="destroy(quantity.id)">
                        {{csrf_field()}}{{method_field('delete')}}
                        <a href="/qty-survey/@{{quantity.id}}/edit" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i> Edit</a>
                        <button class="btn btn-sm btn-warning"><i class="fa fa-trash"></i> Delete</button>
                    </form>
                </td>
            </tr>
            </tbody>
        </table>
        <div class="alert alert-info" v-else>
            <i class="fa fa-info-circle"></i> No quantities found
        </div>
    </div>
</template>

<qty-survey></qty-survey>