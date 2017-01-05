<?php
namespace app\common\model;

use think\Model;
use think\Db;
use app\common\model\base\UsersVoucher;
/**
 * 订单模型
 * add 添加定单
 * edit 编辑定单
 * getOne 得到一个订单
 */
class Orders extends Model
{
    /**
     * addCartOrder 将用户购物车中商品生成定单，并清空购物车
     * @xiao
     * @DateTime 2016-11-16T13:24:54+0800
     * @param    int                $uid 用户id
     * * @param  arr                $spid ['shipping_com_id'=>'','pay_id'=>'']
     * @return   array              [error_code, error_msg, order_id]
     */
    static public function addCartOrder($uid,$spid)
    {   
        $shop_price = 0;
        $byunm = 0;
        $specdata = array();
        $goods = array();
        $cart = Db::name('cart')
        ->alias('c')
        ->join('goods g','g.goods_id = c.goods_id','left')
        ->field("c.goods_id,c.goods_name,c.shop_price,c.buy_num,c.goods_sn,c.spec_id,c.spec_value,c.market_price,g.cover_img")
        ->where(['c.uid' => $uid,'c.selected'=>1])
        ->select();
        if ($cart) {
            //计算 购物车 总价 重量 商品spec_id与buy_num
            foreach ($cart as $v) {
                $shop_price += $v['shop_price']*$v['buy_num'];
                $byunm += $v['buy_num'];
                $specdata[] = ['spec_id' => $v['spec_id'],'buy_num' => $v['buy_num']];
                $goods[$v['spec_id']] = $v['goods_name'];
            }
            //检查 库存
            $checkeds = GoodsSpecs::cheCkeds($specdata,$goods);
            if ($checkeds['error_code'] == 0) {
                // 用户地址
                $address = UsersAddress::getAddress($uid);
                if ($address['error_code'] == 0) {
                    // 配送方式
                    if ($spid['shipping'] == 'wuliu') {
                        //物流配送
                        $Shipping['shipping_name'] = '物流配送';
                        $Shipping['pickup_id'] = '';
                    }else{
                        //到店取货
                        $Shipping['shipping_name'] = '到店取货';
                        $Shipping['pickup_id'] = $spid['shipping'];
                    }              
                    //得到 支付信息
                    if (isset($spid['pay_id']) ) {
                        $pay = Payments::getOne($spid['pay_id']);
                    }else{
                        $pay['error_code'] = 0;
                        $pay['data']['pay_code'] = 0;
                        $pay['data']['pay_name'] = '无';
                    }
                    // 订单数据
                    $data = [
                        'uid' => $uid,
                        'order_sn' => time(),
                        'create_time' => time(),
                        'order_amount' => $shop_price,
                        'consignee' => $address['data']['consignee'],
                        'country' => $address['data']['country'],
                        'province' => $address['data']['province'],
                        'city' => $address['data']['city'],
                        'district' => $address['data']['district'],
                        'town' => $address['data']['town'],
                        'address' => $address['data']['address'],
                        'zipcode' => $address['data']['zipcode'],
                        'phone' => $address['data']['mobile'],
                        'shipping_name' => $Shipping['shipping_name'],
                        'pickup_id' => $Shipping['pickup_id'],
                        'pay_code' => $pay['data']['pay_code'],
                        'pay_name' => $pay['data']['pay_name'],
                        'is_rebate' => $spid['rebate'],
                        'order_status' => 1
                    ];
                    // 添加订单
                    $order = self::add($data);
                    if ($order['error_code'] == 0) {
                        // 添加订单 商品 详情
                        if(OrdersGoods::add($order['order_id'],$cart)){
                            $result['error_code'] = 0;
                            $result['error_msg'] = "";
                            $result['order_id'] = $order['order_id'];
                            //删除购物车
                            Db::name('cart')->where(['uid' => $uid,'selected'=>1])->delete();
                            //预定 库存 
                            GoodsSpecs::stockDec($specdata);
                        }else{
                            $result['error_code'] = 5;
                            $result['error_msg'] = "订单详情添加失败";
                        }
                    }else{
                        $result['error_code'] = 2;
                        $result['error_msg'] = "添加订单失败";
                    }
                }else{
                    $result['error_code'] = 4;
                    $result['error_msg'] = "用户未设置地址";
                }
            }else{
                $result['error_code'] = 6;
                $result['error_msg'] = $checkeds['error_msg'];
            }
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "购物车为空";
        }
        return $result;
    }
    
    /**
     * add 添加定单
     * @xiao
     * @DateTime 2016-09-06T06:19:16+0800
     * @param    array              $data 添加数组
     * @return   array              [error_code, error_msg, order_id]
     */
    static public function add($data)
    {
        $row = DB::name('orders')->insertGetId($data);
        if ($row)
        {
           $result['error_code'] = 0;
           $result['error_msg'] = "";
           $result['order_id'] = $row;
        }
        else
        {
           $result['error_code'] = 1;
           $result['error_msg'] = "订单添加失败";
        } 
        return $result;
    }

    /**
     * add 添加定单
     * @xiao
     * @DateTime 2016-09-06T06:19:16+0800
     * @param    array              $data 添加数组
     * @return   array              [error_code, error_msg,id]
     */
    static public function payAdd($data)
    {
        $row = DB::name('payment_orders')->insertGetId($data);
        if ($row)
        {
           $result['error_code'] = 0;
           $result['error_msg'] = "";
           $result['id'] = $row;
        }
        else
        {
           $result['error_code'] = 1;
           $result['error_msg'] = "订单添加失败";
        } 
        return $result;
    }

    /**
     * payInfo pay定单详情
     * @karl
     * @DateTime 2016-09-06T06:19:16+0800
     * @param    int                   $order_id 订单id
     * @param    array                 $data 编辑数据
     * @return   array                 [error_code, error_msg]                  
     */
    static public function payInfo($id)
    { 
        $res = Db::name('payment_orders')->where(['id'=>$id])->find();
        if ($res)
        {
           $result['error_code'] = 0;
           $result['error_msg'] = "";
           $result['data'] = $res;
           $result['money'] = $res['money'];
        }
        else
        {
           $result['error_code'] = 1;
           $result['error_msg'] = "订单添加失败";
        } 
        return $result;
    }

    /**
     * payEdit 编辑定单
     * @karl
     * @DateTime 2016-09-06T06:19:16+0800
     * @param    int                   $order_id 订单id
     * @param    array                 $data 编辑数据
     * @return   array                 [error_code, error_msg]                  
     */
    static public function payEdit($id,$data)
    { 
        $where['id'] = $id;
        $row = Db::name('payment_orders')->where($where)->update($data);
        if ($row) 
        {                
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] = $row;
        }
        else{                 
            $result['error_code'] = 1;
            $result['error_msg'] = "编辑订单失败";
        }
        return $result;
    }

    /**
     * edit 编辑定单
     * @karl
     * @DateTime 2016-09-06T06:19:16+0800
     * @param    int                   $order_id 订单id
     * @param    array                 $data 编辑数据
     * @return   array                 [error_code, error_msg]                  
     */
    static public function edit($order_id,$data)
    { 
        $where['order_id'] = $order_id;
        $row = Db::name('orders')->where($where)->update($data);
            if ($row) 
            {                
                $result['error_code'] = 0;
                $result['error_msg'] = "";
                $result['data'] = $row;
            }
            else{                 
                $result['error_code'] = 1;
                $result['error_msg'] = "编辑订单失败";
            }
            return $result;
    }

    /**
     * editComment 留言状态
     * @karl
     * @DateTime 2016-09-06T06:19:16+0800
     * @param    int                   $order_id 
     * @param    array                 $Comment_id 
     * @return   array                 [error_code, error_msg]                  
     */
    static public function editComment($order_id,$goods_id,$comment_id)
    {   
        $data['comment_id'] = $comment_id;
        $where['order_id'] = $order_id;
        $where['goods_id'] = $goods_id;
        $row = Db::name('orders_goods')->where($where)->update($data);
            if ($row) 
            {                
                $result['error_code'] = 0;
                $result['error_msg'] = "";
                $result['data'] = $row;
            }
            else{                 
                $result['error_code'] = 1;
                $result['error_msg'] = "编辑订单失败";
            }
            return $result;

    }
    
    /**
     * getOne 得到一个订单
     * @karl
     * @DateTime 2016-09-28T22:41:15+0800
     * @param    int                $order_id 定单id
     * @return   array              [error_code, error_msg, data=> [] ]
     */
    static public function getOne($order_id)
    {   
        $row = Db::name('orders')->where(['order_id' => $order_id])->find();
        if($row)
        {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] = $row;
        }
        else
        {
            $result['error_code'] = 1;
            $result['error_msg'] = "没有得到订单详情";
        }
        return $result;

    }

    /**
     * addOpe 添加订单操作 记录
     * @karl
     * @DateTime 2016-09-28T22:41:15+0800
     * @param    int                $order_id 定单id
     * @return   array              [error_code, error_msg, data=> [] ]
     */
    static public function addOpe($data)
    { 
        $res = Db::name('orders_operation')->insertGetId($data);
        if($res)
        {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['id'] = $res;
        }
        else
        {
            $result['error_code'] = 1;
            $result['data'] = '';
            $result['error_msg'] = "失败";
        }
        return $result;
    }

    /**
     * getOpelist 订单操作列表
     * @karl
     * @DateTime 2016-09-28T22:41:15+0800
     * @param    int                $order_id 定单id
     * @return   array              [error_code, error_msg, data=> [] ]
     */
    static public function getOpelist($order_id)
    { 
        $where['order_id'] = $order_id;
        $res = Db::name('orders_operation')->where($where)->select();
        if($res)
        {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] = $res;
        }
        else
        {
            $result['error_code'] = 1;
            $result['data'] = '';
            $result['error_msg'] = "没有得到订单详情";
        }
        return $result;
    }

    /**
     * getList 得到订单列表
     * @xiao
     * @DateTime 2016-09-28T22:41:15+0800
     * @param    array              $data [''=>''] 条件
     * @return   array              [error_code, error_msg, data=> [] ]
     */
    static public function getList($data,$page = 1, $page_num = 10)
    {   
        //订单信息
        $where['is_dels'] = 0;
        $row = DB::name('orders')
            ->where($data)
            ->field("order_id,order_sn,order_status,pay_code,create_time,order_amount,shipping_time,shipping_price")
            ->order("order_id desc")
            ->page($page,$page_num)
            ->select();
        if ($row) {
            //订单商品信息
            foreach ($row as $k => $v) {
                $id['order_id'] = $v['order_id'];    
                $row[$k]['data'] = Db::name("orders_goods")
                ->field(["id","goods_name","renew_time","buy_num","shop_price","cover_img","goods_id","comment_id"])
                ->where($id)
                ->select();
            }
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['data'] = $row;
        }
        else{
            $result['error_code'] = 1;
            $result['error_msg'] = '得到订单详情失败';
        }
        return $result;

    }

    /**
     * ordersList 得到订单列表
     * @xiaoyajun
     * @DateTime 2016-09-28T22:41:15+0800
     * @param    int                $num 页数
     * @param    array                $where 条件
     * @param    array                $url 参数
     * @return   array              [error_code, error_msg, data=> [] ]
     */
    static public function ordersList($num,$where,$url = [])
    {   
        $where['d.is_dels'] = 0;
        $data = Db::name('orders')
            ->alias('d')
            ->join('users_customers u','u.uid = d.uid','left')
            ->field(["d.order_sn","d.order_status","d.pickup_id","d.pay_name","d.voucher_cash","d.order_amount","d.create_time","d.order_id","d.consignee","u.nickname"])
            ->where($where)
            ->order("d.create_time desc")
            ->paginate($num,false,array('query'=>$url)); 
        if($data){
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['data'] = $data;
        }
        else{
            $result['error_code'] = 1;
            $result['error_msg'] = '未得到得到订单详情';
        }
        return $result;

    }

    /**
     * ordersConsume 用户是否消费者
     * @xiaoyajun
     * @DateTime 2016-09-28T22:41:15+0800
     * @param    int                $uid 
     * @return   array              [error_code, error_msg, data=> [] ]
     */
    static public function ordersConsume($uid)
    {   
        $where['uid'] = $uid;
        $where['is_pay'] = 1;
        $res = Db::name('orders')->where($where)->find();
        if($res)
        {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] = $res;
        }
        else
        {
            $result['error_code'] = 1;
            $result['data'] = '';
            $result['error_msg'] = "没有得到订单详情";
        }
        return $result;
    }

    /**
     * ordersList 得到订单详情
     * @xiaoyajun
     * @DateTime 2016-09-28T22:41:15+0800
     * @param    int                $order_id 
     * @return   array              [error_code, error_msg, data=> [] ]
     */
    static public function ordersInfo($order_id)
    {   
        $where['d.order_id'] = $order_id;
        $where['d.is_dels'] = 0;
        $data = Db::name('orders')
            ->alias('d')
            ->join('users_customers u',' d.uid = u.uid','left')
            ->where($where)
            ->find();
        if ($data) {
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['data'] = $data;
            $list = OrdersGoods::getList($data['order_id']);
            if ($list['error_code'] == 0) {
                $result['list'] = $list['data'];
            }else{
                $result['list'] = '';
            }
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = '得到订单详情失败';
        }
        return $result;
    }
}

?>