import Vue from 'vue';
import Wbs from './components/Wbs';
import Breakdown from './components/datasheet';
import Alert from './components/Alert';

Vue.filter('slug', function(value){
    return value.replace(/\W/g, '-').replace(/-{2,}/g, '-').toLowerCase();
});

window.app = new Vue({
    el: '#datasheet',

    data: {
        selected: 0
    },

    methods: {
        reload(component, alert) {
            $('#IframeModal').modal('hide');
            this.$broadcast('reload_' + component);
            this.$broadcast('show_alert', alert);
        }
    },

    events: {
        request_alert(alert) {
            this.$broadcast('show_alert', alert);
        },

        wbs_changed(params) {
            this.selected = params.selection;
            this.$broadcast('wbs_changed', params);
        },
    },

    components: {
        Alert,
        Wbs,
        Breakdown
    }
});

