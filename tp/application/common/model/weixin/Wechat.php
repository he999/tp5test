<?php
namespace app\common\model\weixin;

/**
* 微信接口通讯
*/
class Wechat
{
    private $data = array();
    public function __construct($token)
    {
        zlog("进入wechat");
        $this->auth($token) || exit;
        if(isset($_GET['echostr']))
        {
            echo($_GET['echostr']);
            exit;
        }
        else
        {
            $xml = file_get_contents("php://input");
            zlog($xml);
            $xml = new \SimpleXMLElement($xml);
            $xml || exit;
            foreach ($xml as $key => $value){
                $this->data[$key] = strval($value);
            }
        }
    }
	
    public function request()
    {
        return $this->data;
    }

    /**
     * [transmitText 组合返回给 微信公众平台的数据]
     * @param  [type] $object  [xml对象]
     * @param  [type] $content [回复内容]
     * @return [type]          [xml格式字符串]
     */
    public function transmitText($object,$content){
        $time = time();
        $result = "<xml>
            <ToUserName><![CDATA[{$object['ToUserName']}]]></ToUserName>
            <FromUserName><![CDATA[{$object['FromUserName']}]]></FromUserName>
            <CreateTime>{$time}</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[{$content}]]></Content>
            <FuncFlag>0</FuncFlag>
            </xml>";
        return $result;
    }
    /**
     * [transmitText 组合返回给 微信公众平台的数据]
     * @param  [type] $object  [xml对象]
     * @param  [type] $content [回复内容]
     * @return [type]          [xml格式字符串]
     */
    public function transmitInfo($object){
        $time = time();
        $result = "<xml>
            <ToUserName><![CDATA[{$object['ToUserName']}]]></ToUserName>
            <FromUserName><![CDATA[{$object['FromUserName']}]]></FromUserName>
            <CreateTime>123</CreateTime>
             <MsgType><![CDATA[news]]></MsgType>
             <ArticleCount>1</ArticleCount>
             <Articles>
             <item>
             <Title><![CDATA['标题标题'']]></Title> 
             <Description><![CDATA['说明'']]></Description>
             <PicUrl><![CDATA['http://img2.imgtn.bdimg.com/it/u=3407852600,1845840160&fm=21&gp=0.jpg']]></PicUrl>
             <Url><![CDATA['www.baidu.com']]></Url>
             </item>
            
             </Articles>
             </xml> ";
        zlog($result);
        return $result;
    }
	
    /**
     * response 回复消息
     * @karl
     * @DateTime 2016-09-29T07:04:23+0800
     * @param    string                   $content 回复内容
     * @param    string                   $type    内容形式text|music|news
     * @param    integer                  $flag    标识
     * @return   [type]                            [description]
     */
    public function response($content, $type = 'text', $flag = 0)
    {
        $time = time();
        $this->data = array('ToUserName' => $this->data['FromUserName'], 'FromUserName' => $this-> data['ToUserName'], 'CreateTime' => $time, 'MsgType' => $type);
        $this->$type($content);
        $this->data['FuncFlag'] = $flag;
        $xml = new \SimpleXMLElement('<xml></xml>');
        $this->data2xml($xml, $this->data);
        zlog($xml->asXML());
        exit($xml->asXML());
    }

    private function text($content)
    {
        $this->data['Content'] = $content;
    }

    private function music($music)
    {
        list($music['Title'], $music['Description'], $music['MusicUrl'], $music['HQMusicUrl']) = $music;
        $this->data['Music'] = $music;
    }

    private function news($news)
    {
        $articles = array();
        foreach ($news as $key => $value){
            list($articles[$key]['Title'], $articles[$key]['Description'], $articles[$key]['PicUrl'], $articles[$key]['Url']) = $value;
            if($key >= 9){
                break;
            }
        }
        $this->data['ArticleCount'] = count($articles);
        $this->data['Articles'] = $articles;
    }

    private function transfer_customer_service($content)
    {
        $this->data['Content'] = '';
    }

    private function data2xml($xml, $data, $item = 'item')
    {
        zlog($data);
        foreach ($data as $key => $value){
            is_numeric($key) && $key = $item;
            if(is_array($value) || is_object($value)){
                zlog("taaa");
                $child = $xml -> addChild($key);
                $this -> data2xml($child, $value, $item);
            }else{
                zlog("tbbb");
                if(is_numeric($value)){
                zlog("tccc");
                    $child = $xml -> addChild($key, $value);
                }else{
                zlog("tddd");zlog($key);
                    $child = $xml -> addChild($key);
                    $node = dom_import_simplexml($child);
                    $node -> appendChild($node->ownerDocument->createCDATASection($value));
                }
            }
        }
    }

    /**
     * 验证tokean
     * @DateTime 2016-08-25T20:56:09+0800
     * @param    string                   $token tokan值
     * @return   boolean                         布尔值
     */
    private function auth($token)
    {
        $signature = isset($_GET["signature"]) ? $_GET["signature"] : "";
        $timestamp = isset($_GET["timestamp"]) ? $_GET["timestamp"] : "";
        $nonce = isset($_GET["nonce"]) ? $_GET["nonce"] : "";
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if(trim($tmpStr) == trim($signature)){
            zlog("true");
            return true;
        }else{
            zlog("false");
            return false;
        }
        zlog("true");
        return true;
    }

}  



?>
