<?php
namespace app\common\model\base;

use think\Model;
use think\Db;
use app\common\model\base\Users;
use app\common\model\OrdersGoods;
use app\common\model\Goods;
use app\common\model\base\Coms;
use app\common\model\UsersParent;

/*************************************************  
*ClassName:     UsersMoney
*Description:   用户账户模型
*Others:        
*************************************************/
class UsersMoney extends Model
{
    /**
     * [countBalance 计算余额]
     * 用户账户增加资金的总额减去减少的总额
     * @xiao
     * @DateTime 2016-11-27T21:24:15+0800
     * @param    int                   $uid 用户uid
     * @return   array     [error_code, error_msg, balance]
     */
    static public function countBalance($uid)
    {   
        $where = array('uid' => $uid);
        $income = 0;
        $expense = 0;
        $res = Db::name('users_money')->where($where)->select();
        if ($res) {
            foreach ($res as $key => $v) {
                $income += $v['income'];
                $expense += $v['expense'];
            }
            $balance = sprintf("%.2f",$income - $expense);
            Db::name('users_customers')->where(['uid' => $uid])->update(['balance'=>$balance]);
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['income'] = $income;
            $result['expense'] = $expense;
            $result['balance'] = $balance;
        }else{
            $result['error_code'] = 1;
            $result['balance'] = 0;
            $result['error_msg'] = '查询失败';
        }
        return $result;
    }
        
    /**
     * [incomeAdd 增加用户收入记录]
     * @xiao
     * @DateTime 2016-11-27T21:35:49+0800
     * @param    int                   $uid  
     * @param    array                 $data ['des' => , 'type' => , `income` => , `order_id` => ]
     * @return   array     [error_code, error_msg, balance]
     */
    static public function incomeAdd($data)
    {   
        $res = Db::name('users_money')->insertGetId($data);
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = ''; 
            $row = self::countBalance($data['uid']);
            $balance['balance'] = $row['balance'];
            Db::name('users_money')->where(['id' => $res])->update($balance);
            $result['balance'] = $row['balance'];
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = '添加失败';
        }
        return $result;
    }

    /**
     * [expenseAdd 增加用户支出记录]
     * @xiao
     * @DateTime 2016-11-27T21:35:49+0800
     * @param    int                   $uid  
     * @param    array                 $data ['des' => , 'type' => , `income` => , `order_id` => ]
     * @return   array     [error_code, error_msg, balance]
     */
    static public function expenseAdd($data)
    {
        $res = Db::name('users_money')->insertGetId($data);
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = ''; 
            $row = self::countBalance($data['uid']);
            $balance['balance'] = $row['balance'];
            Db::name('users_money')->where(['id' => $res])->update($balance);
            $result['balance'] = $row['balance'];
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = '添加失败';
        }
        return $result;
    }
 
}

?>