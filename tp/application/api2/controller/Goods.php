<?php
namespace app\api\controller;

use think\Controller;
use think\Request;
use app\common\model\Goods as GoodsModel;

/**
 * 接口
 */
class Goods 
{
    /**
     * addGoods 添加完整商品包括商品图片和商品属性
     * @karl
     * @DateTime 2016-11-16T13:29:34+0800
     * @param    array   $data 添加数组['goods' => [],'specs' => [],'images' => [] ]
     * @return   array   [error_code, error_msg, goods_id]    
     */    
    public function addGoods()
    {
        $input_json = get_php_input();
        if (empty($input_json)) die("input empty");
        $data = json_decode($input_json, true);
        $out_array = GoodsModel::addGoods($data);
        return json($out_array);
    }

    
}

?>