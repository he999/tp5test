<?php
namespace app\wap\controller;

use app\common\model\weixin\WeixinKeywords;
use think\Controller;
use think\View;
use think\Request;
use think\Session;
use think\Validate;

class Article extends Controller
{
     /*************************************************
     * Function:      login
     * Description:   登录界面
     * @param:        void
     * Return:        void
     *************************************************/
    public function index()
    { 
      $artid = Request::instance()->param('artid');
      if($artid){ 
        $this->assign('list',WeixinKeywords::getArticlesOne($artid)); 
      } 
      return $this->fetch();
    }
}