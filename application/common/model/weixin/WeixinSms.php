<?php
namespace model\weixin;

use app\common\model\weixin\WeixinAuth;

/**
 * 模板消息
 */
class WeixinSms
{
	
	/**
     * message 发短信
     * @xiao
     * @DateTime 2017
     * @param    array                    $data 短信 
     * @param    string                   $templaetid 模板ID   
     * @param    string                   $access_token access_token
     * @return   array                    [error_code, error_msg， data=>[]]
     */
    static public function sendMessage($access_token,$templaetid,$data)
    {
    	$url = "https://api.weixin.qq.com/cgi-bin/template/api_set_industry?access_token=".$access_token;

    }

    //https 请求
    protected function http_request($url,$data)
    {
    	$curl = curl_init();
    	$curl_setopt($curl,CURLOPT_URL,$url);
    }
}



?>