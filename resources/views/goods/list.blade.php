@extends('layout.bst')

@section('content')
    <form action="/userlogin" method="post" style="width: 600px; margin-left: 230px;">
        {{csrf_field()}}
        <h2 class="form-signin-heading" style="padding-left: 240px;">List Show</h2>
        <table class="table table-bordered">
            <tr>
                <td>商品ID</td>
                <td>商品名称</td>
                <td>商品库存</td>
                <td>商品价格</td>
                <td>操作</td>
            </tr>
            @foreach($arr as $v)
            <tr>
                <td>{{$v['goods_id']}}</td>
                <td>{{$v['goods_name']}}</td>
                <td>{{$v['store']}}</td>
                <td>{{$v['price']}}</td>
                <td><a href="/goods/detail/{{$v['goods_id']}}">查看</a></td>
            </tr>
            @endforeach
        </table>
        {{--<a href="/cart">购物车页面</a>--}}
    </form>

@endsection
