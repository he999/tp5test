<?php
namespace app\common\model\base;

use think\Model;
use think\Db;
use app\common\model\base\CommonModel;
use app\common\model\base\Users;
use app\common\model\base\Coms;

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
     * @return   array     [error_code, error_msg, balance]
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

}