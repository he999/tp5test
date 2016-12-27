<?php                                                         
namespace weixin\pay;

/**
 * 微信支付类
 */
class WeixinPay
{
    /**
     * createPay 发起支付
     * @karl
     * @DateTime 2016-08-04T13:17:46+0800
     * @param    array                   $data 支付参数
     * ['appid','mchid','key','body','out_order','money','notify_url','open_id','attach']
     * @return   array                         返回值
     */
    public function createPay($data,$key)
    {
        //②、统一下单
        $tools = new JsApiPay();
        $input = new WxPayUnifiedOrder();
        $input->SetAppid($data['appid']);
        $input->SetMch_id($data['mchid']);
        $input->SetKey($key);
        $input->SetAttach($data['attach']);
        $input->SetBody($data['body']);
        $input->SetOut_trade_no($data['out_order']);
        $input->SetTotal_fee($data['money']*100);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetNotify_url($data['notify_url']);
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($data['open_id']);   
        $order = WxPayApi::unifiedOrder($input);
        $jsApiParameters = $tools->GetJsApiParameters($order, $key);
        //③、在支持成功回调通知中处理成功之后的事宜
        return $jsApiParameters;        
    }
}


?>