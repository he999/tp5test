<?php
namespace app\manager\controller;

use think\Session;
use think\Controller;
use think\Validate;
use think\Response;
use think\Request;
use app\common\model\Orders;
use app\common\model\Regions;

class Order extends Manager
{

    /*************************************************  
    *ClassName:     orderDetails
    *Description:   订单详情
    *************************************************/
    public function orderDetails()
    {  
        $input = Request::instance()->param();
        $order_id = $input['id'];
        $res = orders::ordersInfo($order_id);
        if ($res['error_code'] == 0) {
            $data = $res['data'];
            $list = $res['list'];
            $ids = [
                'province' => $res['data']['province'],
                'city' => $res['data']['city'],
                'district' => $res['data']['district'],
                'town' => $res['data']['town'],
            ];
            $distr = Regions::getNameStr($ids);
        }else{
            $this->jsAlert('操作错误','/index.php/manager/order/orderlst');
        }
        $tion = orders::getOpelist($data['order_id']);
        $caozuo = ['1'=>'付款','2'=>'发货','3'=>'收货','4'=>'删除'];
        $order = isset($input['order_status'])?$input['order_status']:'';
        if($_POST){
            $gai['admin'] = session('username');
            $gai['operation'] = $caozuo[$order];
            $gai['content'] = $input['content'];
            $gai['order_id'] = $input['id'];
            $gai['time'] = time();
            orders::addOpe($gai);
            if ($order == 5) {
                $hao = orders::edit($input['id'],['is_dels' => 1]);
                $num=1;
            }else{
                $hao = orders::edit($input['id'],['order_status' => $order]);
                $num=2;
            }
            
            if ($hao['error_code'] == 0) {
                if($num==1){
                 $this->jsAlert('操作成功','/index.php/manager/order/orderlst');
                }else{
                   $this->jsAlert('操作成功','/index.php/manager/order/orderDetails/id/'.$order_id);
                }
                
            }else{
                $this->jsAlert('操作错误');
            }
        }

        $status = ['1'=>'待付款','2'=>'待发货','3'=>'已发成','4'=>'已完成'];
        
        $this->assign('order',$order);
        $this->assign('tion',$tion['data']);
        $this->assign('caozuo',$caozuo);
        $this->assign('status',$status); 
        $this->assign('distr',$distr);
        $this->assign('data',$data);
        $this->assign('list',$list);
        return  $this->fetch();
        
    }  

    /*************************************************  
    *ClassName:     orderLst
    *Description:   订单列表
    *************************************************/
    public function orderLst()
    {  
        $input = Request::instance()->param();
        if ($_GET) {
            $where = array();
            $url = array();
            if (!empty($input['consignee'])) {
                $url['consignee'] = $input['consignee'];
                $where['nickname'] = array("like",'%'.$input['consignee'].'%');
            }
            if (!empty($input['order_sn'])) {
                $url['order_sn'] = $input['order_sn'];
                $where['order_sn'] = array("like",'%'.$input['order_sn'].'%');
            }
            if (!empty($input['status'])) {
                $url['status'] = $input['status'];
                $where['order_status'] = array("eq",$input['status']);
            }
        if (!empty($input['time_start'])&&!empty($input['time_end'])) {
                $url['time_start'] = $input['time_start'];
                $url['time_end'] = $input['time_end'];
                $start = strtotime($input['time_start']);
                $end = strtotime($input['time_end']) + 24*60*60;
                $where['create_time'] = array("between time",[$start,$end]);
            }
            elseif(empty($input['time_start'])&&!empty($input['time_end'])||
                  !empty($input['time_start'])&&empty($input['time_end']) ){
                $this->jsAlert('时间区间未设置！','/index.php/manager/order/orderlst');
            }
            $data = Orders::ordersList(8,$where,$url);
        }else{
            $where['order_id'] = array("gt",0);
            $data = Orders::ordersList(8,$where);
        }
        if ($data['error_code'] != 0) {
            $data['data'] = "未找到订单详情";
            $data['page'] = null;          
        }
        
        $consignee = !empty($input['consignee'])?$input['consignee']:'';
        $order_sn = !empty($input['order_sn'])?$input['order_sn']:'';
        $timear = !empty($input['time_start'])?$input['time_start']:'';
        $timeed = !empty($input['time_end'])?$input['time_end']:'';
        $stat = !empty($input['status'])?$input['status']:0;
        $status = ['0'=>'全部','1'=>'待付款','2'=>'待发货','3'=>'已发货','4'=>'已完成']; 
        $this->assign('stat',$stat);
        $this->assign('timear',$timear);
        $this->assign('timeed',$timeed);
        $this->assign('consignee',$consignee);
        $this->assign('order_sn',$order_sn);
        $this->assign('sta',$status);
        $this->assign('data',$data['data']);
        $this->assign('page',$data['data']->render());
        return $this->fetch();
    }  

    /*************************************************  
    *ClassName:     logisticsLst
    *Description:   物流单
    *************************************************/
    public function logisticsLst()
    {  
        $input = Request::instance()->param();
        if ($_GET) {
            $where = array();
            $url = array();
            if (!empty($input['consignee'])) {
                $url['consignee'] = $input['consignee'];
                $where['consignee'] = array("like",'%'.$input['consignee'].'%');
            }
            if (!empty($input['order_sn'])) {
                $url['order_sn'] = $input['order_sn'];
                $where['order_sn'] = array("like",'%'.$input['order_sn'].'%');
            }
            if (!empty($input['status'])) {
                $url['status'] = $input['status'];
                $where['order_status'] = array("eq",$input['status']);
            }
            if (!empty($input['time_start'])&&!empty($input['time_end'])) {
                $url['time_start'] = $input['time_start'];
                $url['time_end'] = $input['time_end'];
                $start = strtotime($input['time_start']);
                $end = strtotime($input['time_end']) + 24*60*60;
                $where['create_time'] = array("between time",[$start,$end]);
            }
            elseif(empty($input['time_start'])&&!empty($input['time_end'])||
                  !empty($input['time_start'])&&empty($input['time_end']) ){
                $this->jsAlert('时间区间未设置！','/index.php/manager/order/logisticslst');
            }
            $data = Orders::ordersList(8,$where,$url);
        }else{
            $where['order_id'] = array("gt",0);
            $data = Orders::ordersList(8,$where);
        }
        if ($data['error_code'] != 0) {
            $data['data'] = "未找到订单详情";
            $data['page'] = null;          
        }
        $consignee = !empty($input['consignee'])?$input['consignee']:'';
        $order_sn = !empty($input['order_sn'])?$input['order_sn']:'';
        $timear = !empty($input['time_start'])?$input['time_start']:'';
        $timeed = !empty($input['time_end'])?$input['time_end']:'';
        $stat = !empty($input['status'])?$input['status']:0;
        $status = ['0'=>'全部','1'=>'待付款','2'=>'待发货','3'=>'已发货','4'=>'已完成']; 
        $this->assign('stat',$stat);
        $this->assign('timear',$timear);
        $this->assign('timeed',$timeed);
        $this->assign('consignee',$consignee);
        $this->assign('order_sn',$order_sn);
        $this->assign('sta',$status);
        $this->assign('data',$data['data']);
        $this->assign('page',$data['data']->render());
        return $this->fetch();
    }  
    
}