<?php
namespace weixin\hongbao;

class Hongbao
{
    /**
     * pay 支付红包
     * @karl
     * @DateTime 2016-08-04T19:51:18+0800
     * @param    array                   $data 输入数组
     * ['mchid', 'appid', 'open_id', 'money', 'info' ]
     * @return   boolean                         
     */
    public function pay($data)
    {
        $commonUtil = new CommonUtil();
        $wxHongBaoHelper = new WxHongBaoHelper();
        $wxHongBaoHelper->setParameter("mchid", $data['mchid']);//商户号
        $wxHongBaoHelper->setParameter("mch_appid", $data['appid']);
        #$wxHongBaoHelper->setParameter("device_info", '');//微信支付分配的终端设备号
        #$wxHongBaoHelper->setParameter("sub_mch_id", '');//子商户号
        $wxHongBaoHelper->setParameter("nonce_str", $this->great_rand());//随机字符串，丌长于 32 位
        //订单号
        $wxHongBaoHelper->setParameter("partner_trade_no", $data['order_sn']);
        $wxHongBaoHelper->setParameter("openid", $data['open_id']);//相对于医脉互通的openid
        $wxHongBaoHelper->setParameter("check_name",'NO_CHECK');
        #$wxHongBaoHelper->setParameter("nick_name", '罗华峰的红包');//提供方名称
        #$wxHongBaoHelper->setParameter("send_name", '罗华峰的红包');//红包发送者名称
        #$wxHongBaoHelper->setParameter("re_user_name",'');//用户姓名
        $wxHongBaoHelper->setParameter("amount", abs($data['money'])*100);//付款金额，单位分
        #$wxHongBaoHelper->setParameter("amount",$money*100);//付款金额，单位分
        $wxHongBaoHelper->setParameter("desc", $data['info']);//企业付款描述信息
        
        #$wxHongBaoHelper->setParameter("min_value", 1);//最小红包金额，单位分
        #$wxHongBaoHelper->setParameter("max_value", 100);//最大红包金额，单位分
        #$wxHongBaoHelper->setParameter("total_num", 1);//红包収放总人数
        #$wxHongBaoHelper->setParameter("wishing", '恭喜发财哦哦');//红包祝福诧
        $wxHongBaoHelper->setParameter("spbill_create_ip", $_SERVER['REMOTE_ADDR']);//调用接口的机器 Ip 地址
        #$wxHongBaoHelper->setParameter("act_name", '红包活动');//活劢名称
        #$wxHongBaoHelper->setParameter("remark", '快来抢！');//备注信息
        $postXml = $wxHongBaoHelper->create_hongbao_xml($data['apikey']);
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
        zlog($postXml);
        $responseXml = $wxHongBaoHelper->curl_post_ssl($url, $postXml);
        $responseObj = simplexml_load_string($responseXml, 'SimpleXMLElement', LIBXML_NOCDATA);
        zlog($responseObj);
        if($responseObj->return_code=='SUCCESS' && $responseObj->result_code =='SUCCESS')
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * 生成随机数
     * 
     */     
    public function great_rand()
    {
        $str = '1234567890abcdefghijklmnopqrstuvwxyz';
        $t1 = "";
        for($i=0;$i<30;$i++){
            $j=rand(0,35);
            $t1 .= $str[$j];
        }
        return $t1;    
    }    
}


?>