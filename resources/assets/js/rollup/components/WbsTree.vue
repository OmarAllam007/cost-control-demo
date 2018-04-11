<template>
    <ul class="wbs-tree list-unstyled" id="wbs-tree">
        <wbs-level :initial="level" v-for="level in levels" depth="0" @selected="wbsChanged(level)"></wbs-level>
    </ul>
</template>

<script>
    export default {
        name: 'wbs-tree',

        props: ['initial'],

        data() {
            return {levels: this.initial};
        },

        methods: {
            wbsChanged(level) {
                this.$dispatch('wbsChanged', level);
            }
        }
    };
</script>
