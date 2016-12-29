<?php
namespace app\manager\controller;

use think\Session;
use think\Controller;
use think\Validate;
use think\Response;
use think\Request;
use app\common\model\Shipping;
use app\common\model\Regions;

class Shippingpickup extends Manager
{
	public function index()
    {  
        $input = Request::instance()->param();
		if ($_GET) {
			if (!empty($input['consignee'])) {
					$url['consignee'] = $input['consignee'];
					$where['consignee'] = array("like",'%'.$input['consignee'].'%');
				}
			if (!empty($input['order_sn'])) {
				$url['order_sn'] = $input['order_sn'];
				$where['order_sn'] = array("like",'%'.$input['order_sn'].'%');
			}	
			$data = Shipping::getShippingPickup(8,$where,$url);
		}else{
			$where['order_id'] = array("gt",0);
            $data = Shipping::getShippingPickup(8,$where);
		}
		if ($data['error_code'] != 0) {
            $data['data'] = "未找到订单详情";
            $data['page'] = null;          
        }
		$consignee = !empty($input['consignee'])?$input['consignee']:'';
        $order_sn = !empty($input['order_sn'])?$input['order_sn']:''; 
        $this->assign('consignee',$consignee);
        $this->assign('order_sn',$order_sn);
        $this->assign('sta',$status);
        $this->assign('data',$data['data']);
        $this->assign('page',$data['data']->render());
        return $this->fetch();
    }
	
}