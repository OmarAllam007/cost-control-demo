@extends('layouts.app')

@section('title', 'Send Dashboard')

@section('header')
    <div class="display-flex">
        <h2 class="flex">Send Dashboard</h2>
        <a href="{{url('/dashboard')}}" class="btn btn-sm btn-default">
            <i class="fa fa-chevron-left"></i> Back to Dashboard
        </a>
    </div>
@endsection

@section('body')
    <form action="" method="post">
        {{csrf_field()}}

        <div class="row">
            <article class="form-group {{$errors->first('period_id', 'has-error')}} col-md-3">
                {{Form::label('period_id', 'Period', ['class' => 'control-label']) }}
                {{Form::select('period_id', $periods->pluck('name', 'id'), null, ['class' => 'form-control'])}}
                {!! $errors->first('period_id', '<div class="help-block">:message</div>') !!}
            </article>
        </div>

        <div class="row">
            <div class="col-md-6">
                <label for="recipients-0_name">Send to:</label>

                <table class="table table-bordered table-striped table-condensed mb-0">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody id="recipients">
                        @foreach(old('recipients', []) as $key => $recipient)
                            @include('dashboard._recipient', compact('key'));
                        @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="3" class="text-right">
                            <button class="btn btn-success btn-sm" type="button" id="addRecipient">
                                <i class="fa fa-plus-circle"></i> Add Recipient
                            </button>
                        </td>
                    </tr>
                    </tfoot>
                </table>

                {!! $errors->first('recipients', '<div class="text-danger">:message</div>') !!}

                <template id="recipientTemplate">
                    @include('dashboard._recipient', ['key' => '#']);
                </template>
            </div>
        </div>


        <div class="form-group mt-20">
            <button class="btn btn-primary"><i class="fa fa-send"></i> Send</button>
        </div>
    </form>
@endsection

@section('javascript')
    <script>
        $(function() {
            const template = document.getElementById('recipientTemplate').innerHTML;
            const tbody = $('#recipients');
            let lastIndex = {{count(old('recipients', []))}};

            $('#addRecipient').on('click', function(e) {
                e.preventDefault();

                let newHtml = template.replace(/#/g, lastIndex++);
                tbody.append(newHtml);
            });

            tbody.on('click', '.delete-recipient', function(e) {
                e.preventDefault();
                $(this).closest('tr').remove();
            });
        });
    </script>
@endsection