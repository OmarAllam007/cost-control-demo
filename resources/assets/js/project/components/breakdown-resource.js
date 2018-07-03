export default {
    props: ['resource', 'activity'],

    data() {
        return {expanded: false, is_rolled_up: false}
    },

    methods: {
        deleteResource() {
            this.$emit('show_delete_resource', this.resource);
        },

        deleteActivity() {
            this.$emit('show_delete_activity', this.resource);
        }
    },

    computed: {
        activity_id() {
            return this.activity.replace(/[\s\W]+/, '-').toLowerCase();
        }
    },

    events: {

    }
}