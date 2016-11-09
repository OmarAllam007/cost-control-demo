import Vue from 'vue';
import Breakdown from './components/Breakdown';
import Boq from './components/Boq';
import QtySurvey from './components/QtySurvey';
import Alert from './components/Alert';

window.app = new Vue({
    el: '#wbsArea',

    data: {
        selected: 0
    },

    watch: {
        selected(selection) {
            this.$broadcast('wbs_changed', {selection});
        }
    },

    methods: {
        reload(component, alert) {
            $('#EditResourceModal').modal('hide');
            this.$broadcast('reload_' + component);
            this.$broadcast('show_alert', alert);
        }
    },

    events: {
        request_alert(alert) {
            this.$broadcast('show_alert', alert);
        }
    },

    components: {
        Alert,
        Breakdown,
        Boq,
        QtySurvey
    }
});

