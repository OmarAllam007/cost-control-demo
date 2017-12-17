import User from './User';

export default  {
    props: ['role_id', 'users', 'errors'],

    components: {
        User
    },

    // events: {
    //     dropUser(key) {
    //         this.$emit('dropUser', this.user_key);
    //     }
    // },
}