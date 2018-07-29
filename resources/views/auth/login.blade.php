<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Al-Kifah Contracting &mdash; KPS</title>
    <link rel="stylesheet" href="{{asset('css/app.css')}}">
</head>

<style>
    html, body {
        background: url({{asset('images/login-bg.jpg')}});
        background-size: cover;
        height: 100%;
    }

    /*.app {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .wrapper {
        height: 100%;
        width: 100%;
    }*/

    .app {
        height: 100%;
    }

    .container-fluid {
        height: 100%;
    }
    form {
        background: rgba(255, 255, 255, 0.6);
        border-radius: 30px;
        margin-top: 70px;
        width: 60%;
        padding: 20px;
    }

    @media (min-width: 1024px) {
        form {
            width: 50%;
        }
    }

    @media (max-width: 700px) {
        form {
            width: 80%;
        }
    }

    .credits {
        padding: 15px;
        margin-top: auto;
    }

    .logo-box {
        margin-bottom: 40px;
    }
</style>
<body>
<div class="app">
    <div class="container-fluid display-flex-c">

        <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
            {{ csrf_field() }}

            <div class="logo-box text-center">
                <img src="{{asset('images/kcc-logo.png')}}" alt="" width="200">
            </div>

            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                <label for="email" class="col-md-3 control-label">Email</label>

                <div class="col-md-7">
                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}"
                           autofocus>

                    @if ($errors->has('email'))
                        <span class="help-block">
                <strong>{{ $errors->first('email') }}</strong>
            </span>
                    @endif
                </div>
            </div>

            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                <label for="password" class="col-md-3 control-label">Password</label>

                <div class="col-md-7">
                    <input id="password" type="password" class="form-control" name="password">

                    @if ($errors->has('password'))
                        <span class="help-block">
                <strong>{{ $errors->first('password') }}</strong>
            </span>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-6 col-md-offset-3">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="remember"> Remember Me
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-7 col-sm-offset-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-btn fa-sign-in"></i> Login
                    </button>

                    <a href="{{url('/auth/google')}}" class="btn btn-danger"><i class="fa fa-google"></i> Sign in with Google</a>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-6 col-sm-offset-3 display-flex">
                <a href="{{ url('/password/reset') }}">Forgot Your Password?</a>
                </div>
            </div>
        </form>

        <div class="credits text-center col-sm-6">
            <strong>Copyright &copy; <a href="http://www.alkifah.com.sa">Al-Kifah Holding</a> &mdash; {{date('Y')}}</strong>
        </div>
    </div>
</div>

</body>
</html>





