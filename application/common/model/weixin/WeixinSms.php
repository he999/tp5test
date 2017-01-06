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
    	$url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
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
    	$curl = curl_init($url);
		$header = array();
		$header[] = 'Content-Type: application/x-www-form-urlencoded';
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		// 不输出header头信息
		curl_setopt($curl, CURLOPT_HEADER, 0);
		// 伪装浏览器
		curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36');
		// 保存到字符串而不是输出
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		// post数据
		curl_setopt($curl, CURLOPT_POST, 1);
		// 请求数据 
		curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($data));
		$response = curl_exec($curl);
		curl_close($curl);
		return $response;
    }
}



?>