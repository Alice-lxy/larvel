<h3>欢迎进入个人中心</h3>
<input type="hidden" id="token" value="{{$token}}">
<input type="hidden" id="id" value="{{$uid}}">



<script src="{{URL::asset('/js/jquery-1.12.4.min.js')}}"></script>
<script>
    $(function(){
        //alert(111);
        var token = $('#token').val();
        var uid = $('#id').val();
        var info = function(){
            $.post(
                    'https://lxy.qianqianya.xyz/pc/token',
                    {token:token,uid:uid},
                    function(data){
                        //alert(data)
                        if(data==2){
                            alert('此账号已有其他用户登录');
                            window.location.href='login.html'
                        }
                    }
            );
        }
        var s = setInterval(function(){
            info();
        },1000*3)
    })
</script>

