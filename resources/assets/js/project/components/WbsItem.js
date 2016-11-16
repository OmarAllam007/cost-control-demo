export default {
    template: document.getElementById('WbsItemTemplate').innerHTML,

    props: ['item', 'hasFilter'],

    name: 'wbs-item',

    methods: {
        setSelected() {
            this.$dispatch('wbs_changed', {selection: this.item.id});
        }
    }
}