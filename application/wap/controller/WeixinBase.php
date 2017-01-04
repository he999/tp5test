<?php
namespace app\wap\controller;

use think\Controller;
use think\Log;
use weixin\WeixinAuth;
use app\common\model\base\UsersWeixin;
use app\common\model\base\Coms;
use app\common\model\base\UserLogs;
use app\common\model\base\Users;

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
		if (!session('denopen_id')) {
zlog('winxin:1');
			if(WeixinAuth::isWeixin()) {
zlog('winxin:.2');
				$url = "http://".$_SERVER['HTTP_HOST'].$_SERVER["REQUEST_URI"];
				$openInfo = WeixinAuth::getOpenInfo($this->appid, $this->appsecret, $url);
				$open_id = $openInfo['openid'];
				$res = UsersWeixin::getOne($open_id);
				if ($res['error_code'] == 0) {
zlog('winxin:.3'); 
					$uid = $res['data']['uid']; 
					if ($res['data']['nickname'] == NULL) {
zlog('winxin:.4');
						$userinfo = WeixinAuth::getWeiwinInfo($openInfo['access_token'], $openInfo['openid']);
	 					$data['open_id']  = $userinfo['openid'];
	                    $data['nickname'] = $userinfo['nickname'];
	                    $data['face']     = $userinfo['headimgurl'];
	                    $data['sex']      = $userinfo['sex'];
	                    if (isset($userinfo['unionid'])){
zlog('winxin:.5');	                    	
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
					dupm($userinfo['nickname']);
				}else{
zlog('winxin:.6');
					die("未关注");
				}
				session("denopen_id", $open_id);
                session("uid", $uid);
			} else {
				//非微信浏览器处理
				die("非微信浏览器");
			}
		}
zlog('winxin:.6'.session('open_id'));
		//echo '<center>=>=>=>=>正在建设中<=<=<=<=</center>';
	}


}


?>