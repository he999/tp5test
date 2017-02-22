<?php
namespace app\common\model\base;

use think\Model;
use think\Db;

/*************************************************  
*ClassName:     CommonModel
*Description:   公用模型
*Others:      
*************************************************/

class CommonModel extends Model
{
    static protected $table_name = "";

    /**
     * setTable 设置表名
     * @xiao
     * @DateTime 2016-11-21T20:53:41+0800
     * @param    string                   $table_name 表名
     */
    static public function setTable($table_name)
    {
        self::$table_name = $table_name;
    }

    /**
     * add 增加
     * @xiao
     * @DateTime 2016-07-31T07:38:20+0800
     * @param    array                   $data 输入数据
     * @return   array                   [error_code, error_msg, id ]
     */
    static public function add($data)
    {
        $res = Db::name(self::$table_name)->insertGetId($data);
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['id'] = $res;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = '添加失败';
        }
        return $result;
    }

    /**
     * edit 编辑
     * @xiao
     * @DateTime 2016-07-31T07:38:20+0800
     * @param    array                   $data 输入数据
     * @return   array                   [error_code, error_msg]
     */
    static public function edit($id,$data)
    {   
        $res = Db::name(self::$table_name)->where($id)->update($data);
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = '';
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = '编辑失败';
        }
        return $result;
    }

    /**
     * del 删除
     * @xiao
     * @DateTime 2016-07-31T07:38:20+0800
     * @param    array                   $data 输入数据
     * @return   array                   [error_code, error_msg]
     */
    static public function del($data)
    {
        $res = Db::name(self::$table_name)->where($data)->delete();
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = '';
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = '删除失败';
        }
        return $result;
    }

    /**
     * getOneRow 查询一条数据
     * @xiao
     * @DateTime 2016-11-22T14:45:03+0800
     * @return   array                   [error_code, error_msg, data ]
     */
    static public function getOneRow($data = '')
    {
        $res = Db::name(self::$table_name)->where($data)->find();
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['data'] = $res;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = '查询失败';
        }
        return $result;
    }

    /**
     * getRows 查询多条数据
     * @xiao
     * @param    array                   $where 查询条件
     * @param    integer                 $page  当前页
     * @param    integer                 $page_num   每页显示数量
     * @param    array                   $sort  排序条件
     * @return   array                   [error_code, error_msg, data=> [] ]
     */
    static public function getRows($where = '', $page = 1, $page_num = 10, $sort = "")
    {

    }

}