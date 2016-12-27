a<?php
namespace app\api\controller;

use think\Controller;
use model\weixin\Wechat;

/**
 * 微信绑定接口
 */
class WeixinBind
{
    /**
     * index 接口入口
     * @karl
     * @DateTime 2016-08-25T20:31:12+0800
     * @return   void
     */
    public function index()
    {
        $token = "abcd"
        $weixin = new Wechat($token);
        //获取微信接口数据
        $data = $weixin->request();
        //获取内容，类型
        list ($content, $type) = $this->reply($data);   
        //内容为空则不输出
        if ($content) {
             $weixin->response($content,$type);
        }             
    }

    /**
     * reply 接口回复处理
     * @karl
     * @DateTime 2016-08-25T22:15:14+0800
     * @param    array                   $data 接口回复数组
     * @return   array                         回复数据
     */
    private function reply($data)
    {
        if ('subscribe' == $data['Event']) 
        {
            
        }
        elseif ('unsubscribe' == $data['Event'])
        {

        }
    }

    /**
     * subscribe 关注微信处理
     * @karl
     * @DateTime 2016-08-26T06:15:22+0800
     * @return   [type]                   [description]
     */
    private function subscribe()
    {

    }

    /**
     * unsubscribe 取消关注微信处理
     * @karl
     * @DateTime 2016-08-26T06:15:22+0800
     * @return   [type]                   [description]
     */
    private function unsubscribe()
    {

    }    
}

?>