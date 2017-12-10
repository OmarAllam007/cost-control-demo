import Users from './Users'
export default {
    props: ['key', 'role', 'errors'],
        data() {
        let users = [];
        if (this.role.users && this.role.users.length) {
            users = this.role.users;
        }

        let enabled = this.role.enabled;

        return {enabled, users};
    },

    methods: {
        addUser() {
            this.users.push({name: '', email: ''})
        }
    },

    watch: {
        enabled() {
            if (this.enabled && (!this.role.users || !this.role.users.length)) {
                this.addUser();
            }
        }
    },

    events: {
        dropUser(key) {
            if (this.users && this.users.length > 1) {
                this.users.splice(key, 1);
            }
        }
    },

    components: {
        Users
    }
}
