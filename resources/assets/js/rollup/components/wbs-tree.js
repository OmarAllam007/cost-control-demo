export default {
    name: 'wbs-tree',

    props: ['initial'],

    data() {
        return {levels: this.initial};
    }
};