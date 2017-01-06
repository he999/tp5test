<?php
namespace app\common\model\base;

use think\Model;
use think\Db;
use app\common\model\base\CommonModel;
use app\common\model\base\Users;
use app\common\model\base\Coms;
use app\common\model\base\UsersWeixin;
use app\common\model\weixin\WeixinSms;
use app\common\model\UsersParent;

/*************************************************  
*ClassName:     UsersVoucher
*Description:   用户 券 模型
*Others:        
*************************************************/
class UsersVoucher extends Model
{
	/**
    * [countBalance 计算财富券] ys_users_money_voucher
    * 用户账户增加资金的总额减去减少的总额
    * @xiao
    * @DateTime 2016-11-27T21:24:15+0800
    * @param    int                   $uid 用户uid
    * @return   array     [error_code, error_msg, balance]
    */
    static public function countVoucher($uid)
    {  
		$where = array('uid' => $uid);
        $income = 0;
        $expense = 0;
        $res = Db::name('users_money_voucher')->where($where)->select();
        if ($res) {
            foreach ($res as $key => $v) {
                $income += $v['income'];
                $expense += $v['expense'];
            }
            $balance = sprintf("%.2f",$income - $expense);
            Db::name('users_customers')->where($where)->update(['voucher'=>$balance]);
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['income'] = $income;
            $result['expense'] = $expense;
            $result['balance_voucher'] = $balance;
        }else{
            $result['error_code'] = 1;
            $result['balance_voucher'] = 0;
            $result['error_msg'] = '查询失败';
        }
        return $result;
    }

    /**
     * [incomeAdd 增加用户财富券收入记录]
     * @xiao
     * @DateTime 2016-11-27T21:35:49+0800 
     * @param    array                 $data ['des' => , 'type' => , `income` => , `order_id` => ]
     * @return   array     [error_code, error_msg, balance]
     */
    static public function incomeVoucherAdd($data)
    {
		$res = Db::name('users_money_voucher')->insertGetId($data);
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = ''; 
            $row = self::countVoucher($data['uid']);
            $balance['balance_voucher'] = $row['balance_voucher'];
            Db::name('users_money_voucher')->where(['id' => $res])->update($balance);
            $result['balance'] = $row['balance_voucher'];
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = '添加失败';
        }
        return $result;
    }

    /**
     * [expenseAdd 增加用户财富券支出记录]
     * @xiao
     * @DateTime 2016-11-27T21:35:49+0800 
     * @param    array                 $data ['des' => , 'type' => , `income` => , `order_id` => ]
     * @return   array     [error_code, error_msg, balance]
     */
    static public function expenseVoucherAdd($data)
    {
		$res = Db::name('users_money_voucher')->insertGetId($data);
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = ''; 
            $row = self::countVoucher($data['uid']);
            $balance['balance_voucher'] = $row['balance_voucher'];
            Db::name('users_money_voucher')->where(['id' => $res])->update($balance);
            $result['balance'] = $row['balance_voucher'];
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = '添加失败';
        }
        return $result;
    }

    /**
     * [voucherSetAdd 用户财富券设置 ]
     * @xiao
     * @DateTime 2016-11-27T21:35:49+0800
     * @param    int                   $type  
     * @param    array                 $data 
     * @return   array     [error_code, error_msg, ]
     */
    static public function voucherSetAdd($data)
    {
       $res = Db::name('voucher_set')->insertAll($data);
       if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = '';
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = '插入失败';
            $result['data'] = '';
        }
        return $result;
    }

    /**
     * [voucherSetAdd 用户财富券设置 ]
     * @xiao
     * @DateTime 2016-11-27T21:35:49+0800
     * @param    int                   $id  
     * @param    array                 $data 
     * @return   array     [error_code, error_msg, balance]
     */
    static public function voucherSetEdit($id,$data)
    {
        $res = Db::name('voucher_set')->where($id)->update($data);
        if ($res != false) {
            $result['error_code'] = 0;
            $result['error_msg'] = '';
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = '更新失败';
            $result['data'] = '';
        }
        return $result;
    }

    /**
     * [voucherSetAdd 用户财富券设置 ]
     * @xiao
     * @DateTime 2016-11-27T21:35:49+0800
     * @param    int                   $id  
     * @param    array                 $data 
     * @return   array     [error_code, error_msg, balance]
     */
    static public function voucherSetDel($id)
    {
        $res = Db::name('voucher_set')->where($id)->delete();
        if ($res != false) {
            $result['error_code'] = 0;
            $result['error_msg'] = '';
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = '更新失败';
            $result['data'] = '';
        }
        return $result;
    }

    /**
     * [voucherSetList 用户财富券设置列表 ]
     * @xiao
     * @DateTime 2016-11-27T21:35:49+0800
     * @param    int                   $type   
     * @return   array     [error_code, error_msg, balance]
     */
    static public function voucherSetList($type)
    {
		$type =['type'=>'recharge'];
        $res = Db::name('voucher_set')->where($type)->select();
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['data'] = $res;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = '查找失败';
            $result['data'] = '';
        }
        return $result;
    }

    /**
     * [voucherKey 用户财富券可使用对应的金额 ]
     * @xiao
     * @DateTime 2016-11-27T21:35:49+0800
     * @param    int                   $money   
     * @return   array     [error_code, error_msg, voucher]
     */
    static public function voucherKey($money,$type)
    {
        $res = Db::name('voucher_set')->where($type)->order('money desc')->select();
        if ($res) {
            foreach ($res as $k => $v) {
                if ($money >= $v['money']) {
                    $voucher = $v['voucher'];
                    break;
                }
            }
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['voucher'] = isset($voucher)?$voucher:0;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = '查找失败';
            $result['voucher'] = 0;
        }
        return $result;
    }

    /**
     * [PaymentVoucher 支付券]
     * @xiao
     * @DateTime 2016-11-27T21:35:49+0800
     * @param    int                   $uid
     * @param    int                   $order_id  
     * @param    array                 $data ['des' => , 'type' => , `income` => , `order_id` => ]
     * @return   
     */
    static public function paymentVoucher($uid,$order_id,$mone)
    {   
        $type =  Users::myInfo($uid)['data']['member_type'];
        if ($type == 0) {
            $inc = UsersMoney::countBalance($uid)['income'];
            $sho = Coms::getValue('threshold_money_set')['data'];
            if ($inc >= $sho ) {
                Users::editUsersCustomers($uid,['member_type' => 1]);
                $gvs = Coms::getValue('give_voucher_set')['data'];
                $add = [
                    'uid' => $uid,
                    'des' => '返佣券',
                    'type' => 'recharge',
                    'income'=> $gvs,
                    'order_id'=> $order_id,
                    'time'=>time()
                ];
                self::incomeVoucherAdd($add);
                self::smsMessage($uid);
            }
        }else{
            $vouchera = self::voucherKey($mone,['type'=>'recharge'])['voucher'];
            if ($vouchera > 0) {
                $add = [
                    'uid' => $uid,
                    'des' => '返佣券',
                    'type' => 'recharge',
                    'income'=>$vouchera,
                    'order_id'=>$order_id,
                    'time'=>time()
                ];
                self::incomeVoucherAdd($add);
            }
        }
    }

    /**
     * [smsMessage 发微信短信给上级]
     * @xiao
     * @DateTime 2016-11-27T21:35:49+0800
     * @param    int                   $uid
     * @return   
     */
    static public function smsMessage($uid)
    { 
        $pid = UsersParent::getParent($uid);
        if ($pid['error_code'] == 0) {
            $id = $pid['pid'];
        }else{
            return;
        }

        $info = UsersWeixin::getOneinfo($id)['data'];
        if ($info['attention'] != 1) {
            return;
        }
        $openid = $info['open_id'];
        $template =[
            'touser' => $openid,
            'template_id' => 'cat_6NC0Vfysze5ghA8c94lqRZozt6DrjxFZwAOBE2c',
            'url' => 'http://fsm.yuncentry.com/index.php/wap',
            'data' =>[
                'first' => ['value' => '分享提醒', 'color' => '#000'],
                'keyword1' => ['value' => $info['uid'], 'color' => '#666666'],
                'keyword2' => ['value' => $info['nickname'], 'color' => '#666666'],
                'keyword3' => ['value' => date('Y-m-d H:i'), 'color' => '#666666'],
                'remark' => ['value' => '通过你的分享，已成功加入平台进行平台消费后，您可以获得相应的返佣和财富券，以后的每一次订单消费也可以获得返佣喔！', 'color' => '#666666']
            ]
        ];
        $appid = Coms::getValue('appid')['data'];
        $appsecret = Coms::getValue('appsecret')['data'];
        $accesstoken = WeixinSms::getsAccessToken($appid,$appsecret);
        return WeixinSms::sendMessage($accesstoken,$template));
    }
    // 'data' =>[
    //             'first' => ['value' => '分享提醒', 'color' => '#000'],
    //             'keyword1' => ['value' => $info['nickname'].'通过你的分享，已成功加入平台', 'color' => '#666666'],
    //             'keyword2' => ['value' => date('Y-m-d H:i'), 'color' => '#666666'],
    //             'remark' => ['value' => '进行平台消费后，您可以获得相应的返佣和财富券，以后的每一次订单消费也可以获得返佣喔！', 'color' => '#666666']
    //         ]
}