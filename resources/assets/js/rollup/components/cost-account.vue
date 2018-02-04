<template>
    <li>
        <label>
            <input type="checkbox" :value="cost_account.id" :name="`cost_account[${cost_account.id}]`">
            {{cost_account.code}} &mdash; <small>{{cost_account.description}}</small>
        </label>
    </li>
</template>

<script>
    export default {
        props: ['initial'],

        data() {
            return { cost_account: this.initial };
        }
    }
</script>