<?php
namespace app\wap\controller;

use think\Controller;
use think\Log;
use weixin\WeixinAuth;
use app\common\model\base\UsersWeixin;
use app\common\model\base\Coms;
use app\common\model\base\UserLogs;
use app\common\model\base\Users;
use think\Request;

/*
* 手机端微信基类
*/
class WeixinBase extends Controller
{
	public    $issub;
	public    $appid;
	public    $appsecret;

	protected function _initialize() 
	{
		$this->appid = Coms::getValue('appid')['data'];
		$this->appsecret = Coms::getValue('appsecret')['data'];
		$this->con();
		$this->weiwinInit();

	}

    /*************************************************  
    * Function:      weiwinInit
    * Description:   微信接入初始化
    * @param:        void
    * Return:        void
    *************************************************/
    private function weiwinInit()
	{  	
		if (!session('?open_id')) { 
			if(WeixinAuth::isWeixin()) {
				$url = "http://".$_SERVER['HTTP_HOST'].$_SERVER["REQUEST_URI"];
				$openInfo = WeixinAuth::getOpenInfo($this->appid, $this->appsecret, $url);
				$open_id = $openInfo['openid'];
				$res = UsersWeixin::getOne($open_id);
				if ($res['error_code'] == 0) { 
					$uid = $res['data']['uid']; 
					if ($res['data']['nickname'] == NULL) {
						$userinfo = WeixinAuth::getWeiwinInfo($openInfo['access_token'], $openInfo['openid']);
	 					$data['open_id']  = $userinfo['openid'];
	                    $data['nickname'] = $userinfo['nickname'];
	                    $data['face']     = $userinfo['headimgurl'];
	                    $data['sex']      = $userinfo['sex'];
	                    if (isset($userinfo['unionid'])){
	                    	$data['union_id'] = $userinfo['unionid'];
	                    } 
						UsersWeixin::edit($open_id,$data);
						$data2 = [
							'nickname' => $userinfo['nickname'],
							'face' => $userinfo['headimgurl'],
							'sex' => $userinfo['sex']
						];
						Users::editUsersCustomers($uid,$data2);
						Users::userUpdates($uid,['username' => $userinfo['nickname']]);
					}
					session("open_id", $open_id);
                	session("uid", $uid);
				}else{
					session(null);
					die("未关注");
				}
			} else {
				//非微信浏览器处理
				//die("非微信浏览器");
				session('uid','1');
        		session("open_id",'oSMR0t1SaQbjlhNAdSA75h9N1gqg');
			}
		}
		
	}
	
	private function con()
	{
		$request = Request::instance();
		$con=$request->controller();
		$this->assign('con',$con);
	}
}

?>