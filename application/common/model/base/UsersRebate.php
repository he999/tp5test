<?php
namespace app\common\model\base;

use think\Model;
use think\Db;
use app\common\model\base\CommonModel;
use app\common\model\base\Users;

/*************************************************  
*ClassName:     UsersRebate
*Description:   用户返佣模型
*Others:        
*************************************************/
class UsersRebate extends Model
{
    /**
    * listRebate 返佣列表 ys_users_money_rebate
    * @xiao
    * @param    array                   $num 每页记录
    * @param    array                   $data 条件
    * @param    array                   $url 参数 paginate()
    * @return   array              [error_code, error_msg,data=>[]]
    */
    static public function listRebate($num,$data = '',$url = [])
    {  
		$data['is_del'] = 0;
        $res = Db::name('users_money_rebate')->where($data)->order("time desc")->paginate($num,false,array('query'=>$url));
        if($data){
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['data'] = $res;
        }
        else{
            $result['error_code'] = 1;
            $result['error_msg'] = '未得到返佣列表';
        }
        return $result;
    }

    /**
     * delRebate 删除返佣记录
     * @xiao
     * @param    array                   $id 
     * @return   array              [error_code, error_msg]
     */
    static public function delRebate($id)
    {  
		$row = Db::name('users_money_rebate')->delete($id); 
        if ($row) 
        {    
            $result['error_code'] = 0;
            $result['error_msg'] = "";
        }
        else{                 
             $result['error_code'] = 1;
             $result['error_msg'] = "删除返佣记录失败";
        }
        return $result;
    }
    
    /**
     * [countBalance 计算返佣] ys_users_money_rebate
     * 用户账户增加资金的总额减去减少的总额
     * @xiao
     * @DateTime 2016-11-27T21:24:15+0800
     * @param    int                   $uid 用户uid
     * @return   array     [error_code, error_msg, balance]
     */
    static public function countRebate($uid)
    {   
		$where = array('uid' => $uid);
        $income = 0;
        $expense = 0;
        $res = Db::name('users_money_rebate')->where($where)->select();
        if ($res) {
            foreach ($res as $key => $v) {
                $income += $v['income'];
                $expense += $v['expense'];
            }
            $balance = sprintf("%.2f",$income - $expense);
            Db::name('users_customers')->where($where)->update(['balance_rebate'=>$balance]);
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['income'] = $income;
            $result['expense'] = $expense;
            $result['balance_rebate'] = $balance;
        }else{
            $result['error_code'] = 1;
            $result['balance_rebate'] = 0;
            $result['error_msg'] = '查询失败';
        }
        return $result;
    }

    /**
     * [incomeAdd 增加用户返佣金收入记录]
     * @xiao
     * @DateTime 2016-11-27T21:35:49+0800
     * @param    int                   $uid  
     * @param    array                 $data ['des' => , 'type' => , `income` => , `order_id` => ]
     * @return   array     [error_code, error_msg, balance]
     */
    static public function incomeRebateAdd($data)
    {
		$res = Db::name('users_money_rebate')->insertGetId($data);
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = ''; 
            $row = self::countRebate($data['uid']);
            $balance['balance_rebate'] = $row['balance_rebate'];
            Db::name('users_money_rebate')->where(['id' => $res])->update($balance);
            $result['balance'] = $row['balance_rebate'];
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = '添加失败';
        }
        return $result;
    }

    /**
     * [expenseAdd 增加用户返佣金支出记录]
     * @xiao
     * @DateTime 2016-11-27T21:35:49+0800
     * @param    int                   $uid  
     * @param    array                 $data ['des' => , 'type' => , `expense` => , `order_id` => ]
     * @return   array     [error_code, error_msg, balance]
     */
    static public function expenseRebateAdd($data)
    {   
		$res = Db::name('users_money_rebate')->insertGetId($data);
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = ''; 
            $row = self::countRebate($data['uid']);
            $balance['balance_rebate'] = $row['balance_rebate'];
            Db::name('users_money_rebate')->where(['id' => $res])->update($balance);
            $result['balance'] = $row['balance_rebate'];
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

        $info = Users::myInfo($uid)['data'];
        $rebate = Coms::getRebateInfos(['id' => $info['member_type'] ])['data'];

        //佣金比例
        $reb1 = $rebate[0]['rebate_rate_lv1']/100;
        $reb2 = $rebate[0]['rebate_rate_lv2']/100;
        $reb3 = $rebate[0]['rebate_rate_lv3']/100;


        //对应 金额
        $rebsum1 = 0;
        $rebsum2 = 0;
        $rebsum3 = 0;

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