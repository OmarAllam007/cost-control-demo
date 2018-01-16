<form action="" class="row">
    <section class="col-sm-6 display-flex">
        <article class="form-group flex">
            <label for="dateInput" class="sr-only"></label>
            <input type="date" name="date" id="dateInput" placeholder="Select date" class="form-control" value="{{$date->format('Y-m-d')}}">
        </article>

        <article class="form-group flex">
            <label for="userInput" class="sr-only"></label>
            <select name="user" id="userInput" class="form-control">
                <option value="">-- All Users --</option>
                @foreach($project_users as $project_user)
                    <option value="{{$project_user->id}}" {{$user_id == $project_user->id? 'selected': ''}}>{{$project_user->name}}</option>
                @endforeach
            </select>
        </article>

        <span class="form-group">
            <button class="btn btn-primary"><i class="fa fa-check"></i> Change</button>
        </span>
    </section>

    <div class="col-sm-6 col-md-4 col-md-offset-2">
        <div class="text-right pagination-top">
            {{$logs->appends('date', $date->format('Y-m-d'))->links()}}
        </div>
    </div>
</form>