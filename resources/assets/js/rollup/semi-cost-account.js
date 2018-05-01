import Vue from 'vue';
import WbsTree from './components/WbsTree';
import WbsLevel from './components/wbs-level.vue';
import Activity from './components/cost-account-activity.vue';
import CostAccount from './components/cost-account-with-resources.vue';

Vue.component('wbs-tree', WbsTree);
Vue.component('wbs-level', WbsLevel);
Vue.component('activity', Activity);
Vue.component('cost-account', CostAccount);

window.app = new Vue({
    el: '#RollupForm'
});
