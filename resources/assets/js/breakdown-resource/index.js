var App = new Vue({
    el: '#BreakdownResourceForm',

    data: {
        resource: resource,
        productivity: productivity
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
        resources: Resources,
        productivity: Productivity,
        variables: Variables
    },

    events: {
        'resource-changed': function (resource) {
            this.resource = resource;
        },

        'productivity-changed': function (productivity) {
            this.productivity = productivity;
        }
    }
});


$(function(){
    $('#BreakdownResourceForm').on('shown.bs.modal', '.modal', function(){
        $(this).find('input.search').focus();
    });
});
