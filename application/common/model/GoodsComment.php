<?php
namespace app\common\model;

use think\Model;
use think\Db;

/**
 * 商品留言模型
 */
class GoodsComment extends Model
{
    /**
     * add 添加商品留言
     * @xiao
     * @DateTime 2016-09-06T06:19:16+0800
     * @param    array              $data 添加数组 []
     * @return   array              [error_code, error_msg, goods_id]
     */
    static public function add($data)
    {
        $row = Db::name('goods_comment')->insertGetId($data);
        if ($row)
        {
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['comment_id'] = $row;
        }
        else
        {
            $result['error_code'] = 1;
            $result['error_msg'] = '商品留言失败';
        }
      
        return $result;
    }

    /**
     * del 删除留言
     * @xiao
     * @DateTime 2016-09-06T06:53:19+0800
     * @param    array                 $data ['id'=>'']
     * @return   array                 [error_code, error_msg]                
     */
    static public function del($comment_id)
    {   
        $where['comment_id'] = $comment_id;
        if($del = Db::name('goods_comment')->where($where)->delete())
        {
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['goods_id'] = $del;
        }
        else
        {
            $result['error_code'] = '1';
            $result['error_msg'] = '留言删除失败';
        }
        return $result;
    }

    /**
     * getComment 得到一个商品的留言列表
     * @xiao
     * @DateTime 2016-09-28T22:41:15+0800
     * @param    int                $goods_id 商品id
     * @return   array              [error_code, error_msg, data=> [] ]
     */
    static public function getComment($goods_id,$page = 1, $page_num = 10)
    {
        if ($goods_id)
        {
            $where = array('goods_id' => $goods_id);
            $data = Db::name('goods_comment')
            ->alias('g')
            ->join('users_customers c','g.uid = c.uid','left')
            ->field('g.*,c.nickname,c.face')
            ->where($where)
            ->page($page,$page_num)
            ->select();
            if($data)
            {
                $result['error_code'] = 0;
                $result['error_msg'] = '';
                $result['data'] = $data;
            }
            else
            {
                $result['error_code'] = 1;
                $result['error_msg'] = '没有查到商品属性详情';
            }
            
        }
            return $result;

    } 

    /**
     * getCommentList 得到商品的留言列表
     * @karl
     * @DateTime 2016-08-08T14:28:02+0800
     * @param    int                   $quyu_id  city id
     * @param    array                   $where  
     * @return   array                         
     */
    static public function getCommentList($where, $page_num, $url = [])
    {
      return $list = Db::table('ys_goods_comment')->alias('c')->join('ys_goods g','c.goods_id = g.goods_id','left')->join('ys_users_customers u','c.uid = u.uid','left')->where($where)->field('g.goods_name,u.nickname,c.grade,c.content,c.time,c.comment_id,c.is_hide')->order("time desc")->paginate($page_num, false , array('query'=>$url));    
    }  
}

?>