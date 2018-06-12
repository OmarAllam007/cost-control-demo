export default {
    props: ['resource', 'activity'],

    data() {
        return {expanded: false, is_rolled_up: false}
    },

    computed: {
        activity_id() {
            return this.activity.replace(/[\s\W]+/, '-').toLowerCase();
        }
    },

    events: {

    }
}