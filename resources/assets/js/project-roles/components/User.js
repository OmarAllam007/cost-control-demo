export default {
    template: `
        <tr>
            <td><input class="form-control input-sm" type="text" :name="name_input" v-model="user.name"></td>
            <td><input class="form-control input-sm" type="email" :name="email_input" v-model="user.email"></td>
            <td class="text-right"><a href="#" class="btn btn-danger btn-sm" @click.prevent="dropUser"><i class="fa fa-remove"></i></a></td>
        </tr>
    `,
    props: ['role_key', 'user_key', 'user_data'],

    data() {
        return { user: {name: this.user_data.name, email: this.user_data.email} }
    },

    methods: {
        dropUser() {
            this.$dispatch('dropUser', this.user_key);
        }
    },

    computed: {
        name_input() {
            return `roles[${this.role_key}][users][${this.user_key}][name]`;
        },

        email_input() {
            return `roles[${this.role_key}][users][${this.user_key}][email]`;
        }
    }
}