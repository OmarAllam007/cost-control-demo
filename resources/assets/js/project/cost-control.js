import Vue from 'vue';
import Wbs from './components/Wbs';
import Datasheet from './components/datasheet';
import Alert from './components/Alert';
import Resources from './components/CostResources';
import ActivityLog from './components/ActivityLog'
import { DataUploads } from './components/data-uploads';

Vue.filter('slug', function(value){
    return value.replace(/\W/g, '-').replace(/-{2,}/g, '-').toLowerCase();
});


Vue.filter('number_format', function(number) {
    if (!number) {
        number = 0;
    }

    const f= new Intl.NumberFormat();
    let formatted = f.format(number.toFixed(2));

    if (!/\./.test(formatted)) {
        formatted += '.00';
    }

    return formatted
});

window.app = new Vue({
    el: '#projectArea',

    data: {
        selected: 0, loading: false
    },

    methods: {
        reload(component, alert) {
            $('#IframeModal').modal('hide');
            this.$broadcast('reload_' + component);
            this.$broadcast('show_alert', alert);
        },

        deleteProjectCurrent() {
            this.loading = true;
            $.ajax({
                url: '/api/cost/delete-current/' + this.project_id, method: 'delete', dataType: 'json',
                data: { _method: 'delete', _token: document.querySelector('[name=csrf-token]').content }
            }).success(response => {
                this.loading = false;
                $('#DeleteCurrentModal').modal('hide');
                this.reload('wbs');
                this.$dispatch('request_alert', {
                    type: response.ok? 'info' : 'error', message: response.message
                });
            }).error(() => {
                this.loading = false;
                $('#DeleteWbsDataModal').modal('hide');
                this.$dispatch('request_alert', {
                    type: 'error', message: 'Could not delete current data for this WBS'
                });

            });
        }
    },

    events: {
        request_alert(alert) {
            this.$broadcast('show_alert', alert);
        },

        wbs_changed(params) {
            this.selected = params.selection;
            this.$broadcast('wbs_changed', params);
        },

        reload(params) {
            this.reload(params.component, params.alert);
        }
    },

    components: {
        Alert, Wbs, Datasheet, Resources, ActivityLog, DataUploads
    }
});

