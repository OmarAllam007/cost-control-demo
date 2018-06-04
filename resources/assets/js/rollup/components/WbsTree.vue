<template>
    <ul class="wbs-tree list-unstyled br-1">
        <wbs-level :initial="level" v-for="level in levels" depth="0"></wbs-level>
    </ul>
</template>

<script>
    export default {
        name: 'wbs-tree',

        props: ['initial'],

        data() {
            return {levels: this.initial};
        }
    };
</script>
