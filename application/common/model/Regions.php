<?php
namespace app\common\model;

use think\Model;
use think\Db;

/**
 * 地区模型
 * getName 得到地区名称
 * getParents 得到子级数据
 */
class Regions extends Model
{

    /**
     * getName 得到地区名称
     * @karl
     * @DateTime 2016-08-08T12:13:52+0800
     * @param    int                   $id 地区id
     * @return   array                 [error_code, error_msg, data]                
     */
    static public function getName($id)
    {
        $data = DB::name("regions")->where(['id' => $id])->find();
        if ($data) 
        {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] = $data;
        }
        else
        {
            $result['error_code'] = 1;
            $result['error_msg'] = "没有得到地区详情";
        }
        return $result;
    }

    /**
     * getNameStr 得到地址的地区名称
     * @karl
     * @DateTime 2016-08-08T12:13:52+0800
     * @param    int                   $id[''] 地区id
     * @return   array                 [error_code, error_msg, data]                
     */
    static public function getNameStr($idarr)
    {   
        $str = '';
        foreach ($idarr as $key => $v) {
            if ($v != 0) {
                $res = self::getName($v);
                $str.=$res['data']['name'];
            }
        }
        return $str;
    }

    /**
     * getParents 得到子级数据
     * @karl
     * @DateTime 2016-08-08T12:14:44+0800
     * @param    int                   $id    地区id
     * @return   array                 [error_code, error_msg, data] 
     */
    static public function getChildren($id)
    {
        $data = DB::name("regions")->where(['parent_id' => $id])->select();
        if ($data) 
        {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] = $data;
        }
        else
        {
            $result['error_code'] = 1;
            $result['error_msg'] = "没有得到地区详情";
        }
        return $result;
    }

    /**
     * getLevel 分级得到地区名称
     * @xiao
     * @DateTime 2016-08-08T12:13:52+0800
     * @param    int                   $level 地区分级
     * @param    int                   $pid   地区父级
     * @return   array                 [error_code, error_msg, data]                
     */
    static public function getLevel($level,$pid = '')
    {   
        $where['level'] = $level;
        $where['parent_id'] = $pid;
        $data = DB::name("regions")->where($where)->select();
        if ($data) 
        {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] = $data;
        }
        else
        {
            $result['error_code'] = 1;
            $result['error_msg'] = "没有得到地区详情";
        }
        return $result;
    }

}

?>