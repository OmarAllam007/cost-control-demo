import Vue from 'vue';
import Breakdown from './components/Breakdown';
import Boq from './components/Boq';
import QtySurvey from './components/QtySurvey';

new Vue({
    el: '#wbsArea',

    data: {
        selected: 0
    },

    watch: {
        selected(selection) {
            this.$broadcast('wbs_changed', {selection});
        }
    },

    components: {
        Breakdown,
        Boq,
        QtySurvey
    }
});

