<?php
namespace app\common\model\base;

use think\Model;
use think\Db;
/*************************************************  
*ClassName:     Coms
*Description:   公司模型
*Others:        
*************************************************/

class Coms extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'ys_coms_info';
    /**
     * add 增加一个公司
     * @karl
     * @DateTime 2016-07-31T07:38:20+0800
     * @param    array                   $data 输入数据
     * @return   array                   [error_code, error_msg, com_id=> [] ]
     */
    static public function add($data)
    {
        $result = Db::name('coms_info')->insertGetId($data);
        if($result) 
        {
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['com_id'] = $result;
        }
        else
        {
            $result['error_code'] = 1;
            $result['error_msg'] = '公司信息添加失败';
        }
        return $result;
    }

    /**
     * set 设置更新公司值 操作com_info表。无值就添加，有值就更新
     * @karl
     * @DateTime 2016-07-31T07:42:03+0800
     * @param    array                 $data   设置值
     * @param    int                   $com_id 公司id
     * @return   array                 [error_code, error_msg, com_id=> [] ] 返回结果
     */
    static public function set($data,$com_id = 1)
    {
        $error = [];
        foreach ($data as $key => $value) {
            $array = ['name' => $key, 'value' => $value, 'com_id' => $com_id];
            $row = self::get(['name' => $key]);
            if ($row) {
                if (!DB::name("coms_info")->update($array)) {
                    $error[] = "updata $key error,the value is $value";
                }
            }else{
                if (!DB::name("coms_info")->insert($array)) {
                    $error[] = "insert $key error,the value is $value";
                }
            }
        }
        if (count($error))
        {
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['com_id'] = $error;
        } 
        else
        {
            $result['error_code'] = 1;
            $result['error_msg'] = '操作完成';
        }
        return array('result' => $result, 'error' => $error);
    }

    /**
     * getValue 得到值
     * @karl
     * @DateTime 2016-08-02T07:41:49+0800
     * @param    string                   $name   标识
     * @param    integer                  $com_id 公司ID
     * @return   array                    [error_code, error_msg, data=> []
     */
    static public function getValue($name, $com_id = 1)
    {
        $row = self::get(['name' => $name, 'com_id' => $com_id]);
        if ($row)
        {
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['data'] = $row['value'];  
        }
        else
        {
            $result['error_code'] = 1;
            $result['error_msg'] = '得到失败';
            $result['data'] = '';
        }
        return $result;
    }

    /**
     * getInfos 得到一个公司com_info信息
     * @karl
     * @DateTime 2016-07-31T07:38:20+0800
     * @param    int                       $com_id 公司id
     * @return   array                    [error_code, error_msg, array=> [] 
     */
    static public function getInfos($com_id = 1)
    {
        $out_array = [];
        if ($array = DB::name("coms_info")->where(['com_id' => $com_id])->select()) {
            foreach($array as $row) {
                $out_array[$row['name']] = $row['value'];
            }
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['data'] = $out_array;
        } 
        else 
        {
            $result['error_code'] = 1;
            $result['error_msg'] = '没有得到com_info信息';
        }
        return $out_array;
    }

    /**
     * getInfos 得到返佣比例信息
     * @xiao
     * @DateTime 2016-07-31T07:38:20+0800
     * @param    int                      $id id
     * @return   array                    [error_code, error_msg, data=> [] ]
     */
    static public function getRebateInfos($where)
    {
        $res = Db::name('rebate_set')->where($where)->select();
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
     * getInfos 设置返佣比例信息
     * @xiao
     * @DateTime 2016-07-31T07:38:20+0800
     * @param    array                      $data [rebate_rate_lv1=> ,....]
     * @return   array                    [error_code, error_msg, data=> [] ]
     */
    static public function setRebateInfos($id,$data)
    {
        $res = Db::name('rebate_set')->where(['id' => $id])->update($data);
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['data'] = $res;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = '修改失败';
        }
        return $result;
    }
}