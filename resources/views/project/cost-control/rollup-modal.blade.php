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
                    <h4>Cost Account</h4>
                    <p>Roll up all resource to cost account level (for back to back subcontractors)</p>
                    <a class="btn btn-primary btn-sm" href="/project/{{$project->id}}/rollup-cost-account">Select Cost Accounts</a>
                    {{--<form action="/project/{{$project->id}}/rollup-project-cost-account" method="post" class="btn-toolbar">
                        {{csrf_field()}}
                        <button type="submit" class="btn btn-warning btn-sm" href="">Whole project</button>
                    </form>--}}
                    <hr>
                </article>

                <article>
                    <h4>Semi Cost Account</h4>
                    <p>Select resources to rollup on cost account level. This excludes important resources.</p>
                    <a class="btn btn-primary btn-sm" href="/project/{{$project->id}}/rollup-semi-cost-account">Select Resources</a>
                    {{--<form action="/project/{{$project->id}}/rollup-project-semi-cost-account" method="post" class="btn-toolbar">
                        {{csrf_field()}}
                        <button type="submit" class="btn btn-warning btn-sm" href="">Whole project</button>
                    </form>--}}
                    <hr>
                </article>

                <article>
                    <h4>Semi Activity</h4>
                    <p>Select resources to rollup on activity level. This excludes important resources.</p>
                    <a class="btn btn-primary btn-sm" href="/project/{{$project->id}}/rollup-semi-activity">Select Resources</a>
                    {{--<form action="/project/{{$project->id}}/rollup-project-semi-activity" method="post" class="btn-toolbar">
                        {{csrf_field()}}
                        <button type="submit" class="btn btn-warning btn-sm" href="">Whole project</button>
                    </form>--}}
                    <hr>
                </article>

                <article>
                    <h4>Activity</h4>
                    <p>Rollup all budget resources on activity level</p>
                    <form action="/project/{{$project->id}}/rollup-project-activity" method="post" class="btn-toolbar">
                        {{csrf_field()}}
                        <button type="submit" class="btn btn-warning btn-sm" href="">Whole project</button>
                    </form>
                </article>
            </main>

        </div>
    </div>
</div>
