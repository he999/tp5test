<?php
namespace app\common\model\base;

use think\Model;
use think\Db;

/*************************************************  
*ClassName:     UserAccess
*Description:   用户访问模型
*Others:        
*************************************************/

class UsersAccess extends Model
{

    /**
     * Function:      createAccess
     * Description:   新建一个访问
     * @param: string $uid                          用户id
     * @param：string $expire                       过期时间
     * Return: array [error_code, error_msg, data]  用户访问凭据信息      
     */
    static public function open($uid, $expire = 3600)
    {
        $result = ['error_code' => 0, 'error_msg' => ""];
        $access_token = md5(uniqid(rand()));
        $data = ['uid' => $uid, 'access_token' => $access_token, 'create_time' => time(), 'expire_time' => time() + $expire];
        if (self::create($data)) {
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['data'] = $data;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = '访问失败';
        }
        return $result;
    }

    /** 
     * Function:      getAccess
     * Description:   获取access信息
     * @param: string $access_token                用户id
     * Return: array [error_code, error_msg, data] 用户访问凭据信息      
     */    
    static public function getAccess($access_token)
    {
        $result = ['error_code' => 0, 'error_msg' => ""];
        $row = self::get(['access_token' => $access_token]);
        if ($row && $row->data['expire_time'] > time()) {
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['data'] = $row->data;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = '获取失败';
        }
        return $result;        
    }

    /**
     * Function:      renew
     * Description:   续约
     * @param: string $access_token           用户id
     * @param: string $expire_time            到期时间
     * Return: array [error_code, error_msg]  用户访问凭据信息      
     */    
    static public function renew($expire_time = 3600)
    {
        $result = ['error_code' => 0, 'error_msg' => ""];
        if (self::save(['expire_time' => $expire_time],['access_token' => $access_token]))
        {
            $result['error_code'] = 0;
            $result['error_msg'] = '';
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = '续约失败';
        }
        return $result;
    }
}

?>