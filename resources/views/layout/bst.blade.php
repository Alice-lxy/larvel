<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-COMPATIBLE" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <title>BootStrap</title>
    <link rel="stylesheet" href="{{URL::asset('/bootstrap/css/bootstrap.min.css')}}">
</head>
<body>
{{--center--}}
<div class="container">
    <!-- Static navbar -->
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="/">首页</a>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
                <ul class="nav navbar-nav">
                    <li class="active"><a href="#">分类1</a></li>
                    <li><a href="#">分类2</a></li>
                    <li><a href="#">分类3</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="/usercenter" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">个人中心<span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="/order/list">我的订单</a></li>
                            <li><a href="/weixin/login">微信登录</a></li>
                            <li><a href="#">待收货</a></li>
                            <li><a href="#">Something else here</a></li>
                            <li role="separator" class="divider"></li>
                            <li class="dropdown-header">Nav header</li>
                            <li><a href="#">Separated link</a></li>
                            <li><a href="#">One more separated link</a></li>
                        </ul>
                    </li>
                    @if($login!=1)
                        <li><a href="/userquit">退出</a></li>
                    @else
                        <li><a href="http://passport.larvel.com/userreg">注册</a></li>
                        <li><a href="http://passport.larvel.com/userlogin">登录</a></li>
                    @endif{{--?rediret={{$current_url}}--}}
                </ul>
            </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
    </nav>
    @yield('content')
</div>
{{--<div class="container">
    @yield('content')
</div>--}}

{{--bottom--}}
@section('footer')
    <script src="{{URL::asset('/js/jquery-1.12.4.min.js')}}"></script>
    <script src="{{URL::asset('/bootstrap/js/bootstrap.min.js')}}"></script>
    <script src="{{URL::asset('/bootstrap/js/jquery.qrcode.min.js')}}"></script>
@show
</body>
</html>