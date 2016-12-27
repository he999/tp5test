<?php
namespace app\common\model;

use think\Model;
use think\Db;

/**
 * 用户地址模型
 */
class UsersAddress extends Model
{
     /**
     * add 添加用户地址
     * @pk
     * @DateTime 2016-10-13T09:41:15+0800
     * @param    array                 $data 地址数据
     * @return   array                 [error_code, error_msg, address_id]
     */
    static public function add($data)
    {
        $result = [];
        if ($data){
            $data['is_default'] = time();
            if ($row = Db::name('users_address')->insertGetId($data)){
                $result['error_code'] = 0;
                $result['error_msg'] = '';
                $result['address_id'] = $row;
            }
            else{
                $result['error_code'] = 2;
                $result['error_msg'] = '未更改'; //插入失败
            }
        }
        return $result;
    }

    /**
     * edit 修改用户地址
     * @pk
     * @DateTime 2016-10-13T09:41:15+0800
     * @param    int                   $address_id 地址id
     * @param    array                 $data 更新数据
     * @return   array                 [error_code, error_msg]
     */
    static public function edit($address_id, $data)
    {
        $where = array('address_id' => $address_id);
        if($update = Db::name('users_address')->where($where)->update($data)){
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['address_id'] = $update;
        }
        else{
            $result['error_code'] = '1';
            $result['error_msg'] = 'update error';  //编辑失败
        }
        return $result;
    }

    /**
     * del 删除地址
     * @pk
     * @DateTime 2016-10-13T09:41:15+0800
     * @param    int                   $address_id  地址id
     * @return   array                 [error_code, error_msg]
     */
    static public function del($address_id)
    {
        $where = array('address_id' => $address_id);
        if($del = Db::name('users_address')->where($where)->delete()){
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['address_id'] = $del;
        }
        else{
            $result['error_code'] = '1';
            $result['error_msg'] = 'del error'; //删除失败
        }
        return $result;
    }

    /**
     * edit 修改用户默认地址
     * @pk
     * @DateTime 2016-10-13T09:41:15+0800
     * @param    int                   $address_id 地址id
     * @return   array                 [error_code, error_msg]
     */
    static public function editDefault($address_id)
    {
        $where = array('address_id' => $address_id);
        $data['is_default'] = time();
        if($del = Db::name('users_address')->where($where)->update($data)){
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['address_id'] = $del;
        }
        else{
            $result['error_code'] = '1';
            $result['error_msg'] = 'del error'; //删除失败
        }
        return $result;
    }

    /**
     * getList 得到地址列表
     * @pk
     * @DateTime 2016-10-13T09:41:15+0800
     * @param    int                $uid 用户id
     * @return   array              [error_code, error_msg, data=> array（数据）,] ]
     */
    static public function getList($uid)
    {
        if ($uid){
            $where = array('uid' => $uid);
            if($data = Db::name('users_address')->where($where)->order("is_default desc")->select()){
                $result['error_code'] = 0;
                $result['error_msg'] = '';
                $result['data'] = $data;
            }
            else{
                $result['error_code'] = '1';
                $result['error_msg'] = 'gets error';  //查找失败
            } 
        }
        return $result;
    }

    /**
     * getList 得到用户默认地址
     * @xiao
     * @DateTime 2016-10-13T09:41:15+0800
     * @param    int                $uid 用户id
     * @return   array              [error_code, error_msg, data=> array（数据）,] ]
     */
    static public function getAddress($uid,$address_id = '')
    {
        if ($uid){
            $where = array('uid' => $uid);
            $data = Db::name('users_address')->where($where)->order('is_default desc')->find();
            if($data){
                $result['error_code'] = 0;
                $result['error_msg'] = '';
                $result['data'] = $data;
            }
            else{
                $result['error_code'] = '1';
                $result['error_msg'] = '查找失败';
            } 
        }
        return $result;
    }
}

?>