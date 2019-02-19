@extends('layout.bst')

@section('content')
    <div class="container">
        <h3 align="center">Goods Detail</h3>
        <p>Name:{{$goods->goods_name}}</p>
        <span>Price:{{$goods->price}}</span>
        <form class="form-inline">
            <div class="form-group">
                <label class="sr-only" for="goods_num">Amount (in dollars)</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="goods_num" value="1">
                </div>
            </div>
            <input type="hidden" id="goods_id" value="{{$goods->goods_id}}">
            <button type="submit" class="btn btn-primary" id="add_cart_btn">加入购物车</button>
        </form>
        <a href="/cart">查看购物车</a>
    </div>
@endsection

@section('footer')
    @parent
    <script src="{{URL::asset('/js/goods/goods.js')}}"></script>
@endsection
