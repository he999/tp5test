<?php
namespace app\common\model\weixin;

use app\common\model\weixin\WeixinAuth;
use app\wap\controller\WeixinBase;
use app\common\model\base\Coms;
use think\Model;
use think\Db;

/**
 * 微信自定义菜单
 */
class DiyMenu extends Model
{
    /**
     * 自定义菜单列表，可用于后台显示
     * @颜林梧
     * @DateTime 2016-09-06T06:11:42+0800
     * @param    integer                  $com_id 公司id，默认为1
     * @return   array                    返回数组
     */
    static public function menulist($where=null,$com_id = 1)
    {
        $result = Db::table('ys_weixin_diymenu')->where($where)->order('sort desc')->select();
        return $result;
    }

    /**
     * add 添加菜单项
     * @颜林梧
     * @DateTime 2016-09-06T06:19:16+0800
     * @param    array                   $row 添加数组
     * @return   int                     $result['code'] 1、一级菜单不能超过3个  2、二级菜单不能超过6个
     * @return   string                  $result['error'] no 添加失败 yes 添加成功
     * @return   string                  $result['info'] 成功返回id值 失败返回错误信息
     * 一个公司一级菜单不能超过3个，二级菜单不能超过6个?
     */
    static public function add($data)
    {
        $weixinauth = new DiyMenu();
        $maxcolum_1 = Db::table('ys_weixin_diymenu')->where(array('parent_id' => '0'))->count();
        $maxcolum_2 = Db::table('ys_weixin_diymenu')->where('parent_id',$data['parent_id'])->count();
        if ($data['parent_id'] == 0){
            if ($maxcolum_1 >= 3)
            {
                $result['error'] = 'no';
                $result['code'] = '1';
                $result['info'] = '一级菜单不能超过3个';
            }else{
                $id = DB::table('ys_weixin_diymenu')->insertGetId($data);
            }
        }
        if ($data['parent_id'] != 0){
            if ($maxcolum_2 >= 6){
                $result['error'] = 'no';
                $result['code'] = '2';
                $result['info'] = '二级菜单不能超过6个';
            }else{
                $id = DB::table('ys_weixin_diymenu')->insertGetId($data);
            }
        }
        if (isset($id)){
            if ($id)
            {
                $result['error'] = 'yes';
                $result['info'] = $id;
            }else {
                $result['error'] = 'no';
                $result['info'] = '添加数据失败';
            }
        }
        return $result;

    }

    /**
     * edit 添加菜单项
     * @颜林梧
     * @DateTime 2016-09-06T06:19:16+0800
     * @param    array                   $row 添加数组
     * @return   int                     $result['info'] 不为0时更新成功，为0更新失败
     * 一个公司一级菜单不能超过3个，二级菜单不能超过6个?
     */
    static public function edit($id,$data)
    {
        $where = array('id' => $id);
        $maxcolum_1 = Db::table('ys_weixin_diymenu')->where(array('parent_id' => '0'))->count();
        $maxcolum_2 = Db::table('ys_weixin_diymenu')->where('parent_id',$data['parent_id'])->count();
        $parent_id = Db::table('ys_weixin_diymenu')->where($where)->field('parent_id')->find();
        if ($data['parent_id'] == 0){
            if ($parent_id['parent_id'] == $data['parent_id'])
            {
                if ($maxcolum_1 > 4)
                {
                    $result['error'] = 'no';
                    $result['code'] = '1';
                    $result['info'] = '一级菜单不能超过3个';
                }else{
                    $code = Db::table('ys_weixin_diymenu')->where($where)->update($data);
                    
                }
            }
            if ($parent_id['parent_id'] != intval($data['parent_id']) )
            {
                if ($maxcolum_1 >= 3)
                {
                    $result['error'] = 'no';
                    $result['code'] = '1';
                    $result['info'] = '一级菜单不能超过3个';
                }else{
                    $code = Db::table('ys_weixin_diymenu')->where($where)->update($data);
                    
                }
            }
        }
        if ($data['parent_id'] != 0){
            if ($parent_id['parent_id'] == $data['parent_id'])
            {
                if ($maxcolum_2 > 6){
                    $result['error'] = 'no';
                    $result['code'] = '2';
                    $result['info'] = '二级菜单不能超过6个';
                }else{
                    $code = Db::table('ys_weixin_diymenu')->where($where)->update($data);
                }
            }
            if ($parent_id['parent_id'] != $data['parent_id'])
            {
                if ($maxcolum_2 >= 6){
                    $result['error'] = 'no';
                    $result['code'] = '2';
                    $result['info'] = '二级菜单不能超过6个';
                }else{
                    $code = Db::table('ys_weixin_diymenu')->where($where)->update($data);
                }
            }
        }
        if (isset($code)){
            if ($code)
            {
                $result['error'] = 'yes';
                $result['info'] = $code;
                unset($result['code']);
            }else {
                $result['error'] = 'no';
                $result['info'] = '修改数据失败';
            }
        }
        return $result;
    }

    /**
     * del 删除菜单项
     * @颜林梧
     * @DateTime 2016-09-06T06:53:19+0800
     * @param    int                   $id 删除id
     * @return   boolean                
     */
    static public function del($id)
    {
        $where = array('id' => $id);
        $result = Db::table('ys_weixin_diymenu')->where($where)->delete();
        return $result;
    }

    /*************************************************
     * Function:      getsWeiwinInfo
     * @颜林梧
     * Description:   获取微信用户的信息
     * Return:        array  $access_token
     *************************************************/
    static public function getsAccessToken($appid, $appsecret){
        define("APPID", "$appid");
        define("APPSECRET", "$appsecret");
        $token_access_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".APPID."&secret=".APPSECRET;
        $res = file_get_contents($token_access_url); //获取文件内容或获取网络请求的内容
        zlog("url:".$token_access_url);
        //echo $res;
        $result = json_decode($res, true); //接受一个 JSON 格式的字符串并且把它转换为 PHP 变量
        $access_token = $result['access_token'];
        return $access_token;
    }
    
    /**
     * createMeun 调用微信接口，生成自定义菜单
     * @karl
     * @DateTime 2016-09-06T06:28:20+0800
     * @param    integer                  $com_id 公司id
     * @return   array                    ['result'=>'ok/no', 'error_code'=>'', 'error_info'=>'']
     */
    static public function createMeun($com_id = 1)
    {  
        $appid = Coms::getValue('appid')['data'];
        $appsecret = Coms::getValue('appsecret')['data'];
        $accesstoken = self::getsAccessToken($appid,$appsecret);
        zlog($accesstoken);
        $data = '{"button":[';
        $class = Db::table('ys_weixin_diymenu')->where(array('parent_id' => '0', 'is_show' => '1'))->limit(3)->order('sort desc')->select();//dump($class);
        $kcount = Db::table('ys_weixin_diymenu')->where(array('parent_id' => '0', 'is_show' => '1'))->limit(3)->order('sort desc')->count();
        $k = 1;
        foreach ($class as $key => $vo) {
            //主菜单
            $data .= '{"name":"' . $vo['title'] . '",';
            $c = Db::table('ys_weixin_diymenu')->where(array('parent_id' => $vo['id'], 'is_show' => 1))->limit(5)->order('sort desc')->select();
            $count = Db::table('ys_weixin_diymenu')->where(array('parent_id' => $vo['id'], 'is_show' => 1))->limit(5)->order('sort desc')->count();
        
            //子菜单
            $vo['url'] = str_replace(array('&amp;'), array('&'), $vo['url']);
            if ($c != false) {
                $data .= '"sub_button":[';
            } else {
                if (!$vo['url']) {
                    $data .= '"type":"click","key":"' . $vo['keyword'] . '"';
                } else {
                    $data .= '"type":"view","url":"' . $vo['url'] . '"';
                }
            }
        
            $i = 1;
            foreach ($c as $voo) {
                $voo['url'] = str_replace(array('&amp;'), array('&'), $voo['url']);
                if ($i == $count) {
                    if ($voo['url']) {
                        $data .= '{"type":"view","name":"' . $voo['title'] . '","url":"' . $voo['url'] . '"}';
                    } else {
                        $data .= '{"type":"click","name":"' . $voo['title'] . '","key":"' . $voo['keyword'] . '"}';
                    }
                } else {
                    if ($voo['url']) {
                        $data .= '{"type":"view","name":"' . $voo['title'] . '","url":"' . $voo['url'] . '"},';
                    } else {
                        $data .= '{"type":"click","name":"' . $voo['title'] . '","key":"' . $voo['keyword'] . '"},';
                    }
                }
                $i++;

            }
        
            if ($c != false) {
                $data .= ']';
            }
            if ($k == $kcount) {
                $data .= '}';
            } else {
                $data .= '},';
            }
            $k++;
        }
        $data .= ']}';
        file_get_contents('https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=' . $accesstoken);
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=' . $accesstoken;
        zlog($data);
        $rt = wxcurl_post($url, $data);
 
        if ($rt['rt'] == false) {
            $result['error'] = $rt['errorno'];
            return $result;
        } else {
            $result['error'] = 'yes';
            return $result;
        }
        exit;


        }

    /**
     * createQRCode 生成数组
     * @karl
     * @DateTime 2016-08-09T16:26:26+0800
     * @param    array                    $categories 分类数组
     * @param    integer                  $cateid     分类id
     * @param    integer                  $li         第几级
     * @param    string                   $additional 附加内容
     * @return   array                                数组
     */
     static public function createQRCode($uid)
    {  
        $appid = Coms::getValue('appid')['data'];
        $appsecret = Coms::getValue('appsecret')['data'];
        $accesstoken = self::getsAccessToken($appid,$appsecret);
        zlog($accesstoken);
        
        $data ='{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": '.$uid.'}}}';
        $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $accesstoken;
        zlog($data);
        $rt = curl_post($url, $data);
        $ket = json_decode($rt,true);
        $urls = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$ket['ticket'];
        $targetName = './uploads/qrscene/qrscene_'.$uid.'.jpg';
        if (file_put_contents($targetName, file_get_contents($urls)) ) {
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['url'] = '/uploads/qrscene/qrscene_'.$uid.'.jpg';
        }else{
            $result['error_code'] = 0;
            $result['error_msg'] = '';
        }

        return $result;
       // {"ticket":"gQHr8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyX2g1X1FLODQ5UVQxMDAwME0wM2IAAgQOSkpYAwQAAAAA","url":"http:\/\/weixin.qq.com\/q\/02_h5_QK849QT10000M03b"}
       // https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=gQHr8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyX2g1X1FLODQ5UVQxMDAwME0wM2IAAgQOSkpYAwQAAAAA
        //$targetName = './uploads/qrscene/qrscene_888.jpg'; 
       // 
        }
    
        
        
        
    /**
     * buildTree 建立树形结构
     * @karl
     * @DateTime 2016-08-09T16:26:26+0800
     * @param    array                    $categories 分类数组
     * @param    integer                  $cateid     分类id
     * @param    integer                  $li         第几级
     * @param    string                   $additional 附加内容
     * @return   array                                数组
     */
    static public function buildTree($categories, $id = 0, $li = 0, $additional = '')
    {
        $icons = array('│', '├', '└', '&nbsp;&nbsp;'); //'　'
        $return_categories = array();
        $sub_categories = array();

        foreach($categories as $category)
        {
            if($category['parent_id'] == $id)
            {
                    $sub_categories[] = $category;
            }
        }
        /* 在子分类中查找子分类 */
        $length = count($sub_categories);
        for($i = 0; $i < $length; $i++){
            $category = $sub_categories[$i];
            $category['dis'] = $i + 1;
            if($li == 0)
            {
                $category['prefix'] = '';
                $additional_new = $additional . $icons[3];
            }
            else
            {
                if($i + 1 == $length)
                {
                    $category['prefix'] = $additional . $icons[2];
                }
                else
                {
                    $category['prefix'] = $additional . $icons[1];
                }
                $additional_new = $i + 1 == $length ? $additional . $icons[3] : $additional . $icons[0] . $icons[3];
            }
            $sub_cate = self::buildTree($categories, $category['id'], $li+1, $additional_new); 
            if (count($sub_cate)) $category['sub'] = 1;else $category['sub'] = 0;
            $return_categories[$category['id']] = $category;
            $return_categories += self::buildTree($categories, $category['id'], $li+1, $additional_new);
        }
        return $return_categories;
    }

    /**
     * getAntisList 查询一条
     * @张家伟
     * @DateTime 2016-09-27T15:44:35+0800
     * @param    void
     * @return   boolean                         
     */
    static public function getOne($id)
    {   
        return $row = Db::name('weixin_diymenu')->where('id',$id)->find();          
    }
}
?>