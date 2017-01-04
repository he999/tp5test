<?php
namespace app\wap\controller;

use think\Controller;
use think\Validate;
use think\Request;
use think\Session;
use app\common\model\Goods;
use app\common\model\Cart as CartModel;
use app\common\model\GoodsSpecs;
use app\common\model\base\Users;

/*************************************************
 * @ClassName:     Cart
 * @Description:   购物车控制器
 * @author:        
 * @DateTime      
 *************************************************/
class Cart extends WeixinBase
{
    /*************************************************
     * Function:      index
     * Description:   购物车显示
     * @param:        void
     * Return:        void
     *************************************************/
    public function index()
    {
        $uid = session('uid');
        $data = CartModel::getList($uid);
        if ($data['error_code'] == 0) {
            $this->assign('data',$data['data']); 
        }else{
            return $this->fetch();
        }
        return $this->fetch('indexs');
    }

    /*************************************************
     * Function:      ajaxSelect
     * Description:   更改购物车 选择状态
     * @param:        void
     * Return:        void
     *************************************************/
    public function ajaxSelect()
    {
        $input = Request::instance()->param();
        /******************* 验证网址参数 ********************/
        $rule = [
            'id'  => 'require|max:11',
            'select'      => 'require|max:1|number',
        ];
        $msg = [
            'id.max'      =>  'id最长为11位',
            'id.require'  =>  'id必须填写',
            'select.require'  =>  'select必须填写',
            'select.max'      =>  'select最长为1位',
            'select.number'      =>  'select为数字',
        ];
        $validate = new Validate($rule, $msg);
        $result   = $validate->check($input);
        if (!$result)
        {   
            $arr['error_code'] = 1;
            $arr['error_msg'] = $validate->getError();
            return json($arr);
        }
        $id = $input['id'];
        $data['selected'] = $input['select'];
        $res = CartModel::edit($id,$data);
        if ($res['error_code'] == 0) {
            $arr['error_code'] = 0;
            $arr['error_msg'] = '';
        }else{
            $arr['error_code'] = 2;
            $arr['error_msg'] = $res['error_msg'];
        }
        return json($arr);

    }

    /*************************************************
     * Function:      ajaxSelect
     * Description:   更改购物车 选择状态
     * @param:        void
     * Return:        void
     *************************************************/
    public function ajaxAllSelect()
    {
        $input = Request::instance()->param();
        /******************* 验证网址参数 ********************/
        $rule = [
            'select'      => 'require|max:1|number',
        ];
        $msg = [
            'select.require'  =>  'select必须填写',
            'select.max'      =>  'select最长为1位',
            'select.number'      =>  'select为数字',
        ];
        $validate = new Validate($rule, $msg);
        $result   = $validate->check($input);
        if (!$result)
        {   
            $arr['error_code'] = 1;
            $arr['error_msg'] = $validate->getError();
            return json($arr);
        }
        $uid = session('uid');
        $data['selected'] = $input['select'];
        $res = CartModel::editAll($uid,$data);
        if ($res['error_code'] == 0) {
            $arr['error_code'] = 0;
            $arr['error_msg'] = '';
        }else{
            $arr['error_code'] = 2;
            $arr['error_msg'] = $res['error_msg'];
        }
        return json($arr);
    }

    /*************************************************
     * Function:      ajaxNum
     * Description:   更改数量
     * @param:        void
     * Return:        void
     *************************************************/
    public function ajaxNum()
    {
        $input = Request::instance()->param();
        /******************* 验证网址参数 ********************/
        $rule = [
            'id'  => 'require|max:10',
            'buy_num'      => 'require|max:6',
        ];
        $msg = [
            'id.max'      =>  'id最长为10位',
            'id.require'  =>  'id必须填写',
            'buy_num.require'  =>  'buy_num必须填写',
        ];
        $validate = new Validate($rule, $msg);
        $result   = $validate->check($input);
        if (!$result)
        {   
            $arr['error_code'] = 1;
            $arr['error_msg'] = $validate->getError();
            return json($arr);
        }
        $id = $input['id'];
        $data['buy_num'] = $input['buy_num'];
        $res = CartModel::edit($id,$data);
        if ($res['error_code'] == 0) {
            $arr['error_code'] = 0;
            $arr['error_msg'] = '';
        }else{
            $arr['error_code'] = 2;
            $arr['error_msg'] = $res['error_msg'];
        }
        return json($arr);
    }

    /*************************************************
     * Function:      ajaxDel
     * Description:   删除
     * @param:        void
     * Return:        void
     *************************************************/
    public function ajaxDel()
    {
        $input = Request::instance()->param();
        /******************* 验证网址参数 ********************/
        $rule = [
            'id'  => 'require|max:10',
        ];
        $msg = [
            'id.max'      =>  'id最长为10位',
            'id.require'  =>  'id必须填写',
        ];
        $validate = new Validate($rule, $msg);
        $result   = $validate->check($input);
        if (!$result)
        {   
            $arr['error_code'] = 1;
            $arr['error_msg'] = $validate->getError();
            return json($arr);
        }
        $id = $input['id'];
        $res = CartModel::del($id);
        if ($res['error_code'] == 0) {
            $arr['error_code'] = 0;
            $arr['error_msg'] = '';
        }else{
            $arr['error_code'] = 2;
            $arr['error_msg'] = $res['error_msg'];
        }
        return json($arr);
    }

    /*************************************************
     * Function:      AjaxAddCart
     * Description:   添加到购物车
     * @param:        void
     * Return:        void
     *************************************************/
    public function AjaxAddCart()
    {
        $input = Request::instance()->param();
        /******************* 验证网址参数 ********************/       
        $rule = [
            'goods_id'  => 'require',
        ];
        $msg = [
            'goods_id.require'  =>  'goods_id必须填写',
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
        $goods_id = $input['goods_id'];
        $res = GoodsSpecs::getOneSpecs($goods_id);
        if ($res['error_code'] == 0) {
            $data = ['buy_num' => isset($input['goods_num'])?$input['goods_num']:1,
                     'spec_id' => $res['data']['spec_id'],
            ];
            $resd = CartModel::add($uid,$data);
            if ($resd['error_code'] == 0) {
                $arr['error_msg'] =  '';
                $arr['error_code'] = 0;
            }else{
                $arr['error_msg'] = $resd['error_msg'];
                $arr['error_code'] = 0;
            }
            return json($arr);
        }else{
            $arr['error_msg'] =  '没有找到该商品';
            $arr['error_code'] = 3;
            return json($arr); 
        }
    }
}