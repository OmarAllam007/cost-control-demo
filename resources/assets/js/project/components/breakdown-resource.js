export default {
    props: ['resource'],

    data() {
        return {expanded: false, is_rolled_up: false}
    },

    methods: {
        add_to_rollup() {
            this.is_rolled_up = !this.is_rolled_up;
            this.$dispatch(this.is_rolled_up? 'add_to_rollup' : 'remove_from_rollup', this.resource.id);
        }
    },

    events: {

    }
}