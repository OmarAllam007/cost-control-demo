import Vue from 'vue';
import Breakdown from './components/Breakdown';
import Boq from './components/Boq';
import QtySurvey from './components/QtySurvey';

window.app = new Vue({
    el: '#wbsArea',

    data: {
        selected: 0,
        reload: ''
    },

    watch: {
        selected(selection) {
            this.$broadcast('wbs_changed', {selection});
        }
    },

    methods: {
        reload(component) {
            this.$broadcast('reload_' + component);
        }
    },

    components: {
        Breakdown,
        Boq,
        QtySurvey
    }
});

