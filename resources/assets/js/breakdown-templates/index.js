import Vue from 'vue';
import Division from './division.vue';
import Divisions from './divisions.vue';
import Templates from './templates.vue';

Vue.component('division', Division);
Vue.component('divisions', Divisions);
Vue.component('templates', Templates);

const app = new Vue({
    el: '#breakdownTemplates',

    data: {
        division: 0, activity: 0
    },

    computed: {
        url() {
            let url = '';
            if (this.activity || this.division) {
                url = `/api/breakdown-template?division=${this.division}&activity=${this.activity}`;
            }
            return url;
        }
    }
});


