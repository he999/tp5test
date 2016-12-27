<?php
namespace app\common\model;

use think\Model;
use think\Db;

/**
 * 支付模型
 * Payments 得到店铺的支付方式
 * getOne 得到一种支付方式信息
 */
class Payments extends Model
{

    /**
     * Payments 得到店铺的支付方式
     * @karl
     * @DateTime 2016-09-28T22:41:15+0800
     * @param    int                   $goods_id 商品id
     * @return   array                 [error_code, error_msg, data=> [] ]
     */
    static public function getPayments($com_id)
    {
        if($com_id)
        {
            $where = ['com_id' => $com_id];
            if($row = Db::name('payments')->where($where)->select())
            {
                $result['error_code'] = 0;
                $result['error_msg'] ='';
                $result['data'] = $row;
            }
            else
            {
                $result['error_code'] = 1;
                $result['error_msg'] = '没有得到店铺的支付方式';
            }
        }
        return $result;
        

    }

    /**
     * getOne 得到一种支付方式信息
     * @karl
     * @DateTime 2016-10-07T21:32:41+0800
     * @param    int                   $com_id 公司id
     * @return   array                 [error_code, error_msg, data=> [] ]
     */
    static public function getOne($pay_id)
    {
        if($pay_id)
        {
            $where = ['pay_id' => $pay_id];
            if($row = Db::name('payments')->where($where)->find())
            {
                $result['error_code'] = 0;
                $result['error_msg'] ='';
                $result['data'] = $row;
            }
            else
            {
                $result['error_code'] = 1;
                $result['error_msg'] = '没有得到支付信息详情';
            }
        }
        return $result;

    }

    /**
     * getList 得到支付方式列表
     * @xiao
     * @DateTime 2016-10-07T21:32:41+0800
     * @param    int                   $com_id 公司id
     * @return   array                 [error_code, error_msg, data=> [] ]
     */
    static public function getList()
    {

        $where['enabled'] = 1;
        if($row = Db::name('payments')->where($where)->select())
        {
            $result['error_code'] = 0;
            $result['error_msg'] ='';
            $result['data'] = $row;
        }
        else
        {
            $result['error_code'] = 1;
            $result['error_msg'] = '没有得到支付信息详情';
        }
        return $result;

    }


}

?>