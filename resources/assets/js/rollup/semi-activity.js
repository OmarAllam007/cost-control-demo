import Vue from 'vue';
import WbsTree from './components/wbs-tree';
import WbsLevel from './components/wbs-level.vue';
import Activity from './components/activity-resources.vue';

Vue.component('wbs-tree', WbsTree);
Vue.component('wbs-level', WbsLevel);
Vue.component('activity', Activity);

window.app = new Vue({
    el: '#RollupForm'
});
