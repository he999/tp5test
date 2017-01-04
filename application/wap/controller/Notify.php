<?php
namespace app\wap\controller;

use think\Controller;
use weixin\pay\WeixinPay;
use think\Db;
use app\common\model\Course;
use app\common\model\base\UsersWeixin;
use app\common\model\base\UsersVoucher;
use app\common\model\base\UsersRebate;
use app\common\model\Orders;
use app\common\model\UsersCustomers;
use app\common\model\base\UsersMoney;
use app\common\model\OrdersGoods;

/*************************************************
 *ClassName:     Notify
 *Description:   微信支付回调
 *Others:
 *************************************************/
class Notify
{
    public function index()
    {
        zlog("支付回调时间".time());
        //存储微信的回调
        if (isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
            $xmls = $GLOBALS['HTTP_RAW_POST_DATA'];
            $postObj = simplexml_load_string($xmls, 'SimpleXMLElement', LIBXML_NOCDATA);
            $result_code = $postObj->result_code; //SUCCESS
            if ($result_code == "SUCCESS") 
            {
                $attach = $postObj->attach;
                $arr = explode('-',$postObj->out_trade_no);
                if ( $attach == 'dindan' ) 
                {
                    $order_id = $arr[0];
                    $data['order_status'] = 1;
                    $data['is_pay'] = 1;
                    $data['pay_time'] = time();
                    $res = Orders::getOne($order_id);
                    if ($res['error_code'] == 0) {
                        if ($res['data']['pay_time'] == 0) {
                            $uid = $res['data']['uid'];
        $vouchera = UsersVoucher::voucherKey($res['data']['order_amount'],['type'=>'buy']);//可用券
        $voucherc = UsersVoucher::countVoucher($uid);//现有券
        (int)$c = $voucherc['balance_voucher'];  
        (int)$a = $vouchera['voucher'];
        if ($c <= $a) {
            $voucher = $c;
        }else{
            $voucher = $a;
        }
        if ($res['data']['is_rebate'] == 1) {
            $rebate = UsersRebate::countRebate($uid)['balance_rebate'];//佣金
            $pays = [
                'des' => '购买',
                'type' => 'buy',
                'expense' => $rebate,
                'time' => time(),
                'order_id' => $order_id,
                'uid' => $uid
            ];
            UsersRebate::expenseRebateAdd($pays);
            $abc = $rebate;
        }else{
            $abc = 0;
        }
                            $data['order_status'] = 1;
                            $data['is_pay'] = 1;
                            $data['pay_time'] = time();
                            $datas['voucher_cash'] = $voucher;
                            $datas['rebate_cash'] = $abc;
                            Orders::edit($order_id,$data);
                            UsersMoney::PaymentCommission($uid,$order_id,$res['data']['order_amount']);
                            echo 'SUCCESS';
                        }
                    }
                // }elseif ($attach == 'jieyue') {
                //     $id = $arr[0];
                //     $data['pay_time'] = time();
                //     $info = Orders::payInfo($id);
                //     if ($info['error_code'] == 0) 
                //     {
                //         if ($info['data']['pay_time'] == '') {
                //             Orders::payEdit($id,$data);
                //             $uid = $info['data']['uid']; 
                //             UsersCustomers::edit($uid,['member_type'=>1,'opening_time'=>time()]);
                //             echo 'SUCCESS';
                //         }
                        
                //     }
                // }elseif ($attach == 'nianfei') {
                //     $id = $arr[0];
                //     $data['pay_time'] = time();
                //     $info = Orders::payInfo($id);
                //     if ($info['error_code'] == 0) 
                //     {   
                //         if ($info['data']['pay_time'] == '') {
                //             Orders::payEdit($id,$data);
                //             $uid = $info['data']['uid'];  
                //             UsersCustomers::edit($uid,['member_type'=>2,'opening_time'=>time()]);
                //             echo 'SUCCESS';
                //         }
                //     }
                }elseif ($attach == 'chonzhi') {
                    $id = $arr[0];
                    $data['pay_time'] = time();
                    $row = Orders::payInfo($id);
                    if ($row['error_code'] == 0) 
                    {
                        if ($row['data']['pay_time'] == '') {
                            $add = [
                                'des' => '充值',
                                'type' => 'recharge',
                                'income' => $row['money'],
                                'time' => time(),
                                'order_id' => $id,
                                'uid' => $row['data']['uid']
                            ];
                            UsersMoney::incomeAdd($add);
                            Orders::payEdit($id,$data);
                            echo 'SUCCESS';
                        }
                    }
                // }elseif ($attach == 'yuqi') {
                //     $id = $arr[0];
                //     $data['pay_time'] = time();
                //     $row = Orders::payInfo($id);
                //     if ($row['error_code'] == 0) 
                //     {
                //         if ($row['data']['pay_time'] == '') {
                //             $ard = explode('-',$row['data']['type']);
                //             $ids = $ard[0];
                //             $add = ['renew_time' => time()];
                //             OrdersGoods::editOne($ids,$add);
                //             Orders::payEdit($id,$data);
                //             echo 'SUCCESS';
                //         }
                //     }
                }
                echo 'SUCCESS';
            }
            zlog($postObj);
        }
        zlog("支付流程走完结束后时间".time());
    }
}

?>