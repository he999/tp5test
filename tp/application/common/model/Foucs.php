<?php
namespace app\common\model;

use think\Model;
use think\Db;

/*************************************************  
*ClassName:     Foucs
*Description:   主图焦点模型
*Others:        
*************************************************/

class Foucs extends Model
{
    /**
     * add 添加主图
     * @karl
     * @DateTime   
     * @param     array                   $data     主图信息
     * @return    array   [error_code, error_msg, goods_id] 
     */
    static public function add($data, $com_id = 1)
    {
        $data['com_id'] = $com_id;
        $row = Db::name('foucs')->insertGetId($data);
        if ($row)
        {
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['data'] = $row;
        }
        else
        {
            $result['error_code'] = 1;
            $result['error_msg'] = '主图添加失败';
        }
        return $result;
    }

    /**
     * update 更新主图
     * @karl
     * @DateTime  2016-07-31T07:30:13+0800
     * @param     array                $data     主图信息
     * @return    [error_code, error_msg, data=>[]]
     */
    static public function edit($data)
    {
        //array $data 数据中必须包含主键
        $row = Db::name('foucs')->update($data);
        if ($row)
        {
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['data'] = $row;
        }
        else
        {
            $result['error_code'] = 1;
            $result['error_msg'] = '主图编辑失败';
        }
        return $result;
    }

    /**
     * getList 查询主图信息
     * @karl
     * @DateTime  
     * @param    int                     $num      查询数量
     * @param    array                   $where    查询条件
     * @return   array                             返回数据
     */
    static public function getList($where = ['com_id' => 1] )
    {
        $array = Db::table('ys_foucs')->where($where)->order("sort desc")->select();
        if ($array)
        {
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['data'] = $array;
        }
        else
        {
            $result['error_code'] = 1;
            $result['error_msg'] = '主图信息查询失败';
        }
        return $result;
    }

    /**
     * del 删除主图
     * @karl
     * @param    int                   $fid      id条件 
     * @return   int  4                           返回数据
     */
    static public function del($fid)
    {       
        $row = Db::name('foucs')->delete($fid);
        if($row)
        {
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['data'] = $row;
        }
        else
        {
            $result['error_code'] = 1;
            $result['error_msg'] = '主图删除失败';
        }
       return $result;
    }

    /**
     * advertisementDel删除广告
     * @karl
     * @param    int                   $fid      id条件 
     * @return   int  4                           返回数据
     */
    static public function advertisementDel($fid)
    {       
        $row = Db::name('foucs')->delete($fid);
        if($row)
        {
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['data'] = $row;
        }
        else
        {
            $result['error_code'] = 1;
            $result['error_msg'] = '主图删除失败';
        }
       return $result;
    }

}
