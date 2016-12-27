<?php
namespace app\manager\controller;

use app\common\model\Com;
use weixin\hongbao\Hongbao;

/**
 * 功能测试
 */
Class integral
{
    public function hongbao()
    {
        //微付款
        $data['open_id'] = 'oSMR0tyYB24CgwRhUFEpNXzRKaf8';
        $data['appid'] = Coms::getValue('appid');
        $data['mchid'] = Coms::getValue('mchid');
        $data['apikey'] = Coms::getValue('apikey');
        $data['money'] = 1;
        $data['order_sn'] = time();
        $data['info'] = '测试';
        $hongbao = new Hongbao;
        if($hongbao->pay($data))
        {
            echo "成功";
        }
        else
        {
            echo "失败";
        }
    }
    public function add_product()
    {
        return $this->fetch();
    }
}


?>