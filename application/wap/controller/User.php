<?php
namespace app\wap\controller;

use think\Session;
use think\Controller;
use think\Validate;
use think\Request;
use app\common\model\base\Users;
use app\common\model\UsersAddress;
use app\common\model\Regions;
use app\common\model\UsersParent;
use app\common\model\base\UsersMoney;
use app\common\model\weixin\DiyMenu;
use app\common\model\base\UsersPoints;
use app\common\model\Orders;
use app\common\model\UsersCustomers;
use app\common\model\base\Coms;
use weixin\pay\WeixinPay;

/*************************************************
 * @ClassName:     User
 * @Description:   用户控制器
 * @author:        
 * @DateTime      
 *************************************************/
class User extends WeixinBase
{
    /*************************************************
     * Function:      myDatum
     * Description:   我的资料
     * @param:        void
     * Return:        void
     *************************************************/
    public function myDatum()
    {  
        $uid = session('uid');
        $info = Users::myInfo($uid);
        $address = UsersAddress::getList($uid);
        if ($address['error_code'] == 0) {
            foreach ($address['data'] as $k => $v) {
                $ids = [
                    'province' => $v['province'],
                    'city' => $v['city'],
                    'district' => $v['district'],
                    'town' => $v['town'],
                ];
                $str = Regions::getNameStr($ids);
                $address['data'][$k]['str'] = $str;
            }
        }else{
            $address['data'] = '';
        }
        $this->assign('address',$address['data']);
        $this->assign('data',$info['data']);
        return $this->fetch();
    }

    /*************************************************
     * Function:      ajaxDefaultAdderss
     * Description:   更改默认地址
     * @param:        void
     * Return:        void
     *************************************************/
    public function ajaxDefaultAdderss()
    {
        $input = Request::instance()->param();
        $rule = [
            'id'  => 'require',
        ];
        $msg = [
            'nickname.require'  =>  'id必须填写',
        ];
        $validate = new Validate($rule, $msg);
        $result   = $validate->check($input);
        if (!$result)
        {   
            $arr['error_msg'] = $validate->getError();
            return $arr;
        }
        $address_id = $input['id'];
        $res = UsersAddress::editDefault($address_id);
        if ($res['error_code'] == 0) {
            $arr['error_code'] = 0;
            $arr['error_msg'] = '';
        }else{
            $arr['error_code'] = 1;
            $arr['error_msg'] = '更新失败';
        }
        return $arr;
    }

    /*************************************************
     * Function:      ajaxDatum
     * Description:   更新我的资料
     * @param:        void
     * Return:        void
     *************************************************/
    public function ajaxDatum()
    { 
        $uid = session('uid');
        $input = Request::instance()->param();
        $rule = [
            'nickname'  => 'require',
            'email'  => 'require',
            'moblie'  => 'require',
        ];
        $msg = [
            'nickname.require'  =>  '昵称必须填写',
            'email.require'  =>  '邮箱必须填写',
            'moblie.require'  =>  '手机必须填写',
        ];
        $validate = new Validate($rule, $msg);
        $result   = $validate->check($input);
        if (!$result)
        {   
            $arr['error_msg'] = $validate->getError();
            return $arr;
        }
        $data = [
            'sex' => $input['sex'],
            'nickname' => $input['nickname'],
            'email' => $input['email'],
            'moblie' => $input['moblie'],
        ];
        $res = UsersCustomers::edit($uid,$data);
        if ($res['error_code'] == 0) {
            $arr['error_code'] = 0;
            $arr['error_msg'] = '';
        }else{
            $arr['error_code'] = 1;
            $arr['error_msg'] = '更新失败';
        }
        return $arr;
    }

    /*************************************************
     * Function:      myIntegral
     * Description:   我的积分
     * @param:        void
     * Return:        void
     *************************************************/
    public function myIntegral()
    {   
        $uid = session('uid');
        $info =  UsersPoints::countIntegral($uid);
        $res = Users::integralList($uid);
        if ($res['error_code'] == 0) {
            $data = $res['data'];
        }else{
            $data = '';
        }
        $this->assign('points',$info['points']);
        $this->assign('data',$data);
        return $this->fetch();
    }
	
    /*************************************************
     * Function:      myrebate
     * Description:   我的返佣
     * @param:        void
     * Return:        void
     *************************************************/
    public function myrebate()
    {   
        $uid = session('uid');
        $res =  Users::myInfo($uid);
		if ($input = Request::instance()->param()) {
            if (isset($input['type'])) {
                if($input['type'] == 'cash'){
                    $where['type'] = 'cash';
                    $type = 2;
                }
            }
        }else{
            $where = '';
            $type = 1;
        }
        if ($res['error_code'] == 0) {
            $data = $res['data'];
        }else{
            $data = '';
        }
        $this->assign('data',$data);
		$this->assign('type',$type);
        return $this->fetch();
    }
	
	
	/*************************************************
     * Function:      myvoucher
     * Description:   我的返佣
     * @param:        void
     * Return:        void
     *************************************************/
    public function myvoucher()
    {   
        $uid = session('uid');
        $res =  Users::myInfo($uid);
		if ($input = Request::instance()->param()) {
            if (isset($input['type'])) {
                if($input['type'] == 'cash'){
                    $where['type'] = 'cash';
                    $type = 2;
                }
            }
        }else{
            $where = '';
            $type = 1;
        }
        if ($res['error_code'] == 0) {
            $data = $res['data'];
        }else{
            $data = '';
        }
        $this->assign('data',$data);
		$this->assign('type',$type);
        return $this->fetch();
    }
	
	/*************************************************
     * Function:      problem
     * Description:   常见问题
     * @param:        void
     * Return:        void
     *************************************************/
    public function problem()
    {   
        $uid = session('uid');
        $res =  Users::myInfo($uid);
		if ($input = Request::instance()->param()) {
            if (isset($input['type'])) {
                if($input['type'] == 'cash'){
                    $where['type'] = 'cash';
                    $type = 2;
                }
            }
        }else{
            $where = '';
            $type = 1;
        }
        if ($res['error_code'] == 0) {
            $data = $res['data'];
        }else{
            $data = '';
        }
        $this->assign('data',$data);
		$this->assign('type',$type);
        return $this->fetch();
    }

    /*************************************************
     * Function:      recharge
     * Description:   充值
     * @param:        void
     * Return:        void
     *************************************************/
    public function recharge()
    {
        return $this->fetch(); 
    }
    
    /*************************************************
     * Function:      ajaxrecharge
     * Description:   充值 数据
     * @param:        void
     * Return:        void
     *************************************************/
    public function ajaxrecharge()
    {    
        if ($input = Request::instance()->param()) {
            $date = [
                'uid' => session('uid'),
                'type' => '充值订单',
                'money' => $input['money'],
                'set_time' => time()
            ];
            if ($id = Orders::payAdd($date)['id']) {
                $key = Coms::getValue('apikey')['data'];
                $data['appid'] = Coms::getValue('appid')['data'];
                $data['appsecret'] = Coms::getValue('appsecret')['data'];
                $data['mchid'] = Coms::getValue('mchid')['data'];
                $data['open_id'] = session("open_id");
                $data['body'] = "充值订单";
                $data['attach'] = "chonzhi";
                $data['money'] = 0.01; // $input['money'];
                $data['out_order'] = $id.'-'.time().rand(100, 999);
                $data['notify_url'] = "http://fsm.yuncentry.com/weixinpaynotify.php";
                $weixinpay = new WeixinPay;
                $jsApiParameters = $weixinpay->createPay($data, $key);
                zlog($jsApiParameters);
                return json($jsApiParameters);
            }else{
                return false;
            }
        }
    }

    /*************************************************
     * Function:      cash
     * Description:   提现
     * @param:        void
     * Return:        void
     *************************************************/
    public function cash()
    {   
        $max =  Coms::getValue('least_money_limit')['data']; //最少提现
        $money = UsersMoney::countBalance(session('uid'));
        $this->assign('money',$money['balance']);
        $this->assign('max',$max);
        return $this->fetch();
    }

    /*************************************************
     * Function:      ajaxCashAdd
     * Description:   申请提现
     * @param:        void
     * Return:        void
     *************************************************/
    public function ajaxCashAdd(){
        $input = Request::instance()->param();
        $uid = session('uid');
        $row = UsersPoints::info($uid,['transit_time'=> '']);
        if($row['error_code'] == ''){
            $arr['error_code'] = 2;
            $arr['error_msg'] = '你有提现在申请中，不能再提现';
            return $arr;
        }
        $data = [
            'uid' => $uid,
            'time' => time(),
            'money' => $input['money']
        ];
        $res = UsersPoints::addOn($data);
        if ($res['error_code'] == 0) {
            $arr['error_code'] = 0;
            $arr['error_msg'] = '';
        }else{
            $arr['error_code'] = 1;
            $arr['error_msg'] = '提交失败';
        }
        return $arr;      
    }

    /*************************************************
     * Function:      distribution
     * Description:   分享 二维码
     * @param:        void
     * Return:        void
     *************************************************/
    public function distribution()
    {   
        $uid = session('uid');
        if (Orders::ordersConsume($uid)['error_code'] == 0 ) {
            $res = Users::myInfo($uid);
            if ($res['data']['code_url'] == '') {
                $row = DiyMenu::createQRCode($uid);
                UsersCustomers::edit($uid,['code_url' => $row['url']]);
            }
        }
        $res = Users::myInfo($uid);        
        if ($res['error_code'] == 0) {
            $data = $res['data'];
        }else{
            $data = '';
        }
        $infoimg = Coms::getValue('code_template_image');
        if ($infoimg['error_code'] == 0) {
          $image = $infoimg['data'];
        }else{
          $image = '';
        }
        $this->assign('image',$image);
        $this->assign('data',$data);
        return $this->fetch();
    }

    /*************************************************
     * Function:      myMoney
     * Description:   我的钱包
     * @param:        void
     * Return:        void
     *************************************************/
    public function myMoney()
    {
        $uid = session('uid');
        if ($input = Request::instance()->param()) {
            if (isset($input['type'])) {
                if ($input['type'] == 'recharge') {
                    $where['type'] = 'recharge';
                    $type = 2;
                }elseif($input['type'] == 'cash'){
                    $where['type'] = 'cash';
                    $type = 3;
                }
            }
        }else{
            $where = '';
            $type = 1;
        }
        $row = UsersMoney::countBalance($uid);
        $res = Users::moneyList($uid,$where);
        if ($res['error_code'] == 0) {
            $data = $res['data'];
        }else{
            $data = '';
        }
        $this->assign('money',$row['balance']);
        $this->assign('type',$type);
        $this->assign('data',$data);
        return $this->fetch();
    }

    /*************************************************
     * Function:      ajaxAddress
     * Description:   地址获取
     * @param:        void
     * Return:        void
     *************************************************/
    public function ajaxAddress()
    {
        $input = Request::instance()->param();
        $html = '<option value="">请选择</option>';
        $res = Regions::getChildren($input['id']);
        if ($res['error_code'] == 0) {
            foreach ($res['data'] as $val) {
                $html.= '<option value="'.$val["id"].'">'.$val["name"].'</option>';
            }
        }
        return $html;
    }

    /*************************************************
     * Function:      ajaxAddAddress
     * Description:   添加地址
     * @param:        void
     * Return:        void
     *************************************************/
    public function ajaxAddAddress()
    {
        /******************* 验证网址参数 ********************/
        $input = Request::instance()->param();
        $rule = [
            'consignee'  => 'require',
            'country'  => 'require',
            'city'  => 'require',
            'district'  => 'require',
            'province'  => 'require',
            'address'  => 'require',
            'mobile'  => 'require',
            'zipcode'  => 'require',
        ];
        $msg = [
            'consignee.require'  =>  'consignee必须填写',
            'country.require'  =>  'country必须填写',
            'city.require'  =>  'city必须填写',
            'district.require'  =>  'district必须填写',
            'province.require'  =>  'province必须填写',
            'address.require'  =>  'address必须填写',
            'mobile.require'  =>  'mobile必须填写',
            'zipcode.require'  =>  'zipcode必须填写',
        ];
        $validate = new Validate($rule, $msg);
        $result   = $validate->check($input);
        if (!$result)
        {   
            $arr['error_code'] = 2;
            $arr['error_msg'] = $validate->getError();
            return $arr;
        }
        $data = [
            'uid' => session('uid'),
            'consignee'  => $input['consignee'],
            'country'  => $input['country'],
            'province' => $input['province'],
            'city'  => $input['city'],
            'district'  => $input['district'],
            'town'  => isset($input['town'])?$input['town']:'',
            'address'  => $input['address'],
            'mobile'  => $input['mobile'],
            'zipcode'  => $input['zipcode'],
        ];
        $res = UsersAddress::add($data);
        if ($res['error_code'] == 0) {
            $arr['error_code'] = 0;
            $arr['error_msg'] = '';
        }else{
            $arr['error_code'] = 1;
            $arr['error_msg'] = $res['error_msg'];
        }
        return $arr;
    }

    /*************************************************
     * Function:      addAddress
     * Description:   添加地址
     * @param:        void
     * Return:        void
     *************************************************/
    public function addAddress()
    {   
        $res = Regions::getLevel(1);
        $this->assign('data',$res['data']);
        return $this->fetch();
    }

    /*************************************************
     * Function:      upAddress
     * Description:   修改地址
     * @param:        void
     * Return:        void
     *************************************************/
    public function upAddress()
    {   
        $input = Request::instance()->param();
        $rule = [
            'id'  => 'require',
        ];
        $msg = [
            'id.require'  =>  'id必须填写',
        ];
        $validate = new Validate($rule, $msg);
        $result   = $validate->check($input);
        if (!$result)
        {   
            $this->error($validate->getError() );
        }
        $address_id = $input['id'];
        $uid = session('uid');
        $address = UsersAddress::getAddress($uid,$address_id);
        $city = Regions::getName($address['data']['city']);
        $district = Regions::getName($address['data']['district']);
        if ($address['data']['town'] != 0) {
            $town = Regions::getName($address['data']['town']);
        }else{
            $town['data'] = false;
        }
        $res = Regions::getLevel(1);
        $this->assign('city',$city['data']);
        $this->assign('district',$district['data']);
        $this->assign('town',$town['data']);
        $this->assign('address',$address['data']);
        $this->assign('data',$res['data']);
        return $this->fetch();
    }

    /*************************************************
     * Function:      ajaxDelAdderss
     * Description:   删除用户地址
     * @param:        void
     * Return:        void
     *************************************************/
    public function ajaxDelAdderss()
    {   
        $input = Request::instance()->param();
        $rule = [
            'id'  => 'require',
        ];
        $msg = [
            'id.require'  =>  'id必须填写',
        ];
        $validate = new Validate($rule, $msg);
        $result   = $validate->check($input);
        if (!$result)
        {   
            $arr['error_code'] = 2;
            $arr['error_msg'] = $validate->getError();
            return $arr;
        }
        $address_id = $input['id'];
        $res = UsersAddress::del($address_id);
        if ($res['error_code'] == 0) {
            $arr['error_code'] = 0;
            $arr['error_msg'] = '';
        }else{
            $arr['error_code'] = 1;
            $arr['error_msg'] = $res['error_msg'];
        }
        return $arr;
    }

    /*************************************************
     * Function:      ajaxUpAddress
     * Description:   更新地址
     * @param:        void
     * Return:        void
     *************************************************/
    public function ajaxUpAddress()
    {
        /******************* 验证网址参数 ********************/
        $input = Request::instance()->param();
        $rule = [
            'id' => 'require',
            'consignee'  => 'require',
            'country'  => 'require',
            'city'  => 'require',
            'district'  => 'require',
            'province'  => 'require',
            'address'  => 'require',
            'mobile'  => 'require',
            'zipcode'  => 'require',
        ];
        $msg = [
            'id.require'  =>  'id必须填写',
            'consignee.require'  =>  'consignee必须填写',
            'country.require'  =>  'country必须填写',
            'city.require'  =>  'city必须填写',
            'district.require'  =>  'district必须填写',
            'province.require'  =>  'province必须填写',
            'address.require'  =>  'address必须填写',
            'mobile.require'  =>  'mobile必须填写',
            'zipcode.require'  =>  'zipcode必须填写',
        ];
        $validate = new Validate($rule, $msg);
        $result   = $validate->check($input);
        if (!$result)
        {   
            $arr['error_code'] = 2;
            $arr['error_msg'] = $validate->getError();
            return $arr;
        }
        $data = [
            'uid' => session('uid'),
            'consignee'  => $input['consignee'],
            'country'  => $input['country'],
            'province' => $input['province'],
            'city'  => $input['city'],
            'district'  => $input['district'],
            'town'  => isset($input['town'])?$input['town']:'',
            'address'  => $input['address'],
            'mobile'  => $input['mobile'],
            'zipcode'  => $input['zipcode'],
        ];
        $address_id = $input['id'];
        $res = UsersAddress::edit($address_id,$data);
        if ($res['error_code'] == 0) {
            $arr['error_code'] = 0;
            $arr['error_msg'] = '';
        }else{
            $arr['error_code'] = 1;
            $arr['error_msg'] = $res['error_msg'];
        }
        return $arr;
    }

    /*************************************************
     * Function:      userInfo
     * Description:   用户信息
     * @param:        void
     * Return:        void
     *************************************************/
    public function userInfo()
    {   
        $uid = session('uid');
        $res = Users::myInfo($uid);
        if ($res['error_code'] == 0) {
            $data = $res['data'];
        }else{
            $data = '';
        }
        $this->assign('data',$data);
        return $this->fetch();
    }    


    /*************************************************
     * Function:      myShare
     * Description:   我的分享
     * @param:        void
     * Return:        void
     *************************************************/
    public function myShare()
    {   
        return $this->fetch();
    }

    /*************************************************
     * Function:      myShare
     * Description:   我的分享详情
     * @param:        void
     * Return:        void
     *************************************************/
    public function myShareinfo()
    {   
        $input = Request::instance()->param();
        $uid['0'] = ['uid' => session('uid')];
        $res = UsersParent::getChildren($uid,3);
        if (isset($res[$input['id']])) {
            $ids = $res[$input['id']];
            $row = Users::shareList($ids);
            if ($row['error_code'] == 0) {
                $data = $row['data'];
            }else{
                $data = '';
            }
        }else{
            $data = '';
        }
        $this->assign('date',date('Y-m-d H:i'));
        $this->assign('data',$data);
        return $this->fetch();
    }

    /*************************************************
     * Function:      integralRanking
     * Description:   积分排行
     * @param:        void
     * Return:        void
     *************************************************/
    public function integralRanking()
    {   
        $uid = session('uid');
        $resd = UsersPoints::countIntegral($uid);
        if ($resd['error_code'] == 0) {
            $points = $resd['points'];
        }else{
            $points = 0;
        }
        $this->assign('points',$points);
        $res = Users::rankingList();
        if ($res['error_code'] == 0) {
            $data = $res['data'];
        }else{
            $data = '';
        }
        foreach ($data as $key => $v) {
            if ($v['uid'] == $uid) {
                $ran = $key + 1;
                break;
            }else{
                $ran = '';
            }
        }
        $this->assign('ran',$ran);
        $this->assign('date',date('Y-m-d H:i'));
        $this->assign('data',$data);
        return $this->fetch();
    }
}