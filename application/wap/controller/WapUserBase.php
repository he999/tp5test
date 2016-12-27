<?php
namespace app\wap\controller;

use think\Controller;
use think\Log;
use weixin\WeixinAuth;
use app\common\model\UserWeixin;
use app\common\model\base\Com;
use app\common\model\UserLogs;

/*
* 手机端微信基类
*/
class WapUserBase extends WapBase
{
    /*************************************************  
    * Function:      _initialize
    * Description:   初始化
    * @param:        void
    * Return:        void
    *************************************************/
	protected function _initialize() 
	{

	}

}


?>