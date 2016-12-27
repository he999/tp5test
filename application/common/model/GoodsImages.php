<?php
namespace app\common\model;

use think\Model;
use think\Db;

/**
 * 商品图片模型
 * add 添加商品图片
 * edit 修改商品图片
 * del 删除商品图片
 * getList 得到一个商品下图片列表
 */
class GoodsImages extends Model
{
    /**
    * add 添加商品图片
    * @karl
    * @DateTime 2016-09-27T14:54:43+0800
    * @param    int       $goods_id  商品id
    * @param    array     $data [['image_name' => '图片名', 'image_url' => '图片地址' ],[...]]
    * @return   array     [error_code, error_msg, data]
    */
    static public function add($goods_id, $data)
    {
        $result['error_code'] = 0;
        if ($data)
        {
            foreach ($data as $key => $value) 
            {   
                $add = array_merge($value,['goods_id' => $goods_id ]);
                $row = Db::name('goods_images')->insertGetId($add);
               if ($row)
                {
                    
                    $result['error_msg'] = '';
                    $result['data'][] = $row;
                }
                else
                {
                    $result['error_code'] += 1;
                    $result['error_msg'] = '商品图片添加失败';
                }
            } 
        }
            
        return $result;
    }

    /**
    * edit 修改商品图片
    * @karl
    * @DateTime 2016-09-27T14:54:43+0800
    * @param    array   $data [图片id=> '',['image_name' => '图片名', 'image_url' => '图片地址'], ]
    * @return   array   [error_code, error_msg]
    */
    static public function edit($data)
    {
        foreach ($data as $key => $value) 
        {
            $where = array('img_id' => $value['img_id']);

            if($update = Db::name('goods_images')->where($where)->update($value))
            {
                $result['error_code'] = 0;
                $result['error_msg'] = '';            
            }
            else
            {
                $result['error_code'] = 1;
                $result['error_msg'] = '商品图片编辑失败';
            }
        } 
        return $result;
    }

    /**
     * del 删除商品图片
     * @karl
     * @DateTime 2016-09-27T14:54:43+0800
     * @param    array                 $data [图片id,]
     * @return   array                 [error_code, error_msg]
     */
    static public function del($data)
    {
        $where = array('img_id' => $data['img_id']);
        if($del = Db::name('goods_images')->where($where)->delete())
        {
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['goods_id'] = $del;
        }
        else
        {
            $result['error_code'] = 1;
            $result['error_msg'] = '商品图片删除失败';
        }
        return $result;

    }

    /**
     * getList 得到一个商品下图片列表
     * @karl
     * @DateTime 2016-09-28T22:41:15+0800
     * @param    int                $order_id 定单id
     * @return   array              [error_code, error_msg, data=> [图片id => [],] ]
     */
    static public function getList($goods_id)
    {
         if ($goods_id)
        {
            $where = array('goods_id' => $goods_id);
            if($data = Db::name('goods_images')->where($where)->order("img_id asc")->select())
            {
                $result['error_code'] = 0;
                $result['error_msg'] = '';
                $result['data'] = $data;
            }
            else
            {
                $result['error_code'] = 1;
                $result['error_msg'] = '没有得到一个商品下图片列表';
            }
            
        }
            return $result;

    }
}

?>