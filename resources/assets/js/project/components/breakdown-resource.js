export default {
    props: ['resource', 'rollup_activity', 'rollup_wbs'],

    data() {
        return {expanded: false, is_rolled_up: false}
    },

    methods: {
        add_to_rollup() {
            this.is_rolled_up = !this.is_rolled_up;
            this.$dispatch(this.is_rolled_up? 'add_to_rollup' : 'remove_from_rollup', this.resource);
        }
    },

    computed: {
        can_be_rolled_up() {
            if (this.resource.is_rolled_up) {
                return false;
            }

            if (!this.rollup_activity && !this.rollup_wbs) {
                return true;
            }

            return this.rollup_activity == this.resource.activity_id && this.rollup_wbs == this.resource.wbs_id;
        }
    },

    events: {

    }
}