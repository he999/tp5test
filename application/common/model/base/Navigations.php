<?php
namespace app\common\model\base;

use think\Model;
use think\Db;

/*************************************************  
*ClassName:     Navigations
*Description:   导航栏模型
*Others:        
*************************************************/
class Navigations extends Model
{
    /**
     * getByParentId 根据父ID求子分类ID
     * @karl
     * @DateTime 2016-08-09T15:55:26+0800
     * @param    int                   $cateid 分类id
     * @return   array                         子分类数组
     */
    static public function getByParentId($nav_id)
    {
        $data = DB::name("navigations")->where(['parentid' => $nav_id])->select();
        if ($data) 
        {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] = $data;
        }
        else
        {
            $result['error_code'] = 1;
            $result['error_msg'] = "没有得到分类详情";
        }
        return $result;
    }

    /**
     * getById 得到分类数组
     * @karl
     * @DateTime 2016-08-09T21:27:39+0800
     * @param    int                   $nav_id 分类id
     * @return   array                           分类数组
     */
    static public function getById($nav_id)
    {
        return self::get(['nav_id' => $nav_id]);
    }

    /**
     * getAll 得到所有分类
     * @karl
     * @DateTime 2016-08-09T15:59:08+0800
     * @param    int                   $com_id 公司id
     * @return   array                   分类数据
     */
    static public function getAll($com_id = 1)
    {
        $contents = [];
        $data = DB::table("navigations")->order('sort ASC, cateid ASC')->where(['com_id' => $com_id])->select();
        foreach($data as $row)
        {
            $contents[$row['nav_id']] = $row;
        }
        return $contents;
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
    static public function buildTree($navigations, $nav_id = 0, $li = 0, $additional = '')
    {
        $icons = array('│', '├', '└', '&nbsp;&nbsp;'); //'　'
        $return_navigations = array();
        $sub_navigations = array();

        foreach($navigations as $nav)
        {
            if($nav['parentid'] == $cateid)
            {
                    $sub_navigations[] = $nav;
            }
        }
        /* 在子分类中查找子分类 */
        $length = count($sub_navigations);
        for($i = 0; $i < $length; $i++){
            $nav = $sub_navigations[$i];
            $nav['dis'] = $i + 1;
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
            $sub_cate = self::buildTree($navigations, $nav['cateid'], $li+1, $additional_new); 
            if (count($sub_cate)) $category['sub'] = 1;else $category['sub'] = 0;
            $return_categories[$category['cateid']] = $category;
            $return_categories += self::buildTree($categories, $category['cateid'], $li+1, $additional_new);
        }
        return $return_categories;
    }

    /**
     * getChildrenCateids 获某分类ID下的所有子分类的cateid
     * @karl
     * @DateTime 2016-08-09T16:40:20+0800
     * @param    array                 $navigations 导航数组
     * @param    integer               $nav_id     导航ID
     * @return   array                           
     */
    static public function getChildrenNavids($navigations, $nav_id = 0)
    {
        $navigations = self::getChildren($navigations, $nav_id, $com_id);
        return array_keys($navigations);
    }

    /**
     * getChildren 获某分类ID下的所有子分类
     * @karl
     * @DateTime 2016-08-10T06:10:22+0800
     * @param    array                    $navigations 分类数组
     * @param    integer                  $nav_id     分类id
     * @param    integer                  $li         当前第几级(递归用)
     * @return   array                               
     */
    static public function getChildren($navigations, $nav_id = 0, $li = 0)
    {
        $return_categories = [];
        foreach($navigations as $nav)
        {
            if($nav['parentid'] == $nav_id)
            {
                    $nav['li'] = $li;
                    $return_navigations[$nav['cateid']] = $nav;
                    $return_navigations += self::getChildren($navigations, $nav['nav_id'], $li+1);
            }
        }
        return $return_navigations;
    }

    /**
     * getChildren 获某分类ID下的所有“父级”分类
     * @karl
     * @DateTime 2016-08-09T16:46:36+0800
     * @param    array                    $navigations 分类数组
     * @param    int                      $nav_id     分类id
     * @param    integer                  $stopid     即到哪个父级ID停止，默认是0
     * @return   array                              
     */
    static public function getParents($navigations, $nav_id = 0, $stopid = 0)
    {
        $return_navigations = [];
        $parentid = isset($categories[$nav_id]) ? $categories[$nav_id]['parentid'] : 0;
        if($parentid == $stopid)
        {
            return $return_navigations;
        }
        foreach($navigations as $nav)
        {
            if($nav['nav_id'] == $parentid)
            {
                    $return_navigations += self::getParents($navigations, $nav['cateid'], $stopid);
                    $return_navigations[$nav['cateid']] = $nav;
            }
        }
        return $return_navigations;
    }

    /**
     * getPeers 获某分类ID下的所有“同级”子分类[即同一个父亲id]
     * @karl
     * @DateTime 2016-08-09T16:49:15+0800
     * @param    array                    $categories 分类数组
     * @param    integer                  $cateid     分类id
     * @return   array                               
     */
    static public function getPeers($navigations, $nav_id = 0)
    {
        $return_navigations = array();
        if($nav_id != 0 && !isset($navigations[$nav_id]))
        {
            return $return_navigations;
        }
        $parentid = $nav_id == 0 ? 0 : $navigations[$nav_id]['parentid'];

        foreach($navigations as $nav)
        {
            if($nav['parentid'] == $parentid)
            {
                    $return_navigations[$nav['cateid']] = $nav;
            }
        }
        return $return_navigations;
    }

    /**
     * getSubs 所有“下一级”子分类
     * @karl
     * @DateTime 2016-08-09T16:51:43+0800
     * @param    array                    $categories 分类数组
     * @param    integer                  $cateid     
     * @return   array                               
     */
    static public function getSubs($categories, $cateid = 0)
    {
        $return_categories = array();
        foreach($categories as $category)
        {
            if($category['parentid'] == $cateid)
            {
                $return_categories[$category['cateid']] = $category;
            }
        }
        return $return_categories;
    }

    /**
     * getlink 得到外链
     * @karl
     * @DateTime 2016-08-09T16:56:09+0800
     * @param    [type]                   $cateid [description]
     * @return   [type]                           [description]
     */
    public function getlink($cateid)
    {
        $data = DB::table("categories")->where(['cateid' => $cateid])->find();
        if ($data) 
        {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] = $data;
        }
        else
        {
            $result['error_code'] = 1;
            $result['error_msg'] = "没有得到外链";
        }
        return $result;
    }

    /**
     * add 添加分类
     * @karl
     * @DateTime 2016-08-09T17:08:44+0800
     * @param    array                   $data 分类数组
     * @return   mix                     成功用户cateid                     
     */
    static public function add($data)
    {
        $cateid = Db::table('categories')->insertGetId($data);
        if ($cateid) 
        {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['cateid'] = $cateid;
        }
        else
        {
            $result['error_code'] = 1;
            $result['error_msg'] = "添加失败";
        }
        return $result;
    }

     /**
     * delete 删除
     * @karl
     * @DateTime 2016-08-09T19:35:59+0800
     * @param    int                   $cateid 栏目参数
     * @return   boolean                          
     */
    static public function del($cateid)
    {
        $row = DB::table("categories")->delete($cateid);
        if ($row) 
        {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
        }
        else
        {
            $result['error_code'] = 1;
            $result['error_msg'] = "删除失败";
        }
        return $result;
        
    }

    /**
     * menu 得到菜单数组支持两级菜单
     * @karl
     * @DateTime 2016-08-10T15:14:16+0800
     * @param    array                   $categories 分类数组
     * @return   array                               菜单数组
     */
    static public function menu($categories)
    {
        $menu = [];
        $top_cates = self::getPeers($categories);
        foreach($top_cates as $key => $row)
        {
            $menu[$key] = $row;
            if ($down_cates = self::getChildren($categories, $row['cateid']))
            {
                $menu[$key]['down']  = $down_cates;
            }
        }
        return $menu;
    }
 
}



?>