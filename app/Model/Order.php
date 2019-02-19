<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    public $table = 'order';
    public $timestamps = false;

    /**
     * 2019年1月10日08:29:49
     * 生成订单号
     */
    public static function generateOrderSN()
    {
        return date('Ymdhis') . rand(11111,99999) . rand(2222,9999);
    }

}
