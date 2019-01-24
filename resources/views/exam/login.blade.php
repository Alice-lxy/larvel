@extends('layout.bst')

@section('content')
    <form action="/exam/login" method="post">
        <h2>Users Login</h2>
        {{csrf_field()}}
        <div class="form-group">
            <label for="exampleInputEmail1">NickName</label>
            <input type="text" class="form-control" name="name" placeholder="Nickname" required>
        </div>
        <div class="form-group">
            <label for="exampleInputEmail1">Pwd</label>
            <input type="password" class="form-control" name="pwd" placeholder="***" required>
        </div>
        <input type="submit" value="Login">
    </form>

@endsection