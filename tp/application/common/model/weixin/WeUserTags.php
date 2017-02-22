<?php
namespace app\common\model\weixin;

use think\Db;
use app\common\model\base\Com;
/**
 * 微信用户标记
 */
class WeUserTags
{

    /**
     * add 添加标签
     * @karl
     * @DateTime 2016-09-22T12:18:12+0800
     * @param    string                   $name         标签名称
     * @param    string                   $access_token access_token
     * @return   array                    [error_code, error_msg， data=>[]]
     */
    static public function add($name, $access_token)
    {
        $data = [];
        $result = [];
        $url = "https://api.weixin.qq.com/cgi-bin/tags/create?access_token=".$access_token;
        $data = ['tag'=> ['name' => $name] ];
		zlog($data);
        $data = json_encode($data,JSON_UNESCAPED_UNICODE);
        $info = curl_post($url, $data);
        $return_data = json_decode($info, true);
        if (isset($return_data['tag']))
        {
            $id = $return_data['tag']['id'];
            $updata = ['id' => $id, 'name' => $name];
            $row = Db::name('weixin_groups')->insertGetId($updata);
            $result['error_code'] = 0;
            $result['error_msg'] = 'ok';
            $result['data'] = $id;
        }
        else
        {
            $result['error_code'] = $return_data['errcode'];
            $result['error_msg'] = $return_data['errmsg'];
        }
        return $result;
    }

    /**
     * tagsList 得到标签列表
     * @颜林梧
     * @DateTime 2016-09-22T12:16:11+0800
     * @param    string                $access_token access_token
     * @return   array                 [error_code, error_msg， data=>[]]
     */
    static public function tagsList($access_token)
    {   
        $url = "https://api.weixin.qq.com/cgi-bin/tags/get?access_token=".$access_token;
        $data = ['tag' => ['id'=>'','name'=>'','count'=>''] ];
        $datas = json_encode($data,JSON_UNESCAPED_UNICODE);
        $info = curl_post($url, "");        
        $return_data = json_decode($info, true);            
        if (isset($return_data['tags']))
        {            
            $result['error_code'] = 0;
            $result['error_msg'] = 'ok';
            $result['data'] = $return_data;
        }
        else
        {
            $result['error_code'] = $return_data['errcode'];
            $result['error_msg'] = $return_data['errmsg'];
        }
        return $result;
    }

    /**
     * edit 编辑
     * @颜林梧
     * @DateTime 2016-09-22T12:14:35+0800
     * @param    int                   $tagid        标签id
     * @param    string                $name         标签名称
     * @param    string                $access_token access_token
     * @return   array                 [error_code, error_msg]
     */
    static public function edit($tagid, $name, $access_token)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/tags/update?access_token=".$access_token;
        $data = ['tag'=> ['id'=>$tagid,'name' => $name] ];
        $datas = json_encode($data,JSON_UNESCAPED_UNICODE);
        $info = curl_post($url, $datas);
        $return_data = json_decode($info, true);
        if ($return_data['errmsg'] == 'ok')
        {
        	
            $where = array('id' => $tagid);
            $update = ['name' => $name];
            $row = Db::name('weixin_groups')->where($where)->update($update);
            $result['error_code'] = 0;
            $result['error_msg'] = 'ok';
        }
        else
        {
            $result['error_code'] = $return_data['errcode'];
            $result['error_msg'] = $return_data['errmsg'];
        }
        return $result;
    }

    /**
     * del 删除标签
     * @颜林梧
     * @DateTime 2016-09-22T12:11:23+0800
     * @param    int                   $tagid        标签id
     * @param    string                $access_token access_token
     * @return   array                 [error_code, error_msg]
     */
    static public function del($tagid, $access_token)
    {
    	$url = "https://api.weixin.qq.com/cgi-bin/tags/delete?access_token=".$access_token;
    	$data = ['tag'=> ['id'=>$tagid] ];
    	$datas = json_encode($data,JSON_UNESCAPED_UNICODE);
    	$info = curl_post($url, $datas);
    	$return_data = json_decode($info, true);
    	if ($return_data['errmsg'] == 'ok')
    	{
    		 
    		$where = array('id' => $tagid);
    		$row = Db::name('weixin_groups')->where($where)->delete();
    		$result['error_code'] = 0;
    		$result['error_msg'] = 'ok';
    	}
    	else
    	{
    		$result['error_code'] = $return_data['errcode'];
    		$result['error_msg'] = $return_data['errmsg'];
    	}
    	return $result;
    }

    /**
     * usersList 标识下的用户列表
     * @颜林梧
     * @DateTime 2016-09-22T12:07:03+0800
     * @param    int                   $tagid        标识id
     * @param    string                $access_token access_token
     * @return   array                 [error_code, error_msg, data=>[]]
     */
    static public function usersList($tagid, $access_token)
    {
    	$url = "https://api.weixin.qq.com/cgi-bin/user/tag/get?access_token=".$access_token;
    	$data = ['tagid'=>$tagid ,"next_openid"=>"" ];
    	$datas = json_encode($data,JSON_UNESCAPED_UNICODE);
    	$info = curl_post($url, $datas);
    	$return_data = json_decode($info, true);
    	if (isset($return_data['count']))
    	{
    		$result['error_code'] = 0;
    		$result['error_msg'] = 'ok';
    		$result['data'] = $return_data;
    	}
    	else
    	{
    		$result['error_code'] = $return_data['errcode'];
    		$result['error_msg'] = $return_data['errmsg'];
    	}
    	return $result;
    }

    /**
     * tagging 批量为用户打标签
     * @颜林梧
     * @DateTime 2016-09-22T10:56:13+0800
     * @param    array                   $openids      openid数组
     * @param    int                     $tagid        标签id
     * @param    string                  $access_token access_token
     * @return   [error_code, error_msg]                                 
     */
    static public function tagging($openids, $tagid, $access_token)
    {
    	$data = '{"openid_list":["';
    	$url = "https://api.weixin.qq.com/cgi-bin/tags/members/batchtagging?access_token=".$access_token;
		$count = count($openids) - 1;
		foreach ($openids as $k => $v)
		{
			if ($count == $k)
			{
				$data .= $v.'"';
			}else{
				$data .= $v.'","';
			}
		}
		$data = substr($data,0,strlen($data)-1);
    	$data .= '"],"tagid":'.$tagid.'}';
    	$datas = json_encode($data);			
    	$info = curl_post($url, $data);		
    	$return_data = json_decode($info, true);
    	if ($return_data['errmsg'] == 'ok')
    	{
    		$result['error_code'] = 0;
    		$result['error_msg'] = 'ok';
    	}
    	else
    	{
    		$result['error_code'] = $return_data['errcode'];
    		$result['error_msg'] = $return_data['errmsg'];
    	
    	}
    	return $result;
    }

    /**
     * untagging 批量为用户取消标签
     * @颜林梧
     * @DateTime 2016-09-22T10:56:13+0800
     * @param    array                   $openids      openid数组
     * @param    int                      $tagid        标签id
     * @param    string                   $access_token access_token
     * @return   array                    [error_code, error_msg]                                 
     */
    static public function untagging($openids, $tagid, $access_token)
    {
    	$data = '{"openid_list":["';
    	$url = "https://api.weixin.qq.com/cgi-bin/tags/members/batchuntagging?access_token=".$access_token;
		$count = count($openids) - 1;
		foreach ($openids as $k => $v)
		{
			if ($count == $k)
			{
				$data .= $v.'"';
			}else{
				$data .= $v.'","';
			}
		}
		$data = substr($data,0,strlen($data)-1);
    	$data .= '"],"tagid":'.$tagid.'}';
    	$datas = json_encode($data,JSON_UNESCAPED_UNICODE);
    	$info = curl_post($url, $data);
    	$return_data = json_decode($info, true);   
    	if ($return_data['errmsg'] == 'ok')
    	{
    		$result['error_code'] = 0;
    		$result['error_msg'] = 'ok';
    	}
    	else
    	{
    		$result['error_code'] = $return_data['errcode'];
    		$result['error_msg'] = $return_data['errmsg'];
    	}
    	return $result;
    }

    /**
     * getTags 获取用户身上的标签列表
     * @颜林梧
     * @DateTime 2016-09-22T10:53:42+0800
     * @param    string                   $openid       openid
     * @param    string                   $access_token access_token
     * @return   array                    [error_code, tags=>[], error_msg]                                
     */
    static public function getTags($openid, $access_token)
    {
    	$url = "https://api.weixin.qq.com/cgi-bin/tags/getidlist?access_token=".$access_token;
    	$data = ['openid' => $openid];
    	$datas = json_encode($data,JSON_UNESCAPED_UNICODE);
    	$info = curl_post($url, $datas);
    	$return_data = json_decode($info, true);
    	if (isset($return_data['tagid_list']))
    	{
    		$result['error_code'] = 0;
    		$result['error_msg'] = 'ok';
    		$result['tags'] = $return_data;
    	}
    	else
    	{
    		$result['error_code'] = $return_data['errcode'];
    		$result['error_msg'] = $return_data['errmsg'];
    	}
    	return $result;
    }

}

?>