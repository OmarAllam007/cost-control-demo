import Vue from 'vue';
import WbsModal from './components/WbsModal.vue';
import WbsTree from './components/WbsTree.vue';
import WbsLevel from './components/wbs-level.vue';
import ActivityList from './components/ActivityList.vue';
import ActivityRollupForm from './components/ActivityRollupForm.vue';

Vue.component('activity-list', ActivityList);
Vue.component('wbs-tree', WbsTree);
Vue.component('wbs-modal', WbsModal);
Vue.component('wbs-level', WbsLevel);
Vue.component('rollup-form', ActivityRollupForm);

window.app = new Vue({
    el: '#RollupForm',

    data: {
        wbs: {},
        activity_code: ''
    },

    methods: {
        show_wbs_modal() {
            this.$broadcast('show_wbs_modal');
        }
    }
});
