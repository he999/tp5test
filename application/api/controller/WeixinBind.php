<?php
namespace app\api\controller;

use think\Db;
use think\Controller;

use app\common\model\Common;
use app\common\model\base\Users;
use app\common\model\base\UsersWeixin;
use app\common\model\base\Coms;
use app\common\model\weixin\WeixinKeywords;
use app\wap\controller\WeixinBase;
use app\common\model\weixin\Wechat;
use app\common\model\base\UsersPoints;

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
        $token = "abcd";
        $weixin = new Wechat($token);
        $data = $weixin->request();
       //获取内容，类型
       list($content,$type,$flg) = $this->reply($data);
        //内容为空则不输出
        if ($content) {
             $weixin->response($content,$type,$flg);
        } else {
            die("");
        } 
    }
    /**
     * reply 接口回复处理
     * @karl
     * @DateTime 2016-08-25T22:15:14+0800
     * @param    array $data 接口回复数组
     * @return   array                         回复数据
     */
    private function reply($data)
    {
        //判断微信$data xml传过来的操作类型
         //event 关注类型
        if ('event'==$data['MsgType'])
        {    
             //subscribe 关注操作
           if ('subscribe' == $data['Event']) 
           {
              return $this->subscribe($data);
             //unsubscribe 取消关注操作
           }
           else if ('unsubscribe' == $data['Event']) 
           {
            $this->unsubscribe($data);
            }elseif ('CLICK' == $data['Event']) {
                zlog('CLICK');
               return $this->clickKeyWords($data);
            } 
            //text 文本类型 关键字操作
        }else if ('text'==$data['MsgType']) 
        {
           return $this->keywords($data);
        } else {
            return ['','text',0];
        }

    }
      
    /**
     * subscribe 关注微信处理
     * @karl
     * @DateTime 2016-08-26T06:15:22+0800
     * @return   [type]                   [description]
     */
    private function subscribe($data)
    {   
        $content='';
        $type='text';
        $flag=0;
        $user_info = UsersWeixin::getOne($data['FromUserName']);
        //是否关注过
        if ($user_info['error_code']==0) {
            //关注过 修改attention
            if($user_info['data']['attention']==0)
            {
               UsersWeixin::edit($data['FromUserName'],['attention'=>1]);
            } 
           // 关注回复
            $content= Coms::getValue('attention_reply')['data'];
        } else {
            //未关注过  插入信息
            $weixin_info['open_id']=$data['FromUserName'];
            $weixin_info['attention']=1;
            $uid = UsersWeixin::add($weixin_info)['uid'];
            $points = Coms::getValue('attention_integral')['data'];
            $addpoints = ['uid' => $uid, 'type' => '关注送积分','time' => time(),'points'=>$points];
            UsersPoints::add($addpoints);
            //扫码 分销
              zlog('扫描关注'.$uid);
            $arr = explode('qrscene_',$data['EventKey']);
             if(count($arr)>1){
                zlog('扫描关注进来');
                $add['parent'] = $arr[1];
                $add['uid'] = $uid;
                $add['weixin_code'] =Db::table('ys_users_parent')->count('distinct parent');
                Users::addParent($add);
                $recom = Coms::getValue('recommend_integral')['data'];
                $addegr = ['uid'=>$arr[1],'type'=>'推荐关注送积分','time' => time(),'points'=>$recom];
                UsersPoints::add($addegr);
            } 

                

            // }
            //关注回复
             $content = Coms::getValue('attention_reply')['data'];
        }
        return [$content,$type,$flag];
    }
    /**
     * unsubscribe 取消关注微信处理
     * @karl
     * @DateTime 2016-08-26T06:15:22+0800
     * @return   [type]                   [description]
     */
    private function unsubscribe($data)
    {
        $open_id =$data['FromUserName'];
        $weixin_info['attention']=0;
        UsersWeixin::edit($open_id,$weixin_info);
    }
    
      /**
     * clickKeyWords 菜单点击
     * @karl
     * @DateTime 2016-08-26T06:15:22+0800
     * @return   [type]                   [description]
     */
    public function clickKeyWords($data)
    {
        $content='';
        $type='text';
        $flag=0;
        //查询关键字是否有信息
        $Keyword_info = WeixinKeywords::getKeyOne($data['EventKey']);
        if($Keyword_info){
           $key_bind_info = WeixinKeywords::getBindOne($Keyword_info['id']);
         //如果有判断bind表是否有信息
          if($key_bind_info){
             if($Keyword_info['type']=='text'){
                 $text_info = WeixinKeywords::getTextOne($key_bind_info['content_id']);
                 if($text_info){
                     $content=$text_info['text'];
                 }
                }
          }
       } 

        return [$content,$type,$flag];
    }    
    /**
     * keywords 关键词处理
     * @karl
     * @DateTime 2016-09-28T15:29:17+0800
     * @param    array                   $data 关键词数组
     * @return   array                   返回内容
     *                          [description]
     */
    public function keywords($data)
    {  
        $content='';
        $type='text';
        $flag=0;
        //查询关键字是否有信息
        $Keyword_info = WeixinKeywords::getKeyOne($data['Content']);
        if($Keyword_info){
           $key_bind_info = WeixinKeywords::getBindOne($Keyword_info['id']);
         //如果有判断bind表是否有信息
          if($key_bind_info){
            //查询关键字是什么类型。通过类型查询不同的表
             if($Keyword_info['type']=='text'){
                 $text_info = WeixinKeywords::getTextOne($key_bind_info['content_id']);
                 if($text_info){
                     $content=$text_info['text'];
                 }
               }elseif ($Keyword_info['type']=='news') {
                  $type='news';
                  $articles_info = WeixinKeywords::getArticlesOne($key_bind_info['content_id']);
                   if($articles_info){ 
                    if($articles_info['jumplink']==''){
                    $content=[0 => [$articles_info['title'],$articles_info['description'],$articles_info['pic'],$articles_info['link'].$articles_info['artid']]];
                    }else{
                        $content=[0 => [$articles_info['title'],$articles_info['description'],$articles_info['pic'],$articles_info['jumplink']]];
                    }
                 }
                   
               }
          }
       } 

        return [$content,$type,$flag];
    }
}
?>