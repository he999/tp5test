<?php
namespace app\common\model;

use think\Model;
use think\Db;

/**
 * 运输模型
 * add 添加运输模型
 * edit 编辑运输模型
 * getOne 得到一个运输记录
 * getShippingList 得到公司运输方式列表
 * getShipping 得到运输方式
 * getShippingPrice 得到运输费用
 */
class Shipping extends Model
{
    /**
     * add 添加运输模型
     * @karl
     * @DateTime 2016-09-06T06:19:16+0800
     * @param    array                   $data 添加数组
     * @param    array                   $com_id 公司id
     * @return   array              [error_code, error_msg,shipping_id]
     */
    static public function add($data ) //,$com_id = 1
    {   
        //$data['com_id'] = $com_id;
        $row = DB::name('shipping_companies')->insertGetId($data);
        if ($row)
        {
           $result['error_code'] = 0;
           $result['error_msg'] = "";
           $result['shipping_id'] = $row;
        }
        else 
        {
           $result['error_code'] = 1;
           $result['error_msg'] = "添加失败";
        } 
        return $result;
    }

    /**
     * addShipping 添加运输区域
     * @tanlong
     * @DateTime 2016-09-06T06:19:16+0800
     * @param    array                   $data 添加数组
     * @param    array                   $com_id 公司id
     * @return   array              [error_code, error_msg,shipping_id]
     */
    static public function addShipping($data,$input_region,$input_region2,$com_id = 1)
    {   
        $data['com_id'] = $com_id;
        $row = DB::name('shipping')->insertGetId($data);
        if ($row)
        {
              if($input_region[0]!=''){
                 $region_id=$input_region[0];               
              }
              if($input_region[1]!=''){
                    $region_id=$input_region[1];
              }
              if($input_region[2]!=''){
                    $region_id=$input_region[2];
              }
              $data = [
                      'shipping_id' => $row,
                      'region_id'=> $region_id,
                     ];           
              $row2 = DB::name('shipping_regions')->insert($data);
              if($input_region2!==false){
                  $key = array_search($region_id,$input_region2);
                  if ($key !== false){
                       array_splice($input_region2, $key);
                  }
                  $input_region2=array_unique($input_region2);
                  foreach ($input_region2 as $k => $v) {   //多个添加的地区
                          $data = [
                          'shipping_id' => $row,
                          'region_id'=> $v,
                          ];       
                          $row3 = DB::name('shipping_regions')->insert($data);   
                  } 
              }             
           if($row2){
               $result['error_code'] = 0;
               $result['error_msg'] = "";
               $result['shipping_id'] = $row;
           }else{
                $result['error_code'] = 1;
                $result['error_msg'] = "添加失败";
           }        
        }
        else 
        {
           $result['error_code'] = 1;
           $result['error_msg'] = "添加失败";
        } 
        return $result;
    }

    /**
     * editShipping 编辑运输区域
     * @tanlong
     * @DateTime 2016-09-06T06:19:16+0800
     * @param    array                   $data 添加数组
     * @param    array                   $com_id 公司id
     * @return   array              [error_code, error_msg,shipping_id]
     */
    static public function editShipping($data,$input_region,$input_region2,$com_id = 1)
    {   
        $data['com_id'] = $com_id;
        $row = DB::name('shipping')->update($data);
        if ($row!==false)
        {
              DB::name('shipping_regions')->where(['shipping_id'=>$data['shipping_id']])->delete();
              if($input_region[0]!=''){
                 $region_id=$input_region[0];               
              }
              if($input_region[1]!=''){
                    $region_id=$input_region[1];
              }
              if($input_region[2]!=''){
                    $region_id=$input_region[2];
              }
              if(isset($region_id)){
                      $data = [
                      'shipping_id' => $data['shipping_id'],
                      'region_id'=> $region_id,
                     ];           
                     $row2 = DB::name('shipping_regions')->insert($data);
              }      
              if($input_region2!==false){
                  if(isset($region_id)){
                      $key = array_search($region_id,$input_region2);
                      if ($key !== false){
                           array_splice($input_region2, $key);
                      }
                  } 
                  $input_region2=array_unique($input_region2);
                  foreach ($input_region2 as $k => $v) {   //多个添加的地区
                          $data = [
                          'shipping_id' => $data['shipping_id'],
                          'region_id'=> $v,
                          ];       
                          $row3 = DB::name('shipping_regions')->insert($data);   
                  } 
              }             
               $result['error_code'] = 0;
               $result['error_msg'] = "";
               $result['shipping_id'] = $row; 
        }
        else 
        {
           $result['error_code'] = 1;
           $result['error_msg'] = "添加失败";
        } 
        return $result;
    }
    /**
     * edit 编辑运输模型
     * @karl
     * @DateTime 2016-09-06T06:19:16+0800
     * @param    array                   $data 添加数组
     * @param    array                   $shipping_id id
     * @return   array                 [error_code, error_msg]                  
     */
    static public function edit($data)    //,$shipping_id
    {
        $row = Db::name('shipping_companies')->update($data);  //->where(['shipping_id' => $shipping_id])
        if ($row!==false) 
        {                
            $result['error_code'] = 0;
            $result['error_msg'] = "";
        }
        else{                 
             $result['error_code'] = 1;
             $result['error_msg'] = "编辑订单失败";
        }
        return $result;

    }

    /**
     * del 删除运输模型
     * @karl
     * @DateTime 2016-09-06T06:19:16+0800
     * @param    array                   $shipping_id id
     * @return   array                 [error_code, error_msg]                  
     */
    static public function del($id)    
    {
        $row = Db::name('shipping_companies')->delete($id); 
        if ($row) 
        {                
            $shi=Db::name('shipping')->where(array('shipping_com_id'=>$id))->select();
            Db::name('shipping')->where(array('shipping_com_id'=>$id))->delete();
            foreach ($shi as $k => $v) {
                Db::name('shipping_regions')->where(array('shipping_id'=>$v['shipping_id']))->delete();
            }
            $result['error_code'] = 0;
            $result['error_msg'] = "";
        }
        else{                 
             $result['error_code'] = 1;
             $result['error_msg'] = "删除订单失败";
        }
        return $result;

    }
    
    /**
     * getOne 得到一个公司记录
     * 联表查询shipping_regions表对应的地区
     * @karl
     * @DateTime 2016-09-28T22:41:15+0800
     * @param    int                $shipping_id 运输id
     * @return   array              [error_code, error_msg, data=> [] ]
     */
    static public function getOne($id)
    {
        $row = Db::name('shipping_companies')->find($id);
        if($row)
        {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] = $row;
        }
        else
        {
            $result['error_code'] = 1;
            $result['error_msg'] = "没有得到订单详情";
        }
        return $result;
    }

    /**
     * getShippingList 得到公司运输方式列表
     * @karl
     * @DateTime 2016-10-07T21:49:42+0800
     * @param    int                   $com_id 公司id
     * @return   array                 [error_code, error_msg, data] 
     */
    static public function getShippingList()
    {   
        $where['enable'] = 1;
        $row = Db::name('shipping_companies')->where($where)->select();
        if($row)
        {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] = $row;
        }
        else
        {
            $result['error_code'] = 1;
            $result['error_msg'] = "没有找到开启的";
        }
        return $result;
    }

    /**
     * getShipping 得到运输方式
     * @tanlong
     * @DateTime 2016-10-07T21:49:42+0800
     * @param    int                   $region_id 地区id
     * @param    int                   $com_id 公司id
     * @param    int                   $shopping_com_id 运输公司id
     * @return   array                 [error_code, error_msg, data] 
     */
    static public function getShipping($shopping_com_id)
        {   
            $lst = Db::name('shipping')
                  ->where(array('shipping_com_id'=>$shopping_com_id))
                  ->field(["shipping_id","shipping_name","shipping_des"])
                  ->select();
            if($lst)
            {    
                foreach ($lst as $k => $v) {
                    $arr=Db::name('shipping_regions')
                    ->field('region_id')
                    ->where(array('shipping_id'=>$v['shipping_id']))
                    ->select();
                    foreach ($arr as $k2 => $v2) {
                        $name=Db::name('regions')
                        ->field('name')
                        ->where(array('id'=>$v2['region_id']))
                        ->find();
                        $lst[$k][$k2]['name']=$name['name'];
                    }
                }    
                // dump($lst);
                // die;
                $result['error_code'] = 0;
                $result['error_msg'] = "";
                $result['data'] = $lst;
            }
            else
            {
                $result['error_code'] = 1;
                $result['error_msg'] = "没有得到订单详情";
            }
            return $result;
    }

    /**
     * getShippingOne 编辑界面默认属性
     * @tanlong
     * @DateTime 2016-10-07T21:49:42+0800
     * @param    int                   $region_id 地区id
     * @param    int                   $com_id 公司id
     * @param    int                   $shopping_com_id 运输公司id
     * @return   array                 [error_code, error_msg, data] 
     */
    static public function getShippingOne($id)
        {   
            $lst = Db::name('shipping')
                  //->field(["shipping_id","shipping_name","shipping_des"])
                  ->find($id);
            $city=Db::name('shipping_regions')
                  ->where(['shipping_id'=>$id])
                  ->select();
            $names=[];
            foreach ($city as $k2 => $v2) {
                        $name=Db::name('regions')
                        ->field('name')
                        ->where(array('id'=>$v2['region_id']))
                        ->find();
                        $names[]=$name;
            }
            if($lst)
            {    
                $result['error_code'] = 0;
                $result['error_msg'] = "";
                $result['data'] = $lst;
                $result['city'] = $city;
                $result['names'] = $names;
            }
            else
            {
                $result['error_code'] = 1;
                $result['error_msg'] = "没有得到订单详情";
            }
            return $result;
    }

    /**
     * del 删除区域
     * @tanlong
     * @DateTime 2016-09-06T06:19:16+0800
     * @param    array                   $shipping_id id
     * @return   array                 [error_code, error_msg]                  
     */
    static public function regionDel($shipping_id)    
    {
        $row = Db::name('shipping')->delete($shipping_id);  
        if ($row) 
        {                
            Db::name('shipping_regions')->where(array('shipping_id'=>$shipping_id))->delete();
            $result['error_code'] = 0;
            $result['error_msg'] = "";
        }
        else{                 
             $result['error_code'] = 1;
             $result['error_msg'] = "删除订单失败";
        }
        return $result;
    }

    /**
     * getShipping 得到运输方式
     * @tanlong
     * @DateTime 2016-10-07T21:49:42+0800
     * @param    int                   $region_id 地区id
     * @param    int                   $com_id 公司id
     * @param    int                   $shopping_com_id 运输公司id
     * @return   array                 [error_code, error_msg, data] 
     */
    static public function getTacitShipping($company_name = '货到付款')
    {
        $res = Db::name('shipping_companies')->where(['company_name' => $company_name])->find();
        if($res)
        {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] = $res;
        }
        else
        {
            $result['error_code'] = 1;
            $result['error_msg'] = "没有找到";
        }
        return $result;
    }

    /**
     * getShippingPrice 地区查找运输费用
     * @xiao
     * @DateTime 2016-10-07T21:49:42+0800
     * @param    int                   $weight 重量
     * @param    int                   $region_id 地区id
     * @param    int                   $com_id 公司id
     * @param    int                   $shopping_com_id 运输公司id
     * @return   array                 [error_code, error_msg, price,data=>[]] 
     */
    static public function getShippingPrice($weight, $region_id,$shipping_com_id = 1,$byunm,$com_id = 1 )
    {
        foreach ($region_id as $v) {
            $where['r.region_id'] = $v;
            $where['s.com_id'] = $com_id;
            $where['c.shipping_com_id'] = $shipping_com_id;
            
            $row = Db::name('shipping')
            ->alias('s')
            ->join('shipping_regions r','s.shipping_id = r.shipping_id','left')
            ->join('shipping_companies c','s.shipping_com_id = c.shipping_com_id','left')
            ->where($where)
            ->find();

            if ($row) {
                break;
            }
        }
        
        if($row)
        {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            //按件数
            if ($row['type'] == 1) {
                if ($byunm == 1) { //一件的
                    $sum = $row['num_price'];
                }else{
                    $sum = $row['num_price'];
                    $sum += $row['num_price_out']*($byunm-1);
                    if ($sum >= $row['num_price_max']) {
                        $sum = $row['num_price_max'];
                    }
                }
                $result['price'] = $sum;
            }else{ //按重量
                if ($row['weight_in'] >= $weight) {
                    //重量 在范围内
                    $sum = $row['weight_price_in'];
                }elseif ($weight > $row['weight_in']) {
                    //重量 超重
                    $sum = $row['weight_price_in'];
                    $sum += ceil(($weight - $row['weight_in'])/$row['weight_out'])*$row['weight_price_out'];
                }
                $result['price'] = $sum;
                
            }
            $result['data'] = $row;
        }
        else
        {
            $result['error_code'] = 1;
            $result['error_msg'] = "没有得到详情";
        }
        return $result;
    }

}

?>