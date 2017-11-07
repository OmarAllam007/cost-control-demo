<form action="" class="panel panel-default">
    <div class="panel-body">

        <div class="col-sm-3 form-group">
            <label for="type">Resource Type</label>
            <a href="#ResourceTypeModal" data-toggle="modal" class="btn btn-default btn-block">Select Resource Type</a>
        </div>

        <div class="col-sm-4 form-group">
            <label for="resourceFilter">Resource</label>
            <input class="form-control" type="search" name="resource" placeholder="Search by code or name" value="{{request('resource')}}" id="resourceFilter">
        </div>

        <div class="col-sm-2 checkbox">
            <label style="margin-top: 25px;">
                <input type="checkbox" name="negative" id="negative">
                Negative variance
            </label>
        </div>

        <div class="col-sm-3 form-group text-right" style="padding-top: 25px;">
            <button class="btn btn-primary"><i class="fa fa-filter"></i> Filter</button>
        </div>

        <div class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        <h4 class="modal-title">Select Resource Type</h4>
                    </div>
                    <div class="modal-body">

                    </div>
                </div>
            </div>
        </div>
    </div>
</form>