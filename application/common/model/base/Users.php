<?php
namespace app\common\model\base;

use think\Model;
use think\Db;
use think\Session;

use app\common\model\base\UsersAccess;

/*************************************************  
*ClassName:     UsersManagers
*Description:   商户管理用户类
*Others:        
*************************************************/

class Users extends Model
{
    
    /*************************************************  
    * Function:      login
    * Description:   得到登陆信息
    * @param: string $username
    * @param：string $password     
    * Return:        fix 用户信息,包括access token,失败返回false
    *************************************************/
    static public function login($username, $password)
    {
        $password = md5($password);
        $row = self::get(['username' => $username, 'password' => $password]);
        $return = [];
        if ($row)
        {   
            $access = UsersAccess::open($row['uid']);
            if($access['error_code'] ==0){
                $return['error_code'] = 0;
                $result['error_msg'] = '登录成功';
                $return['access_token'] = $access['data']['access_token'];
                $return['data'] = $row->data;
            }else{
                $return['error_code'] = 2;
                $result['error_msg'] = '访问失败';
            }
        }
        else
        {
            $return['error_code'] = 1;
            $result['error_msg'] = '密码与用户名验证错误';
        }
        return $return;
    }
    /**
     * add 添加用户 
     * 包括users表和users_weixins表  user_customers表
     * @karl
     * @param    array     $data
     * @return   array     [error_code, error_msg, id]
     * @DateTime 2016-11-22T20:46:59+0800
     */
    static public function add($data)
    {    
        $user['create_time']=time();
        $uid = Db::name('users')->insertGetId($user);
        $customer['uid']=$uid;
        Db::name('users_customers')->insert($customer);
        $data['uid']=$uid;
        $row = Db::name('users_weixin')->insert($data);
        if ($row) {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "添加失败";
        }
    return $result;
        
    }

    /**
     * editUsersCustomers 修改用户信息 
     * @xiao
     * @param    array     $data
     * @return   array     [error_code, error_msg, id]
     * @DateTime 2016-11-22T20:46:59+0800
     */
    static public function editUsersCustomers($uid,$data)
    {   
        $where['uid'] = $uid;
        $res = Db::name('users_customers')->where($where)->update($data);
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "修改失败";
        }
        return $result;
    }

    /**
     * myinfo 用户信息 
     * @tanlong
     * @param    array     $data
     * @return   array     [error_code, error_msg, id]
     * @DateTime 2016-11-22T20:46:59+0800
     */
    static public function myInfo($uid)
    {   
        $res = Db::name('users_customers')->where(['uid' => $uid])->find();
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] = $res;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "查询失败";
        }
        return $result;
    }

    /**
     * info 用户信息 
     * @tanlong
     * @param    array     $data
     * @return   array     [error_code, error_msg, id]
     * @DateTime 2016-11-22T20:46:59+0800
     */
    static public function info($uid)
    {   
        $res = Db::name('users_customers')->alias('a')->join('users_address c','a.uid = c.uid','left')
        ->field(['a.uid,a.commission,a.nickname,a.balance,a.voucher,a.sex,a.moblie,a.email,a.member_type,a.face','c.consignee,c.address,c.zipcode,c.province,c.city,c.district'])
        ->find($uid);
        // $res2=Db::name('users_money')->where(['uid'=>$uid])->select();
        // $commission=0;
        // foreach ($res2 as $k => $v) {
        //      $commission+=$v['income']-$v['expense'];
        // }
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] = $res;
            //$result['commission'] = $commission;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "查询失败";
        }
        return $result;
    }

    /**
     * editUsers 修改用户信息 
     * @tanlong
     * @param    array     $data
     * @return   array     [error_code, error_msg, id]
     * @DateTime 2016-11-22T20:46:59+0800
     */
    static public function editUsers($uid,$data)
    {   
        $where['uid'] = $uid;
        $res = Db::name('users_customers')
        ->where($where)
        ->update(['nickname'=>$data['nickname'],
                         'email'=>$data['email'],
                         'moblie'=>$data['moblie'],     
                         'sex'=>$data['sex'],             
            ]);
        $res2= Db::name('users_address')
        ->where($where)
        ->update(['zipcode'=>$data['zipcode'],  
                         'address'=>$data['address'],    
                         'province'=>$data['province'],   
                         'city'=>$data['city'],   
                         'district'=>$data['district'],  
                         'consignee'=>$data['consignee'],                   
            ]);
        if($data['commission']!=''){
             if($data['commission']>0){
                $res3=Db::name('users_money')->insert(['des'=>'佣金','uid'=>$uid,'type'=>'commission','income'=>$data['commission'],'time'=>time()]);               
            }else{
                $res3=Db::name('users_money')->insert(['des'=>'佣金','uid'=>$uid,'type'=>'commission','expense'=>-$data['commission'],'time'=>time()]);
            }
            $res10=Db::name('users_customers')->find($uid);
            $money=$res10['balance']+$data['commission'];
            if($money<0){
                $money=0;
            }
            Db::name('users_customers')->where($where)->update(['balance'=>$money]);          
            $res7=Db::name('users_money')->where(['uid'=>$uid])->select();
            $commission=0;
            foreach ($res7 as $k => $v) {
                 $commission+=$v['income']-$v['expense'];
            }
            if($commission<0){
               $commission=0;
            }
            Db::name('users_customers')->where($where)->update(['commission'=> $commission]);   
        }
        // if($data['points']!=''){
                // $res4=Db::name('users_points')->insert(['uid'=>$uid,'type'=>'管理员修改','points'=>$data['points'],'time'=>time()]);
                // if($res4){
                // $res5=Db::name('users_points')->where(['uid'=>$uid])->select();
                // $points=0;
                // foreach ($res5 as $k => $v) {
                     // $points+=$v['points'];
                // } 
                // Db::name('users_customers')->where($where)->update(['points'=>$points]);         
            // }
        // }      
        if ($res!==false && $res2!==false ) {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] = $res;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "修改失败";
        }
        return $result;
    }

    /**
     * moneyLst 佣金记录
     * @tanlong
     * @param    array     $data
     * @return   array     [error_code, error_msg, id]
     * @DateTime 2016-11-22T20:46:59+0800
     */
    static public function moneyLst($num,$id)
    {   
        $res = Db::name('users_money_rebate')->where(['uid'=>$id,'is_del'=>0])->order('time desc')->paginate($num);
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] = $res;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "修改失败";
        }
        return $result;
    }
	
	/**
     * balanceLst 余额记录
     * @tanlong
     * @param    array     $data
     * @return   array     [error_code, error_msg, id]
     * @DateTime 2016-11-22T20:46:59+0800
     */
    static public function balanceLst($num,$id)
    {   
        $res = Db::name('users_money')->where(['uid'=>$id,'is_del'=>0])->order('time desc')->paginate($num);
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] = $res;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "修改失败";
        }
        return $result;
    }

    /**
     * voucherLst 积分记录
     * @tanlong
     * @param    array     $data
     * @return   array     [error_code, error_msg, id]
     * @DateTime 2016-11-22T20:46:59+0800
     */
    static public function voucherLst($num,$id)
    {   
        $res = Db::name('users_money_voucher')->where(['uid'=>$id,'is_del'=>0])->order('time desc')->paginate($num);
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] = $res;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "修改失败";
        }
        return $result;
    }

    /**
     * editUsersup 修改用户信息 
     * @xiao
     * @param    array     $data
     * @return   array     [error_code, error_msg, id]
     * @DateTime 2016-11-22T20:46:59+0800
     */
    static public function editUsersup($uid,$data)
    {   
        $where['uid'] = $uid;
        $res = Db::name('users_customers')->where($where)->update($data);
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] = $res;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "修改失败";
        }
        return $result;
    }
    /**
     * comsLst 加盟列表
     * @tanlong
     * @param    array     $data
     * @return   array     [error_code, error_msg, id]
     * @DateTime 2016-11-22T20:46:59+0800
     */
    static public function comsLst($num)
    {   
        $res = Db::name('application_join')->order('time desc')->paginate($num);
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] = $res;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "修改失败";
        }
        return $result;
    }

    /**
     * comsDel 加盟删除
     * @tanlong
     * @DateTime 2016-07-31T07:38:20+0800
     * @param    array                   $data 输入数据
     * @return   array                   [error_code, error_msg, com_id=> [] ]
     */
    static public function comsDel($id)
    {
        $res = Db::name('application_join')->delete($id);
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] = $res;;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "删除失败";
        }
        return $result;
    }

    /**
     * twodimensionalCodes 二维码列表
     * @tanlong
     * @param    array     $data
     * @return   array     [error_code, error_msg, id]
     * @DateTime 2016-11-22T20:46:59+0800
     */
    static public function twodimensionalCodes($num,$where,$url)
    {   
        $where['code_url']=array("neq",''); 
        $res = Db::name('users_customers')
        ->where($where)
        ->order('opening_time')
        ->field('nickname,code_url')
        ->paginate($num,false,array('query'=>$url)); 
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] = $res;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "修改失败";
        }
        return $result;
    }

    /**
     * jifen 全部积分记录
     * @tanlong
     * @param    array     $data
     * @return   array     [error_code, error_msg, id]
     * @DateTime 2016-11-22T20:46:59+0800
     */
    static public function rebateLst($num)
    {   
        $res = Db::name('users_money_rebate')
        ->alias('a')->where(['a.is_del'=>0]) ->join('users_customers c','a.uid = c.uid','left')->order('time desc')
        ->field('a.*,c.nickname')
        ->paginate($num); 
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] = $res;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "修改失败";
        }
        return $result;
    }

    /**
     * jifenDel 积分记录删除
     * @tanlong
     * @DateTime 2016-07-31T07:38:20+0800
     * @param    array                   $data 输入数据
     * @return   array                   [error_code, error_msg, com_id=> [] ]
     */
    static public function jifenDel($id)
    {
        $res = Db::name('users_points')->where(['id'=>$id])->update(['is_del'=>1]);
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] = $res;;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "删除失败";
        }
        return $result;
    }

    /**
     * allMoneyLst 全部资金记录
     * @tanlong
     * @param    array     $data
     * @return   array     [error_code, error_msg, id]
     * @DateTime 2016-11-22T20:46:59+0800
     */
    static public function allMoneyLst($num)
    {   
        $res = Db::name('users_money')
        ->alias('a')
        ->where(['a.is_del'=>0]) 
        ->join('users_customers b','a.uid = b.uid','left')
        ->join('orders d','a.order_id = d.order_id','left')
        ->order('time desc')
        ->field('a.id,a.des,a.time,a.income,a.expense,b.nickname,d.consignee,d.order_sn')
        ->paginate($num); 
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] = $res;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "修改失败";
        }
        return $result;
    }

    /**
     * allMoneyDel 金钱记录删除
     * @tanlong
     * @DateTime 2016-07-31T07:38:20+0800
     * @param    array                   $data 输入数据
     * @return   array                   [error_code, error_msg, com_id=> [] ]
     */
    static public function allMoneyDel($id)
    {
        $res = Db::name('users_money')->where(['id'=>$id])->update(['is_del'=>1]);
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] = $res;;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "删除失败";
        }
        return $result;
    }

    /**
     * memberLst 用户列表
     * @tanlong
     * @DateTime 2016-07-31T07:38:20+0800
     * @param    array                   $data 输入数据
     * @return   array                   [error_code, error_msg, com_id=> [] ]
     */
    static public function memberLst($num,$where,$url)
    {
        $res = Db::name('users')
              ->alias('a')
              ->join('users_customers c','a.uid = c.uid','left')
              ->field(["a.create_time","a.uid","c.nickname","c.face","c.moblie","c.member_type"])
              ->where($where)
              ->paginate($num,false,array('query'=>$url)); 
        if($res){
            $counts=array();
            foreach ($res as $k => $v){
                 $count=Db::name('users_parent')
                 ->where(array('parent'=>$v['uid']))
                 ->count();
             $counts[]=$count;
            }
        }
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] = $res;
            $result['data2'] = $counts;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "查询失败";
        }
        return $result;
    }

     /**
     * memberDel 会员删除
     * @tanlong
     * @DateTime 2016-07-31T07:38:20+0800
     * @param    array                   $data 输入数据
     * @return   array                   [error_code, error_msg, com_id=> [] ]
     */
    static public function memberDel($id)
    {
        $res = Db::name('users')->where(['uid'=>$id])->update(['status'=>2]);
        $res2=Db::name('users_customers')->where(['uid'=>$id])->update(['is_del'=>1]);
        if ($res && $res2) {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] = $res;;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "查询失败";
        }
        return $result;
    }

     /**
     * memberRelation 会员关系
     * @tanlong
     * @DateTime 2016-07-31T07:38:20+0800
     * @param    array                   $data 输入数据
     * @return   array                   [error_code, error_msg, com_id=> [] ]
     */
    static public function memberRelation($num,$id='')
    {
        if($id!=''){
             $where['a.uid']=$id;
        }
        $where['role']='customer';
        $where['status']=1;
        $res = Db::name('users')
        ->where($where)
        ->alias('a')
        ->join('users_customers b','a.uid=b.uid','left')
        ->join('users_weixin c','b.uid=c.uid','left')
        ->field(['a.create_time','a.uid','b.nickname','b.commission','c.attention'])
        ->paginate($num); 
        if($res){
            $counts=array();
            foreach ($res as $k => $v) {
                 $count=Db::name('users_parent')
                 ->where(array('parent'=>$v['uid']))
                 ->count();
                 $counts[]=$count;
            }
        }
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] = $res;
            $result['counts'] = $counts;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "查询失败";
        }
        return $result;
    }

    /**
     * memberRelation 会员关系
     * @tanlong
     * @DateTime 2016-07-31T07:38:20+0800
     * @param    array                   $data 输入数据
     * @return   array                   [error_code, error_msg, com_id=> [] ]
     */
    static public function numAjax($uid)
    {
        $res = Db::name('users_parent')
        ->where(['parent'=>$uid])
        ->select();
        $res2=[];
        foreach ($res as $k => $v) {
            $res2[] = Db::name('users')
            ->where(['role'=>'customer','status'=>1,'a.uid'=>$v['uid']])
            ->alias('a')
            ->join('users_customers b','a.uid=b.uid','left')
            ->join('users_weixin c','b.uid=c.uid','left')
            ->field(['a.create_time','a.uid','b.nickname','b.commission','c.attention'])
            ->find(); 
        }
        $res2=array_filter($res2); //去除数组中的null元素
        if($res2){
            $counts=array();
            foreach ($res2 as $k => $v) {
                 $count=Db::name('users_parent')
                 ->where(array('parent'=>$v['uid']))
                 ->count();
                 $counts[]=$count;
            }
        }
        if ($res && $res2) {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] = $res2;
            $result['counts'] = $counts;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "查询失败";
        }
        return $result;
    }

    /**
     * withdrawalRequest 提款申请列表
     * @tanlong
     * @DateTime 2016-07-31T07:38:20+0800
     * @param    array                   $data 输入数据
     * @return   array                   [error_code, error_msg, com_id=> [] ]
     */
    static public function withdrawalRequest($num,$where,$url)
    {
        $res = Db::name('withdrawals')
              ->alias('a')
              ->join('users_customers b','a.uid = b.uid','left')
              ->where($where)
              ->order('time desc')
              ->paginate($num,false,array('query'=>$url)); 
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] =$res ;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "查询失败";
        }
        return $result;
    }

    /**
     * withdrawalEdit 提款申请修改
     * @tanlong
     * @DateTime 2016-07-31T07:38:20+0800
     * @param    array                   $data 输入数据
     * @return   array                   [error_code, error_msg, com_id=> [] ]
     */
    static public function withdrawalEdit($input,$money,$uid)
    {
        $res = Db::name('withdrawals')
        ->where(['id'=>$input['id']])
        ->update(['is_on'=>$input['type'],'transit_time'=>time()]);   
        $res2 = Db::name('users_money')
        ->insert(['uid'=>$uid,'des'=>'提现','type'=>'cash','expense'=>$money,'time'=>time()]);   
        $res3= Db::name('users_customers')
        ->field('balance')
        ->find($uid);
        $balance=$res3['balance']-$money;
        $res4 = Db::name('users_customers')
        ->where(['uid'=>$uid])
        ->update(['balance'=>$balance]);   
        if ($res && $res2 && $res4) {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] =$res ;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "查询失败";
        }
        return $result;
    }

    /**
     * withdrawalEditOne 提款申请少数
     * @tanlong
     * @DateTime 2016-07-31T07:38:20+0800
     * @param    array                   $data 输入数据
     * @return   array                   [error_code, error_msg, com_id=> [] ]
     */
    static public function withdrawalEditOne($input)
    {
        $res = Db::name('withdrawals')
        ->where(['id'=>$input['id']])
        ->update(['is_on'=>$input['type'],'transit_time'=>time()]);   
        if ($res!==false) {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] =$res ;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "查询失败";
        }
        return $result;
    }

    /**
     * withdrawalCheck 提款安全验证
     * @tanlong
     * @DateTime 2016-07-31T07:38:20+0800
     * @param    array                   $data 输入数据
     * @return   array                   [error_code, error_msg, com_id=> [] ]
     */
    static public function withdrawallCheck($id)
    {
        $res = Db::name('withdrawals')->find($id);  
        $res2=Db::name('users_customers')->find($res['uid']);  
        if ($res &&  $res2 && $res2['balance']>$res['money']) {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] =$res ;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "查询失败";
        }
        return $result;
    }

    /**
     * findMoney 找提现的钱的相关信息
     * @tanlong
     * @DateTime 2016-07-31T07:38:20+0800
     * @param    array                   $data 输入数据
     * @return   array                   [error_code, error_msg, com_id=> [] ]
     */
    static public function findMoney($uid)
    {
        $open_id = Db::name('users_weixin')
        ->field(['open_id'])
        ->find($uid);  
        $apikey = Db::name('coms_info')
        ->field(['value'])
        ->where(['name'=>'apikey'])
        ->find();
        $appid = Db::name('coms_info')
        ->field(['value'])
        ->where(['name'=>'appid'])
        ->find();
        $mchid = Db::name('coms_info')
        ->field(['value'])
        ->where(['name'=>'mchid'])
        ->find();
        if ($open_id && $apikey) {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['open_id'] =$open_id ;
            $result['apikey'] =$apikey ;
            $result['appid'] =$appid ;
            $result['mchid'] =$mchid ;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "查询失败";
        }
        return $result;
    }

    /**
     * excel 提款申请列表
     * @tanlong
     * @DateTime 2016-07-31T07:38:20+0800
     * @param    array                   $data 输入数据
     * @return   array                   [error_code, error_msg, com_id=> [] ]
     */
    static public function excel($data)//,$pagenum
    {
        $count=Db::name('withdrawals')->count();
        if($data['first']>$count/$data['pagenum']){
             $result['error_code'] = 1;
        }
        $one=($data['first']-1)*$data['pagenum'];
        $two=$data['end']*$data['pagenum'];
        $res = Db::name('withdrawals')
              ->alias('a')
              ->join('users_customers b','a.uid = b.uid','left')
              ->field('b.nickname,a.money,a.time,a.transit_time,is_on')
              ->order('time desc')
              ->limit($one,$two)
              ->select();
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] =$res ;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "查询失败";
        }
        return $result;
    }



        /**
     * ranking 用户排行
     * @xiao
     * @param    array     $data
     * @return   array     [error_code, error_msg, id]
     * @DateTime 2016-11-22T20:46:59+0800
     */
    static public function rankingList($num = 20)
    {   
        $where = '';
        $res = Db::name('users_customers')->where($where)->order("points desc")->paginate($num);
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] = $res;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "查询失败";
        }
        return $result;
    }

     /* integralList用户积分
     * @xiao
     * @param    array     $data
     * @return   array     [error_code, error_msg, id]
     * @DateTime 2016-11-22T20:46:59+0800
     */
    static public function integralList($uid)
    {   
        $where['uid'] = $uid;
        $res = Db::name('users_points')->where($where)->select();
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] = $res;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "查询失败";
        }
        return $result;
    }

     /* maxWeixinCode
     * @xiao    
     * @param    array     $data
     * @return   array     [error_code, error_msg, id]
     * @DateTime 2016-11-22T20:46:59+0800
     */
    static public function maxWeixinCode($parent)
    {   //微信 关联 勿动
        $res = Db::name('users_parent')->distinct(true)->field('parent')->select();
        if ($res) {
            if (Db::name('users_parent')->field('parent')-where(['parent'=>$parent])->find() ) {
                $result['count'] = count($res);
            }else{
                $result['count'] = count($res)+1;
            }
        }else{
           $result['count'] = 0;
        }
        return $result;
    }

    /* addparent
     * @xiao  
     * @param    array     $data
     * @return   array     [error_code, error_msg, id]
     * @DateTime 2016-11-22T20:46:59+0800
     */
    static public function addParent($data)
    {   //微信 关联 勿动
       return  Db::name('users_parent')->insert($data);
    }

    /* upParent
     * @xiao  
     * @param    array     $data
     * @return   array     [error_code, error_msg, id]
     * @DateTime 2016-11-22T20:46:59+0800
     */
    static public function upParent($uid,$data)
    {   //微信 关联 勿动
       return  Db::name('users_parent')->where(['uid'=>$uid])->update($data);
    }

    /**
     * shareCode 我的分享码
     * @xiao
     * @param    array     $uid
     * @return   array     [error_code, error_msg, id]
     * @DateTime 2016-11-22T20:46:59+0800
     */
    static public function shareCode($uid)
    {  
        $where['u.uid'] = $uid;
        $res = Db::name('users_customers')
              ->alias('u')
              ->join('users_parent p','u.uid = p.uid','left')
              ->where($where)
              ->find();
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] = $res;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "查询失败";
        }
        return $result;
    }

    /**
     * memberSum vip开通人数
     * @xiao
     * @param    array     $uid
     * @return   array     [error_code, error_msg, id]
     * @DateTime 2016-11-22T20:46:59+0800
     */
    static public function memberSum($vip,$sum)
    {  
        $res = Db::name('users_customers')->field("uid")->where(['member_type'=>$vip])->limit($sum)->select();
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['count'] = count($res);
        }else{
            $result['error_code'] = 1;
            $result['count'] = 0;
            $result['error_msg'] = "查询失败";
        }
        return $result;
    }
        

    /**
     * shareCode 我的分享码
     * @xiao
     * @param    array     $uid
     * @return   array     [error_code, error_msg, id]
     * @DateTime 2016-11-22T20:46:59+0800
     */
    static public function addInc($uid)
    {  
        $res = Db::name('users_customers')->where(['uid'=>$uid])->setInc('all_num');
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "添加失败";
        }
        return $result;
    }

    /**
     * shareCode 我的分享列表
     * @xiao
     * @param    array     $uid
     * @return   array     [error_code, error_msg, id]
     * @DateTime 2016-11-22T20:46:59+0800
     */
    static public function shareList($ids)
    { 
        $where['uid'] = ['in',$ids];
        $res = Db::name('users_customers')
        ->where($where)
        ->field('uid,nickname,commission,face')
        ->select();
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] = $res;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "查询失败";
        }
        return $result;
    }
	
	
	/**
     * shareCode 我的返佣列表
     * @xiao
     * @param    array     $uid
     * @return   array     [error_code, error_msg, id]
     * @DateTime 2016-11-22T20:46:59+0800
     */
    static public function myrebateList($uid)
    { 
        $where = array('uid' => $uid);
        $res = Db::name('users_customers')
        ->where($where)
        ->field('uid,nickname,commission,face')
        ->select();
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] = $res;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "查询失败";
        }
        return $result;
    }
    

    /**
     * money 用户钱包
     * @xiao
     * @param    array     $data
     * @return   array     [error_code, error_msg, id]
     * @DateTime 2016-11-22T20:46:59+0800
     */
    static public function moneyList($uid,$where)
    {   
        $where['uid'] = $uid;
        $res = Db::name('users_money')->where($where)->select();
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] = $res;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "查询失败";
        }
        return $result;
    }

    /**
     * addAdmin 添加管理员
     * @xiao
     * @DateTime 2016-08-02T06:12:40+0800
     * @param    array                   $data 添加数组
     * @param    int                     $role 角色id
     * @return   array                   [error_code, error_msg, id] 
     */
    static public function addAdmin($data,$role)
    {
        $result = ['error_code' => 0, 'error_msg' => ""];
        $username = $data['username'];
        if (self::get(['username' => $username])) {
            $result['error_code'] = 1;
            $result['error_msg'] = '用户名已存在';
        }elseif ($id = Db::name('users')->insertGetId($data)) {
            $list['uid'] = $id;
            $list['group_id'] = $role;
            if (Db::name('auth_groups_access')->insert($list)) {
                $result['error_code'] = 0;
                $result['error_msg'] = '';
                $result['id'] = $id;
            }else{
                $result['error_code'] = 3;
                $result['error_msg'] = '添加角色接口失败';
            }
        }else{
            $result['error_code'] = 2;
            $result['error_msg'] = '添加失败';
        }
        return $result;
    }

    /**
    * Jscheck js检查账号否存在
    * @xiao
    * @DateTime 2016-10-12T14:26:59+0800
    * @param     array             $data  账号
    * @return    array             [error_code, error_msg]
    */
    static public function jsCheck($data)
    {   
        $where['username'] = $data['name'];
        $res = Db::name('users')->where($where)->find();
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = "账号已存在";
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "";
        }
        return $result;
    }

    /**
    * Function:      getOne
    * Description:   获取一条用户信息
    * @param   array    $where                         条件
    * @return  array  [error_code, error_msg, data]  用户信息   
    */
    static public function getOne($where)
    {
        if ($data = Db::name('users')->where($where)->find()) {
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['data'] = $data;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = '获取失败';
        }
        return $result;
    }

    /**
     * updata 更新 管理员 数据
     * @xiaoyajun
     * @DateTime 2016-08-01T18:16:20+0800
     * @param    array                   $data  更新数据数组
     * @param    array                     $uid        uid
     * @param    array                     $role      角色id
     * @return   array                [error_code,error_msg] 
     */
    static public function updates($uid,$data,$role)
    {   
        $info = Db::name('users')->where($uid)->update($data);
        $list = Db::name('auth_groups_access')->where($uid)->update($role);
        if ($info||$list) {
            $result['error_code'] = 0;
            $result['error_msg'] = '更新成功';
        }
        else{
            $result['error_code'] = 1;
            $result['error_msg'] = '更新失败';
        }
        return $result;
    }

    /**
     * updata 更新数据
     * @xiao
     * @DateTime 2016-08-01T18:16:20+0800
     * @param    array                   $data  更新数据数组
     * @return   int                     更新的数据，没有更新显示0
     */
    static public function userUpdates($uid,$data)
    {
        $info =  Db::name('users')->where(['uid'=>$uid])->update($data);
         if ($info) {
            $result['error_code'] = 0;
            $result['error_msg'] = '更新成功';
        }
        else{
            $result['error_code'] = 1; 
            $result['error_msg'] = '更新失败';
        }
        return $result;
    }

    /**
     * userDel  管理用户删除
     * @xiao
     * @DateTime 2016-08-07T19:18:33+0800
     * @return   boolean                   返回数据
     */
    static public function userDel($uid)
    {   
        $data['status'] = 2;
        $row = Db::table('ys_users')->where(['uid'=>$uid])->update($data);

        $res = Db::table('ys_auth_groups_access')->where(['uid'=>$uid])->delete();
        //  print_r($res);die;
       // $res = Db::name('auth_groups_access')->where($uid)->update($data); 
        if($row && $res){
            $result['error_code'] = 0;
            $result['error_msg'] = '';
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = '删除失败';
        }
        return $result;
    }
}

?>