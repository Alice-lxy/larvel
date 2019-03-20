<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
    <form action="/curl/dologin" method="post" style="width: 600px; margin-left: 230px;">
       {{-- {{csrf_field()}}--}}
        <h2 class="form-signin-heading" style="padding-left: 160px;">User Login</h2>
        <div class="form-group" style="padding-bottom: 15px;">
            <label for="exampleInputEmail1">NickName</label>
            <input type="text" class="form-control" name="name" placeholder="Nickname" required>
        </div>
        <div class="form-group" style="padding-bottom: 15px;">
            <label for="exampleInputPassword1">Password</label>
            <input type="password" class="form-control" name="pwd" placeholder="***" required>
        </div>
        <button type="submit" class="btn btn-default">Login</button>
        <div class="form-group" style="padding-left: 50px; padding-top: 15px;">
            <a href='/curl/reg'>注册账号?</a> <span>|</span>
            <a href="javascript:;">忘记密码?</a>
        </div>
    </form>
</body>
</html>