import Vue from 'vue';
import Division from './division.vue';
import Divisions from './divisions.vue';
import Templates from './templates.vue';
import BreakdownTemplates from "./BreakdownTemplate.vue";

Vue.component('division', Division);
Vue.component('divisions', Divisions);
Vue.component('templates', Templates);
Vue.component('breakdown-templates', BreakdownTemplates);

const app = new Vue({
    el: '#breakdownTemplates'
});


