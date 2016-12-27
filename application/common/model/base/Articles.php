<?php
namespace app\common\model\base;

use think\Model;
use think\Db;

/*************************************************  
*ClassName:     Articles
*Description:   文章模型
*Others:        
*************************************************/
class Articles extends Model
{

    /**
     * getList 得到文章列表
     * @karl
     * @DateTime 2016-08-09T14:50:36+0800
     * @param    array                    $where         条件数组
     * @param    array                    $cate_where    分类条件数组
     * @param    int                      $page_num      每页条数
     * @param    array                    $url           附加网址
     * @param    int                      $page          页号
     * @return   array                                   文章列表
     */
    static public function getList($where, $page_num, $url=[], $page='')
    {
        $where_cates = "";
        if (isset($where['cateid']))
        {
            $cateid = $where['cateid'];
            $all_cates = Categories::getAll();
            $cateids = Categories::getChildrenCateids($all_cates, $cateid);
            $cateids[] = $cateid;
            $cateids = implode(',', $cateids);
            $where_cates .= "a.cateid IN($cateids)";
            unset($where['cateid']);            
        }
        $where2 = [];
        foreach($where as $key => $value)
        {
            $where2['a.'.$key] = $value;
        }
        if (empty($page))
        {
            $data = Db::table('articles')->alias('a')->join('categories c','c.cateid = a.cateid', 'left')->field('a.*,c.catename')->where($where2)->where($where_cates)->order("a.create_timestamp DESC, a.sort DESC, a.artid DESC")->paginate($page_num, false , array('query'=>$url));
        }
        else
        {
            $data = Db::table('articles')->alias('a')->join('categories c','c.cateid = a.cateid', 'left')->field('a.*,c.catename')->where($where2)->where($where_cates)->order("a.create_timestamp DESC, a.sort DESC, a.artid DESC")->page($page, $page_num)->select();
        }
        return $data;
    }
    

    static public function getArticlesAll($where)
    {
       return Db::table('ys_articles')->where($where)->order("create_timestamp DESC")->select();

    }   
    /**
     * getNext 得到下一篇文章
     * @karl
     * @DateTime 2016-08-10T20:34:40+0800
     * @param    int                   $artid   文章id
     * @return   array   [error_code, error_msg, data]             
     */
    static public function getNext($artid)
    {
        $row = DB::table('articles')->where(['artid' => $artid])->find();
        $cateid = $row['cateid'];
        $data = DB::table('articles')->where(['cateid' => $cateid])
                ->where("artid", ">", $artid)->order("artid")->find();
        if ($data) {
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['data'] = $data;
        } else {
            $result['error_code'] = 1;
            $result['error_msg'] = '查询出错';
        }
        return $result;
    }

    /**
     * getPrev 得到上一篇文章
     * @karl
     * @DateTime 2016-08-10T20:35:18+0800
     * @param    int                   $artid 文章id
     * @return   array     [error_code, error_msg, data]                     
     */
    static public function getPrev($artid)
    {
        $row = DB::table('articles')->where(['artid' => $artid])->find();
        $cateid = $row['cateid'];
        $data = DB::table('articles')->where(['cateid' => $cateid])
                ->where("artid", "<", $artid)->order("artid")->find();
        if ($data) {
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['data'] = $data;
        } else {
            $result['error_code'] = 1;
            $result['error_msg'] = '查询出错';
        }
        return $result;
    }

    /**
     * getById 得到文章数组
     * @karl
     * @DateTime 2016-08-09T21:27:39+0800
     * @param    int                   $artid    文章id
     * @return   array      [error_code, error_msg,sum]
     */
    static public function getById($artid)
    {
        $article = DB::table("articles")->where(['artid' => $artid])->find();
        $content = DB::table("article_contents")->where(['artid' => $artid])->find();
        if ($article && $content)
        {   
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['sum'] = $article + $content; 
        }
        else
        {
            $result['error_code'] = 0;
            $result['error_msg'] = '查询出错';
        }
        return $result;
    }
    /**
     * add 添加文章
     * @karl
     * @DateTime 2016-08-09T15:27:49+0800
     * @param    array                   $data 添加数组
     * @return   array      [error_code, error_msg,artid]
     */
    static public function add($data)
    {
        if($data)
        {
            if (isset($data['content'])) $content = $data['content'];
            if (isset($data['album'])) $content = $data['album'];
            unset($data['content'], $data['album']);  

            if($artid = DB::table("ys_articles")->insertGetId($data))
            {
                 zlog(Db::table('ys_articles')->getLastSql()); 
                $input['artid'] = $artid;
                $input['content'] = $content;
                DB::table("ys_articles_contents")->insert($input);
                $result['error_code'] = 0;
                $result['error_msg'] = '';
                $result['data'] = $artid;
            }
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = '添加失败';
        }
        return $result;
    }    

    /**
     * add 更新文章
     * @karl
     * @DateTime 2016-08-09T15:27:49+0800
     * @param    array      [error_code, error_msg]
     */
    static public function edit($data)
    {
        $artid = $data['artid'];
        $content = $album = "";
        if (isset($data['content'])) $content = $data['content'];
        if (isset($data['album'])) $album = $data['album'];
        $is_update_content = isset($data['content']) ? true : false;
        $is_update_album = isset($data['album']) ? true : false;
        unset($data['content'], $data['album']);

        if(DB::table("ys_articles")->update($data))
        {
            if ($is_update_content || $is_update_album)
            {
                $input['artid'] = $artid;
                $input['content'] = $content;
                $input['album'] = $album;
                DB::table("ys_articles_contents")->update($input);
                $result['error_code'] = 0;
                $result['error_msg'] = '';
            }else{
                $result['error_code'] = 1;
                $result['error_msg'] = '';
            }
        }
        else
        {
            $result['error_code'] = 2;
            $result['error_msg'] = '更新失败';
        }
        return $result;
    }

    /**
     * delete 删除
     * @karl
     * @DateTime 2016-08-09T19:35:59+0800
     * @param    int                   $artid 文章参数
     * @return   boolean                          
     */
    static public function del($artid)
    {
        if (DB::table("articles")->delete($artid))
        {
            if (DB::table("article_contents")->delete($artid))
            {
                $result['error_code'] = 0;
                $result['error_msg'] = '';
            }
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = '删除失败';
        }
        return $result;
    }


}



?>