<?php
namespace app\wap\controller;

use think\Controller;
use think\Session;
use think\Request;
use think\Validate;
use app\common\model\Orders as OrdersModel;
use app\common\model\GoodsComment;
use app\common\model\base\Coms;
use weixin\pay\WeixinPay;
use app\common\model\base\UsersMoney;
use app\common\model\base\UsersVoucher;
use app\common\model\Regions;
use app\common\model\Shipping;

/*************************************************
 * @ClassName:     Orders
 * @Description:   订单控制器
 * @author:        
 * @DateTime      
 *************************************************/
class Orders extends WeixinBase
{
    /**
     * discuss 定单评论
     * @
     * @DateTime 2016-10-15T10:10:36+0800
     * @param    void                
     * @return   void
     */
    public function discuss()
    {   
        $input = Request::instance()->param();
        /******************* 验证网址参数 ********************/
        $rule = [
            'goods_id'  => 'require|max:10',
            'order_id'  => 'require|max:11',
        ];
        $msg = [
            'goods_id.max'      =>  'order_id最长为10位',
            'goods_id.require'  =>  'order_id必须填写',
            'order_id.require'  =>  'order_id必须填写',
        ];
        $validate = new Validate($rule, $msg);
        $result   = $validate->check($input);
        if (!$result){
            $this->error($validate->getError());
        }
        $goods_id = $input['goods_id'];
        $order_id = $input['order_id'];
        $this->assign('order_id',$order_id);
        $this->assign('goods_id',$goods_id);
        return $this->fetch();
    }

    /**
     * ajaxComment ajax留言
     * @xiao
     * @DateTime 2016-10-15T10:10:36+0800
     * @param    void                
     * @return   void
     */
    public function ajaxComment()
    {   
        $input = Request::instance()->param();
        /******************* 验证网址参数 ********************/
        $rule = [
            'goods_id'  => 'require|max:10',
            'content'  => 'require',
            'order_id'  => 'require|max:11',
        ];
        $msg = [
            'goods_id.max'      =>  'order_id最长为10位',
            'goods_id.require'  =>  'order_id必须填写',
            'content.require'  =>  '内容必须填写',
            'order_id.require'  =>  'order_id必须填写',
        ];
        $validate = new Validate($rule, $msg);
        $result   = $validate->check($input);
        if (!$result){
            $arr['error_code'] == 2;
            $arr['error_msg'] == $validate->getError();
            return $arr;
        }
        if (isset($input['xun'])) {
            $data['is_hide'] = $input['xun'];
        }
        $order_id = $input['order_id'];
        $goods_id = $input['goods_id'];
        $uid = session('uid');
        $data = [
            'uid' => $uid,
            'grade' => $input['grade'],
            'goods_id' => $goods_id,
            'content' => $input['content'],
            'time' => time(),
        ];
        $res = GoodsComment::add($data);
        if ($res['error_code'] == 0) {
            OrdersModel::editComment($order_id,$goods_id,$res['comment_id']);
            $arr['error_code'] = 0;
            $arr['error_msg'] = '';
        }else{
            $arr['error_code'] = 1;
            $arr['error_msg'] = '留言错误';
        }
        return $res;
    }

    /**
     * orderAllList 订单待付款
     * @xiao
     * @DateTime 2016-10-15T10:10:36+0800
     * @param    void                
     * @return   void
     */
    public function orderAllList()
    {   
        $where['uid'] = session('uid');
        $where['order_status'] = 1;
        $res = OrdersModel::getList($where);
        if ($res['error_code'] == 0 ) {
            $data = $res['data'];
        }else{
            $data = '';
        }
        $this->assign('data',$data);
        return $this->fetch();
    }

    /**
     * orderpreparelist 订单待发货
     * @xiao
     * @DateTime 2016-10-15T10:10:36+0800
     * @param    void                
     * @return   void
     */
    public function orderpreparelist()
    {   
        $where['uid'] = session('uid');
        $where['order_status'] = 2;
        $res = OrdersModel::getList($where);
        if ($res['error_code'] == 0 ) {
            $data = $res['data'];
        }else{
            $data = '';
        }
        $this->assign('data',$data);
        return $this->fetch();
    }

    /**
     * orderungetlist 订单已发货
     * @xiao
     * @DateTime 2016-10-15T10:10:36+0800
     * @param    void                
     * @return   void
     */
    public function orderungetlist()
    {   
        $where['uid'] = session('uid');
        $where['order_status'] = 3;
        $res = OrdersModel::getList($where);
        if ($res['error_code'] == 0 ) {
            $data = $res['data'];
        }else{
            $data = '';
        }
        $this->assign('data',$data);
        return $this->fetch();
    }

    /**
     * orderList 订单已完成
     * @xiao
     * @DateTime 2016-10-15T10:10:36+0800
     * @param    void                
     * @return   void
     */
    public function orderendlist()
    {   
        $where['uid'] = session('uid');
        $where['order_status'] = 4;
        $res = OrdersModel::getList($where);
        if ($res['error_code'] == 0 ) {
            $data = $res['data'];
        }else{
            $data = '';
        }
        $this->assign('data',$data);
        return $this->fetch();
    }

    /**
     * ajaxOrdersOver 订单完成
     * @xiao
     * @DateTime 2016-10-15T10:10:36+0800
     * @param    void                
     * @return   void
     */
    public function ajaxOrdersOver()
    { 
        $input = Request::instance()->param();
        $order_id = $input['order_id'];
        $res = OrdersModel::edit($order_id,['order_status' => 3]);
        if ($res['error_code'] == 0) {
            $arr['error_code'] = 0;
            $arr['error_msg'] = '';
        }else{
            $arr['error_code'] = 1;
            $arr['error_msg'] = '修改失败';
        }
        return $arr;
    }

     /**
     * orderInfo 订单详情
     * @xiao
     * @DateTime 2016-10-15T10:10:36+0800
     * @param    void                
     * @return   void
     */
    public function orderInfo()
    {
        $input = Request::instance()->param();
        /******************* 验证网址参数 ********************/
        $rule = [
            'order_id'  => 'require|max:15',
        ];
        $msg = [
            'order_id.max'      =>  'order_id最长为15位',
            'order_id.require'  =>  'order_id必须填写',
        ];
        $validate = new Validate($rule, $msg);
        $result   = $validate->check($input);
        if (!$result){
            $this->error($validate->getError());
        }
        $row = OrdersModel::getOne($input['order_id'])['data'];
        if ($row['pickup_id'] != 0 ) {
            $info = Shipping::getInfoShippingPickup($row['pickup_id'])['data'];
            $distr = '';
        }else{
            $ids = [
                'province' => $row['province'],
                'city' => $row['city'],
                'district' => $row['district'],
                'town' => $row['town']
            ];
            $distr = Regions::getNameStr($ids);
            $info = '';
        }
        $voucher = UsersVoucher::voucherKey($row['order_amount'],['type'=>'buy'])['voucher'];
        $voucherc = UsersVoucher::countVoucher(session('uid'));
        dump($voucher);
        dump($voucherc['balance_voucher']);
        $where['uid'] = session('uid');
        $where['order_id'] = $input['order_id'];
        $res = OrdersModel::getList($where);
        if ($res['error_code'] == 0 ) {
            $data = $res['data'];
        }else{
            $this->error('操作错误');
        }
        $this->assign('voucher',$voucher);
        $this->assign('voucherc',$voucherc['balance_voucher']);
        $this->assign('row',$row);
        $this->assign('info',$info);
        $this->assign('distr',$distr);
        $this->assign('data',$data);
        return $this->fetch();
    }

    /**
     * ajaxOrderid 订单完成
     * @xiao
     * @DateTime 2016-10-15T10:10:36+0800
     * @param    void                
     * @return   void
     */
    public function ajaxOrderid()
    { 
        $input = Request::instance()->param();
        $order_id = $input['order_id'];
        session('payorder_id',$order_id);
        return $arr['error_code'] = 0 ;
    }

    /**
     * orderInfo 订单支付
     * @xiao
     * @DateTime 2016-10-15T10:10:36+0800
     * @param    void                
     * @return   void
     */
    public function orderPay()
    {
        $where['uid'] = session('uid');
        $order_id = session('payorder_id');
        $res = OrdersModel::ordersInfo($order_id);
        if ($res['error_code'] == 0 ) {
            $data = $res['data'];
            $list = $res['list'];
        }else{
            $this->error('操作错误');
        }
        $key = Coms::getValue('apikey')['data'];
        $data['appid'] = Coms::getValue('appid')['data'];
        $data['appsecret'] = Coms::getValue('appsecret')['data'];
        $data['mchid'] = Coms::getValue('mchid')['data'];
        $data['open_id'] = $open_id = session("open_id");
        $data['body'] = "订单支付";
        $data['attach'] = 'dindan';
        $data['money'] = 0.01; //$data['order_amount'];
        $data['out_order'] = $data['order_id'].'-'.time().rand(100, 999);
        $data['notify_url'] = "http://yshop.wiwibao.com/weixinpaynotify.php";
        $weixinpay = new WeixinPay;
        $jsApiParameters = $weixinpay->createPay($data, $key);
        $this->assign("jsApiParameters", $jsApiParameters);
        $this->assign('list',$list);
        $this->assign('data',$data);
        return $this->fetch();
    }

    /**
     * orderInfo 订单支付
     * @xiao
     * @DateTime 2016-10-15T10:10:36+0800
     * @param    void                
     * @return   void
     */
    public function orderYuePay()
    {   
        $input = Request::instance()->param();
        $order_id = $input['order_id'];
        $where['uid'] = session('uid');
        $res = OrdersModel::ordersInfo($order_id);
        if ($res['error_code'] == 0 ) {
            $data = $res['data'];
            $list = $res['list'];
        }else{
            $this->error('操作错误');
        }
        $this->assign('list',$list);
        $this->assign('data',$data);
        return $this->fetch();
    }

    /**
     * ajaxyuepay 订单支付
     * @xiao
     * @DateTime 2016-10-15T10:10:36+0800
     * @param    void                
     * @return   void
     */
    public function ajaxyuepay()
    {   
        $uid = session('uid');
        $input = Request::instance()->param();
        $order_id = $input['order_id'];
        $data = OrdersModel::ordersInfo($order_id)['data'];
        $money = $data['order_amount'] + $data['shipping_price'];
        $row = UsersMoney::countBalance($uid);
        $yuemoney = $row['balance'];
        if($yuemoney < $money ){
            $arr['error_code'] = 1;
            $arr['error_msg'] = '余额不足，请充值再试';
            return $arr;
        }
        $pay = [
            'des' => '购买',
            'type' => 'buy',
            'expense' => $money,
            'time' => time(),
            'order_id' => $order_id,
            'uid' => $uid
        ];
        $res = UsersMoney::expenseAdd($pay);
        if ($res['error_code'] == 0) {
            $datas['order_status'] = 1;
            $datas['is_pay'] = 1;
            $datas['pay_time'] = time();
            if (OrdersModel::edit($order_id,$datas)['error_code'] == 0) {
                UsersMoney::PaymentCommission($uid,$order_id,$data['order_amount']);
                $arr['error_code'] = 0;
                $arr['error_msg'] = '支付成功';
            }
        }else{
            $arr['error_code'] = 2;
            $arr['error_msg'] = '网络繁忙';
        }
        return $arr;
    }
    
}