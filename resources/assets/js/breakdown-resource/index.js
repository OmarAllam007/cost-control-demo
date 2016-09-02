var App = new Vue({
    el: '#BreakdownResourceForm',

    data: {
        resource: resource,

        productivity_code: ''
    },

    computed: {
        show_productivity: function () {
            //If the main type of the resource is labor type
            //show productivity options
            var laborx = /labor|labour|equipment|scaffold/i;
            return laborx.test(this.resource.root_type);
        }
    },

    components: {
        Resources: Resources,
        Productivity: Productivity
    },

    events: {
        'resource-changed': function (resource) {
            this.resource = resource;
        }
    }
});
