<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{asset('css/app.css')}}">
    <title>KPS | Home</title>

    <style>

        html, body  {
            height: 100%;
        }

        body {
            background: url("{{asset('images/login-bg.jpg')}}");
            background-size: cover;
        }

        #app {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            width: 100%;
            height: 100%;
        }

        .content {
            background: rgba(240, 245, 251, 0.9);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 1px 2px 4px 2px rgba(0, 0, 0, 0.08);
        }

        .topbar {
            padding-top: 15px;
            padding-bottom: 15px;
        }

        .user-area {
            padding: 15px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.4);
            box-shadow: 1px 2px 4px 2px rgba(0, 0, 0, 0.08);
        }

        .icons {
            margin-bottom: 50px;
        }

        .icons:last-child {
            margin-bottom: 0;
        }

        .icons a {
            display: flex;
            flex-direction: column;
            align-items: center;
            font-weight: 600;
            height: 100px;
        }

        .icons a span {
            margin-top: auto;
        }

        .icons img {
            margin-bottom: 5px;
        }

        .footer {
            padding: 15px 0;
        }
    </style>
</head>
<body>
<div id="app">
    <div class="topbar">
        <div class="container">
            <div class="col-md-10 col-md-offset-1 col-sm-12 display-flex">
                <div class="logo-line">
                    <img src="{{asset('images/kcc-logo.png')}}" alt="" width="150">
                </div>

                <div class="user-area">
                <strong>

                    {{auth()->user()->name}},

                    <a href="{{url('/logout')}}">Logout</a>
                </strong>
                </div>
            </div>
        </div>

    </div>
    <div class="container">
        <main class="col-md-10 col-md-offset-1 col-sm-12 content">

            <article class="icons row">
                <div class="col-sm-3">
                    <a href="{{url('/dashboard')}}">
                        <img src="{{asset('images/icons/dashboard.svg')}}" alt="" width="64">
                        <span>Dashboard</span>
                    </a>
                </div>


                <div class="col-sm-3">
                    <a href="{{url('/budget')}}">
                        <img src="{{asset('images/icons/budget.svg')}}" alt="" width="64">
                        <span>Budget</span>
                    </a>
                </div>

                <div class="col-sm-3">
                    <a href="{{url('/cost-control')}}">
                        <img src="{{asset('images/icons/cost.svg')}}" alt="" width="64">
                        <span>Cost Control</span>
                    </a>
                </div>

                <div class="col-sm-3">
                    <a href="{{url('/subcontractors')}}">
                        <img src="{{asset('images/icons/subcontractors.svg')}}" alt="" width="64">
                        <span>Subcontractors</span>
                    </a>
                </div>
            </article>
            <article class="icons row">
                <div class="col-sm-3">
                    <a href="{{url('/users')}}">
                        <img src="{{asset('images/icons/users.svg')}}" alt="" width="64">
                        <span>Users</span>
                    </a>
                </div>

                <div class="col-sm-3">
                    <a href="{{url('/master-data')}}">
                        <img src="{{asset('images/icons/masterdata.svg')}}" alt="" width="64">
                        <span>Master Data</span>
                    </a>
                </div>

                <div class="col-sm-3">
                    <a href="{{url('/acknowledgement')}}">
                        <img src="{{asset('images/icons/acknowledgement.svg')}}" alt="" width="64">
                        <span>Acknowledgement</span>
                    </a>
                </div>
                <div class="col-sm-3">
                    <a href="{{url('/reports')}}">
                        <img src="{{asset('images/icons/reports.svg')}}" alt="" width="64">
                        <span>Reports</span>
                    </a>
                </div>
            </article>
        </main>
    </div>
    <div class="container">
        <div class="col-md-10 col-md-offset-1 col-sm-12">
            <div class="footer">
                Copyright &copy; <a href="http://www.alkifah.com.sa">Al-Kifah Holding</a> {{date('Y')}}
            </div>
        </div>
    </div>

</div>
</body>
</html>
