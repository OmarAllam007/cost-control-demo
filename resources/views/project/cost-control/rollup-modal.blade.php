<div class="modal" tabindex="-1" role="dialog" id="RollupModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <header class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

                <h3 class="modal-title">Resources Rollup</h3>
            </header>

            <main class="modal-body">
                <article>
                    <h4>Level 2 &mdash; Cost Account</h4>
                    <p>Roll up all resource up to cost account level under activities (for back to back subcontractors)</p>
                    <p>
                        <a href="{{route('project.rollup', $project)}}">Select Resources</a>&nbsp;&nbsp; | &nbsp;&nbsp;
                        <a href="">Whole project</a>
                    </p>
                    <hr>
                </article>

                <article>
                    <h4>Level 2 &mdash; Resources</h4>
                    <p>Select resources to rollup on cost account level</p>
                    <p>
                        <a href="{{route('project.rollup.edit', $project)}}">Select Resources</a>&nbsp;&nbsp; | &nbsp;&nbsp;
                        <a href="">Whole project</a>
                    </p>
                    <hr>
                </article>

                <article>
                    <h4>Level 3 &mdash; Activity</h4>
                    <p>Rollup all budget resources on activity level</p>
                    <p><a href="">Whole project</a></p>
                </article>
            </main>

        </div>
    </div>
</div>
