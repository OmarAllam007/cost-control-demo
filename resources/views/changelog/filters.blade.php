<form action="" class="row">
    <section class="col-md-4 col-sm-6">
        <article class="form-group input-group">
            <label for="dateInput" class="sr-only"></label>
            <input type="date" name="date" id="dateInput" placeholder="Select date" class="form-control" value="{{$date->format('Y-m-d')}}">

            <span class="input-group-btn">
                <button class="btn btn-primary"><i class="fa fa-check"></i> Change</button>
            </span>
        </article>
    </section>

    <div class="col-sm-6 col-md-4 col-md-offset-4">
        <div class="text-right pagination-top">
            {{$logs->appends('date', $date->format('Y-m-d'))->links()}}
        </div>
    </div>
</form>