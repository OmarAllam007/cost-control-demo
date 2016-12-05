import Vue from 'vue';

const UserForm = {
    props: ['users'],

    template: document.getElementById('user-form').innerHTML,

    data() {
        return { user: {}, edit: false };
    },

    watch: {
        'user.budget': function() {
            if (!this.user.budget) {
                this.user.reports = this.user.wbs = this.user.breakdown = this.user.breakdown_templates = this.user.resources = this.user.productivity = false;
            }
        },
        'user.cost_control': function() {
            if (!this.user.cost_control) {
                this.user.actual_resources = false;
            }
        },

        user: {
            handler: function(user) {
                if (user.reports || user.wbs || user.breakdown || user.breakdown_templates || user.resources || user.productivity) {
                    user.budget = true;
                }

                if (user.actual_resources) {
                    user.cost_control = true;
                }
            },
            deep: true
        }
    },

    methods: {
        save() {
            const action = this.edit? 'updateUser' : 'addUser';
            this.user.name = this.users[this.user.user_id];
            this.$dispatch(action, this.user);
            $(this.$el).modal('hide');
        }
    },

    events: {
        showAddUser() {
            this.user = {};
            this.edit = false;
            $(this.$el).modal();
        },

        showEditUser(user) {
            this.user = user;
            this.edit = true;
            $(this.$el).modal();
        }
    }
};

const UserList = {
    template: document.getElementById('user-list').innerHTML,

    props: ['users'],

    methods: {
        editUser(user) {
            this.$dispatch('showEditUser', user);
        },

        removeUser(user) {
            this.users = this.users.filter(u => u.user_id != user.user_id);
        }
    },

    events: {
        addUser(user) {
            this.users.push(user);
        },

        updateUser(user) {
            for (let u in this.users) {
                if (user.user_id = this.users[u].user_id) {
                    this.users[u] = user;
                }
            }
        }
    }
};

new Vue({
    el: '#vueRoot',

    components: {
        UserList, UserForm
    },

    methods: {
        addUser() {
            this.$broadcast('showAddUser');
        }
    },

    events: {
        addUser(user) {
            this.$broadcast('addUser', user);
        },

        showEditUser(user) {
            this.$broadcast('showEditUser', user);
        },

        updateUser(user) {
            this.$broadcast('updateUser', user);
        }
    }
});
