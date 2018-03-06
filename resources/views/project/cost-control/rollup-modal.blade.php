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
                    <form action="{{route('project.rollup.level-2a', $project)}}" method="post" class="btn-toolbar">
                        {{csrf_field()}}
                        <a class="btn btn-outline btn-primary btn-sm" href="{{route('project.rollup', $project)}}">Select Cost Accounts</a>
                        <button type="submit" class="btn btn-warning btn-sm" href="">Whole project</button>
                    </form>
                    <hr>
                </article>

                <article>
                    <h4>Level 2 &mdash; Resources</h4>
                    <p>Select resources to rollup on cost account level</p>
                    <form action="{{route('project.rollup.level-2b', $project)}}" method="post" class="btn-toolbar">
                        {{csrf_field()}}
                        <a class="btn btn-outline btn-primary btn-sm" href="{{route('project.rollup.edit', $project)}}">Select Resources</a>
                        <button type="submit" class="btn btn-warning btn-sm" href="">Whole project</button>
                    </form>
                    <hr>
                </article>

                <article>
                    <h4>Level 3 &mdash; Activity</h4>
                    <p>Rollup all budget resources on activity level</p>
                    <form action="{{route('project.rollup.level-3', $project)}}" method="post" class="btn-toolbar">
                        {{csrf_field()}}
                        <button type="submit" class="btn btn-warning btn-sm" href="">Whole project</button>
                    </form>
                </article>
            </main>

        </div>
    </div>
</div>
