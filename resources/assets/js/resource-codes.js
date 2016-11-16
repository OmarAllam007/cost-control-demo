import Vue from 'vue';

const CodeItem = {
    template: $('#CodeItemTemplate').html(),

    props: ['code', 'index'],

    methods: {
        removeMe () {
            this.$dispatch('removeCode', this.index);
        }
    }
};

const Codes = {
    props: ['codes'],

    template: `
            <ul class="list-group">
                <code-item v-for="(index,code) in codes" :index="index" :code="code"></code-item>
            </ul>
    `,

    computed: {
        nextIndex() {
            return this.codes.length;
        }
    },

    events: {
        addCode() {
            this.codes.push({
                id: '',
                code: ''
            });
        },

        removeCode(index) {
            this.codes = this.codes.filter((code, idx) => idx != index);
        }
    },

    components: {CodeItem}
};

new Vue({
    el: '#CodesPanel',

    methods: {
        addCode() {
            this.$broadcast('addCode');
        }
    },

    components: {Codes}
});
