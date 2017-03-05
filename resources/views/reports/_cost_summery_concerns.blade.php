<div class="modal fade" id="AllModal" tabindex="-1" role="dialog">
    <div class="modal-dialog  modal-lg">
        <form action="" class="modal-content">
            {{csrf_field()}} {{method_field('post')}}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title">Report Concerns</h4>
            </div>
            <div class="modal-body">
                <table class="table table-condensed table-bordered">
                    <thead >
                    <tr class="bg-success">
                        <td>Resource Type</td>
                        <td style="">Comment</td>
                        {{--<td style=""></td>--}}
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($concerns as $key=>$concern)
                        <tr class="names">
                            <td>{{$key}}</td>
                            <td></td>
                        </tr>
                        @foreach($concern['comments'] as $comment)
                            <tr>
                                <td></td>
                                <td>{{$comment}}</td>
                            </tr>
                        @endforeach
                    @endforeach
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>