<?php
namespace app\common\model;

use think\Model;
use think\Db;
use app\common\model\GoodsImages;
use app\common\model\GoodsSpecs;

/**
 * 商品模型
 * add 添加商品
 * edit 编辑商品
 * del 删除商品
 * getOne 得到一个商品
 * getHot 得到热门商品
 * getNew 得到最新商品
 * getRecommend 得到推荐商品
 * getList 商品列表
 */
class Goods extends Model
{
    /**
     * addGoods 添加完整商品包括商品图片和商品属性
     * @xiao
     * @DateTime 2016-11-16T13:29:34+0800
     * @param    array   $data 添加数组['goods' => [],'specs' => [],'images' => [] ]
     * @return   array   [error_code, error_msg, goods_id]    
     */
    static public function addGoods($data)
    {
        if (!isset($data['goods']['cover_img']) || empty($data['goods']['cover_img']) ) {
            $data['goods']['cover_img'] = '\static\images\ondata.jpg';
        }
        $goodsid = self::add($data['goods']);
        if ($goodsid['error_msg'] == 0) {
            if (!isset($data['specs']) || empty($data['specs']) ) {
                $data['specs'][] =[
                    'price' => isset($data['goods']['shop_price'])?$data['goods']['shop_price']:0,
                    'stock' => isset($data['goods']['stock'])?$data['goods']['stock']:0,
                ];
            }
            if (!isset($data['images']) || empty($data['images']) ) {
                $data['images'][] =[
                    'image_url' => '\static\images\ondata.jpg',
                    'update_time' => time(),
                    'create_time' => time()
                ];
            }
            $goodsss = GoodsSpecs::add($goodsid['goods_id'],$data['specs']);
            $goodsimg = GoodsImages::add($goodsid['goods_id'],$data['images']);
            if ($goodsss['error_code'] == 0 && $goodsimg['error_code'] == 0 ) {
                $result['error_code'] = 0;
                $result['error_msg'] = '';
                $result['goods_id'] = $goodsid['goods_id'];
            }else{
                $result['error_code'] = 2;
                $result['error_msg'] = '商品规格添加失败';
            }
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = '商品添加失败';
        }
        return $result;
    }

    /**
     * add 添加商品
     * @xiao
     * @DateTime 
     * @param    array                   $data 添加数组
     * @return   array   [error_code, error_msg,goods_id]                                       
     */
    static public function add($data)
    {
        $result = [];
        if ($data)
        {   $data['last_update'] = time();
            if ($row = Db::name('goods')->insertGetId($data))
            {
                $result['error_code'] = 0;
                $result['error_msg'] = '';
                $result['goods_id'] = $row;
            }
            else
            {
                $result['error_code'] = 1;
                $result['error_msg'] = '商品添加失败';
            }
        }
       
        return $result;
    }
    
    /**
     * edit 编辑商品
     * @xiao
     * @DateTime 
     * @param    int                   $goods_id 商品id
     * @param    array                 $data 编辑数据
     * @return   array                 [error_code, error_msg]                  
     */
    static public function edit($goods_id, $data)
    {
        $where = array('goods_id' => $goods_id);
        if($update = Db::name('goods')->where($where)->update($data))
        {
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['goods_id'] = $update;
        }
        else
        {
            $result['error_code'] = 1;
            $result['error_msg'] = '商品编辑失败';
        }
       
        return $result;

    }

    /**
     * del 删除商品
     * @xiao
     * @DateTime 
     * @param    int                   $goods_id 删除id
     * @return   array                 [error_code, error_msg]                
     */
    static public function del($goods_id)
    {
        $where = array('goods_id' => $goods_id);
        if($del = Db::name('goods')->where($where)->delete())
        {
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['goods_id'] = $del;
        }
        else
        {
            $result['error_code'] = 1;
            $result['error_msg'] = '商品删除失败';
        }
        return $result;
    }

    /**
     * getOne 得到一个商品
     * @xiao
     * @DateTime 2016-09-28T22:41:15+0800
     * @param    int                   
     * @return   array                 [error_code, error_msg, data=> [] ]
     */
    static public function getOne($goods_id)
    {
        if ($goods_id)
        {
            $where = array('goods_id' => $goods_id);
            $where['is_del'] = 0; //没有 删除的
            if($data = Db::name('goods')->where($where)->find())
            {
                $result['error_code'] = 0;
                $result['error_msg'] = '';
                $result['data'] = $data;
            }
            else
            {
                $result['error_code'] = 1;
                $result['error_msg'] = '没有得到商品详情';  //查找失败
            }
            
        }
            return $result;
    }
   
       /**
     * getListOne 根据条件得到一个商品
     * @xiao
     * @DateTime 2016-09-28T22:41:15+0800
     * @param    int                   
     * @return   array                 [error_code, error_msg, data=> [] ]
     */
    static public function getListOne($where)
    {
     
        if($data = Db::name('goods')->where($where)->find())
        {
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['data'] = $data;
        }
        else
        {
            $result['error_code'] = 1;
            $result['error_msg'] = '没有得到商品详情';  //查找失败
        }
        return $result;
    }

       /**
     * getWhereList 根据条件得到商品的佣金
     * @xiao
     * @DateTime 2016-09-28T22:41:15+0800
     * @param    array      $where                   
     * @return   array      [error_code, error_msg, data=> [] ]
     */
    static public function getWhereList($where)
    {
        if($data = Db::name('goods')->field("goods_id,sum_brokerage,sum_voucher")->where($where)->select())
        {
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['data'] = $data;
        }
        else
        {
            $result['error_code'] = 1;
            $result['error_msg'] = '没有得到商品详情';  //查找失败
        }
        return $result;
    }

    /**
     * getList 商品列表
     * @xiao
     * @DateTime 
     * @param    array                   $where 查询条件
     * @param    integer                 $page  当前页
     * @param    integer                 $page_num   每页显示数量
     * @param    array                   $sort  排序条件
     * @return   array                   [error_code, error_msg, data=> [] ]
     */
    static public function getList($where = '', $page = 1, $page_num = 10, $sort = "goods_id desc")
    {   
        $where['is_del'] = 0; //没有 删除的
        $data = Db::name('goods')->where($where)->order($sort)->page($page,$page_num)->select(); 
        if($data){
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['data'] = $data;
        }
        else{
            $result['error_code'] = 1;
            $result['error_msg'] = '得到商品详情分页失败';
        }
        return $result;
    }

     /**
     * goodscateDel 商品类型删除
     * @xiao
     * @DateTime 
     * @param    array                   $where 查询条件
     * @param    integer                 $page  当前页
     * @param    integer                 $page_num   每页显示数量
     * @param    array                   $sort  排序条件
     * @return   array                   [error_code, error_msg, data=> [] ]
     */
    static public function goodscateDel($cate_id)
    {   
        $data = Db::name('goods_cates')->delete($cate_id); 
        if($data){
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['data'] = $data;
        }
        else{
            $result['error_code'] = 1;
            $result['error_msg'] = '得到商品详情分页失败';
        }
        return $result;
    }
    
     /**
     * getList 商品列表
     * @xiao
     * @DateTime 
     * @param    array                   $where 查询条件
     * @param    integer                 $page  当前页
     * @param    integer                 $page_num   每页显示数量
     * @param    array                   $sort  排序条件
     * @return   array                   [error_code, error_msg, data=> [] ]
     */
    static public function getGoodsList($where,$url,$num = 10, $sort = "goods_id desc")
    {   
        $where['is_del'] = 0; //没有 删除的
        $data = Db::name('goods')->where($where)->order($sort)->paginate($num,false,array('query'=>$url)); 
        if($data){
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['data'] = $data;
        }
        else{
            $result['error_code'] = 1;
            $result['error_msg'] = '得到商品详情分页失败';
        }
        return $result;
    }
    // /**
    //  * getHot 得到热门商品
    //  * @
    //  * @DateTime 
    //  * @param    integer                  $page_num    一页数量
    //  * @param    integer                  $page   页号
    //  * @param    integer                  $com_id 公司id
    //  * @return   array                    [error_code, error_msg, data=> [] 
    //  */
    // static public function getHot($page_num = 4, $page = 1, $com_id =1)
    // {
    //     $where = ['is_hot' => 1, 'com_id' => $com_id];
    //     $where['is_del'] = 0; //没有 删除的
    //     $list = Db::name('goods')->where($where)->page($page,$page_num)->select();          
    //     if($list)
    //     {
    //         $result['error_code'] = 0;
    //         $result['error_msg'] = '';
    //         $result['data'] = $list;
    //     }
    //     else
    //     {
    //         $result['error_code'] = 1;
    //         $result['error_msg'] = '没有得到热门商品';
    //     }       
    //     return $result;
    // }

    // /**
    //  * getNew 得到最新商品
    //  * @
    //  * @DateTime 
    //  * @param    integer                  $page_num    一页数量
    //  * @param    integer                  $page   页号
    //  * @param    integer                  $com_id 公司id
    //  * @return   array                    [error_code, error_msg, data=> [] 
    //  */
    // static public function getNew($page_num = 4, $page = 1, $com_id =1)
    // {
    //     $where = ['is_new' => 1 , 'com_id' => $com_id];
    //     $where['is_del'] = 0; //没有 删除的
    //     $list = Db::name('goods')->where($where)->page($page, $page_num)->select();
    //     if($list)
    //     {
    //         $result['error_code'] = 0;
    //         $result['error_msg'] = '';
    //         $result['data'] = $list;
    //     }
    //     else
    //     {
    //         $result['error_code'] = 1;
    //         $result['error_msg'] = '没有得到最新商品';
    //     }
    //     return $result;
        
    // }

    // /**
    //  * getRecommend 得到推荐商品
    //  * @
    //  * @DateTime 
    //  * @param    integer                  $page_num    一页数量
    //  * @param    integer                  $page   页号
    //  * @param    integer                  $com_id 公司id
    //  * @return   array                    [error_code, error_msg, data=> [] 
    //  */
    // static public function getRecommend($page_num = 4, $page = 1, $com_id =1)
    // {
    //     $where = ['is_recommend' => 1, 'com_id' => $com_id];
    //     $where['is_del'] = 0; //没有 删除的
    //     $list = Db::name('goods')->where($where)->page($page,$page_num)->select();
    //     if($list)
    //     {
    //         $result['error_code'] = 0;
    //         $result['error_msg'] = '';
    //         $result['data'] = $list;
    //     }
    //     else
    //     {
    //         $result['error_code'] = 1;
    //         $result['error_msg'] = '没有得到商品推荐';
    //     }
    //     return $result;   
    // }
}

?>