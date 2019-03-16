@extends('layout.bst')

@section('content')
    <form action="/fasong" method="post">
        {{csrf_field()}}
        <textarea name="message" id="" cols="30" rows="10"></textarea>
        <input type="submit" value="发送">
    </form>
@endsection