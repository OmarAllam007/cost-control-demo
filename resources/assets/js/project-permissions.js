import Vue from 'vue';

const UserForm = {
    props: ['users'],

    template: document.getElementById('user-form').innerHTML,

    data() {
        return { user: { user_id: ''}, edit: false };
    },

    watch: {
        'user.budget': function(budget) {
            if (!budget) {
                this.user.reports = this.user.wbs = this.user.breakdown = this.user.breakdown_templates =
                    this.user.resources = this.user.productivity = this.user.boq = this.user.qty_survey = false;
            }
        },

        'user.cost_control': function(cost_control) {
            if (!cost_control) {
                this.user.actual_resources = false;
            }
        },

        'user.reports': function (reports) {
            if (reports) {
                this.user.budget = true;
                this.user.cost_control = true;
            }
        },

        'user.wbs': function (wbs) {
            if (wbs) {
                this.user.budget = true;
            }
        },

        'user.breakdown': function (breakdown) {
            if (breakdown) {
                this.user.budget = true;
            }
        },

        'user.breakdown_templates': function (breakdown_templates) {
            if (breakdown_templates) {
                this.user.budget = true;
            }
        },

        'user.resources': function (resources) {
            if (resources) {
                this.user.budget = true;
            }
        },

        'user.productivity': function (productivity) {
            if (productivity) {
                this.user.budget = true;
            }
        },

        'user.boq': function (boq) {
            if (boq) {
                this.user.budget = true;
            }
        },

        'user.qty_survey': function (qty_survey) {
            if (qty_survey) {
                this.user.budget = true;
            }
        },

        'user.actual_resources': function (actual_resources) {
            if (actual_resources) {
                this.user.cost_control = true;
            }
        }
    },

    methods: {
        save() {
            const action = this.edit? 'updateUser' : 'addUser';
            const keys = Object.keys(this.users);
            const idx = Object.values(this.users).indexOf(this.user.user_id);
            this.user.name = keys[idx];
            this.$dispatch(action, this.user);
            $(this.$el).modal('hide');
        }
    },

    events: {
        showAddUser() {
            this.user = {
                budget: false, reports: false, wbs: false, productivity: false, resources: false, boq: false, user_id: '',
                qty_survey: false, breakdown: false, breakdown_templates: false, cost_control: false, actual_resources: false
            };
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
                if (user.user_id == this.users[u].user_id) {
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
