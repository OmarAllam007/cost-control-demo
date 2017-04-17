import Vue from 'vue';
import ChartArea from './ChartArea';

const app = new Vue({
    el: '#dashboard',

    data: {
        charts: [0]
    },

    methods: {
        add_chart() {
            this.charts.push(this.charts.length);
        }
    },

    components: { ChartArea }
});
