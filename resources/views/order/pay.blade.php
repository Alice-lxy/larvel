@extends('layout.bst')
@section('content')
<input type="hidden" value="{{$cd_url}}" id="url">
<input type="hidden" id="order_number" value="{{$order_number}}">
    <div id="code"></div>
@endsection
<script src="{{URL::asset('/js/jquery-3.2.1.min.js')}}"></script>
<script>
    $(function(){
        $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
        var code_url = $('#url').val();
        var order_number = $('#order_number').val();
        //console.log(order_number);
        $("#code").qrcode({
            render: "canvas", //table方式
            width: 200, //宽度
            height:200, //高度
            text:code_url //任意内容
        });
        var success = function(){
            $.post(
                    "/weixin/success",
                    {order_number:order_number},
                    function(msg){
                        console.log(msg);
                    }
            );
        }
        //计时器
        var s = setInterval(function(){
            success();
        },1000*3)


    })
</script>