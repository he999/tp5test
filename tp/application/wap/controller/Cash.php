<?php
namespace app\wap\controller;

use think\Session;
use think\Validate;
use think\Request;
use app\common\model\Cart;
use app\common\model\UsersAddress;
use app\common\model\Orders;
use app\common\model\Regions;
use app\common\model\Payments;
use app\common\model\Shipping;
use app\common\model\base\Users;


/*************************************************
 * @ClassName:     Cash
 * @Description:   收银台控制器
 * @author:        
 * @DateTime      
 *************************************************/
class Cash extends WeixinBase
{
    /*************************************************
     * Function:      CreateOrderList
     * Description:   订单确认详情
     * @param:        void
     * Return:        void
     *************************************************/
    public function CreateOrderList()
    {
        $input = Request::instance()->param();
        $uid = session('uid');
        $res = UsersAddress::getAddress($uid);
        if ($res['error_code'] == 0) {
            $ids = [
                'province' => $res['data']['province'],
                'city' => $res['data']['city'],
                'district' => $res['data']['district'],
                'town' => $res['data']['town'],
            ];
            $distr = Regions::getNameStr($ids);
            $this->assign('distr',$distr);
            $this->assign('address',$res['data']);
        }else{
            return $this->redirect('/index.php/wap/user/addaddress?key=1');
        }
        $where['selected'] = 1;
        $data = Cart::getList($uid,$where);
        if ($data['error_code'] == 0) {
            $datas = $data['data'];
        }else{
            $datas = '';
        }
        if (isset($input['pickup_id']) ) {
            $pickup_id = $input['pickup_id'];
            $name = Shipping::getInfoShippingPickup($pickup_id)['data']['name'];
        }else{
            $pickup_id = '';
            $name = '';
        }
        $info = Users::myInfo($uid);
        $this->assign('info',$info['data']);
        $this->assign('pickup_id',$pickup_id);
        $this->assign('name',$name);
        $this->assign('data',$datas);
        return $this->fetch();
    }

    /*************************************************
     * Function:      ajaxCreateOrder
     * Description:   生成订单
     * @param:        void
     * Return:        void
     *************************************************/
    public function ajaxCreateOrder()
    {   
        $input = Request::instance()->param();
        $rule = [
            'payid'  => 'require',
            'shipping'  => 'require',
        ];
        $msg = [
            'payid.require'  =>  '支付方式必须选',
            'shipping.require'  =>  '配送方式必须填选',
        ];
        $validate = new Validate($rule, $msg);
        $result   = $validate->check($input);
        if (!$result){
            $arr['error_code'] = 2;
            $arr['error_msg'] = $validate->getError();
            return $arr;
        }
        $data['pay_id'] = $input['payid'];
        $data['shipping'] = $input['shipping'];
        $data['rebate'] = $input['rebate'];
        $uid = session('uid');
        $res = Orders::addCartOrder($uid,$data);
        if ($res['error_code'] == 0) {
            $arr['error_msg'] =  "";
            $arr['error_code'] = 0;
        }else{
            $arr['error_msg'] =  $res['error_msg'];
            $arr['error_code'] = 1;
        }
        return $arr;
    }

    /*************************************************
     * Function:      pay
     * Description:   支付订单
     * @param:        void
     * Return:        void
     *************************************************/
    public function pay()
    {
        $input = Request::instance()->param();
        $uid = $this->uid;
        $com_id = $this->com_id;
        $order_id = $this->order_id;
        /******************* 验证网址参数 ********************/
        $rule = [
            'order_id'  => 'require|max:10',
        ];
        $msg = [
            'order_id.max'      =>  'order_id最长为10位',
            'order_id.require'  =>  'order_id必须填写',
        ];
        $validate = new Validate($rule, $msg);
        $result   = $validate->check($input);
        if (!$result)
        {
            $this->jsAlert($validate->getError());
        }
        $order_info = Orders::getOne($order_id);
        $payment_list = Payments::getPayments($com_id);

        $this->assign('order_info', $order_info);
        $this->assign('payment_list', $payment_list);
        return $this->fetch();
    }

    /*************************************************
     * Function:      shippingPickup
     * Description:   取货门店
     * @param:        void
     * Return:        void
     *************************************************/
    public function shippingPickup()
    {   
        $res = Shipping::listShippingPickup();
        if ($res['error_code'] == 0) {
            $data = $res['data'];
        }else{
            $data = '';
        }
        $this->assign('data',$data);
        return $this->fetch();
    }
}