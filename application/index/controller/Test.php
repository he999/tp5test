<?php
namespace app\index\controller;

use app\common\model\Goods;
use app\common\model\GoodsImages;
use app\common\model\GoodsSpecs;
use app\common\model\OrdersGoods;
use app\common\model\Payments;
use app\common\model\Orders;
use app\common\model\OrdersActions;
use app\common\model\Customers;
use app\common\model\Point;
use app\common\model\Activity;
use app\common\model\base\Auth;
use app\common\model\base\Users;
use app\common\model\base\CommonModel;
use app\common\model\base\UsersWeixin;
use app\common\model\UsersParent;
use app\common\model\weixin\DiyMenu;
use think\Controller;
use think\Model;


class Test extends controller
{   

    
    public function test(){
        $add['parent'] = 2;
                $add['uid'] = 4;
                $add['weixin_code'] = Users::maxWeixinCode()['count']+1;
        $res = Users::addParent($add);
        dump($res);
    }


    // public function test(){
    //     $res = Users::maxWeixinCode()['max(weixin_code)'];
    //     dump($res);
    // }

    // public function test(){
    //     $res = DiyMenu::createQRCode(2);
    //     dump($res);
    // }

    // public function test(){
    //     $uid[] = ['uid'=>15];
    //     $res = UsersParent::getChildren($uid,3);
    //     dump($res);
    // }

    // public function testUserWeixin()
    // {
    //     UsersWeixin::init();
    //     UsersWeixin::add(['uid' => 1, 'nickname' => 'ccc']);
    // }

    // public function testCommonModel()
    // {

    //     CommonModel::setTable("abc");
    //     CommonModel::add("");
    // }
    //添加
	// public function xx()
 //    {
   
 //        echo "string";
 //    }
    
    //编辑
    // public function test1()
    // {
    //     $goods_id = 1;   
    //     $data=array("goods_id"=>"1","goods_name"=>"37");
    // 	$result = Goods::edit($goods_id, $data);
    // 	var_dump($result);die;
    // 	return $result;
    // }
    
    // //删除
    // public function test2()
    // {
    //     $goods_id = 1;
    // 	$result = Goods::del($goods_id);
    // 	var_dump($result);die;
    // 	return $result;
    // }

    // //查看一条
    // public function test3()
    // {   
    //     $goods_id = 1;
    //     $result = Goods::getOne($goods_id);
    //     var_dump($result);die;
    //     return $result;
    // }

    // //得到热门产品
    // public function testa()
    // {
    //     $com_id =1;
    //     $result = Goods::getHot(4, $com_id =1);
    //     var_dump($result);die;
    //     return $result;
    // }
    // //最新产品
    // public function testb()
    // {
    //     $com_id =1;
    //     $result = Goods::getNew(4, $com_id =1);
    //     var_dump($result);die;
    //     return $result;
    // }
    // //是否推荐
    // public function testc()
    // {
    //     $com_id =1;
    //     $result = Goods::getRecommend(4, $com_id =1);
    //     var_dump($result);die;
    //     return $result;
    // }

    // //图片添加
    // public function test4()
    // {
    //     $goods_id = 1;   
    //     $data = [ ['image_name'=>'99999999999999','image_url' => '888888888888'] , ['image_name'=>'888888888888','image_url' => 'qwertyuiopmnbvc'] ];
    //     $result = GoodsImages::add(1, $data);
    //     print_r($result);die;         
    // }
    
    // //图片编辑
    // public function test5()
    // {   
    //     $data = [["img_id" => '47', "image_name" => "aaaaaaaaaaaaaaa", "image_url"=>"37" ],["img_id" => '48', "image_name" => "oooooooooooo", "image_url"=>"37" ] ];
    //     $result = GoodsImages::edit($data);
    //     print_r($result);die;
    //     return $result;
    // }

    // //图片删除
    //  public function test6()
    // {
        
    //     $data=["img_id"=>"2"];
    //     $result = GoodsImages::del($data);
    //     var_dump($result);die;
    //     return $result;
    // }

    // //查看一条
    // public function test7()
    // {   
    //     $goods_id = 2;
    //     $result = GoodsImages::getList($goods_id);
    //     var_dump($result);die;
    //     return $result;
    // }


    // //商品属性添加
    // public function test8()
    // {   
    //     $goods_id = 3;
    //     $data = [['goods_id' => $goods_id,'spec_1' => 'bai'], ['goods_id' => $goods_id,'spec_1' => 'hei']];
    //     $result = GoodsSpecs::add($goods_id,$data);
    //     var_dump($result);die;
    //     return $result;
    // }

    // //商品属性编辑
    // public function test9()
    // {   
    //     $data=[
    //             ['spec_id' => '8', 'goods_id'=>'3', 'spec_1' => 'aaaaaaaa','spec_2' => 'bai','spec_3' => 'hong','color' => '绿'],
    //             ['spec_id' => '9', 'goods_id'=>'4', 'spec_1' => 'oooooooooo','spec_2' => 'bai','spec_3' => 'hei','color' => '绿'],
    //           ];
    //     $result = GoodsSpecs::edit($data);
    //     var_dump($result);die;
    //     return $result;
    // }

    // //商品属性删除
    //  public function test10()
    // {
    //     $data=['goods_id' => 3];
    //     $result = GoodsSpecs::del($data);
    //     var_dump($result);die;
    //     return $result;
    // }

    // //查看商品属性
    // public function test11()
    // {   
    //     $goods_id = 3;
    //     $result = GoodsSpecs::getSpecs($goods_id);
    //     var_dump($result);die;
    //     return $result;
    // }

    // //订单添加
    // public function test12()
    // {   
    //     $data = [
    //                 'uid' => '1',
    //                 'order_sn' => '11234543', 
    //                 'com_id' => '2', 
    //                 'is_pay' => '3'
    //             ];
    //     $result = Orders::add($data);
    //     var_dump($result);die;
    //     return $result;
    // }

    // //订单编辑
    // public function test13()
    // {   
    //     $order_id = 1;
    //     $data=[
    //             'order_sn' => '987654',
    //             'uid' => '2',
    //             'com_id' => '3',
    //             'is_pay' => '1'
    //           ];
    //     $result = Orders::edit($order_id, $data);
    //     var_dump($result);die;
    //     return $result;
    // }

    // //查看一条订单
    // public function test14()
    // {   
    //     $order_id = 1;
    //     $result = Orders::getOne($order_id);
    //     var_dump($result);die;
    //     return $result;
    // }

    // //添加订单操作
    // public function test15()
    // {
    //     $order_id = 1;   
    //     $data = [
    //                 'order_id' => '2',
    //                 'action_user' => '1234554',
    //                 'order_status' => '1', 
    //                 'order_status' => '2', 
    //                 'shipping_status' => '3'
    //             ];
    //     $result = OrdersActions::add($order_id,$data);
    //     var_dump($result);die;
    //     return $result;
    // }

    // //得到订单列表
    // public function test16()
    // {   
    //     $order_id = 2;
    //     $result = OrdersActions::getList($order_id);
    //     var_dump($result);die;
    //     return $result;
    // }


    // //定单添加商品
    // public function test17()
    // {   
    //     $order_id = 1;
    //     $data = [
    //                 ['order_id' => $order_id,'goods_name' => 'xiaoming','buy_num' => '1223456543'], 
    //                 ['order_id' => $order_id,'goods_name' => 'mingxiao','buy_num' => '1223456543']
    //             ];
    //     $result = OrdersGoods::add($order_id,$data);
    //     var_dump($result);die;
    //     return $result;
    // }

    // //定单编辑商品
    // public function test18()
    // {   
    //     $data=[
    //             ['id' => '1', 'order_id'=>'2', 'goods_name' => 'aaaaaaaa','buy_num' => '1233'],
    //             ['id' => '2', 'order_id'=>'2', 'goods_name' => 'oooooooooo','buy_num' => '5433']
    //           ];
    //     $result = OrdersGoods::edit($data);
    //     var_dump($result);die;
    //     return $result;
    // }


    // //得到订单列表
    // public function test19()
    // {   
    //     $order_id = 1;
    //     $result = OrdersGoods::getList($order_id);
    //     var_dump($result);die;
    //     return $result;
    // }

    // //得到支付方式
    // public function test20()
    // {
    //     $com_id = 1;
    //     $result = Payments::getPayments($com_id);
    //     var_dump($result);die;
    //     return $result;

    // }

    // //得到支付方式详情
    // public function test21()
    // {
    //     $pay_id = 1;
    //     $result = Payments::getOne($pay_id);
    //     var_dump($result);die;
    //     return $result;

    // }

    // //得到一条客户信息
    // public function test22()
    // {
    //     $uid = 1;
    //     $result = Customers::getOne($uid);
    //     var_dump($result);die;
    //     return $result;
    // }

    // //添加客户信息
    // public function test23()
    // {
       
    //     $data=[
    //             'nickname' =>'hh', 
    //             'face' =>'1', 
    //             'sex' =>'1', 
    //             'moblie' =>'1234565432', 
    //             'email' =>'123@qq.com'
    //           ];
    //     $result = Customers::add($data);
    //     var_dump($result);die;
    //     return $result;
    // }
    // //编辑客户信息
    // public function test24()
    // {
    //     $uid = 1;
    //     $data=[
    //             'nickname' =>'qq', 
    //             'face' =>'pp', 
    //             'sex' =>'0', 
    //             'moblie' =>'9876456778', 
    //             'email' =>'00987566@qq.com',
    //           ];
    //     $result = Customers::edit($uid,$data);
    //     var_dump($result);die;
    //     return $result;
    // }
    
}
 