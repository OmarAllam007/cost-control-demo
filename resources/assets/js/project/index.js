import Vue from 'vue';
import Breakdown from './components/Breakdown';
import Boq from './components/Boq';
import QtySurvey from './components/QtySurvey';
import Alert from './components/Alert';
import Wbs from './components/Wbs';

Vue.filter('slug', function(value){
    return value.replace(/\W/g, '-').replace(/-{2,}/g, '-').toLowerCase();
});

window.app = new Vue({
    el: '#wbsArea',

    data: {
        selected: 0, wiping: false
    },

    methods: {
        reload(component, alert) {
            $('#IframeModal').modal('hide');
            this.$broadcast('reload_' + component);
            this.$broadcast('show_alert', alert);
        }
    },

    events: {
        request_alert(alert) {
            this.$broadcast('show_alert', alert);
        },

        wbs_changed(params) {
            this.selected = params.selection;
            this.$broadcast('wbs_changed', params);
        }
    },

    components: {
        Alert,
        Breakdown,
        Boq,
        QtySurvey,
        Wbs
    }
});

