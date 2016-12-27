<?php
namespace app\index\controller;

use app\common\model\Goods;
use app\common\model\GoodsImages;
use app\common\model\GoodsSpecs;

class GoodsTest
{
    //完整添加
    public function testadd()
    {
        $spec_info[]=[
                'spec_1' => 'spec_1',
                'spec_2' => 'spec_2',
                'spec_3' => 'spec_3',
                'price' => 335,
                'stock' => 2,
                'spec_id' => 7];
        $result = GoodsSpecs::edit($spec_info);
        var_dump($result);
         // $data['goods']=['goods_name' => 'sadasfa'];       
        /*
        $data['goods']=['goods_name' => 'sadasfa',
                        'original_img' => '\static\images\ondata.jpg',
                        'cover_img' => '\static\images\ondata.jpg',
                        'shop_price' => '325',
                        'goods_sn' => '23453456',
                        'spec1_name' => 'a',
                        'spec2_name' => 'b',
                        'spec3_name' => 'c',
                        'weight' => '34',
                        'stock' => '44'
                        ];
        */
//         $data['images'][] = ['image_name'=>'99999999999999','image_url' => '/uploads/123.jpg','create_time'=>time(),'update_time'=>time()]; 
//         $data['images'][] = ['image_name'=>'888888888888','image_url' => '/uploads/123.jpg','create_time'=>time(),'update_time'=>time()];
// $data['specs'][] = ['price'=>'333','spec_1' => 'aaaaaaaa','spec_2' => 'bai','spec_3' => 'hong','color' => '绿'];
// $data['specs'][] = ['price'=>'135','spec_1' => 'oooooooooo','spec_2' => 'bai','spec_3' => 'hei','color' => '蓝'];
        
        // $result = Goods::addGoods($data);
        // var_dump($result);
    }
    public function testadd1()
    {
        $data['goods']=['goods_name' => 'sadasfa',
                        'original_img' => 'fafagafa',
                        'shop_price' => '325',
                        'goods_sn' => '23453456',
                        'spec1_name' => 'a',
                        'spec2_name' => 'b',
                        'spec3_name' => 'c',
                        'weight' => '34'
                        ];
        $data['images'][] = ['image_name'=>'99999999999999','image_url' => '888888888888','create_time'=>time(),'update_time'=>time()]; 
        $data['images'][] = ['image_name'=>'888888888888','image_url' => 'qwertyuiopmnbvc','create_time'=>time(),'update_time'=>time()];
        $data['specs'][] = ['price'=>'','spec_1' => '','spec_2' => '','spec_3' => '','color' => ''];
        
        $result = Goods::addGoods($data);
        var_dump($result);
    }
	//添加
	public function test()
    {
        $data=['goods_id' => 1,'goods_name' => 22];
		$result = Goods::add($data);
		var_dump($result);
    }
    
    //编辑
    public function test1()
    {
        $goods_id = 1;   
        $data=array("goods_id"=>"1","weight"=>"37");
    	$result = Goods::edit($goods_id, $data);
    	var_dump($result);
    }
    
    //删除
    public function testdel()
    {
        $goods_id = 1;
    	$result = Goods::del($goods_id);
    	var_dump($result);
    }

    //查看一条
    public function test3()
    {   
        $goods_id = 1;
        $result = Goods::getOne($goods_id);
        dump($result);
    }

    //列表
    public function testl()
    {
        $com_id =1;
        $result = Goods::getList();
        var_dump($result);
    }
    //得到热门产品
    public function testl1()
    {
        $com_id =1;
        $result = Goods::getHot(4, $com_id =1);
        var_dump($result);
    }
    //最新产品
    public function testl2()
    {
        $com_id =1;
        $result = Goods::getNew(4, $com_id =1);
        var_dump($result);
    }
    //推荐
    public function testl3()
    {
        $com_id =1;
        $result = Goods::getRecommend(4, $com_id =1);
        var_dump($result);
    }




    //图片添加
    public function test4()
    {
        $goods_id = 1;   
        $data = [ ['image_name'=>'99999999999999','image_url' => '888888888888'] , ['image_name'=>'888888888888','image_url' => 'qwertyuiopmnbvc'] ];
        $result = GoodsImages::add(1, $data);
        print_r($result);         
    }
    
    //图片编辑
    public function test5()
    {   
        $data = [["img_id" => '47', "image_name" => "aaaaaaaaaaaaaaa", "image_url"=>"37" ],["img_id" => '48', "image_name" => "oooooooooooo", "image_url"=>"37" ] ];
        $result = GoodsImages::edit($data);
        print_r($result);
    }

    //图片删除
     public function test6()
    {
        
        $data=["img_id"=>"2"];
        $result = GoodsImages::del($data);
        var_dump($result);
    }

    //查看一条
    public function test7()
    {   
        $goods_id = 2;
        $result = GoodsImages::getList($goods_id);
        var_dump($result);
    }





    //商品属性添加
    public function test8()
    {   
        $goods_id = 3;
        $data = [['goods_id' => $goods_id,'spec_1' => 'bai'], ['goods_id' => $goods_id,'spec_1' => 'hei']];
        $result = GoodsSpecs::add($goods_id,$data);
        var_dump($result);
    }

    //商品属性编辑
    public function test9()
    {   
        $data=[
                ['spec_id' => '8', 'goods_id'=>'3', 'spec_1' => 'aaaaaaaa','spec_2' => 'bai','spec_3' => 'hong','color' => '绿'],
                ['spec_id' => '9', 'goods_id'=>'4', 'spec_1' => 'oooooooooo','spec_2' => 'bai','spec_3' => 'hei','color' => '绿'],
              ];
        $result = GoodsSpecs::edit($data);
        var_dump($result);
    }

    //商品属性删除
     public function test10()
    {
        $data=['goods_id' => 3];
        $result = GoodsSpecs::del($data);
        var_dump($result);
    }

    //查看商品属性
    public function test11()
    {   
        $goods_id = 3;
        $result = GoodsSpecs::getSpecs($goods_id);
        var_dump($result);
    }

    
}