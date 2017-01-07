<?php
namespace app\manager\controller;
use think\Session;
use think\Controller;
use think\Validate;
use think\Request;
use app\common\model\Regions;
use app\common\model\base\Users;
use weixin\hongbao\Hongbao;

class Member extends Manager
{
    /*************************************************  
   *ClassName:     memberLst
   *Description:   会员列表
   *************************************************/
    public function memberLst()
    {  
        $input_data = Request::instance()->param();
        $where=[];
        $url=[];
        $where['role']=array("eq","customer");
        $where['status']=array("eq","1");
        //$where['member_type']=array("gt","0");
        if($_GET){
            if(!empty($input_data['nickname'])){
                $url['nickname']=$input_data['nickname'];
                $where['nickname']=array("like",'%'.$input_data['nickname'].'%');
            }
		    if(!empty($input_data['member_type'])){
				if($input_data['member_type']=='普'||$input_data['member_type']=='通'||$input_data['member_type']=='普通'){
					$input_data['member_type']='1';
				}
				if($input_data['member_type']=='代'||$input_data['member_type']=='理'||$input_data['member_type']=='代理'){
					$input_data['member_type']='2';
				}
                $url['member_type']=$input_data['member_type'];
                $where['member_type']=array("like",'%'.$input_data['member_type'].'%');
            }
        }
        $result=users::memberLst(15,$where,$url);
        $this->assign('lst',$result['data']);
        $this->assign('lst2',$result['data2']);
        return  $this->fetch();        
    }   

    /*************************************************  
   *ClassName:     memberManagement
   *Description:   会员修改
   *************************************************/
    public function memberManagement()
    {  
        $id = Request::instance()->param('id');
        $data=Users::info($id);
        $this->assign('data',$data['data']);
        //$this->assign('commission',$data['commission']);
        $res = Regions::getLevel(1);
        $this->assign('res',$res['data']);
        if(!empty($data['data']['city']) && !empty($data['data']['district'])){
            $city = Regions::getName($data['data']['city']);
            $district = Regions::getName($data['data']['district']);
        }else{
            $city['data']['name']="";
            $district['data']['name']="";
        }
        $this->assign('city',$city['data']['name']);
        $this->assign('district',$district['data']['name']);
        $input = Request::instance()->param();
        if($_POST){
            $data=users::editUsers($id,$input);
            if($data['error_code']==0)
            {
                $this->jsAlert('保存成功！','/index.php/manager/Member/memberlst');
            }
            else
            {
                $this->jsAlert('保存失败！','/index.php/manager/Member/memberlst');
            }  
        }
        return  $this->fetch();     
    }    

    /*************************************************  
   *ClassName:     memberDel
   *Description:   会员删除
   *************************************************/
    public function memberDel()
    {  
        $id = Request::instance()->param('id');
        $data=users::memberDel($id);
        if($data['error_code']==0)
        {
            $this->jsAlert('删除成功！','/index.php/manager/Member/memberlst');
        }
        else
        {
            $this->jsAlert('删除失败！','/index.php/manager/Member/memberlst');
        }  
    }    

    /*************************************************  
   *ClassName:     moneyLst
   *Description:   佣金明细
   *************************************************/
    public function moneyLst()
    {  
        $id = Request::instance()->param('id');
        $result=users::moneyLst(15,$id);
        $this->assign('data',$result['data']);
        return  $this->fetch();     
    } 

	/*************************************************  
   *ClassName:     balanceLst
   *Description:   余额明细
   *************************************************/
    public function balanceLst()
    {  
        $id = Request::instance()->param('id');
        $result=users::balanceLst(15,$id);
        $this->assign('data',$result['data']);
        return  $this->fetch('moneylst');     
    } 

     /*************************************************  
   *ClassName:     voucherLst
   *Description:   财富劵明细
   *************************************************/
    public function voucherLst()
    {  
        $id = Request::instance()->param('id');
        $result=users::voucherLst(15,$id);
        $this->assign('data',$result['data']);
        return  $this->fetch('moneylst');     
    } 

     /*************************************************  
   *ClassName:     rebateLst
   *Description:   积分明细
   *************************************************/
    public function rebateLst()
    {  
        $result=users::rebateLst(15);
        $this->assign('data',$result['data']);
        return  $this->fetch();     
    } 

    /*************************************************  
   *ClassName:     jifenDel
   *Description:   积分删除
   *************************************************/
    public function jifenDel()
    {  
        $id = Request::instance()->param('id');
        $result=users::jifenDel($id);
        if($result['error_code']==0)
        {
            $this->jsAlert('删除成功！','/index.php/manager/Member/jifenLst');
        }
        else
        { 
            $this->jsAlert('删除失败！','/index.php/manager/Member/jifenLst');
        }  
        return  $this->fetch();     
    } 

    /*************************************************  
   *ClassName:     allMoneyLst
   *Description:   资金统计
   *************************************************/
    public function allMoneyLst()
    {  
        $result=users::allMoneyLst(15);
        $this->assign('data',$result['data']);
        return  $this->fetch();     
    } 


    /*************************************************  
   *ClassName:     twodimensionalCodes
   *Description:   二维码列表
   *************************************************/
    public function twodimensionalCodes()
    {  
        $input_data = Request::instance()->param();
        $where=array();
        $url=array();
        if($_GET){
            if(!empty($input_data['nickname'])){
                $url['nickname']=$input_data['nickname'];
                $where['nickname']=array("like",'%'.$input_data['nickname'].'%'); 
            }
        }
        $result=users::twodimensionalCodes(15,$where,$url);
        $this->assign('data',$result['data']);
        return  $this->fetch();     
    } 

    /*************************************************  
   *ClassName:    allMoneyDel
   *Description:   钱记录删除
   *************************************************/
    public function allMoneyDel()
    {  
        $id = Request::instance()->param('id');
        $result=users::allMoneyDel($id);
        if($result['error_code']==0)
        {
            $this->jsAlert('删除成功！','/index.php/manager/Member/allMoneyLst');
        }
        else
        {
            $this->jsAlert('删除失败！','/index.php/manager/Member/allMoneyLst');
        }  
        return  $this->fetch();     
    } 

     /*************************************************  
   *ClassName:     memberRelation
   *Description:   会员关系
   *************************************************/
    public function memberRelation()
    {  
        $id = Request::instance()->param('id');
        $result=users::memberRelation(15,$id);
        $this->assign('data',$result['data']);
        $this->assign('counts',$result['counts']);
        return  $this->fetch();     
    } 

     /*************************************************  
   *ClassName:     numAjax
   *Description:   下级会员关系
   *************************************************/
    public function numAjax()
    {  
        $uid=Request::instance()->param('uid');
        $res = users::numAjax($uid);
        return  $res;     
    } 

    /*************************************************
     * Function:      ajaxAddress
     * Description:   地址获取
     * @param:        void
     * Return:        void
     *************************************************/
    public function ajaxAddress()
    {
        $input = Request::instance()->param();
        $html = '';
        $res = Regions::getChildren($input['id']);
        if ($res['error_code'] == 0) {
            foreach ($res['data'] as $val) {
                $html.= '<option value="'.$val["id"].'">'.$val["name"].'</option>';
            }
        }
        return $html;
    }

    /*************************************************  
   *ClassName:     withdrawalRequest
   *Description:   提款申请
   *************************************************/
   
    public function withdrawalRequest()
    {  
        $input_data = Request::instance()->param();
        $where=array();
        $url=array();
        if($_GET){
            if(!empty($input_data['nickname'])){
                $url['nickname']=$input_data['nickname'];
                $where['nickname']=array("like",'%'.$input_data['nickname'].'%'); 
            }
        }      
        $pagenum=15;
        $res = users::withdrawalRequest($pagenum,$where,$url);
        $this->assign('pagenum',$pagenum);
        if ($res['error_code'] == 0) {
            $data = $res['data'];
        }else{
            $data = '';
        }
        $this->assign('data',$res['data']);
        return  $this->fetch();      
    }

     /*************************************************  
   *ClassName:     withdrawalEdit
   *Description:   提款申请验证
   *************************************************/
   
    public function withdrawalEdit()
    {  
        $input = Request::instance()->param();
        if($input['type']==0){
            $check=users::withdrawallCheck($input['id']);
            if($check['error_code']==0){
				if($check['data']['is_on']!=0 && $check['data']['transit_time']==''){
					$res=users::findMoney($check['data']['uid']);
					$data['order_sn']=time().rand(1000,9999);
					$data['apikey']=$res['apikey']['value'];
					$data['open_id'] =$res['open_id']['open_id'];
					$data['appid'] = $res['appid']['value'];
					$data['mchid'] = $res['mchid']['value'];
					$data['money'] =$check['data']['money'];
					$data['info'] = '书社提现';
					$hongbao = new Hongbao;
					if($hongbao->pay($data))
					{   
						$result=users::withdrawalEdit($input,$check['data']['money'],$check['data']['uid']);
						if($result['error_code']==0)
						{
							$this->jsAlert('微付款成功！','/index.php/manager/Member/withdrawalRequest');
						}
						else
						{
							$this->jsAlert('提现失败！','/index.php/manager/Member/withdrawalRequest');
						}                       
					}
					else
					{
						js_alert('付款失败','/index.php/manager/Member/withdrawalRequest');   
					}
				}else{
					js_alert('付款失败','/index.php/manager/Member/withdrawalRequest');   
				}
            }else{
                $this->jsAlert('余额不足,提现失败！','/index.php/manager/Member/withdrawalRequest');
            }          
        }else{
            $result=users::withdrawalEditOne($input);
            if($result['error_code']==0)
            {
				$this->jsAlert('修改成功！','/index.php/manager/Member/withdrawalRequest');
            }
            else
            {
				$this->jsAlert('修改失败！','/index.php/manager/Member/withdrawalRequest');
            }
        }
    }

    /*************************************************  
   *ClassName:     excel
   *Description:   生成excel表
   *************************************************/
    public function excel()
    {  
        $input_data = Request::instance()->param();
        if($_POST){
            $title = array('昵称', '提款金额', '时间', '确认时间', '状态');
            $data = Users::excel($input_data);
            if ($data['error_code'] == 1) {
				$this->jsAlert('查询失败,请输入正确数字！','/index.php/manager/Member/withdrawalRequest');
            }
            $out = implode("\t", $title);
            $out .= "\n";
            foreach ($data['data'] as $value)
            {
				$value['time']=date('Y-m-d H:i:s',intval($value['time'])).' ';
				$value['transit_time']=date('Y-m-d H:i:s',intval($value['transit_time'])).' ';
				if($value['is_on']==0){
				$value['is_on']='成功';
				}else{
					$value['is_on']='失败';
				}
				$out .= implode("\t", $value);
				$out .= "\n";
            }
            $out = mb_convert_encoding($out, "GBK", "UTF-8");
            header('Content-Type: applicationnd.ms-excel');
            header('Content-Disposition: attachment; filename=demo.xls');
            header('Pragma: no-cache');
            header('Expires: 0');     
            echo $out;
            exit;
        }
    }

    
      /**
     * jsAlert JS弹出框
     * @karl
     * @DateTime 2016-08-12T09:29:04+0800
     * @param    string                   $info [description]
     * @param    string                   $url  [description]
     */
    public function jsAlert($info, $url="")
    {
        $this->assign('info', $info);
        $this->assign('url', $url);
        echo $this->fetch(APP_PATH.request()->module().'/view/common/alert.html');
        die;
    }     
}