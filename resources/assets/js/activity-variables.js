import Vue from 'vue';

let Variables = Vue.extend({
    template: document.getElementById('VariablesTemplate').innerHTML,

    props: ['vars'],

    methods: {
        addVariable: function() {
            var index = this.vars.length;
            var number = index + 1;
            var _var = {id: number, name: '$v' + number, label: ''};

            this.vars.push(_var);
        },

        removeVariable: function($index) {
            var newVars = [];
            var i = 0;
            var counter = 0;
            for (i; i < this.vars.length; i++) {
                if ($index != i) {
                    this.vars[i].id = counter;
                    this.vars[i].name = '$v' + (counter + 1);
                    newVars.push(this.vars[i]);
                    counter++;
                }
            }
            this.$set('vars', newVars);
        }
    }
});

new Vue({
    el: 'body',

    components: {
        Variables
    }
});