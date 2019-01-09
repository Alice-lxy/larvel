@extends('layout.bst')

@section('content')
    <form method="post" style="width: 600px; margin-left: 230px;">
        {{csrf_field()}}
        <h2 class="form-signin-heading" style="padding-left: 240px;">Cart Show</h2>
        <table class="table table-bordered">
            <tr>
                <td>Goods ID</td>
                <td>Goods Name</td>
                <td>Goods Price</td>
                <td>Buy Num</td>
                {{--<td>添加时间</td>--}}
                <td>操作</td>
            </tr>
            @foreach($arr as $v)
                <tr>
                    <td>{{$v['goods_id']}}</td>
                    <td>{{$v['goods_name']}}</td>
                    <td>{{$v['price']}}</td>
                    <td>{{$v['num']}}</td>
                    {{--<td>{{$v['add_time']}}</td>--}}
                    <td><a href="/cart/del2/{{$v['goods_id']}}">删除此商品</a></td>
                </tr>
            @endforeach
        </table>
    </form>
@endsection
