<?php
namespace app\common\model\base;

use think\Model;
use think\Db;

use app\common\model\base\CommonModel;
use app\common\model\base\Users;

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
            Db::name('users_money_voucher')->where(['uid' => $uid])->update(['balance_voucher'=>$balance]);
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
     * @param    int                   $uid  
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
     * @param    int                   $uid  
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
        return $balance;
        return $res;
    }
}