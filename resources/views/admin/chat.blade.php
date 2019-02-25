<div>
    <img src="{{$headimgurl}}" style="width: 100px;">
    <div>
        <table>
            <thead id="show">
            @foreach($info as $v)
                <tr>
                    <td>{{$nickname}}:</td>
                </tr>
                <tr>
                   <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$v['message']}}</td>
                </tr>
            @endforeach
            </thead>
        </table>
    </div>
    <div style="float: right" id="kefu"></div>
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
                        if(msg=='success'){
                            $('#kefu').append('<p>'+text+':客服</p>');
                            $('#text').val('');
                        }else{
                            alert('时间已超过48小时,您无法主动与用户进行联系');
                        }
                    }
            );
        });
        var ddd = function(){
            var openid = $('#openid').val();
            var _tr = '';
            $.post(
                    "message",
                    {openid:openid},
                    function(msg){
                        for(var i in msg['data']){
                            _tr+="<tr>" +
                                    "<td>" + msg['name']+":</td>" +
                                    "</tr>" +
                                    "<tr>" +
                                    "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" +msg['data'][i]['message']+"</td>" +
                                    "</tr>"
                        }
                        $('#show').html(_tr);
                    },'json'

            );

        }
        var s = setInterval(function(){
            ddd();
        },1000*3)




    })
</script>


