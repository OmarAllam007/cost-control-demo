import Vue from 'vue';
import Wbs from './components/Wbs';
import Datasheet from './components/datasheet';
import Alert from './components/Alert';
import Resources from './components/CostResources';

Vue.filter('slug', function(value){
    return value.replace(/\W/g, '-').replace(/-{2,}/g, '-').toLowerCase();
});


Vue.filter('number_format', function(number) {
    if (!number) {
        number = 0;
    }

    const f= new Intl.NumberFormat();
    let formatted = f.format(number.toFixed(2));

    if (!/\./.test(formatted)) {
        formatted += '.00';
    }

    return formatted
});

window.app = new Vue({
    el: '#projectArea',

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

        reload(params) {
            console.log(params);
            this.reload(params.component, params.alert);
        }
    },

    components: {
        Alert,
        Wbs,
        Datasheet,
        Resources
    }
});

