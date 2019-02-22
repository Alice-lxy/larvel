@extends('layout.bst')

@section('content')
    <form action="" method="post" enctype="multipart/form-data">
        {{csrf_field()}}
        <input type="text" name="text"><br/><br/>
        <input type="file" name="media"><br/>
        <input type="submit" value="SUBMIT">
    </form>
@endsection
