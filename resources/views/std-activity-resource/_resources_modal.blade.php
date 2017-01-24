
<template id="ResourcesTemplate">
    <div id="ResourcesModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Select Resource</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group form-group-sm">
                        <input type="text" v-model="term" placeholder="Type here to search" class="form-control search"
                               debounce="500" autocomplete="off">
                    </div>
                    <section>
                        <ul class="list-unstyled tree">
                            @foreach($resourcesTree as $type)
                                @include('resources._recursive_resource_input', ['type' => $type, 'value' => Form::getValueAttribute('resource_id')])
                            @endforeach
                        </ul>
                    </section>
                </div>
            </div>
        </div>
    </div>
</template>
{{--@if(request('project_id'))--}}
{{--<resources :resource="{{($resource_id = request('project_id'))? json_encode(\App\Resources::find(request('project_id'))->morphToJSON()) : '{}' }}"></resources>--}}
{{--@else--}}
<resources
        :resource="{{($resource_id = Form::getValueAttribute('resource_id'))? json_encode(\App\Resources::find(Form::getValueAttribute('resource_id'))->morphToJSON()) : '{}' }}"></resources>
{{--@endif--}}