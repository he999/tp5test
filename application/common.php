<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
   /*************************************************  
    * Function:      js_alert
    * Description:   JS弹出框，并跳转URL
    * @param: string $info 弹出信息
    * @param：string $url 跳转网址(可选）     
    * Return:        void       
    *************************************************/
    function js_alert($info,$url = "")
    {   
        echo "<meta charset='utf-8'>";
        echo "<script language='JavaScript'>alert('".$info."');window.location ='".$url."';</script>";
    }    

    /**
     * zlog 日志函数,写入logfile.txt
     * @param  mix      $logthis 写入值
     * @return void
     */
    function zlog( $logthis )
    {
        $s = print_r($logthis,true);
        file_put_contents('./logfile.txt', date("Y-m-d H:i:s"). " " . $s. "\r\n", FILE_APPEND | LOCK_EX);
    }    

    /**
     * wxcurl_post 微信curl_post
     * @karl
     * @DateTime 2016-09-22T13:42:54+0800
     * @param    [type]                   $url  [description]
     * @param    [type]                   $data [description]
     * @return   [type]                         [description]
     */
    function wxcurl_post($url, $data)
    {
        $ch = curl_init();
        $header[] = "Accept-Charset: utf-8";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);
        $errorno = curl_errno($ch);
        zlog($errorno);
        if ($errorno) {
            return array('rt' => false, 'errorno' => $errorno);
        } else {
            $js = json_decode($tmpInfo, 1);
            if ($js['errcode'] == '0') {
                return array('rt' => true, 'errorno' => 0);
            } else {
                exit('发生错误：错误代码' . $js['errcode'] . ',微信返回错误信息：' . $js['errmsg']);
            }
        }
    }
 
    /**
     * curl_post curl提交post数据
     * @karl
     * @DateTime 2016-11-29T16:51:27+0800
     * @param    string                   $url  提交网址
     * @param    string                   $data 提交数据
     * @return   string                   返回数据
     */
    function curl_post($url, $data)
    {
        $ch = curl_init();
        $header[] = "Accept-Charset: utf-8";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);
        return $tmpInfo;
    }      

    /**
     * get_php_input 获取原始php流
     * @karl
     * @DateTime 2016-12-01T11:08:49+0800
     * @return   string                  
     */
    function get_php_input()
    {
        return file_get_contents('php://input', 'r');
    }
