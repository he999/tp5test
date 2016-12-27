<?php
namespace app\common\model\base;

use think\Model;
use think\Db;

use app\common\model\base\CommonModel;
use app\common\model\base\Users;

/*************************************************  
*ClassName:     UsersRebate
*Description:   用户 券 模型
*Others:        
*************************************************/
class UsersRebate extends Model
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
        
    }
}