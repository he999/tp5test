<?php
namespace app\common\model;

use think\Model;
use think\Db;

/**
 * 购物车模型
 */
class Cart extends Model
{
    /**
     * add 购物车添加商品
     * @pk
     * @DateTime 2016-10-13T15:19:16+0800
     * @param    int                   $uid 用户id
     * @param    array                 $data 商品数组['spec_id(商品属性id)','buy_num(购买数量)']
     * @return   array                 
     * [error_code, error_msg,error_id or goods_id]                                       
     */
    static public function add($uid, $data,$is_debit = 0)
    {   
        //检查是否存在
        $cat = self::check($uid,$data['spec_id'],$is_debit);
        if ($cat['error_code'] == 0) {
            $res = self::setInc($cat['data']['id'],$data);
            if ($res['error_code'] == 0 ) {
                $result['error_code'] = 0;
                $result['error_msg'] = '';
                $result['id'] = $cat['data']['id'];
            }
            else{
                $result['error_code'] = 1;
                $result['error_msg'] = '添加错误'; 
            }
            return $result;
        }else{
            //查询数据
            $row = Db::name('goods_specs')
                 ->alias('gs')
                 ->join('goods g','gs.goods_id = g.goods_id','left')
                 ->where('gs.spec_id',$data['spec_id'])
                 ->field("gs.stock,gs.price,gs.goods_id,gs.color,gs.spec_3,gs.spec_1,gs.spec_2,
                    g.goods_name,g.goods_sn,g.weight,g.market_price,g.spec1_name,g.spec2_name,g.spec3_name")
                 ->find();
            $arr['goods_id'] = $row['goods_id'];
            $arr['sku'] = $row['stock'];
            $arr['goods_name'] = $row['goods_name'];
            $arr['goods_sn'] = $row['goods_sn'];
            $arr['market_price'] = $row['market_price'];
            $arr['shop_price'] = $row['price'];
            $arr['weight'] = $row['weight'];
            //拼接商品属性
            $type1 ='';$type2='';$type3='';
            if ( $row['spec1_name'] != '') {
                $type1 .= $row['spec1_name'].":".$row['spec_1'].",";
            }
            if ( $row['spec2_name'] != '') {
                $type2 .= $row['spec2_name'].":".$row['spec_2'].",";
            }
            if ( $row['spec3_name'] != '') {
                $type3 .= $row['spec3_name'].":".$row['spec_3'];
            }
            $arr['spec_value'] = $type1.$type2.$type3."颜色".":".$row['color'];
            $arr['uid'] = $uid;
            $arr['spec_id'] =  $data['spec_id'];
            $arr['add_time'] = time();
            $arr['buy_num'] = $data['buy_num'];
            $arr['is_debit'] = $is_debit;
            //插入数据库
            if ($rows = Db::name('cart')->insertGetId($arr)){
                $result['error_code'] = 0;
                $result['id'] = $rows;
            }
            else{
                $result['error_code'] = 1;
                $result['error_msg'] = '添加错误'; 
            }
            return $result;
        }
    }

    /**
     * editAll 批量购物车编辑
     * @karl
     * @DateTime 2016-09-06T06:19:16+0800
     * @param    int                   $uid 
     * @param    array                 $data
     * @return   array                 
     * [error_code, error_msg,error_id,spec_id]             
     */
    static public function editAll($uid, $data)    
    {   
        $where['uid'] = $uid;
        if($res = Db::name('cart')->where($where)->update($data)){
            $result['error_code'] = 0;
            $result['error_msg'] = '';
        }
        else{
            $result['error_code'] = '1';
            $result['error_msg'] = 'update错误';  
        } 
        return $result;
    }

    /**
     * edit 购物车编辑商品
     * @karl
     * @DateTime 2016-09-06T06:19:16+0800
     * @param    int                   $id 
     * @param    array                 $data 商品数组['buy_num(购买数量)]
     * @return   array                 
     * [error_code, error_msg,error_id,spec_id]             
     */
    static public function edit($id, $data)    
    {   
        $where['id'] = $id;
        if($res = Db::name('cart')->where($where)->update($data)){
            $result['error_code'] = 0;
            $result['error_msg'] = '';
        }
        else{
            $result['error_code'] = '1';
            $result['error_msg'] = 'update错误';  
        } 
        return $result;
    }

    /**
     * setInc 购物车 添加商品数量
     * @karl
     * @DateTime 2016-09-06T06:19:16+0800
     * @param    int                   $id 
     * @param    array                 $data 商品数组['buy_num(购买数量)'spec_id'(商品id)]
     * @return   array                 
     * [error_code, error_msg,error_id,spec_id]             
     */
    static public function setInc($id,$data)    
    {   
        $where['id'] = $id;
        if($res = Db::name('cart')->where($where)->setInc('buy_num',$data['buy_num'])){
            $result['error_code'] = 0;
            $result['error_msg'] = '';
        }
        else{
            $result['error_code'] = '1';
            $result['error_msg'] = 'update错误';  
        } 
        return $result;
    }

    /**
     * del 删除购物车商品
     * @karl
     * @DateTime 2016-09-06T06:19:16+0800
     * @param    int                   $id 购物车id
     * @return   array                 
     * [error_code, error_msg, error_id ]             
     */
    static public function del($id)
    {
        $where['id'] = $id;
        if($data = Db::name('cart')->where($where)->delete()){
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['data'] = $data;
        }
        else{
            $result['error_code'] = '1';
            $result['error_msg'] = '删除购物车商品失败';  
        } 
        
        return $result;
    }

    /**
     * check 检查商品是否在购物车
     * @xiao
     * @DateTime 2016-09-06T06:19:16+0800
     * @param    int                   $uid 用户id
     * @param    int                   $spec_id 商品规格id
     * @return   array                 [error_code, error_msg]     
     */
    static public function check($uid,$spec_id,$is_debit)
    {
        if ($uid){
            $where['uid'] =  $uid;
            $where['spec_id'] =  $spec_id;
            $where['is_debit'] =  $is_debit;
            if($data = Db::name('cart')->where($where)->find()){
                $result['error_code'] = 0;
                $result['error_msg'] = '';
                $result['data'] = $data;
            }
            else{
                $result['error_code'] = 1;
                $result['error_msg'] = '商品不在购物车';  
            } 
        }
        return $result;
    }

    /**
     * clear 清空购物车
     * @karl
     * @DateTime 2016-09-06T06:19:16+0800
     * @param    int                   $uid 用户id
     * 购买数量为0, 删除记录
     * @return   array                 [error_code, error_msg]     
     */
    static public function clear($uid)
    {
        if ($uid){
            $where = array('uid' => $uid);
            if($data = Db::name('cart')->where($where)->delete()){
                $result['error_code'] = 0;
                $result['error_msg'] = '';
                $result['data'] = $data;
            }
            else{
                $result['error_code'] = 1;
                $result['error_msg'] = '购物车清空失败';  
            } 
        }
        return $result;
    }
    
    /**
     * getList 得到用户购物车对应的商品列表
     * @karl
     * @DateTime 2016-09-28T22:41:15+0800
     * @param    int                $uid 用户id
     * @return   array              [error_code, error_msg, data=> []]
     */
    static public function getList($uid,$where = '')
    {
        if ($uid){
            $where['uid'] = $uid;
            $data = Db::name('cart')->alias('c')->join('goods g','g.goods_id = c.goods_id','left') ->field(["c.*","g.stock","g.cover_img"])->where($where)->select();
            if($data){
                $result['error_code'] = 0;
                $result['error_msg'] = '';
                $result['data'] = $data;
            }
            else{
                $result['error_code'] = '1';
                $result['error_msg'] = '得到一个订单下的商品列表失败';  
            }
        }
        return $result;
    }
}

?>