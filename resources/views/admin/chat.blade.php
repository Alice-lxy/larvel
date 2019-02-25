<div>
    <img src="{{$headimgurl}}" style="width: 100px;">
    <form style="padding-top: 300px;">
        {{csrf_field()}}
        <input type="hidden" name="openid" id="openid" value="{{$openid}}">
        <textarea name="text" id="text" cols="200" rows="5"></textarea>
        <input type="button" id="btn" value="SUBMIT">
    </form>
</div>

<script src="{{URL::asset('/js/jquery-3.2.1.min.js')}}"></script>
<script>
    $(function(){

        $('#btn').click(function(){
            var text = $('#text').val();
            var openid = $('#openid').val();
            $.post(
                     "weixinchart",
                    {text:text,openid:openid},
                    function(msg){
                        console.log(msg)
                    }
            );
        })
    })
</script>


