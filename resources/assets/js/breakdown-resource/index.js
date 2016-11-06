import Vue from 'vue';
import Resources from './_resources';
import Productivity from './_productivity';

var App = new Vue({
    el: '#BreakdownResourceForm',
    data: {
        resource: {},
        productivity: productivity,
        labor_count: 0
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
    },

    events: {
        'resource-changed': function (resource) {
            this.resource = resource;
        },

        'productivity-changed': function (productivity) {
            this.productivity = productivity;
        },

        set_labor_count: function(count) {
            this.labor_count = count;
        }
    }
});


$(function(){
    $('#BreakdownResourceForm').on('shown.bs.modal', '.modal', function(){
        $(this).find('input.search').focus();
    });
});
