import Vue from 'vue';
import Roles from './components/Roles';

const app = new Vue({
    el: document.getElementById('rolesForm'),
    components: {
        Roles
    }
});