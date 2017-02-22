<?php
namespace app\manager\controller;
use think\Session;
use think\Controller;
use think\Response;
use think\Request;
use app\common\model\Shipping;

class Shippingpickup extends Manager
{
	/*************************************************  
    *ClassName:     index
    *Description:   门店详情信息
    *************************************************/
	public function index()
    {  
        $input = Request::instance()->param();
		$where = array();
        $url = array();
		if ($_GET) {
			if (!empty($input['number'])) {
				$url['number'] = $input['number'];
				$where['number'] = array("like",'%'.$input['number'].'%');
			}
			if (!empty($input['name'])) {
				$url['name'] = $input['name'];
				$where['name'] = array("like",'%'.$input['name'].'%');
			}	
			$data = Shipping::getShippingPickup(8,$where,$url);
		}else{
            $data = Shipping::getShippingPickup(8,$where); 
		}
		if ($data['error_code'] != 0) {
            $data['data'] = "未找到订单详情";
            $data['page'] = null;        
        }
		$number = !empty($input['number'])?$input['number']:'';
        $name = !empty($input['name'])?$input['name']:''; 
        $this->assign('number',$number);
        $this->assign('name',$name);
        $this->assign('data',$data['data']);
        $this->assign('page',$data['data']->render());
        return $this->fetch();
    }
	
	/*************************************************  
    *ClassName:     add
    *Description:   门店添加
    *************************************************/
	public function add()
    {  
	    $input_data = Request::instance()->param();
        if($_POST)
        {
			$data = [
				'number' => $input_data['number'],
				'name'=> $input_data['name'],
				'responsible'=> $input_data['responsible'],
				'phone'=> $input_data['phone'],
				'address'=> $input_data['address'],
			];
			$res = Shipping::addShippingPickup($data);
			if($res)
			{   
				$this->jsAlert('添加成功','/index.php/manager/Shippingpickup/index'); 
			}
			else
			{
				$this->jsAlert('添加失败','/index.php/manager/Shippingpickup/index'); 
			}     
        }  
        return  $this->fetch();
    }
	
	/*************************************************  
    *ClassName:     edit
    *Description:   门店修改/编辑
    *************************************************/
    public function edit()
    {  
      $input_data = Request::instance()->param();
      if($_POST)
		{
			$data = [
				'number' => $input_data['number'],
				'name'=> $input_data['name'],
				'responsible'=> $input_data['responsible'],
				'phone'=> $input_data['phone'],
				'address'=> $input_data['address'],
			];
			if(Shipping::editShippingPickup($input_data['id'],$data))
			{     
			   $this->jsAlert('修改成功','/index.php/manager/Shippingpickup/index'); 
			}
			else
			{
				$this->jsAlert('修改失败','/index.php/manager/Shippingpickup/edit');
				die;
			}   
        }  
		$list = Shipping::getInfoShippingPickup($input_data['id']);
		$this->assign('list',$list['data']);
		print_r($list);
		return $this->fetch();
    }
	
	/*************************************************  
    *ClassName:     del
    *Description:   门店删除
    *************************************************/
	public function del()
    {  
        $pickup_id = Request::instance()->param('id');
         /******************* 删除数据 ********************/      
        $data=Shipping::delShippingPickup($pickup_id);
        if($data['error_code']==0)
        {
           $this->jsAlert('删除成功！','/index.php/manager/Shippingpickup/index');
        }
        else
        {
           $this->jsAlert('删除失败！','/index.php/manager/Shippingpickup/index');
        } 
    }
	/**
     * verifyAjax  是否存在门店编号
     * @param    array                   $data 输入数据
     * @return   int                     成功返回ok
     */
    public function verifyAjax()
    {
        $request = Request::instance()->param();
        if(isset($request['number'])){
            $where['number'] = $request['number'];
        }
        if(isset($request['number'])){
			$good_info = Shipping::ShippingPickupOne($where);
			if($good_info['error_code']==0){
				return 1;
			}else{
				return 2;
			}
        }
    }
}