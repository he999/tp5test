<?php
namespace app\index\controller;

use app\common\model\OrdersGoods;
use app\common\model\Orders;
use app\common\model\OrdersActions;

class OrdersTest
{
    //订单添加
    public function testadd()
    {   
        $uid = 25;
        $cod['shipping_com_id'] = 1;
        $cod['pay_code'] = 1;
        $result = Orders::addCartOrder($uid,$cod);
        dump($result);
    }
	//订单添加
    public function test()
    {   
        $where['is_debit'] = 1;
        $where['uid'] = 25;
        $where['order_status'] = ['LT',3];
        $book = Orders::getList($where);
        dump($book['data']);
        $sum = 0;
        foreach ($book['data'] as $key => $value) {
            foreach ($value['data'] as $k => $v) {
                $sum += $v['buy_num'];
            }
        }
        echo $sum;
    }

    //订单编辑
    public function test1()
    {   
        $order_id = 1;
        $data=[
			'order_sn' => '987654',
			'uid' => '2',
			'com_id' => '3',
			'is_pay' => '1'
        ];
        $result = Orders::edit($order_id, $data);
        var_dump($result);
    }

    //查看一条订单
    public function test2()
    {   
        $order_id = 1;
        $result = Orders::getOne($order_id);
        var_dump($result);
    }

    //添加订单操作
    public function test3()
    {
        $order_id = 1;   
        $data = [
			'order_id' => '2',
			'action_user' => '1234554',
			'order_status' => '1', 
			'order_status' => '2', 
			'shipping_status' => '3'
		];
        $result = OrdersActions::add($order_id,$data);
        var_dump($result);
    }

    //得到订单列表
    public function test4()
    {   
        $order_id = 2;
        $result = OrdersActions::getList($order_id);
        var_dump($result);
    }

    //定单添加商品
    public function test5()
    {   
        $order_id = 1;
        $data = [
			['order_id' => $order_id,'goods_name' => 'xiaoming','buy_num' => '1223456543'], 
			['order_id' => $order_id,'goods_name' => 'mingxiao','buy_num' => '1223456543']
		];
        $result = OrdersGoods::add($order_id,$data);
        var_dump($result);
    }

    //定单编辑商品
    public function test6()
    {   
        $data=[
			['id' => '1', 'order_id'=>'2', 'goods_name' => 'aaaaaaaa','buy_num' => '1233'],
			['id' => '2', 'order_id'=>'2', 'goods_name' => 'oooooooooo','buy_num' => '5433']
	    ];
        $result = OrdersGoods::edit($data);
        var_dump($result);
    }

    //得到订单列表
    public function test7()
    {   
        $order_id = 1;
        $result = OrdersGoods::getList($order_id);
        var_dump($result);
    }

}