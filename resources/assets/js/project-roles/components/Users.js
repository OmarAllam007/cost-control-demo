import User from './User';

export default  {
    props: ['role_key', 'users'],

    components: {
        User
    },

    // events: {
    //     dropUser(key) {
    //         this.$emit('dropUser', this.user_key);
    //     }
    // },
}