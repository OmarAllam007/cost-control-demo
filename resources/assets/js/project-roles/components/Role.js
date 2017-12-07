import Users from './Users'
export default {
    props: ['key', 'role'],
        data() {
        let users = [];
        if (this.role.users) {
            users = this.role.users;
        }
        return {enabled: false, users};
    },
    methods: {
        addUser() {
            this.users.push({name: '', email: ''})
        }
    },

    events: {
        dropUser(key) {
            this.users.splice(key, 1);
        }
    },

    components: {
        Users
    }
}
