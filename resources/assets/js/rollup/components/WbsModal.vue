<template>
    <div class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>

                    <h4 class="modal-title">Select WBS</h4>
                </div>

                <div class="modal-body">
                    <wbs-tree :initial="levels" @wbsChanged="wbsChanged">
                    </wbs-tree>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['levels'],

        methods: {
            wbsChanged(level) {
                console.log(level);
            }
        },

        events: {
            show_wbs_modal() {
                $(this.$el).modal();
            }
        }
    };
</script>