export default {
    template: `
        <tr>
            <td :class="{'has-error': name_error}">
                <input class="form-control input-sm" type="text" :name="name_input" v-model="user.name">
                <input type="hidden" name="id_input" v-model="user.id">
            </td>
            <td :class="{'has-error': email_error}"><input class="form-control input-sm" type="email" :name="email_input" v-model="user.email"></td>
            <td class="text-center"><a href="#" class="btn btn-danger btn-sm" @click.prevent="dropUser"><i class="fa fa-remove"></i></a></td>
        </tr>
    `,
    props: ['role_id', 'user_key', 'user_data', 'errors'],

    data() {
        return { user: {id: this.user_data.id || 0, name: this.user_data.name, email: this.user_data.email}}
    },

    methods: {
        dropUser() {
            this.$dispatch('dropUser', this.user_key);
        }
    },

    computed: {
        name_input() {
            return `roles[${this.role_id}][users][${this.user_key}][name]`;
        },

        email_input() {
            return `roles[${this.role_id}][users][${this.user_key}][email]`;
        },

        name_error() {
            const key = `roles.${this.role_id}.users.${this.user_key}.name`;
            return this.errors.hasOwnProperty(key);
        },

        email_error() {
            const key = `roles.${this.role_id}.users.${this.user_key}.email`;
            return this.errors.hasOwnProperty(key);
        }
    }
}