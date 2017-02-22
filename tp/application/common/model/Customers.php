<?php
namespace app\common\model;

use think\Model;
use think\Db;

/*************************************************
 *ClassName:     Customers
 *Description:   顾客类
 *Others:
 *************************************************/
class Customers extends CommonModel
{
    /**
     * init 初始化
     * @karl
     * @DateTime 2016-07-31T23:23:02+0800
     */
    static public function init()
    {
        parent::$table_name = "";
    }

}