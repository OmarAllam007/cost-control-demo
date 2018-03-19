<template>
    <li :class="`level-${depth}`">
        <div class="wbs-item">
            <strong>
                <input type="radio" v-if="! level.children.length" @change="setSelected" name="wbs_level">
                <a href="#children-{{level.id}}" class="open-level" @click.prevent="toggleChildren" >
                    <span class="wbs-icon" v-if="level.children.length">
                        <i class="fa" :class="show_children? 'fa-minus-square-o' : 'fa-plus-square-o'"></i>
                    </span>
                    {{level.name}}
                    <small>({{level.code}})</small>
                </a>
            </strong>
        </div>

        <ul v-if="level.children.length" :class="{'collapse' : this.depth, 'in': show_children}">
            <wbs-level :initial="sublevel"
                       v-for="sublevel in level.children"
                       :depth="depth + 1" :name="sublevel.id"
                       :has-activity-rollup="hasActivityRollup"
            ></wbs-level>
        </ul>
    </li>
</template>

<style scoped>
    input[type=radio] {
        position: absolute;
        top: 5px;
        left: 5px;
    }
</style>

<script>
    export default {
        name: 'wbs-level',

        props: ['initial', 'depth', 'hasActivityRollup'],

        data() {
            return {
                level: this.initial,
                activities: [],
                show_children: false,
                loading: false,
                search: '',
                selected: false
            };
        },

        computed: {
            filteredActivities() {
                if (!this.search) {
                    return this.activities;
                }

                const code = this.search.toLowerCase();
                return this.activities.filter(
                    activity => activity.code.toLowerCase().indexOf(code) >= 0
                );
            }
        },

        methods: {
            toggleChildren() {
                this.show_children = !this.show_children;

                if (!this.activities.length) {
                    this.loading = true;

                    $.ajax({
                        url: `/api/rollup/wbs/${this.level.id}`,
                        dataType: 'json'
                    }).then((data) => {
                        this.activities = data;
                        this.loading = false;
                    }, () => {
                        this.loading = false;
                    });
                }
            },

            /*checkAll(state = true) {
                if (!this.hasActivityRollup) {
                    return false;
                }

                this.$children
                    .filter(child => child.constructor.name === 'Activity')
                    .forEach(child => child.setChecked(state));
            },*/

            setSelected() {
                this.selected = ! this.selected;

                if (this.selected) {
                    this.$emit('selected');
                }
            }
        }
    }

</script>