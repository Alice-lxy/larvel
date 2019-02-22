@extends('layout.bst')

@section('content')
    <form action="" method="post">
        {{csrf_field()}}
        <input type="text" name="text">
        <input type="file" name="media">
        <input type="submit" value="SUBMIT">
    </form>
@endsection
