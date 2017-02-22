<?php
namespace app\index\controller;

use app\common\model\Shipping;


class ShippingTest
{
	//添加
	public function test()
    {
        $data=['shipping_id' => 1,'shipping_name' => 22];
		$result = Shipping::add($data);
		var_dump($result);
    }

    //编辑
    public function test1()
    {
        $shipping_id = 1;   
        $data=array("shipping_id"=>"1","shipping_name"=>"37");
    	$result = Shipping::edit($data,$shipping_id);
    	var_dump($result);
    }

    //查看一条
    public function test2()
    {   
        $shipping_id = 1;
        $result = Shipping::getOne($shipping_id);
        var_dump($result);
    }

    //查看列表
    public function test3()
    {   
        $result = Shipping::getShippingList();
        var_dump($result);
    }

    //查看地区运输方式
    public function test4()
    {   
        $region_id = 4;
        $result = Shipping::getShipping($region_id);
        var_dump($result);
    }

    //查看运输
    public function test5()
    {   
        $region_id = 1;
        $weight = 45;
        $result = Shipping::getShippingPrice($weight,$region_id);
        var_dump($result);
    }
}