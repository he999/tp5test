<?php
namespace app\common\model;

use think\Model;
use think\Db;

/**
 * 申请加盟模型
 */
class ApplicationJoin extends Model
{
    /**
     * adds 添加申请加盟
     * @xiao
     * @DateTime 2016-09-06T06:19:16+0800
     * @param    array              $data 添加数组 []
     * @return   array              [error_code, error_msg, goods_id]
     */
    static public function add($data)
    {
        $row = Db::name('application_join')->insertGetId($data);
        if ($row)
        {
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['goods_id'] = $row;
        }
        else
        {
            $result['error_code'] = 1;
            $result['error_msg'] = '添加失败';
        }
      
        return $result;
    }

    /**
     * del 删除申请加盟
     * @xiao
     * @DateTime 2016-09-06T06:53:19+0800
     * @param    array                 $data ['id'=>'']
     * @return   array                 [error_code, error_msg]                
     */
    static public function del($data)
    {
        if($del = Db::name('application_join')->where($where)->delete())
        {
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['goods_id'] = $del;
        }
        else
        {
            $result['error_code'] = '1';
            $result['error_msg'] = '删除失败';
        }
        return $result;
    }

    /**
     * getComment 得到一个申请加盟详情
     * @xiao
     * @DateTime 2016-09-28T22:41:15+0800
     * @param    int                $id 申请加盟id
     * @return   array              [error_code, error_msg, data=> [] ]
     */
    static public function getComment($id)
    {
        if ($goods_id)
        {
            $where = array('goods_id' => $goods_id);
            if($data = Db::name('application_join')->where($where)->select())
            {
                $result['error_code'] = 0;
                $result['error_msg'] = '';
                $result['data'] = $data;
            }
            else
            {
                $result['error_code'] = 1;
                $result['error_msg'] = '没有查到详情';
            }
            
        }
            return $result;

    }   
}

?>