@extends('layouts.app')

@section('header')
    <div class="display-flex">
        <h2 class="flex">{{$project->name}} &mdash; Cost Issues</h2>
        <div class="btn-toolbar">
            <a href="/project/{{$project->id}}/issue-files/create" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Upload file</a>
            <a href="/project/cost-control/{{$project->id}}" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i> Back</a>
        </div>
    </div>
@endsection

@section('body')
   @if ($issueFiles->count())
       <table class="table table-bordered table-striped table-hover">
           <thead>
           <th>Subject</th>
           <th>Period</th>
           <th>Actions</th>
           </thead>
           <tbody>
           <tr>
               @foreach($issueFiles as $file)
               <td>{{$file->subject}}</td>
               <td>{{$file->period->name}}</td>
               <td class="col-sm-3">
                   <a href="{{$file->url()}}" class="btn btn-info btn-sm"><i class="fa fa-download"></i> Download</a>
                   @if (can('cost_owner', $project) || $file->user_id == auth()->id())
                       <form action="{{$file->url()}}" method="post" style="display: inline;">
                           {{csrf_field()}} {{method_field('delete')}}
                           <a href="{{$file->url()}}/edit" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Edit</a>
                           <button href="" class="btn btn-warning btn-sm"><i class="fa fa-trash"></i> Delete</button>
                       </form>
                   @endif
               </td>
               @endforeach
           </tr>
           </tbody>
       </table>

       {{$issueFiles->links()}}
   @else
       <div class="alert alert-info">No files found</div>
   @endif


@endsection