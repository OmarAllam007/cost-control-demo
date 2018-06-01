import Vue from 'vue';
import WbsModal from './components/WbsModal.vue';
import WbsTree from './components/WbsTree.vue';
import WbsLevel from './components/wbs-level.vue';
import ActivityList from './components/ActivityList.vue';
import ActivityRollupForm from './components/ActivityRollupForm.vue';
import Activity from './components/SemiActivity.vue';

window.EventBus = new Vue({
    data: {wbs: {}}
});

Vue.component('activity-list', ActivityList);
Vue.component('activity', Activity);
Vue.component('wbs-tree', WbsTree);
Vue.component('wbs-modal', WbsModal);
Vue.component('wbs-level', WbsLevel);
Vue.component('rollup-form', ActivityRollupForm);

window.app = new Vue({
    el: '#RollupForm'
});
