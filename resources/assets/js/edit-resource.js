import Vue from 'vue';
import Resources from './breakdown-resource/_resources';

new Vue({
    el: 'body',

    data: {
        resource: {},
        activity_id: 0
    },

    components: { Resources },

    events: {
        'resource-changed': function (resource) {
            this.resource = resource;
        },
    }
});


