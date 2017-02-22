<?php
namespace app\common\model\base;

use think\Model;
use think\Db;

/*************************************************  
*ClassName:     UserLogs
*Description:   用户日志
*Others:        
*************************************************/

class UsersLogs extends Model
{

    /**
     * add 添加日志
     * @karl
     * @DateTime 2016-07-31T23:23:02+0800
     * @param    int                      $uid  用户id
     * @param    string                   $info 用户信息
     * @return   array   [error_code, error_msg, id]
     */
    static public function add($uid, $info)
    {
        $result = ['error_code' => 0, 'error_msg' => ""];
        $url = $_SERVER['REQUEST_URI'];
        $ip = $_SERVER["REMOTE_ADDR"];
        $id = Db::table("user_logs")->insertGetId(['uid'=>$uid, 'access_url' => $url,'ip' => $ip, 'info' => $info, 'time' => time()]);
        if ($id) {
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['id'] = $id;
        } else {
            $result['error_code'] = 1;
            $result['error_msg'] = '添加失败';
        }
        return $result;
    }

    /**
     * get 查询日志
     * @karl
     * @DateTime 2016-07-31T23:24:35+0800
     * @param    array  $where                        查询条件
     * @param    int    $page                         第几页
     * @param    int    $page_num                     一页几条数据
     * @return   array  [error_code, error_msg, data] 查询数据
     */
    static public function getLogs($where, $page_num, $url)
    {
        $result = ['error_code' => 0, 'error_msg' => ""];
        $data = Db::name('user_logs')->where($where)->paginate($page_num, false, ['query'=>$url]);
        if ($data) {
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['data'] = $data;
        } else {
            $result['error_code'] = 1;
            $result['error_msg'] = '查询出错';
        }
        return $result;
    }
}