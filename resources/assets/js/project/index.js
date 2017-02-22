import Vue from 'vue';
import Breakdown from './components/Breakdown';
import Boq from './components/Boq';
import QtySurvey from './components/QtySurvey';
import Alert from './components/Alert';
import Wbs from './components/Wbs';
import Resources from './components/Resources';
import BreakdownTemplate from './components/BreakdownTemplate';
import Productivity from './components/Productivity';

Vue.filter('slug', function(value){
    return value.replace(/\W/g, '-').replace(/-{2,}/g, '-').toLowerCase();
});

window.app = new Vue({
    el: '#projectArea',

    data: {
        selected: 0, wiping: false, loading: false
    },

    methods: {
        reload(component, alert) {
            $('#IframeModal').modal('hide');
            this.$broadcast('reload_' + component);
            this.$broadcast('show_alert', alert);
        },

        deleteWbs() {
            this.loading = true;

            $.ajax({
                url: '/wbs-level/' + this.selected,
                method: 'post', dataType: 'json',
                data: {
                    _token: $('meta[name=csrf-token]').attr('content'),
                    _method: 'delete', wipe: true
                }
            }).success(response => {
                $('#DeleteWBSModal').modal('hide');
                this.loading = false;
                this.$broadcast('show_alert', {type: response.ok? 'info' : 'error', message: response.message});
                this.$broadcast('reload_wbs');
            }).error(() => {
                $('#DeleteWBSModal').modal('hide');
                this.loading = false;
                this.$broadcast('show_alert', {type: 'error', message: 'Could not delete WBS'});
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
    },

    components: {
        Alert,
        Breakdown,
        Boq,
        QtySurvey,
        Wbs,
        Resources,
        BreakdownTemplate,
        Productivity,
    }
});
