<?php
namespace app\common\model;

use think\Model;
use think\Db;

/**
 * 商品属性模型
 * adds 添加商品属性(多个)
 * edits 编辑商品属性(多个)
 * del 删除商品(多个)
 * getSpecs 得到一个商品的属性列表
 */
class GoodsSpecs extends Model
{
    /**
     * adds 添加商品属性(多个)
     * @karl
     * @DateTime 2016-09-06T06:19:16+0800
     * @param    int                $goods_id 商品id
     * @param    array              $data 添加数组 [[商品属性数组],]
     * @return   array              [error_code, error_msg, goods_id]
     */
    static public function add($goods_id, $data)
    {
        $result['error_code'] = 0;
        if ($data)
        {
            foreach($data as $key => $value)
            {   
                $add = array_merge($value,['goods_id' => $goods_id ]);
                $row = Db::name('goods_specs')->insertGetId($add);
                if ($row)
                {
                    $result['error_msg'] = '';
                    $result['goods_id'] = $row;
                }
                else
                {
                    $result['error_code'] += 1;
                    $result['error_msg'] = '商品属性添加失败';
                }
            }
        }
        return $result;
    }
    /**
     * addsONE 添加商品属性 
     * @karl
     * @DateTime 2016-09-06T06:19:16+0800
     * @param    int                $goods_id 商品id
     * @param    array              $data 添加数组 [[商品属性数组],]
     * @return   array              [error_code, error_msg, goods_id]
     */
    static public function addOne($data)
    {
        if ($data)
        {
           $row = Db::name('goods_specs')->insertGetId($data);
                if ($row)
                {
                    $result['error_msg'] = '';
                    $result['goods_id'] = $row;
                }
                else
                {
                    $result['error_code'] += 1;
                    $result['error_msg'] = '商品属性添加失败';
                }
           
        }
        return $result;
    }
    /**
     * edits 编辑商品属性(多个)
     * @karl
     * @DateTime 2016-09-06T06:19:16+0800
     * @param    array                 $data 编辑数据 [商品属性id=>[],]
     * @return   array                 [error_code, error_msg]             
     */
    static public function edit($data)
    {
        foreach ($data as $key => $value) 
        {
            $where = ['spec_id' => $value['spec_id']];
            if($update = Db::name('goods_specs')->where($where)->update($value))
            {
                $result['error_code'] = 0;
                $result['error_msg'] = '';
            }
            else
            {
                $result['error_code'] = 1;
                $result['error_msg'] = '商品属性编辑失败';
            }
        } 
        return $result;
    }
     /**
     * editOne 编辑商品属性 
     * @karl
     * @DateTime 2016-09-06T06:19:16+0800
     * @param    array                 $data 编辑数据 [商品属性id=>[],]
     * @return   array                 [error_code, error_msg]             
     */
    static public function editOne($spec_id,$data)
    {
        
        if($update = Db::table('ys_goods_specs')->where(['spec_id'=>$spec_id])->update($data))
        {
            $result['error_code'] = 0;
            $result['error_msg'] = '';
        }
        else
        {
            $result['error_code'] = 1;
            $result['error_msg'] = '商品属性编辑失败';
        }
    
        return $result;
    }
    /**
     * del 删除商品(多个)
     * @karl
     * @DateTime 2016-09-06T06:53:19+0800
     * @param    array                 $data 编辑数据 [商品属性id,]
     * @return   array                 [error_code, error_msg]                
     */
    static public function del($data)
    {
        $where = array('goods_id' => $data['goods_id']);
        if($del = Db::name('goods_specs')->where($where)->delete())
        {
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['goods_id'] = $del;
        }
        else
        {
            $result['error_code'] = '1';
            $result['error_msg'] = '商品删除失败';
        }
        return $result;
    }

    /**
     * getSpecs 得到一个商品的属性列表
     * @karl
     * @DateTime 2016-09-28T22:41:15+0800
     * @param    int                $goods_id 商品id
     * @return   array              [error_code, error_msg, data=> [] ]
     */
    static public function getSpecs($goods_id)
    {
        if ($goods_id)
        {
            $where = array('goods_id' => $goods_id);
            $where['is_del'] = 0; //没有 删除的
            if($data = Db::name('goods_specs')->where($where)->order("spec_id asc")->select())
            {
                $result['error_code'] = 0;
                $result['error_msg'] = '';
                $result['data'] = $data;
            }
            else
            {
                $result['error_code'] = 1;
                $result['error_msg'] = '没有查到商品属性详情';
            }
            
        }
            return $result;
    }   

    /**
     * getOneSpecs 得到一个商品的默认属性
     * @karl
     * @DateTime 2016-09-28T22:41:15+0800
     * @param    int                $goods_id 商品id
     * @return   array              [error_code, error_msg, data=> [] ]
     */
    static public function getOneSpecs($goods_id)
    {
        if ($goods_id)
        {
            $where = array('goods_id' => $goods_id);
            $where['is_del'] = 0; //没有 删除的
            if($data = Db::name('goods_specs')->where($where)->find())
            {
                $result['error_code'] = 0;
                $result['error_msg'] = '';
                $result['data'] = $data;
            }
            else
            {
                $result['error_code'] = 1;
                $result['error_msg'] = '没有查到商品属性详情';
            }
            
        }
            return $result;

    }  

    /**
     * checkeds 检查库存
     * @karl
     * @DateTime 2016-09-28T22:41:15+0800
     * @param    array                $data 
     * @param    array                $goodname 
     * @return   array              [error_code, error_msg, data=> [] ]
     */
    static public function cheCkeds($data,$goodname)
    {   
        $result['error_code'] = 0;
        $result['error_msg'] = '';
        foreach ($data as $key => $v) {
            $where['spec_id'] = $v['spec_id'];
            $buy_num = $v['buy_num'];
            $where['is_del'] = 0; //没有 删除的
            $res = Db::name('goods_specs')->where($where)->find();
            if ($res) {
                if ($res['stock']-$buy_num <= 0) {
                    $result['error_code'] += 1;
                    $result['error_msg'] .= $goodname[$where['spec_id']].'库存不足<br/>';
                }
            }else{
                $result['error_code'] += 1;
                $result['error_msg'] .= $goodname[$where['spec_id']].'该商品已下架<br/>';
            }
        }
        return $result;
    } 

    /**
     * checkeds 检查库存
     * @karl
     * @DateTime 2016-09-28T22:41:15+0800
     * @param    array                $data 
     * @return   array              [error_code, error_msg, data=> [] ]
     */
    static public function stockDec($data)
    {   
        $result['error_code'] = 0;
        $result['error_msg'] = '';
        foreach ($data as $key => $v) {
            $where['spec_id'] = $v['spec_id'];
            $buy_num = $v['buy_num'];
            $res = Db::name('goods_specs')->where($where)->setDec('stock',$buy_num);
            if (!$res) {
                $result['error_code'] += 1;
                $result['error_msg'] .= $where['spec_id'].'减库存'.$buy_num.'错误';
            }
        }
        return $result;
    }
}

?>