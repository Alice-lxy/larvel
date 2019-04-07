<h3>欢迎进入个人中心</h3>
<input type="hidden" id="token" value="{{$token}}">
<input type="hidden" id="id" value="{{$uid}}">



<script src="{{URL::asset('/js/jquery-1.12.4.min.js')}}"></script>
<script>
    $(function(){
        alert(111);
        var token = $('#token').val();
        var uid = $('#id').val();
        console.log(token)
        console.log(uid)
    })
</script>

