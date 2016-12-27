<?php
namespace app\manager\controller;

use think\Session;
use think\Controller;
use think\Validate;
use think\Response;
use think\Request;

use app\common\model\base\Users;
use app\common\model\base\Auth;

class Admin extends Manager
{

    /*************************************************  
    *ClassName:     adminadd
    *Description:   管理员添加
    *************************************************/
    public function adminAdd()
    {  
		$rule = [
            'username'  => 'require',
            'password'  => 'require',
            'confirmpwd'  => 'require',
            'role'  => 'require',
        ];
        $message = [
            'username.require'  =>  '登录账号必须填写',
            'password.require'  =>  '登录密码必须填写',
            'confirmpwd.require'  =>  '确认密码必须填写',
            'role.require'  =>  '请选择职务角色',    
        ];
    	$input_data = Request::instance()->param();
    	if ($input_data) {
    		$validate = new Validate($rule, $message);
            $result   = $validate->check($input_data);
            if (!$result)
            {
                $this->jsAlert($validate->getError());
            }
            if ($input_data['password'] == $input_data['confirmpwd'] ) {
            	$password = md5($input_data['password']);
            	$where = [
	            	'username' => $input_data['username'],
	            	'password' => $password,
	            	'role' => 'manager',
	            ];
	            $list = Users::addAdmin($where,$input_data['role']);
	            if ($list['error_code'] == 0) {
	            	$this->jsAlert('添加成功！','/index.php/manager/admin/authslist');
	            }elseif($list['error_code'] == 1){
	            	$this->jsAlert('账号已存在！','/index.php/manager/admin/authslist');
	            }elseif($list['error_code'] == 2){
	            	$this->jsAlert('添加失败！','/index.php/manager/admin/authslist');
	            }
            }else{
            	$this->jsAlert('密码填写不一致');
            }            
    	}else{
            $res = Auth::getgroupsList();
            $this->assign('data',$res['data']);
    		return $this->fetch();
    	}	
    } 

    /**
    * authsList 权限列表
    * @xiaoyajun
    * @DateTime 2016-10-20T10:10:36+0800
    * @param    void                
    * @return   void
    */
    public function authsList()
    {   
    	$list = Auth::getAuthList(10);
        if($list['error_code'] == 0){
    	   $this->assign('list',$list['data']);
        }else{
            $this->assign('list','');
        }
        $res = Auth::getgroupsList();
        foreach ($res['data'] as $v) {
            $data[$v['id']] = $v['title'];
        }
        $this->assign('data',$data);
    	return $this->fetch();    	
    }

    /**
    * checkuser js检查帐号是否存在
    * @xiao
    * @DateTime 2016-10-12T14:26:59+0800
    * @return    array             [error_code, error_msg]
    */
    static public function cheCkuser()
    {   
        $data = request::instance()->param();
        $result = Users::jsCheck($data);
        return json($result);
    }

    /**
    * delAdmin 删除管理人员
    * @xiaoyajun
    * @DateTime 2016-10-20T10:10:36+0800
    * @param    void                
    * @return   void
    */
    public function delAdmin()
    {	
    	$input_data = Request::instance()->param();
    	$id = $input_data['id'];
    	$row = Users::userDel($id);
    	if ($row['error_code'] == 0 ) {
    		$this->jsAlert('删除成功！','/index.php/manager/admin/authslist'); 
    	}else{
    		$this->jsAlert('删除失败！','/index.php/manager/admin/authslist'); 
    	}

    }

    /**
    * updateAdmin 修改管理人员
    * @xiaoyajun
    * @DateTime 2016-10-20T10:10:36+0800
    * @param    void                
    * @return   void
    */
    public function updateAdmin()
    {	
    	$rule = [
            'password'  => 'require',
            'newpassword'  => 'require',
            'role'  => 'require',
        ];
        $message = [
            'password.require'  =>  '旧密码必须填写',
            'newpassword.require'  =>  '新密码必须填写',
            'role.require'  =>  '请选择职务角色',    
        ];
    	$input_data = Request::instance()->param();
        $id['uid'] = $input_data['id'];
        $gid = $input_data['gid'];
    	if ($_POST) {		
            $where['password'] = md5($input_data['newpassword']);
            $group['group_id'] = $input_data['role'];
            $row = Users::updates($id,$where,$group);
            if ($row['error_code'] == 0) {
                $this->jsAlert('修改成功！','/index.php/manager/admin/authslist');
            }elseif($row['error_code'] == 1){
                $this->jsAlert('未修改！','/index.php/manager/admin/authslist');
            }
    	}else{   		
    		$row = Users::getOne($id);
            if ($row['error_code'] != 0) {
                $this->jsAlert('没有这个管理员信息','/index.php/manager/admin/authslist');
            }
            $res = Auth::getgroupsList();
            $this->assign('data',$res['data']);
            $this->assign('gid',$gid);
    		$this->assign('list',$row['data']);
    		return $this->fetch();
    	}
    }
    
}