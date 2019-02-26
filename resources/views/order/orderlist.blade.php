@extends('layout.bst')
@section('content')
    <form method="post" style="width: 750px; margin-left: 230px;">
    <h2 class="form-signin-heading" style="padding-left: 160px;">Not Paid Order Show</h2>
    {{csrf_field()}}
    <table class="table table-bordered">
        <tr align="center">
            <td>订单ID</td>
            <td>订单号</td>
            <td>订单总价</td>
            <td>订单时间</td>
            <td>操作</td>
        </tr>
        @foreach($order_data as $v)
            <tr align="center">
                <td>{{$v['id']}}</td>
                <td>{{$v['order_number']}}</td>
                <td>￥{{$v['order_amount']/100}}</td>
                <td>{{date('Y-m-d H:i:s',$v['add_time'])}}</td>
                <td>   @if($v['order_status']==1)
                        <a href="/pay/{{$v['order_number']}}">支付宝支付</a>  ||
                        <a href="/weixin/notice/test/{{$v['order_number']}}">微信支付</a> ||
                        <a href="/order/del/{{$v['order_number']}}">取消订单</a>
                    @elseif($v['order_status']==2)
                        <a href="javascript:;">查看物流</a>  ||
                        <a href="/order/del/{{$v['order_number']}}">联系客服</a>
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
    </form>
@endsection