import Vue from 'vue';
import WbsTree from './components/wbs-tree';
import WbsLevel from './components/wbs-level.vue';
import Activity from './components/activity.vue';
import CostAccount from './components/cost-account.vue';

Vue.component('wbs-tree', WbsTree);
Vue.component('wbs-level', WbsLevel);
Vue.component('activity', Activity);
Vue.component('cost-account', CostAccount);

window.app = new Vue({
    el: '#RollupForm'
});
