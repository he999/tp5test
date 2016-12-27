<?php
namespace app\wap\controller;

use think\Controller;
use app\common\model\GoodsCates;

/*************************************************
 * @ClassName:     Categorys
 * @Description:   分类控制器
 * @author:        xiao
 * @DateTime       201611211107
 *************************************************/
class Categorys extends WeixinBase
{
    /*************************************************
     * Function:      catelist
     * Description:   分类列表
     * @param:        void
     * Return:        void
     *************************************************/

    public function catelist()
    {
        $parentinfo = GoodsCates::getByParentId(0);
        if ($parentinfo['error_code'] == 0) {
            $array = $parentinfo['data'];
            foreach ($array as $key => $value) {
                $chirldinfo[$key]['id'] =  $value['cateid'];
                $res = GoodsCates::getByParentId($value['cateid']);
                if ($res['error_code'] == 0) {
                    $chirldinfo[$key]['data'] = $res['data'] ;
                }else{
                    $chirldinfo[$key]['data'] = '';
                }
            }
        }else{
            $parentinfo = '';
            $chirldinfo = '';
        }
        $this->assign("parentinfo",$parentinfo['data']);
        $this->assign("chirldinfo",$chirldinfo);
        return $this->fetch();
    }
}