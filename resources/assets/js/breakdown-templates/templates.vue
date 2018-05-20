<template>
    <section>
        <section class="listing">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <input type="search" class="form-control" v-model="search" placeholder="Search by code or name">
                    </div>
                </div>
                <div class="col-md-8 text-right" v-show="templates.length">
                    <pagination :url="url" property="templates"></pagination>
                </div>
            </div>

            <div class="loading" v-if="loading">
                <i class="fa fa-spinner fa-spin fa-3x"></i>
            </div>

            <table v-if="templates.length" class="table table-condensed table-striped">
                <thead>
                <tr>
                    <th class="select-cell" v-if="enableSelect">
                        <input type="checkbox" @change="selectAll">
                    </th>
                    <th class="">Code</th>
                    <th class="">Template</th>
                    <th class="" v-if="!enableSelect">Actions</th>
                </tr>
                </thead>

                <tbody>
                <tr v-for="tpl in templates">
                    <td v-if="enableSelect" class="select-cell">
                        <input type="checkbox" @change="tpl.selected = ! tpl.selected" name="templates[]" :value="tpl.id" :checked="!! tpl.selected">
                    </td>
                    <td>
                        <a :href="`/breakdown-template/${tpl.id}`" v-text="tpl.code"></a>
                    </td>
                    <td>
                        <a :href="`/breakdown-template/${tpl.id}`" v-text="tpl.name"></a>
                    </td>
                    <td v-if="!enableSelect">
                        <a class="btn btn-sm btn-info" :href="`/breakdown-template/${tpl.id}`">
                            <i class="fa fa-eye"></i> Show
                        </a>

                        <a v-if="can_edit" class="btn btn-sm btn-primary" :href="`/breakdown-template/${tpl.id}/edit`">
                            <i class="fa fa-edit"></i> Edit
                        </a>

                        <button class="btn-warning btn-sm" type="button" @click.prevent="deleteTemplate(tpl.id)"><i
                                class="fa fa-trash"></i> Delete
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
            <div v-else class="alert alert-info">
                <i class="fa fa-arrow-left"></i>
                Please select a division or activity
            </div>
        </section>
    </section>
</template>
<script>
    import _ from 'lodash';
    import pagination from '../LaravelPagination.vue';

    export default {
        name: 'Templates',

        props: ['url', 'can_edit', 'can_delete', 'enableSelect'],

        data() {
            return {templates: [], search: '', loading: false};
        },

        created() {
            $(document).ajaxStart(e => {
                this.loading = true;
            });

            $(document).ajaxComplete(e => {
                this.loading = false;
            });
        },

        watch: {
            search() {
                this.updateSearch();
            }
        },

        methods: {
            updateSearch: _.debounce(function () {
                this.$parent.term = this.search;
            }, 500),

            deleteTemplate(template_id) {
                $.ajax({
                    url: `/breakdown-template/${template_id}`, method: 'delete',
                    data: {
                        _token: document.querySelector('meta[name=csrf-token]').content
                    }, dataType: 'json'
                }).done(() => {
                    this.$children[0].loadData(false);
                });
            },

            selectAll(e) {
                this.templates.forEach(tpl => {
                    tpl.selected = e.target.checked;
                })
            }
        },

        components: {
            pagination
        }
    }
</script>

<style scoped>
    .listing {
        position: relative;
    }

    .loading {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        padding: 100px;
        background: rgba(255, 255, 255, 0.6);
    }

    .select-cell {
        width: 25px;
        min-width: 25px;
        max-width: 25px;
    }
</style>