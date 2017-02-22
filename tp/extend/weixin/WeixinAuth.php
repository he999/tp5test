<?php

/*
* 微信用户认证
*/
namespace weixin;

class WeixinAuth
{
    /*************************************************  
    * Function:      getOpenInfo
    * Description:   获取Openid和access_token
    * @param:        string $appid
    * @param:        string $appsecret    
    * @param:        string $url    
    * @param:        string $scope  微信认证方式
    				 snsapi_userinfo 详细模式 snsapi_base 静默模式    
    * Return:        array('access_token', 'openid')
    *************************************************/
    static public function getOpenInfo($appid, $appsecret, $url, $scope = "snsapi_userinfo")
   	{    
   		$code = self::getCode($appid, $url, $scope);
   		$access_token = self::getAccessToken($appid, $appsecret, $code);
   		return $access_token;
   	}

    /*************************************************  
    * Function:      getCode
    * Description:   获取code
    * @param:        string $appid
    * @param:        string $url    
    * @param:        string $scope  微信认证方式
    				 snsapi_userinfo 详细模式 snsapi_base 静默模式    
    * Return:        string $code
    *************************************************/
	static public function getCode($appid, $url, $scope = "snsapi_userinfo")
	{   
		//是否有code
		if(isset($_GET['code'])) 
		{
		    $code = $_GET['code'];
		}
		else
		{
			header("Location: "."https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=".urlencode($url)."&response_type=code&scope=".$scope."&state=123#wechat_redirect");
			die;
		}
         		
		return $code;
	}

    /*************************************************  
    * Function:      getAccessToken
    * Description:   获取AccessToken
    * @param:        string $appid
    * @param:        string $appsecret
    * @param:        string $code    
    * Return:        array  $access_token
    *************************************************/
	static public function getAccessToken($appid, $appsecret, $code)
	{  
        
		$access_token = file_get_contents('https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$appsecret.'&code='.$code.'&grant_type=authorization_code');
		$access_token = json_decode($access_token,true);
		return $access_token;
	}

    /*************************************************  
    * Function:      getWeiwinInfo
    * Description:   获取微信用户信息
    * @param:        string $access_token
    * @param:        string $openid
    * Return:        array  $weixin_info
    *************************************************/
	static public function getWeiwinInfo($access_token, $openid)
	{
		$weixin_info = file_get_contents('https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN');
		$weixin_info = json_decode($weixin_info,true);	
		return $weixin_info;
	}	

    /*************************************************  
    * Function:      isWeixin
    * Description:   是否为微信浏览器
    * @param:        void
    * Return:        bool  
    *************************************************/
    static public function isWeixin()
	{ 
	    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')!== false) 
	    {
			return true;
	    }
	    else
	    {
			return false;
		}  
	}		

}

?>