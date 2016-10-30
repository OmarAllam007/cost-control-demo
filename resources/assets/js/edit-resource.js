import Vue from 'vue';
import Resources from './breakdown-resource/_resources';

new Vue({
    el: 'body',

    data: {
        resource: {},
        activity_id: 0,
        variables: []
    },

    watch: {
        activity_id(activity_id) {
            $.ajax({
                url: '/api/std-activity/variables/' + activity_id, dataType: 'json'
            }).success(vars => this.variables = vars);
        }
    },

    components: { Resources },

    events: {
        'resource-changed': function (resource) {
            this.resource = resource;
        },
    }
});


