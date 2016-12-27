<?php
namespace app\common\model\base;

use think\Model;
use think\Db;

use app\common\model\base\CommonModel;
use app\common\model\base\Users;

/*************************************************  
*ClassName:     UsersPoints
*Description:   用户积分模型
*Others:        
*************************************************/
class UsersPoints extends Model
{   
    /**
     * countIntegral 用户积分计算
     * @xiao
     * @DateTime 2016-07-31T23:23:02+0800
     */
    static public function countIntegral($uid)
    {
        $where = array('uid' => $uid);
        $points = 0;
        $res = Db::name('users_points')->where($where)->select();
        if ($res) {
            foreach ($res as $key => $v) {
                $points += $v['points'];
            }
            Db::name('users_customers')->where(['uid' => $uid])->update(['points'=>$points]);
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['points'] = $points;
        }else{
            $result['error_code'] = 1;
            $result['points'] = 0;
            $result['error_msg'] = '查询失败';
        }
        return $result;
    }

    /**
     * add 用户积分记录
     * @xiao
     * @DateTime 2016-07-31T23:23:02+0800
     */
    static public function add($data)
    {
        $res = Db::name('users_points')->insertGetId($data);
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['id'] = $res;
            self::countIntegral($data['uid']);
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "查询失败";
        }
        return $result;
    }

    /**
     * info 用户申请提现记录
     * @xiao
     * @DateTime 2016-07-31T23:23:02+0800
     */
    static public function info($uid,$data = '')
    {   
        $data['uid'] = $uid;
        $res = Db::name('withdrawals')->where($data)->find();
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] = $res;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "查询失败";
        }
        return $result;
    }

    /**
     * addon 申请提现
     * @xiao
     * @DateTime 2016-07-31T23:23:02+0800
     */
    static public function addOn($data)
    {
        $res = Db::name('withdrawals')->insertGetId($data);
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['id'] = $res;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "查询失败";
        }
        return $result;
    }

}

?>