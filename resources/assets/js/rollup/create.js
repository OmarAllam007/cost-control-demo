import Vue from 'vue';
import WbsTree from './components/wbs-tree';
import WbsLevel from './components/wbs-level.vue';

Vue.component('wbs-tree', WbsTree);
Vue.component('wbs-level', WbsLevel);

window.app = new Vue({
    el: '#CreateRollupForm'
});
