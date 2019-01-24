@extends('layout.bst')

@section('content')
    <form action="/exam/pwd" method="post">
        <h2>Update Pwd</h2>
        {{csrf_field()}}
        <div class="form-group">
            <label for="exampleInputEmail1">用户名:</label>
            <input type="text" class="form-control" name="name" placeholder="用户名" required>
        </div>
        <div class="form-group">
            <label for="exampleInputEmail1">新密码:</label>
            <input type="password" class="form-control" name="pwd1" placeholder="***" required>
        </div>
        <div class="form-group">
            <label for="exampleInputEmail1">确认密码:</label>
            <input type="password" class="form-control" name="pwd2" placeholder="***" required>
        </div>
        <input type="submit" value="UpdPwd">
    </form>

@endsection