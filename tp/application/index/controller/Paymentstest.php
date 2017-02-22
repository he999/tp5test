<?php
namespace app\index\controller;

use app\common\model\Payments;

class PaymentsTest
{
	//得到支付方式
    public function test()
    {
        $com_id = 1;
        $result = Payments::getPayments($com_id);
        var_dump($result);
    }

    //得到支付方式详情
    public function test1()
    {
        $pay_id = 1;
        $result = Payments::getOne($pay_id);
        var_dump($result);
    }
}