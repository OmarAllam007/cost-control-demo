<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{config('app.name')}}</title>
    <link rel="stylesheet" href="{{asset('/css/print.css')}}">
</head>


<body id="app-layout">

<table>
    <thead>
    <tr>
        <th width="33%">
            <strong>
                AlKifah Contracting Co. <br>
                Project Control Department <br>
                Budget Team <br>
                Project: {{$project->name}} <br>
                {{date('d M Y')}}
            </strong>
        </th>
        <th width="34%" class="header-text text-center">

        </th>
        <th width="33%">
            <img src="{{asset('/images/kcc.png')}}" alt="Logo" class="logo pull-right">
        </th>
    </tr>
    </thead>

    <tbody>
    <tr>
        <td class="col-xs-4">
            <a class="btn btn-success visible-button wbs"  style="width:100%; margin-bottom: 2px; ">WBS (CONTROL
                POINT)</a><br>
            <a class="btn btn-success visible-button std-activity" style="width:100%; margin-bottom: 2px; ">STANDARD
                ACTIVITY</a><br>
            <a class="btn btn-success visible-button productivity"
               style="width:100%;margin-bottom: 2px;">PRODUCTIVITY</a><br>
            <a class="btn btn-success visible-button survey" style="width:100%;margin-bottom: 2px;">QS
                Summary</a><br>
            <a class="btn btn-success visible-button reference" style="width:100%;margin-bottom: 2px;">REFERENCES &
                NOTES</a><br>
            <a class="btn btn-success visible-button boq-price" style="width:100%;margin-bottom: 2px;">BOQ PRICE
                LIST</a><br>
        </td>
        <td class="col-xs-4">
            <a class="btn btn-success visible-button resource-dictionary" style="width:100%; margin-bottom: 2px;background-color: #3e5a20;">RESOURCE
                DICITIONARY</a><br>
            <a class="btn btn-success visible-button high-priority" style="width:100%; margin-bottom: 2px;">High Priority
                Materials</a><br>
            <a class="btn btn-success visible-button budget-number" style="width:100%;margin-bottom: 2px;">Budget Number
                (Manpower)</a><br>
            <a class="btn btn-success visible-button budget-summery" style="width:100%;margin-bottom: 2px;">Budget
                Summary</a><br>
            <a class="btn btn-success visible-button activity-resource" style="width:100%;margin-bottom: 2px;">Activity Resource
                Breakdown</a><br>
            <a class="btn btn-success visible-button revised" style="width:100%;margin-bottom: 2px;">Revised BOQ
            </a><br>
        </td>
        <td class="col-xs-4">
            <a class="btn btn-success visible-button budget-building" style="width:100%; margin-bottom: 2px;">Budget Cost By
                Building</a><br>
            <a class="btn btn-success visible-button cost-discipline" style="width:100%;margin-bottom: 2px;">Budget Cost by
                Discipline</a><br>
            <a class="btn btn-success visible-button cost-break-down" style="width:100%;margin-bottom: 2px;">Budget Cost by
                Item Break Dowm
            </a><br>
            <a class="btn btn-success visible-button cost-dry-building" style="width:100%;margin-bottom: 2px;">Budget Cost v.s
                Dry Cost By Building
            </a><br>
            <a class="btn btn-success visible-button cost-dry-discipline" style="width:100%;margin-bottom: 2px;">Budget Cost v.s
                Dry Cost By Discipline
            </a><br>
            <a class="btn btn-success visible-button cost-dry" style="width:100%;margin-bottom: 2px;">Budget Cost v.s
                Dry Cost QTY & Cost

            </a><br>
        </td>
    </tr>
    </tbody>
</table>

</body>
</html>