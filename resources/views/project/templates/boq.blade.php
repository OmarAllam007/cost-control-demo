<template id="BOQTemplate">
    <div class="breakdown">
        <div class="loader" v-show="loading">
            <i class="fa fa-spinner fa-spin fa-3x"></i>
        </div>

        <section class="filters" id="breakdown-filters">

        </section>

        <section v-if="!empty_boq" class="panel-group" id="BoqAccord">
            <div class="panel panel panel-info panel-collapse" v-for="(discipline, items) in boq">
                <div class="panel-heading">
                    <h4 class="panel-title"><a data-toggle="collapse" data-parent="#BoqAccord" href="#@{{ discipline }}">@{{ discipline }}</a>
                    </h4>
                </div>

                <table class="table table-condensed table-striped table-hover table-fixed collapse"
                       id="@{{ discipline }}">
                    <thead>
                    <tr>
                        <th class="col-md-6">BOQ Item</th>
                        <th class="col-md-3">Cost Account</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="item in items">
                        <td class="col-md-6">@{{item['description']}}</td>
                        <td class="col-md-3">@{{item['cost_account']}}</td>
                        <td>
                            <form action="/boq/@{{item.id}}" method="post" @submit.prevent="destroy(item.id)">
                                {{csrf_field()}} {{method_field('delete')}}
                                <a href="/boq/@{{ item.id }}/edit" class="btn btn-sm btn-primary">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                <button class="btn btn-sm btn-warning"><i class="fa fa-trash-o"></i> Delete</button>
                            </form>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </section>
        <div class="alert alert-info" v-else>
            <i class="fa fa-info-circle"></i> No items found
        </div>
    </div>
</template>
<boq></boq>