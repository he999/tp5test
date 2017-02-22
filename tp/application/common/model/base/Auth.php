<?php
namespace app\common\model\base;

use think\Model;
use think\Db;

/*************************************************  
*ClassName:     Auth
*Description:   权限模型
*************************************************/

class Auth extends Model
{
    /**
     * getgroupsList 获取角色列表
     * @xiao
     * @DateTime 2016-08-14T15:44:35+0800
     * @param    void
     * @return   array  [error_code, error_msg, data]             
     */
    static public function getgroupsList()
    {
        $res = Db::name('auth_groups')->select();
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] = $res;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "未找到角色";
        }
        return $result;
    }

    /**
     * getgroupsList 获取管理详情
     * @xiao
     * @DateTime 2016-08-14T15:44:35+0800
     * @param    void
     * @return   array  [error_code, error_msg, data]             
     */
    static public function getGroupInfo($uid)
    {
        $res = Db::name('auth_groups_access')
            ->alias('a')
            ->join('auth_groups g','a.group_id = g.id','left')
            ->where(['uid' => $uid])
            ->find();
        if ($res)
        {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] = $res;
        }
        else
        {
            $result['error_code'] = 1;
            $result['error_msg'] = "没有得到详情";
        }
        return $result;
    }

    /**
     * getAuthList 获取权限列表
     * @xiao
     * @DateTime 2016-08-14T15:44:35+0800
     * @param    void
     * @return   array  [error_code, error_msg, data]             
     */
    static public function getAuthList($num = 10)
    {
        $list = Db::name('auth_groups_access')
            ->alias('a')
            ->join('users u','a.uid = u.uid','left')->where(['u.status'=>1]) 
            ->paginate($num);
        if ($list) 
        {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] = $list;
        }
        else
        {
            $result['error_code'] = 1;
            $result['error_msg'] = "没有得到详情";
        }
        return $result;
    }

    /**
     * getUserRole 得到一条用户信息
     * @xiaoyajun
     * @DateTime 2016-08-15T14:12:45+0800
     * @param    int                   $uid 用户id
     * @return   array                 [error_code,error_msg]
     */
    static public function getUserRoles($uid)
    {
        $list = Db::name('auth_groups_access')->where("uid", $uid)->find();
        if ($list){
            $result['error_code'] = 0;
            $result['group_id'] = $list['group_id'];
        }
        else {
            $result['error_code'] = 1;
        }
        return $result;
    }

    /**
     * getRule 获取一个角色权限详情
     * @肖亚军
     * @DateTime 2016-08-14T15:41:46+0800
     * @param    int         $uid     角色id
     * @return   array       [error_code,error_msg,id,nameid]
     */
    static public function getRoles($uid)
    {   
        $res = self::getUserRoles($uid);    
        if($res['error_code'] == 0){
            $list = Db::name('auth_groups')->where("id",$res['group_id'])->find();
            $result['error_code'] = 0;
            $result['id'] = $list['rules'];
            $result['nameid'] = $res['group_id'];
        }else{
            $result['error_code'] = 1;
        }
        return $result;
    }

    /**
     * getRule 获取一个角色权限路径
     * @肖亚军
     * @DateTime 2016-08-14T15:41:46+0800
     * @param    int         $uid     角色id
     * @return   array       [error_code,error_msg,data,nameid]
     */
    static public function getAuthRole($uid)
    {   
        $res = self::getRoles($uid);    
        if($res['error_code'] == 0){
            $list = Db::name('auth_rules')->where("id",'in',$res['id'])->select();
            $result['error_code'] = 0;
            $result['data'] = $list;
            $result['nameid'] = $res['nameid'];
        }else{
            $result['error_code'] = 1;
        }
        return $result;
    }
    /**
     * check 检查用户是否有权限
     * @彭凯
     * @DateTime 2016-08-14T16:36:03+0800
     * @param    int                   $uid 用户id
     * @param    char                  $con 控制器名称
     * @return   boolean               
     */
    static public function check($uid,$con)
    {
        //$where['uid'] = $uid;
        //获取用户权限
        $info =  Db::table('auth_group_access')->alias('a') ->join('auth_group g','a.group_id = g.id') ->field("g.rules")->where("uid", $uid)->find();
        $infos = explode(",",$info['rules']);
        if ($info) {
            //判断权限   
            $urls = strtolower($con);
            $where['title'] = array("like",$urls);
            $row = Db::table('auth_rules')->where($where)->find();
            if ($row){
                foreach ($infos as $key => $value) {
                    if ($row['id'] == $value) { 
                        $arr['result'] = "ok";
                        break;
                    }else{
                        $arr['result'] = "no";
                    }
                }
                if ($arr['result'] == "ok") {
                    $arr['msg'] = "auth is exsit ";//存在权限
                }
                else{
                    $arr['msg'] = "auth is not exsit ";//不存在权限
                }
            }
            else{
                $arr['msg'] = "Illegal operation";//非法操作
            }
        }
        else{
            $arr['result'] = "no";
            $arr['msg'] = "auth is not exsit ";//不存在权限
        }
        return  $arr;
    }
//+++++++++++++++++++++++++++++++++++角色权限操作+++++++++++++++++++++++++
    /**
     * addRole 添加权限分组
     * 操作auth_group表
     * @彭凯
     * @DateTime 2016-08-14T13:38:31+0800
     * @param    array                   $data 添加数组
     * @return   array  [error_code, error_msg, id]
     */
    static public function addRole($data)
    {
        $row = Db::name('auth_group')->insertGetId($data);
        if ($row) 
        {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['id'] = $row;
        }
        else
        {
            $result['error_code'] = 1;
            $result['error_msg'] = "没有得到详情";
        }
        return $result;
    }

     /**
     * changeGroup 改变权限组  新增/更新
     * @彭凯
     * @DateTime 2016-08-14T16:09:35+0800
     * @param    int                   $id      分组id
     * @param    string                $title 	分组名字
     * @param    array                 $rules   被选中的id以‘,’连起来
     * @return   array  [error_code, error_msg]
     */
    static public function changeRole($id, $title,$status,$rules)
    {
        $list = Db::table('auth_group')->where("id", $id)->find();
        $arr['title'] = $title;
        $arr['status'] =1;
        $arr['rules'] = $rules;
        if ($list) {
            //更改数据
            $info = Db::table('auth_group')->where("id", $id)->update($arr);
            $rule['error_msg'] = "update";
        }
        else{
            //新增数据
            $arr['id'] = $id;
            $info = Db::table('auth_group')->insert($arr);
            $rule['error_msg'] = "add";
        }
        //操作判断
        if ($info == 1) {
            $rule['error_code'] = 0;
        }
        else{
            $rule['error_code'] = 1;
        }

        return $rule;
    }
    
    /**
     * getRule 获取角色权限详情
     * @彭凯
     * @DateTime 2016-08-14T15:41:46+0800
     * @param    int                     $uid     角色id
     * @return   boolean
     */
    static public function getRole($id="")
    {
        if($id){
			return  Db::table('auth_group')->where("id",$id)->select();
        }
        else
        {
			return  Db::query("select DISTINCT id,title from auth_group ");
        }
    }

    
   /**
     * deleteRule 删除角色权限
     * @彭凯
     * @DateTime 2016-08-14T16:01:46+0800
     * @param    int             $id     分组id
     * @return   array  [error_code, error_msg]
     */
    static public function deleteRole($id)
    {
        $list = Db::table('auth_group')->where("id",$id)->delete();
        //数据库操作后判断
        if ($list) 
        {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
        }
        else
        {
            $result['error_code'] = 1;
            $result['error_msg'] = "失败";
        }
        return $result;
    }

    /**
     * delUserRole 删除用户角色权限
     * @彭凯
     * @DateTime 2016-08-14T16:01:46+0800
     * @param    int     $uid     userid
     * @return   array  [error_code, error_msg]
     */
    static public function delUserRole($uid){
        $list = Db::table('auth_group_access')->where("uid",$uid)->delete();
        //数据库操作后判断
        if ($list) 
        {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
        }
        else
        {
            $result['error_code'] = 1;
            $result['error_msg'] = "失败";
        }
        return $result;
    }

//+++++++++++++++++++++++++++++++++++用户角色权限操作+++++++++++++++++++++++++

    /**
     * changeGroup 改变用户角色 新增/更新
     * @彭凯
     * @DateTime 2016-08-14T16:09:35+0800
     * @param    int                   $uid      用户id
     * @param    int                   $group_id 分组id
     * @param    varchar               $area     地区
     * @return   array  [error_code, error_msg]                     
     */
    static public function changeGroup($uid, $group_id)
    {
        $list = Db::table('auth_group_access')->where("uid", $uid)->find();
        $arr['group_id'] = $group_id;
//         $arr['area'] = $area;
//         $arr['group_name'] = $group_name;
        if ($list) {
            //更改数据
            $info = Db::table('auth_group_access')->where("uid", $uid)->update($arr);
            $rule['error_msg'] = "update";
        }
        else{
            //新增数据
            $arr['uid'] = $uid;
            $info = Db::table('auth_group_access')->insert($arr);
            $rule['error_msg'] = "add";
        }
        //操作判断
        if ($info == 1) {
            $rule['error_code'] = 1;
        }
        else{
            $rule['error_code'] = 0;
        }

        return $rule;
    }

    /**
     * userRoleList 获取user角色列表
     * @karl
     * @DateTime 2016-08-07T19:18:33+0800
     * @return   array                   返回数据
     */
    static public function userRoleList($page_num)
    {
       return $user_role= Db::table('auth_group_access')->alias('a')->join('user u','a.uid = u.uid','left')->join('auth_group ag','ag.id = a.group_id')->field('u.uid,u.username,a.group_id,ag.title ')->order("u.uid asc")->paginate($page_num,false);
    }

     /**
     * getUserRole 得到一条用户信息
     * @pengkai
     * @DateTime 2016-08-15T14:12:45+0800
     * @param    int                   $uid 用户id
     * @return   array    [error_code, error_msg, data]
     */
    static public function getUserRole($uid)
    {
        $list = Db::table('auth_group_access')->alias('a')->join('auth_group ag','a.group_id = ag.id','left')->join('user u','a.uid = u.uid')->field('u.username,u.password,ag.title')->where("a.uid", $uid)->find();
        if ($list){
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['data'] = $list;
        }
        else {
            $result['error_code'] = 1;
            $result['error_msg'] = '查询出错';
        }
        return $result;
    }
    
    /**
     * userRoleList 获取角色权限列表
     * @karl
     * @DateTime 2016-08-07T19:18:33+0800
     * @return   array                   返回数据
     */
    
    //这是嘛？好像没有用，暂时没有动
    
    
    static public function getRoleList()
    {
       $user_role= Db::table('auth_group')->select();
       $auth = Db::table('auth_rules')->field('rules,title,id')->select();
       for ($i=0; $i <count($user_role); $i++) { 
       $role_info=explode(",",$user_role[$i]['rules']); 
       $user_role[$i]['rules']="";
             for ($j=0; $j <count($role_info) ; $j++) { 
                   for ($k=0; $k <count($auth) ; $k++) { 
                       if($role_info[$j]==$auth[$k]['id']){
                        $user_role[$i]['rules'].=$auth[$k]['title']." ";
                       }
                   }
             }
       }
       return $user_role;
    }
}



?>