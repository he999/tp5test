<?php
namespace app\common\model\weixin;

use app\common\model\weixin\WeixinAuth;

/**
 * 模板消息
 */
class WeixinSms
{
	

	/*************************************************
     * Function:      getsWeiwinInfo
     * @颜林梧
     * Description:   获取微信用户的信息
     * Return:        array  $access_token
     *************************************************/
    static public function getsAccessToken($appid, $appsecret)
    {
        define("APPID", "$appid");
        define("APPSECRET", "$appsecret");
        $token_access_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".APPID."&secret=".APPSECRET;
        $res = file_get_contents($token_access_url); //获取文件内容或获取网络请求的内容
        //echo $res;
        $result = json_decode($res, true); //接受一个 JSON 格式的字符串并且把它转换为 PHP 变量
        $access_token = $result['access_token'];
        return $access_token;
    }

	/**
     * message 发短信
     * @xiao
     * @DateTime 2017
     * @param    array                    $data 短信  
     * @param    string                   $access_token access_token
     * @return   array                    [error_code, error_msg， data=>[]]
     */
    static public function sendMessage($access_token,$data)
    {
    	zlog('===duanxin kai===');
    	$url = "https://api.weixin.qq.com/cgi-bin/template/api_set_industry?access_token=".$access_token;
    	zlog($url);
    	$res = self::httpRequest($url,$data);
    	
    	zlog(json_decode($res));
    	zlog('===duanxin jie===');
    	return json_decode($res);
    }

    /**
     * https 请求
     * @xiao
     * @DateTime 2017
     * @param    array                    $url  
     * @param    string                   $data 短信   
     * @return   
     */
    static public function httpRequest($url,$data)
    {
    	$curl = curl_init();
    	$curl_setopt($curl,CURLOPT_URL,$url);
    	$curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,FALSE);
    	$curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,FALSE);
    	if (!empty($data)) {
    		curl_setopt($curl,CURLOPT_POST,1);
    		curl_setopt($curl,CURLOPT_POSTFIELDS,$data);
    	}
    	curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
    	$output = curl_exec($curl);
    	curl_close($curl);
    	return $output;
    }
}



?>