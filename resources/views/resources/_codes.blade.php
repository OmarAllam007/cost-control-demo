@if (isset($resources) && !$override)
    <section class="col-sm-4">

        <div class="panel panel-default panel-form" id="CodesPanel">
            <div class="panel-heading clearfix">
                <h4 class="panel-title pull-left">
                    Equivalent Codes
                </h4>

                <button type="button" @click="addCode" class="btn btn-primary btn-sm pull-right"><i class="fa fa-plus"></i></button>
            </div>

            <codes :codes="{{$resources->codes}}"></codes>
        </div>

        <template id="CodeItemTemplate">
            <li class="list-group-item">
                    <input type="hidden" name="codes[@{{index}}][id]" :value="code.id">
                    <div class="input-group">
                        <input class="form-control input-sm" type="text" name="codes[@{{index}}][code]" :value="code.code" placeholder="Resource Code">
                        <span class="input-group-btn">
                            <button type="button" @click="removeMe()" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                        </span>
                    </div>
            </li>
        </template>

    </section>
@endif