<?php
namespace app\common\model;

use think\Model;
use think\Db;

/**
 * 订单商品模型
 * add 定单添加商品
 * edit 定单编辑商品
 * getList 得到一个订单下的商品列表
 */
class OrdersGoods extends Model
{
    /**
     * add 定单添加商品
     * @xiao
     * @DateTime 2016-09-06T06:19:16+0800
     * @param    int        $order_id 订单id
     * @param    array      $data 添加数组
     * @return   array      [error_code, error_msg, data=>[商品订单号,]]
     */
    static public function add($order_id, $data)
    {
        $result = [];
        if ($data)
        {
            foreach($data as $key => $value)
            {   
                $add = array_merge($value,['order_id' => $order_id]);
                $goodsid['goods_id'] = $value['goods_id'];
                
                $row = Db::name('orders_goods')->insertGetId($add);
                Db::name('goods')->where($goodsid)->setInc('sales_sum',$value['buy_num']);
                if ($row)
                {
                    $result['error_code'] = 0;
                    $result['error_msg'] = '';
                    $result['data'][] = $row;
                }
                else
                {
                    $result['error_code'] = 1;
                    $result['error_msg'] = '定单添加商品失败';
                }
            }
        }
        return $result;

    }

    /**
     * edit 定单编辑商品
     * @karl
     * @DateTime 2016-09-06T06:19:16+0800
     * @param    array                 $data 编辑数据 [商品订单号=>[],]
     * @return   array                 [error_code, error_msg]                  
     */
    static public function edit($data)
    {
        foreach ($data as $key => $value) 
        {
            $where = ['id' => $value['id']];
            if($update = Db::name('orders_goods')->where($where)->update($value))
            {
                $result['error_code'] = 0;
                $result['error_msg'] = '';
            }
            else
            {
                $result['error_code'] = 1;
                $result['error_msg'] = '订单编辑商品失败';
            }
        } 
        return $result;

    }

    /**
     * editOne 定单编辑商品
     * @karl
     * @DateTime 2016-09-06T06:19:16+0800
     * @param    array                 $data 编辑数据 [商品订单号=>[],]
     * @return   array                 [error_code, error_msg]                  
     */
    static public function editOne($id,$data)
    {
        $where = ['id' => $id];
        if($update = Db::name('orders_goods')->where($where)->update($data))
        {
            $result['error_code'] = 0;
            $result['error_msg'] = '';
        }
        else
        {
            $result['error_code'] = 1;
            $result['error_msg'] = '订单编辑商品失败';
        }
        return $result;

    }
    
    /**
     * getList 得到一个订单下的商品列表
     * @karl
     * @DateTime 2016-09-28T22:41:15+0800
     * @param    int                $order_id 定单id
     * @return   array              [error_code, error_msg, data=> [] ]
     */
    static public function getList($order_id)
    {
        if ($order_id)
        {
            $where = array('order_id' => $order_id);
            if($data = Db::name('orders_goods')->where($where)->select())
            {
                $result['error_code'] = 0;
                $result['error_msg'] = '';
                $result['data'] = $data;
            }
            else
            {
                $result['error_code'] = 1;
                $result['error_msg'] = '没有得到一个订单下的商品列表';
            }
            
        }
            return $result;
    }

}

?>