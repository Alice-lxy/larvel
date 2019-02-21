@extends('layout.bst')

@section('content')
    <form action="send" method="post">
        {{csrf_field()}}
        <input type="text" name="text">
        <input type="submit" value="ALLSEND">
    </form>
@endsection
