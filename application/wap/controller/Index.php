<?php
namespace app\wap\controller;

use think\Controller;
use think\Validate;
use think\Request;
use think\Session;
use app\common\model\Foucs;
use app\common\model\Goods;
use app\common\model\Cart;
use app\common\model\GoodsCates;

/*************************************************
 * @ClassName:     index
 * @Description:   首页控制器
 * @author:     xiao   
 * @DateTime    201611211107  
 *************************************************/
class Index extends WeixinBase
{
    /*************************************************
     * Function:      index
     * Description:   首页
     * @param:        void
     * Return:        void
     *************************************************/
    public function index()
    {
        //轮播图
        $foucs_list = "";
        $foucs_data = Foucs::getList();
        if ($foucs_data['error_code'] == 0) {
            $foucs_list = $foucs_data['data'];
        }else{
            $foucs_list = "";
        }
        //商品分类
        $parentinfo = GoodsCates::getByParentId(0);
        if ($parentinfo['error_code'] == 0) {
            $array = $parentinfo['data'];
            foreach ($array as $key => $value) {
                $where['parentid'] = $value['cateid'];
                $sort = 'last_update desc';
                $res = Goods::getList($where,1,6,$sort);
                if ($res['error_code'] == 0) {
                    $array[$key]['data'] = $res['data'];
                }
            }
        }else{
            $array = '';
        }
        //全部商品
        $all_where['goods_id'] = ['gt', 0];
        $all_where['stock'] = ['gt', 0];
        $all_data = Goods::getlist($all_where, 1, 10);
        if ($all_data['error_code'] == 0) {
            $all_list = $all_data['data'];
            $all_num = count($all_data['data']);
        }else{
            $all_list = '';
            $all_num = 0;
        }
        $this->assign("data",$array);
        $this->assign('foucs_list', $foucs_list);
        $this->assign('all_list', $all_list);
        $this->assign('all_num', $all_num);
        return $this->fetch();
    }

    /**
     * ajaxGetMore 商品加载更多
     * @pk
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
        ];
        $msg = [
            'pages.require'     =>  '页号必须填写',
            'page.number'         =>  '只能为数字',
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
            //全部商品
            $all['goods_id'] = array("gt",0);
            $all['stock'] = array("gt",0);
            $favourite_goods = Goods::getList($all,$input['pages']+1,6);
            return json($favourite_goods);
        }
    }

    /**
     * selectgoods 根据关键字查询商品
     * @pk
     * @DateTime 2016-10-18T10:10:36+0800
     * @param    void                
     * @return   void
     */
    public function selectGoods()
    { 
        $input = Request::instance()->param();
        /******************* 验证网址参数 ********************/       
        $rule = [
            'name'  => 'require',  
        ];
        $msg = [
            'pages.require'     =>  '必须填写',
        ];
        $validate = new Validate($rule, $msg);
        $result   = $validate->check($input);
        if (!$result){ 
            $arr['error_msg'] =  $validate->getError();
            $arr['error_code'] = 1;
            return json($arr);
        }
        else{
            //全部商品
            $all['goods_name'] = array("like","%".$input['name']."%");
            $all['stock'] = array("gt",0);
            $favourite_goods = Goods::getList($all,0,1);
            return json($favourite_goods);
        }
    }

}