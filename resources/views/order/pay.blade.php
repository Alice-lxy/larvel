@extends('layout.bst')
@section('content')
<input type="hidden" value="{{$cd_url}}" id="url">
    <div id="code"></div>
@endsection

<script src="{{URL::asset('/js/jquery-3.2.1.min.js')}}"></script>
<script src="{{URL::asset('/bootstrap/js/jquery.qrcode.min.js')}}"></script>
<script>
    $(function(){
        var code_url = $('#url').val();
        $("#code").qrcode({
            render: "canvas", //table方式
            width: 200, //宽度
            height:200, //高度
            text:code_url //任意内容
        });
    })

</script>