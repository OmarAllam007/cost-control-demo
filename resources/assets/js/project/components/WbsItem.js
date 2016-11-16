export default {
    template: document.getElementById('WbsItemTemplate').innerHTML,

    props: ['item', 'hasFilter'],

    name: 'wbs-item',

    ready() {
        $(this.$el).find('[data-toggle="tooltip"]').tooltip({container: 'body'});
    },

    methods: {
        setSelected() {
            this.$dispatch('wbs_changed', {selection: this.item.id});
        }
    }
}