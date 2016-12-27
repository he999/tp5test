<?php
namespace app\common\model;

use think\Model;
use think\Db;

/**
 * 借阅模型
 *tanlong
 * getBorrow 展示借阅设置
 */
class Borrow extends Model
{
    /**
     * getBorrow 展示借阅设置
     * @tanlong
     * @DateTime 2016-12-5T13:29:34+0800
     * @param    
     * @return   
     */
    static public function getBorrow()
    {
        $row=Db::name('coms_info')->select();
        if($row){
            $arr['error_code'] = 0;
            $arr['error_msg'] = "";
            $arr['data'] = $row;
        }else{
            $arr['error_code'] = 1;
            $arr['error_msg'] = "没有得到订单详情";
        }
        return $arr;
    }

     /**
     * getCustomers 展示借阅用户
     * @tanlong
     * @DateTime 2016-12-5T13:29:34+0800
     * @param    
     * @return   
     */
    static public function getCustomers($num, $where, $url=[], $page='') 
    {
        if (empty($page)){
            $row=Db::name('users_customers')->where($where)->paginate($num, false, array('query'=>$url)); 
        }else{
            $row=Db::name('users_customers')->where($where)->page($page, $num)->select();
        }
        if($row){
            $arr['error_code'] = 0;
            $arr['error_msg'] = "";
            $arr['data'] = $row;
        }else{
            $arr['error_code'] = 1;
            $arr['error_msg'] = "没有得到订单详情";
        }
        return $arr;
    }

    /**
     * userDel 用户借阅删除
     * @tanlong
     * @DateTime 2016-12-5T13:29:34+0800
     * @param    
     * @return   
     */
    static public function userDel($id) 
    {
        $row=Db::name('users_customers')->where( array('uid'=>$id))->update( array('is_del'=>1));
        if($row){
            Db::name('orders')->where( array('uid'=>$id))->update( array('is_dels'=>1));
        }
        if($row){
            $arr['error_code'] = 0;
            $arr['error_msg'] = "";
            $arr['data'] = $row;
        }else{
            $arr['error_code'] = 1;
            $arr['error_msg'] = "没有得到订单详情";
        }
        return $arr;
    }

     /**
     * getRecord 借阅记录展示
     * @tanlong
     * @DateTime 2016-12-5T13:29:34+0800
     * @param    
     * @return   
     */
    static public function getRecord($num,$where,$url=[], $page='') 
    {
         if (empty($page)){
             $row=Db::name('orders')->where($where)
             ->alias('a')
             ->join('users_customers c','a.uid = c.uid','left')
             ->order("create_time DESC")
             ->paginate($num, false, array('query'=>$url)); 
         }else{
             $row=Db::name('orders')->where($where)
             ->alias('a')
             ->join('users_customers c','a.uid = c.uid','left')
             ->order("create_time DESC")
             ->page($page, $num)->select();
         }
         
        if($row){
            $arr['error_code'] = 0;
            $arr['error_msg'] = "";
            $arr['data'] = $row;
        }else{
            $arr['error_code'] = 1;
            $arr['error_msg'] = "没有得到订单详情";
        }
        return $arr;
    }

     /**
     * getRecord 借阅记录单个展示
     * @tanlong
     * @DateTime 2016-12-5T13:29:34+0800
     * @param    
     * @return   
     */
    static public function getRecordOne($id) 
    {
         $row=Db::name('orders')->alias('a')->join('users_customers c','a.uid = c.uid','left')->find($id); 
         $row2=Db::name('orders_goods')
         ->where(array('order_id'=>$id))
         ->field(['goods_sn','goods_name','buy_num','renew_time','is_back'])
         ->select();
        if($row && $row2){
            $arr['error_code'] = 0;
            $arr['error_msg'] = "";
            $arr['data'] = $row;
            $arr['data2'] = $row2;
        }else{
            $arr['error_code'] = 1;
            $arr['error_msg'] = "没有得到订单详情";
        }
        return $arr;
    }

    /**
     * getRecord 借阅记录修改
     * @tanlong
     * @DateTime 2016-12-5T13:29:34+0800
     * @param    
     * @return   
     */
    static public function borrowingRecordEdit($id, $input) 
    {
         $row=Db::name('orders')
         ->where(array('order_id'=>$id))
         ->update(array('consignee'=>$input['consignee'],
                                  'address'=>$input['address'],                             
         ));
         if(array_key_exists('shipping_time',$input)){
            if($input['shipping_time']==3){
             Db::name('orders')
             ->where(array('order_id'=>$id))
             ->update(array('order_status'=>2,'shipping_time'=>time()));
            }
         } 
         if(array_key_exists('status',$input)){
            if($input['status']!=''){
             Db::name('orders')
             ->where(array('order_id'=>$id))
             ->update(array('order_status'=>3,'confirm_time'=>time()));
            }
         }              
         $row2= Db::name('orders_goods')
         ->where(array('order_id'=>$id))
         ->field(['id'])
         ->select();
         if($row2){
            foreach ($row2 as $k => $v) {
             $row3=Db::name('orders_goods')->where(array('id'=>$v['id']))->update(array('is_back'=>$input['is_backs'][$k]));
             if($row3 && $input['is_backs'][$k]==1){   //如果更新成功,再判断是不是更新为还书状态,是的话才改时间
                $row4=Db::name('orders')->where(array('order_id'=>$id))->update(array('confirm_time'=>time()));
             }
            }
         }        
        if($row!==false && $row3!==false){
            $arr['error_code'] = 0;
            $arr['error_msg'] = "";
            $arr['data'] = $row;
            $arr['data2']=$row2;
        }else{
            $arr['error_code'] = 1;
            $arr['error_msg'] = "没有得到订单详情";
        }
        return $arr;
    }

    /**
     * RecordDel 用户记录删除
     * @tanlong
     * @DateTime 2016-12-5T13:29:34+0800
     * @param    
     * @return   
     */
    static public function RecordDel($id) 
    {
        $row=Db::name('orders')->where(array('order_id'=>$id))->update(array('is_dels'=>1,'order_status'=>3));
        if($row){
            $arr['error_code'] = 0;
            $arr['error_msg'] = "";
            $arr['data'] = $row;
        }else{
            $arr['error_code'] = 1;
            $arr['error_msg'] = "没有得到订单详情";
        }
        return $arr;
    }
}
?>