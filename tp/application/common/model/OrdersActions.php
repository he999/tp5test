<?php
namespace app\common\model;

use think\Model;
use think\Db;

/**
 * 订单操作模型
 * add 添加定单操作
 * getList 得到订单操作列表
 */
class OrdersActions extends Model
{
    /**
     * add 添加定单操作
     * @xiao
     * @DateTime 2016-09-06T06:19:16+0800
     * @param    int        $order_id 订单id
     * @param    array      $data 添加数组
     * @return   array      [error_code, error_msg, data=>[商品订单号,]]
     */
    static public function add($order_id, $data)
    {   
        $data['order_id'] = $order_id;
        $row = DB::name('orders_actions')->insertGetId($data);
        if ($row)
        {
           $arr['error_code'] = 0;
           $arr['error_msg'] = "";
           $arr['order_id'] = $row;
        }
        else
        {
           $arr['error_code'] = 1;
           $arr['error_msg'] = "订单操作添加失败";
        } 
        return $arr;

    }
    
    /**
     * getList 得到订单操作列表
     * @karl
     * @DateTime 2016-09-28T22:41:15+0800
     * @param    int                $order_id 定单id
     * @return   array              [error_code, error_msg, data=> [] ]
     */
    static public function getList($order_id)
    {
        $where = array('order_id' => $order_id);
        if($data = Db::name('orders_actions')->where($where)->select())
        {
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['data'] = $data;
        }
        else
        {
            $result['error_code'] = 1;
            $result['error_msg'] = '没有得到订单操作列表';
        }
        return $result;
    }

}

?>