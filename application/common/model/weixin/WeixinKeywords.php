<?php
namespace app\common\model\weixin;

use think\Model;
use think\Db;

/**
 * 微信关键词
 */
class WeixinKeywords extends Model
{
    /**
     * getAntisList 获取内容,关键词列表
     * @张家伟
     * @DateTime 2016-09-27T15:44:35+0800
     * @param    int 
     * @return   boolean                         
     */
    static public function getAntisList($page_num)
    {      
        return $result = Db::table('ys_weixin_text')->order("id asc")->paginate($page_num,false);               
    }

    /**
     * addAntis 插入一条数据
     * @张家伟
     * @DateTime 2016-09-27T15:44:35+0800
     * @param    array 
     * @param    string    表名
     * @return   int                         
     */
    static public function addAntis($data,$table)
    {   
        return $row = Db::name($table)->insertGetId($data);          
    }

    /**
     * getOne 修改查询
     * @张家伟
     * @DateTime 2016-09-27T15:44:35+0800
     * @param    int  
     * @return   int                         
     */
    static public function getTextOne($id)
    {   
        return $row = Db::name('weixin_text')->where('id',$id)->field('text,id')->find();       
    }

     static public function getArticlesOne($artid)
    {   
        return $row = Db::name('articles')->alias('a')
                ->join('articles_contents g','a.artid = g.artid')
                 ->where('a.artid',$artid)->find();       
    }
    
    
    static public function getKeyWord($keyword)
    {   
         $res = Db::name('weixin_text')->where(['keyword'=>$keyword])->find(); 
          if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] = $res;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "";
        }   
        return $result;  
    }

    /**
     * getOne 查询关键词
     * @张家伟
     * @DateTime 2016-09-27T15:44:35+0800
     * @param    int
     * @return   int                         
     */
    static public function getAll($id)
    {   
        return $info =  Db::table('ys_weixin_keywords_bind')
                ->alias('a')
                ->join('weixin_keywords g','a.keyword_id = g.id')
                ->field("g.keyword,g.id")
                ->where("content_id", $id)
                ->select();       
    }

    /**
     * del 删除
     * @张家伟
     * @DateTime 2016-09-27T15:44:35+0800
     * @param    void
     * @return   boolean                         
     */
    static public function del($id)
    {   
        $result = Db::table('ys_weixin_text')->where('id',$id)->delete();
        $result = Db::table('ys_weixin_keywords_bind')->where('content_id',$id)->delete();
        return $result;
    }

     static public function artDel($id)
    {   
        $result = Db::table('ys_articles')->where('artid',$id)->delete();
        $result = Db::table('ys_weixin_keywords_bind')->where('content_id',$id)->delete();
        return $result;
    }
    
     /**
     * del 删除
     * @张家伟
     * @DateTime 2016-09-27T15:44:35+0800
     * @param    void
     * @return   boolean                         
     */
    static public function keyWordsDel($keyword)
    {   
        $result = Db::table('ys_weixin_keywords')->where(['keyword'=>$keyword])->delete();
    }
    /**
     * upd 修改
     * @张家伟
     * @DateTime 2016-09-27T15:44:35+0800
     * @param    void
     * @return   boolean                         
     */
    static public function upd($where,$data)
    {   
       
        return $result = Db::table('ys_weixin_text')->where($where)->update($data);
    }

    /**
     * getKeyOne 修改查询
     * @张家伟
     * @DateTime 2016-09-27T15:44:35+0800
     * @param    void
     * @return   boolean                         
     */
    static public function getKeyOne($keyword)
    {   
        return $row = Db::name('weixin_keywords')->where('keyword',$keyword)->field('keyword,id,type')->find();       
    }
    
    /**
     * getBindOne 修改查询
     * @张家伟
     * @DateTime 2016-09-27T15:44:35+0800
     * @param    void
     * @return   boolean                         
     */
     static public function getBindOne($id)
    {   
        return $row = Db::name('weixin_keywords_bind')->where('keyword_id',$id)->find();       
    }
    /**
     * getIdOne 修改查询
     * @张家伟
     * @DateTime 2016-09-27T15:44:35+0800
     * @param    void
     * @return   boolean                         
     */
    static public function getIdOne($id)
    {   
        return $row = Db::name('weixin_keywords_bind')->where('content_id',$id)->select();       
    }

    /**
     * getOne 删除
     * @张家伟
     * @DateTime 2016-09-27T15:44:35+0800
     * @param    void
     * @return   boolean                         
     */
    static public function delKeyword($id)
    {   
        return $row = Db::name('weixin_keywords_bind')->where('content_id',$id)->delete();       
    }
     static public function updKeyword($data)
    {   
        return DB::table("ys_weixin_keywords")->update($data);     
    }  
          
}

?>