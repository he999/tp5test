<?php
namespace app\wap\controller;

use think\Controller;
use think\Config;
use think\Session;

/*
* 手机端基类
*/
class WapBase extends Controller
{
    /*************************************************  
    * Function:      _initialize
    * Description:   初始化
    * @param:        void
    * Return:        void
    *************************************************/
	protected function _initialize() 
	{  
        // session('uid','31');
        // session("open_id",'oSMR0t-Kpti9zr5MERZgSAie6ans');
	}

}


?>