<?php
namespace app\common\model\base;

use think\Model;
use think\Db;

use app\common\model\base\CommonModel;
use app\common\model\base\Users;

/*************************************************  
*ClassName:     Withdrawals
*Description:   用户提现模型
*Others:        
*************************************************/
class Withdrawals extends Model
{
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