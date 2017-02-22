<?php
namespace app\index\controller;

use app\common\model\Cart;


class CartTest
{
	public function test()
    {
        $uid = 25;
        $data = ["spec_id" => 1 , "buy_num" => 3];
		$result = Cart::add($uid,$data);
		var_dump($result);
    }

    
}