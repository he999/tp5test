<?php
namespace app\index\controller;

class ApiTestGoods
{
	public function testAddGoods()
    {
        echo "testAddGoods";
        $api_url = "http://yshop.wiwibao.com/index.php/api/Goods/addGoods";
        $api_data = json_encode(['goods' => ['goods_name' => 'test_goods1' ]]);
        $return = curl_post($api_url, $api_data);
        print_r($return);
    }
}