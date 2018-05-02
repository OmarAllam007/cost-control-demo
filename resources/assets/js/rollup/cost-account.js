import Vue from 'vue';
import WbsTree from './components/WbsTree.vue';
import WbsLevel from './components/wbs-level.vue';
import Activity from './components/cost-account-activity.vue';
import ActivityList from './components/cost-account-activity-list.vue';
import CostAccount from './components/cost-account.vue';

window.EventBus = new Vue({
    data: {
        wbs: {}
    }
});

Vue.component('wbs-tree', WbsTree);
Vue.component('wbs-level', WbsLevel);
Vue.component('activity', Activity);
Vue.component('activity-list', ActivityList);
Vue.component('cost-account', CostAccount);

window.app = new Vue({
    el: '#RollupForm'
});