export default {
    template: document.getElementById('resource-template').innerHTML,

    data() {
        return {
            items: [], selected: []
        };
    },

    methods: {
        apply() {
            this.$dispatch('update_filters', this.selected)
        }
    }
}