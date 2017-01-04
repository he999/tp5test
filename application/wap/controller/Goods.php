<?php
namespace app\wap\controller;

use think\Controller;
use think\Request;
use think\Validate;
use think\Session;
use app\common\model\Goods as GoodsModel;
use app\common\model\GoodsSpecs;
use app\common\model\GoodsImages;
use app\common\model\Cart;
use app\common\model\GoodsComment;
use app\common\model\base\Users;

/*************************************************
 * @ClassName:     Goods
 * @Description:   商品控制器
 * @author:        xiao
 * @DateTime       201611211650
 *************************************************/
class Goods extends WeixinBase
{
    /**
     * goodsInfo 商品详情
     * @xiao
     * @DateTime 2016-10-15T10:10:36+0800
     * @param    void                
     * @return   void
     */
    public function goodsInfo()
    {
        $input = Request::instance()->param();
        /******************* 验证网址参数 ********************/
        $rule = [
            'id'  => 'require|max:10',
        ];
        $msg = [
            'id.max'      =>  '商品id最长为10位',
            'id.require'  =>  '商品id必须填写',
        ];
        $validate = new Validate($rule, $msg);
        $result   = $validate->check($input);
        if (!$result) {
            $this->error($validate->getError());
        }
        $goods_id = $input['id'];
        $goods_info = GoodsModel::getOne($goods_id);
        $goods_specs = GoodsSpecs::getSpecs($goods_id);
        $goods_images = GoodsImages::getList($goods_id);
        if ($goods_info['error_code'] == 0) {
            $goodsinfo = $goods_info['data'];
        }else{
            $goodsinfo = '';
        }
        if ($goods_specs['error_code'] == 0) {
            $goodsspecs = $goods_specs['data'];
            $spec_1 = $goods_specs['spec_1'];
            $spec_2 = $goods_specs['spec_2'];
            $spec_3 = $goods_specs['spec_3'];
        }else{
            $goodsspecs = '';
            $this->error('未找到该商品详情,请联系我们');
        }
        if ($goods_images['error_code'] == 0) {
            $goodsimages = $goods_images['data'];
        }else{
            $goodsimages = '';
        }
        //评价
        $res = GoodsComment::getComment($goods_info['data']['goods_id']);
        if ($res['error_code'] == 0) {
            $content = $res['data'];
        }else{
            $content = '';
        }
        $this->assign('spec_1',$spec_1);
        $this->assign('spec_2',$spec_2);
        $this->assign('spec_3',$spec_3);
        $this->assign('content',$content);
        $this->assign('goods_info', $goodsinfo);
        $this->assign('goods_specs', $goodsspecs);
        $this->assign('goods_images', $goodsimages);
        return $this->fetch();       
    }

    /**
     * goodsList 商品列表
     * @xiao
     * @DateTime 2016-10-15T10:10:36+0800
     * @param    void                
     * @return   void
     */
    public function goodsList()
    {
        $input = Request::instance()->param();
        /******************* 验证网址参数 ********************/
        $rule = [
            'cid' => 'number',
            'sort' => 'alpha',
        ];
        $msg = [
            'sort.alpha'  =>  '商品类型sort必须为字母',
            'cid.number' => 'id只能为数字',
        ];
        $validate = new Validate($rule, $msg);
        $result   = $validate->check($input);
        if (!$result) {
            $this->error($validate->getError());
        }
        //用于列表里面的排序
        if (isset($input['sort'])) {
            if ($input['sort'] == "money") {
                $order = "shop_price desc"; // 价格
            }
            elseif($input['sort'] == "sum"){
                $order = "sales_sum desc"; // 热销
            }
        }else{
            $order = "goods_id desc";
        }
        //确定商品分类
        if (isset($input['cid'])) { //分类
           $all['cateid'] = $input['cid']; 
           $ids = 'cid';
           $id = $input['cid'];
        }elseif (isset($input['id'])) {
            if (!is_numeric($input['id'])) {//搜索
                $all['goods_name'] = array("like","%".$input['id']."%");
            }else{ //全部
                $all['goods_id'] = array("gt",0);
            }
            $ids = 'id';
            $id = $input['id'];
        }elseif (isset($input['pid'])) {
            $all['parentid'] = $input['pid']; 
            $ids = 'pid';
            $id = $input['pid'];
        }else{
            $all['goods_id'] = array("gt",0);
            $ids = 'id';
            $id = 0;
        }
        $all['stock'] = array("gt",0);
        $goods_data = GoodsModel::getList($all, 1, 6, $order);
        if ($goods_data['error_code'] == 0) {
            $goods_list = $goods_data['data'];
            $goods_num = count($goods_data['data']);
        }else{
            $goods_list = '';
            $goods_num = 0;
        }
        $this->assign('ids', $ids);
        $this->assign('id', $id);
        $this->assign('goods_num', $goods_num);
        $this->assign('goods_list', $goods_list);
        return $this->fetch();
    }

    /**
     * ajaxGetMore 商品加载更多
     * @xiao
     * @DateTime 2016-10-15T10:10:36+0800
     * @param    void                
     * @return   void
     */
    public function ajaxGetMore()
    {
        $input = Request::instance()->param();
        /******************* 验证网址参数 ********************/       
        $rule = [
            'pages'  => 'require|number', 
            'type'   => 'require|in:0,1', 
        ];
        $msg = [
            'pages.require'     =>  '页号必须填写',
            'page.number'         =>  '页号只能为数字',
            'type.require'     =>  '类型必须填写',
            'type.in'         =>  '类型只能为0,1',
        ];
        $validate = new Validate($rule, $msg);
        $result   = $validate->check($input);
        if (!$result)
        {
            $arr['error_msg'] =  $validate->getError();
            $arr['error_code'] = 1;
            return json($arr);
        }
        else{
                //用于列表里面的排序
            if (isset($input['sort'])) {
                if ($input['sort'] == "money") {
                    $order = "shop_price desc"; // 价格
                    $type = 1;
                }
                elseif($input['sort'] == "sum"){
                    $order = "sales_sum desc"; // 热销
                    $type = 0;
                }
            }else{
                $order = "goods_id desc";
                $type = 1;
            }
            //确定商品分类
            if (isset($input['cid'])) { //分类
               $all['cateid'] = $input['cid']; 
               $ids = 'cid';
               $id = $input['cid'];
            }elseif (isset($input['id'])) {
                if (!is_numeric($input['id'])) {//搜索
                    $all['goods_name'] = array("like","%".$input['id']."%");
                }else{ //全部
                    $all['goods_id'] = array("gt",0);
                }
                $ids = 'id';
                $id = $input['id'];
            }elseif(isset($input['pid'])){// 分类id
                $all['parentid'] = $input['pid'];
                $ids = 'pid';
                $id = $input['pid'];
            }else{
                $all['goods_id'] = array("gt",0);
                $ids = 'id';
                $id = 0;
            }
            $all['stock'] = array("gt",0);
            $favourite_goods = GoodsModel::getList($all,$input['pages']+1,6,$order);
            return json($favourite_goods);
        }
    }
    
    
    /**
     * CreateOrder 加入购物车
     * @xiao
     * @DateTime 2016-10-15T10:10:36+0800
     * @param    void                
     * @return   void
     */
    public function CreateOrder()
    {
        $input = Request::instance()->param();
        /******************* 验证网址参数 ********************/       
        $rule = [
            'goods_num'  => 'require',
            'spec_id'      => 'require',
        ];
        $msg = [
            'goods_num.require'  =>  'goods_num必须填写',
            'spec_id.require'  =>  'goods_id必须填写',
        ];
        $validate = new Validate($rule, $msg);
        $result   = $validate->check($input);
        $uid = session('uid');
        $info = Users::myInfo($uid);
        if ($info['data']['member_type'] == 0) {
            $arr['error_msg'] =  '你好，请充值门槛金额再试！';
            $arr['error_code'] = 2;
            return json($arr); 
        }
        if (!$result){
            $arr['error_msg'] =  $validate->getError();
            $arr['error_code'] = 1;  
            return json($arr); 
        }
        $data = ['buy_num' => $input['goods_num'],
                 'spec_id' => $input['spec_id'],
            ];
        $res = Cart::add($uid,$data);
        if ($res['error_code'] == 0) {
            $arr['error_msg'] =  '';
            $arr['error_code'] = 0;
            return json($arr);
        }
        
    }

}