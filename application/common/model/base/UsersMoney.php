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
            $balance = $income-$expense;
           $balance = sprintf("%.2f", $balance);
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

    /**
     * [PaymentCommission 支付佣金]
     * @xiao
     * @DateTime 2016-11-27T21:35:49+0800
     * @param    int                   $uid
     * @param    int                   $order_id  
     * @param    array                 $data ['des' => , 'type' => , `income` => , `order_id` => ]
     * @return   array     [error_code, error_msg, balance]
     */
    static public function PaymentCommission($uid,$order_id,$mone)
    {
        $list = OrdersGoods::getList($order_id)['data'];
        foreach ($list as $k => $v) {
            $ids = $v['goods_id']; 
        }
        $where['goods_id'] = ['in',$ids];
        $goods = Goods::getWhereList($where);

        //佣金比例
        $com1 = (int)Coms::getValue('yi_fanyong_bili')['data']/100;
        $com2 = (int)Coms::getValue('er_fanyong_bili')['data']/100;
        $com3 = (int)Coms::getValue('san_fanyong_bili')['data']/100;
        //对应 金额
        $money1 = 0;
        $money2 = 0;
        $money3 = 0;

        //对应积分
        $points1 = $mone*(int)Coms::getValue('yi_fanyong_jifen')['data']/100;
        $points2 = $mone*(int)Coms::getValue('er_fanyong_jifen')['data']/100;
        $points3 = $mone*(int)Coms::getValue('san_fanyong_jifen')['data']/100;

        foreach ($goods['data'] as $key => $value) {
            $money1 += (int)$value['sum_brokerage']*$com1;
            $money2 += (int)$value['sum_brokerage']*$com2;
            $money3 += (int)$value['sum_brokerage']*$com3;
        }
        //支付 佣金
        $pid1 = UsersParent::getParent($uid);
        if ($pid1['error_code'] == 0) {
            $add1 = [
                'des' => '佣金',
                'type' => 'commission',
                'income' => $money1,
                'time' => time(),
                'order_id' => $order_id,
                'uid' => $pid1['pid']
            ];
            self::incomeAdd($add1);
            $poin1 = ['uid' => $pid1['pid'],'type' => '返佣积分','points'=>$points1,'order_id'=>$order_id,'time'=>time()];
            UsersPoints::add($poin1);
            $pid2 = UsersParent::getParent($pid1['pid']);
            if ($pid2['error_code'] == 0) {
                $add2 = [
                'des' => '佣金',
                'type' => 'commission',
                'income' => $money2,
                'time' => time(),
                'order_id' => $order_id,
                'uid' => $pid2['pid']
            ];
            self::incomeAdd($add2);
            $poin2 = ['uid' => $pid2['pid'],'type' => '返佣积分','points'=>$points2,'order_id'=>$order_id,'time'=>time()];
            UsersPoints::add($poin2);
                $pid3 = UsersParent::getParent($pid2['pid']);
                if ($pid3['error_code'] == 0) {
                    $add3 = [
                    'des' => '佣金',
                    'type' => 'commission',
                    'income' => $money3,
                    'time' => time(),
                    'order_id' => $order_id,
                    'uid' => $pid3['pid']
                ];
                self::incomeAdd($add3);
                $poin3 = ['uid' => $pid3['pid'],'type' => '返佣积分','points'=>$points3,'order_id'=>$order_id,'time'=>time()];
            UsersPoints::add($poin3);
                }
            }
        }
    } 
}

?>