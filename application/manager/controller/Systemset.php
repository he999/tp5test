<?php
namespace app\manager\controller;

use app\common\model\base\Coms;
use app\common\model\base\Articles;
use app\common\model\weixin\DiyMenu;
use app\common\model\weixin\WeixinKeywords;
use think\Session;
use think\Cookie;
use think\Controller;
use think\Validate;
use think\Response;
use think\Request;
use think\Db;

/*************************************************  
*ClassName:     Systemset
*Description:   公众平台控制器类
*************************************************/
class Systemset extends Manager
{

    /*************************************************  
    *ClassName:     weixin_interface
    *Description:   微信接口
    *************************************************/
    public function weixinInterface()
    {  
       $input_data = Request::instance()->param();
       if($_POST)
       {
			/******************* 验证信息 ********************/
		   $rule = [
				'appid'  => 'require|max:100',
				'appsecret'  => 'require|max:100',
				'mchid'  => 'require|max:100',
				'apikey'  => 'require|max:100',
				  
		   ];

		   $msg = [
				'appid.max'      =>  'appid最长为100位',
				'appid.require'  =>  'appid必须填写',
				'appsecret.max'      =>  'appsecret最长为100位',
				'appsecret.require'  =>  'appsecret必须填写',
				'mchid.max'      =>  'mchid最长为100位',
				'mchid.require'  =>  'mchid必须填写',
				'apikey.max'      =>  'apikey最长为100位',
				'apikey.require'  =>  'apikey必须填写',
				 
		   ];

		   $validate = new Validate($rule, $msg);
		   $result   = $validate->check($input_data);
		   if (!$result)
		   {
			  $this->jsAlert($validate->getError());die;
		   } 

		   /******************* 添加数据 ********************/
			
			$file = request()->file('image');
			$arr = (array)$file;
			if (!empty($arr)) 
			{
			   $info = $file->rule('uniqid')->move(ROOT_PATH . 'public' . DS . 'uploads');
				   $data = [
					  'appid' => $input_data['appid'],
					  'appsecret'=> $input_data['appsecret'],
					  'mchid'=> $input_data['mchid'],
					  'apikey'=> $input_data['apikey'],
					  'platform_qr_code' => '/uploads/'.$info->getFilename()
					 ];
			}else{
			   $data = [
					  'appid' => $input_data['appid'],
					  'appsecret'=> $input_data['appsecret'],
					  'mchid'=> $input_data['mchid'],
					  'apikey'=> $input_data['apikey']
					 ];
			}
			if(Coms::set($data))
			{
			   $this->jsAlert('绑定成功！','/index.php/manager/systemset/weixininterface');
			}
			else
			{
			   $this->jsAlert('绑定失败！','/index.php/manager/systemset/weixininterface');
			}
	    }
		$this->assign('info',Coms::getInfos());
		return  $this->fetch();
	}  
		 /*************************************************  
		*ClassName:     menu
		*Description:   自定义菜单
		*************************************************/
	public function menu()
	{  
	    $input_data = Request::instance()->param();
	    if ($_POST) 
	    {
		    $result = DiyMenu::createMeun();
		    if ($result['error'] == 'yes') 
		    {                                
			    $this->jsAlert('生成自定义菜单成功！','/index.php/manager/systemset/menu_bar');                 
		    }
		    else
		    {   
			    $this->jsAlert('生成自定义菜单失败！','/index.php/manager/systemset/menu_bar');               
		    }
		}
	 
	    $list = DiyMenu::menulist(array('com_id' => '1'));
	    $tree = DiyMenu::buildTree($list);        
	    $this->assign('list', $tree);     
	    return $this->fetch(); 
			
    }
     /*************************************************  
    *FunctionName:     menu_del
    *Description:   菜单删除
    *************************************************/
    public function menuDel()
    {  
        $id = Request::instance()->param('id');
        $keyword = Request::instance()->param('keyword');
        if(DiyMenu::del($id))
        {   
            // WeixinKeywords::keyWordsDel($keyword);
            $this->jsAlert('删除成功！','/index.php/manager/systemset/menu');
        }
        else
        {
            $this->jsAlert('删除失败！','/index.php/manager/systemset/menu');
        }
    }  
    /*************************************************  
    *ClassName:     menuShengCheng
    *Description:   自定义菜单生成
    *************************************************/
    public function menuShengCheng()
    {  
        $result = DiyMenu::createMeun();
        if ($result['error'] == 'yes') 
        {                                
            $this->jsAlert('生成自定义菜单成功！','/index.php/manager/systemset/menu');                 
        }
        else
        {   
            $this->jsAlert('生成自定义菜单失败！','/index.php/manager/systemset/menu');               
        }
       
    }   
     /*************************************************  
    *ClassName:     menuAdd
    *Description:   自定义菜单添加
    *************************************************/
    public function menuAdd()
    {  
        $input_data = Request::instance()->param();
        if($_POST)
        {
            $rule = [
                'title'  => 'require|max:50',
                'keyword'  => 'require|max:50',
                'url'  => 'max:255',                                  
            ];       
            $msg = [
                'title.max' =>  '主菜单最长为50位',            
                'title.require' =>  '主菜单名称必须填写',
                'keyword.max' =>  '关键字最长为50位',            
                'keyword.require' =>  '关键字名称必须填写',
                'url.max' =>  'url最长为255位',                        
            ];

            $validate = new Validate($rule, $msg);
            $result   = $validate->check($input_data);
            if (!$result)
            {
                $this->jsAlert($validate->getError());die;
            } 
          
            /******************* 添加数据 ********************/
            if (isset($input_data['parent_id']))
            {                                       
                $parent_id = $input_data['parent_id'];
            }
            else
            {
                $parent_id = 0 ;
            }
            $data = [
                'title' => $input_data['title'],
                'url'=> $input_data['url'],
                'parent_id'=> $parent_id,
                'sort' => $input_data['sort'],
                'com_id' => 1,
                'keyword' => $input_data['keyword'],                                   
            ];

            $result=DiyMenu::add($data);
            if (isset($result['code']))
            {            
                if ($result['code'] == '1') 
                {               
                    $this->jsAlert('一级菜单不能超过3个！','/index.php/manager/systemset/menuadd');                                                                   
                }
                elseif($result['code'] == '2')
                {
                    $this->jsAlert('二级菜单不能超过6个！','/index.php/manager/systemset/menuadd');
                }                                             
            }
            if ($result['error'] == 'yes') 
            {
                $this->jsAlert('增加成功！','/index.php/manager/systemset/menu');
            }
            else
            {
				$this->jsAlert('增加失败！','/index.php/manager/systemset/menuadd');
            }                 
        }

		$this->assign('diy_list',DiyMenu::menulist(array('com_id' => '1','parent_id' => '0')));
		return $this->fetch(); 
    } 

    /*************************************************  
    *ClassName:     menuUpd
    *Description:   修改菜单
    *************************************************/
    public function menuUpd()
    {   
      $input_data = Request::instance()->param();    
      if($_POST)
      {        
            $rule = [
                'title'  => 'require|max:50',
                'url'  => 'max:255',                                   
            ];       
            $msg = [
                'title.max' =>  '主菜单最长为50位',            
                'title.require' =>  '主菜单名称必须填写',
                'url.max' =>  'url最长为255位',                       
            ];
            $validate = new Validate($rule, $msg);
            $result   = $validate->check($input_data);
            if (!$result)
            {
                $this->jsAlert($validate->getError());die;
            } 
            /******************* 验证密码 ********************/
            $user_info=DiyMenu::menulist(array('id' => $input_data['id']));

            /******************* 修改数据 ********************/
            if (isset($input_data['parent_id']))
            {                                       
                $parent_id = $input_data['parent_id'];
            }
            else
            {
                $parent_id = 0 ;
            }
            $data = [
                'title' => $input_data['title'],
                'url'=> $input_data['url'],
                'parent_id'=> $parent_id,
                'sort' => $input_data['sort'],
                'com_id' => 1,
                'keyword' => $input_data['keyword'],                                   
            ];

            $result=DiyMenu::edit($input_data['id'],$data);
			if (isset($result['code']))
			{            
				if ($result['code'] == '1') 
				{               
					$this->jsAlert('一级菜单不能超过3个！','/index.php/manager/systemset/menuUpd/id/'.$input_data['id']);                                                                    
				}
				elseif($result['code'] == '2')
				{
					$this->jsAlert('二级菜单不能超过6个！','/index.php/manager/systemset/menuUpd/id/'.$input_data['id']);
				}                                             
			}
			if ($result['error'] == 'yes')
			{
				$this->jsAlert('修改成功！','/index.php/manager/systemset/menu');
			}
			else{
				$this->jsAlert('修改失败！','/index.php/manager/systemset/menu');
			}    
        }
		$this->assign('list',DiyMenu::menulist(array('com_id' => '1','parent_id' => '0')));
		$user_info=DiyMenu::getOne(Request::instance()->param('id'));
		$this->assign('id',$user_info['id']);
		$this->assign('title',$user_info['title']);
		$this->assign('keyword',$user_info['keyword']);
		$this->assign('url',$user_info['url']);
		$this->assign('sort',$user_info['sort']);
		$this->assign('parent_id',$user_info['parent_id']);
		return $this->fetch(); 
    }

     /*************************************************  
    *ClassName:     articleadd
    *Description:   文章添加
    *************************************************/
    public function articleAdd()
    {  
        $input_data = Request::instance()->param();
        if($_POST)
        {
        /******************* 验证信息 ********************/
        $rule = [
            'title'  => 'require|max:100', 
            'keyword'  => 'require|max:500',
            'description'  => 'require|max:500',
            'content'  => 'require', 
        ];

        $msg = [
            'title.max'      =>  '文章标题最长为100位',
            'title.require'  =>  '文章标题必须填写',
            'keyword.max'      =>  '关键字最长为500位',
            'keyword.require'  =>  '关键字必须填写',
            'description.max'      =>  '图文消息描述最长为500位',
            'description.require'  =>  '图文消息描述必须填写',
            'content.require'  =>  '文章编辑必须填写',
        ];

        $validate = new Validate($rule, $msg);
        $result   = $validate->check($input_data);
        if (!$result)
        {
            $this->jsAlert($validate->getError());die;
        }
        $file = request()->file('image');
        $data = [];
        $arr = (array)$file;
        if (!empty($arr)) 
        {
            $info = $file->rule('uniqid')->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info)
            { 
                $data = [
                    'title' => $input_data['title'],
                    'pic'=> 'http://'.$_SERVER['SERVER_NAME'].'/uploads/'.$info->getFilename(),
                    'keyword'=> $input_data['keyword'],
                    'description'=> $input_data['description'],
                    'content'=>$input_data['content'],
                    'jumplink'=>$input_data['jumplink'],
                    'author'=>'十七书社',
                    'link'=> 'http://'.$_SERVER['SERVER_NAME'].'/index.php/wap/article/index/artid/',
                    'create_timestamp'=>time()
                ]; 
				$art_info =  Articles::add($data); 
                if($art_info['error_code']==0)
                {  
                    $key = [
                        'keyword' => $input_data['keyword'],
                        'create_time' => time(),
                        'type' => 'news'
                    ];
                    $keyid= WeixinKeywords::addAntis($key,'weixin_keywords');
                    $bind = [
                        'keyword_id' => $keyid,
                        'content_id' => $art_info['data'],
                    ];
                    WeixinKeywords::addAntis($bind,'weixin_keywords_bind'); 
                    $this->jsAlert('添加成功','/index.php/manager/systemset/articleList'); 
                }
                else
                {
                    $this->jsAlert('添加失败','/index.php/manager/systemset/articleadd'); 
                }     
            }else{
                // 上传失败获取错误信息
                echo $file->getError();die;
            }
        }else{
            $this->jsAlert('请上传图片','/index.php/manager/systemset/articleadd'); 
        }
    } 
       return  $this->fetch();
   }

    /*************************************************  
    *ClassName:     articleList
    *Description:   文章列表
    *************************************************/
    public function articleList()
    {
        $this->assign('list',Articles::getArticlesAll([])); 
        return  $this->fetch();
    }

    /*************************************************  
    *ClassName:     articleDel
    *Description:   文章删除
    *************************************************/
    public function articleDel()
    {
        $artid = Request::instance()->param('artid');
        $keyword = Request::instance()->param('keyword'); 
        if(WeixinKeywords::artDel($artid))
        {   
            WeixinKeywords::keyWordsDel($keyword);
            $this->jsAlert('删除成功！','/index.php/manager/systemset/articleList');
        }
        else
        {
            $this->jsAlert('删除失败！','/index.php/manager/systemset/articleList');
        }
    }   
    
    /*************************************************  
    *ClassName:     articleUpd
    *Description:   文章修改
    *************************************************/
    public function articleUpd()
    {
        $input_data = Request::instance()->param();
        if($_POST)
        {
        /******************* 验证信息 ********************/
        $rule = [
            'title'  => 'require|max:100', 
            'keyword'  => 'require|max:500',
            'description'  => 'require|max:500',
            'content'  => 'require', 
        ];

        $msg = [
            'title.max'      =>  '文章标题最长为100位',
            'title.require'  =>  '文章标题必须填写',
            'keyword.max'      =>  '关键字最长为500位',
            'keyword.require'  =>  '关键字必须填写',
            'description.max'      =>  '图文消息描述最长为500位',
            'description.require'  =>  '图文消息描述必须填写',
            'content.require'  =>  '文章编辑必须填写',
        ];

        $validate = new Validate($rule, $msg);
        $result   = $validate->check($input_data);
        if (!$result)
        {
            $this->jsAlert($validate->getError());die;
        }
        $file = request()->file('image');
        $data = [];
        $arr = (array)$file;
        if (!empty($arr)) 
        {
            $info = $file->rule('uniqid')->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info)
            { 
                $data = [
                    'title' => $input_data['title'],
                    'pic'=> 'http://'.$_SERVER['SERVER_NAME'].'/uploads/'.$info->getFilename(),
                    'keyword'=> $input_data['keyword'],
                    'description'=> $input_data['description'],
                    'content'=>$input_data['content'],
                    'jumplink'=>$input_data['jumplink'],
                    'author'=>'十七书社',
                    'artid'=> $input_data['artid']
                ];      
            }else{
                // 上传失败获取错误信息
                echo $file->getError();die;
            }
        }
	    else
        {
            $data = [
				'title' => $input_data['title'],
				'keyword'=> $input_data['keyword'],
				'description'=> $input_data['description'],
				'content'=>$input_data['content'],
				'jumplink'=>$input_data['jumplink'],
				'author'=>$input_data['author'],
				'artid'=> $input_data['artid']
            ];  
        }
            $art_info =  Articles::edit($data);
            if($art_info['error_code']==0)
                {  
                    $bind_info = WeixinKeywords::getIdOne($input_data['artid']);
                    $key = [
						'keyword' => $input_data['keyword'],
						'id' => $bind_info[0]['keyword_id'] 
                    ];
                    WeixinKeywords::updKeyword($key);
                    $this->jsAlert('添加成功','/index.php/manager/systemset/articleupd/artid/'.$input_data['artid']); 
                }
                else
                {
                    $this->jsAlert('添加失败','/index.php/manager/systemset/articleupd/artid/'.$input_data['artid']); 
                }
        } 
        $this->assign('list',WeixinKeywords::getArticlesOne(Request::instance()->param('artid'))); 
        return  $this->fetch();
    } 
     /*************************************************  
    *ClassName:     recover
    *Description:   回复设置
    *************************************************/
    public function recover()
    {  
       $input_data = Request::instance()->param();
       if($_POST)
       {
			/******************* 验证信息 ********************/
		    $rule = [
				'attention_reply'  => 'require|max:500' 
		    ];
		    $msg = [
				'attention_reply.max'      =>  '用户关注公众号时回复文字最长为500位',
				'attention_reply.require'  =>  '用户关注公众号时回复文字必须填写' 
			];=
            $validate = new Validate($rule, $msg);
            $result   = $validate->check($input_data);
			if (!$result)
			{
			    $this->jsAlert($validate->getError());die;
			} 
            /******************* 添加数据 ********************/
            $data = [
                'attention_reply' => $input_data['attention_reply'] 
            ];
		    if(Coms::set($data))
			{
			    $this->jsAlert('绑定成功！','/index.php/manager/systemset/recover');
			}
			else
			{
			     $this->jsAlert('绑定失败！','/index.php/manager/systemset/recover');
			}
        }

        $this->assign('attention_reply',Coms::getValue('attention_reply')['data']);
        return  $this->fetch();
        
    }

     /*************************************************  
    *ClassName:     keyWordList
    *Description:   关键字列表
    *************************************************/
    public function keyWordList()
    {  
        $keyword_list = WeixinKeywords::getAntisList(3);
        $this->assign('list',$keyword_list);       
        return $this->fetch();
    }  
     /*************************************************  
    *ClassName:     keyWordAdd
    *Description:   添加文本信息
    *************************************************/
    public function keyWordAdd()
    {  
        $input_data = Request::instance()->param();        
        if($_POST)
        {
            $rule = [
                'keyword'  => 'require',
                'text'  => 'require|max:1000',                                   
            ];       
            $msg = [            
                'keyword.require' =>  '关键词必须填写',
                'text.require' =>  '内容必须填写', 
                'text.max' =>  '内容不能超过1000字',                      
            ];

            $validate = new Validate($rule, $msg);
            $result   = $validate->check($input_data);
            if (!$result)
            {
                $this->jsAlert($validate->getError());die;
            } 
            if ($input_data['text']=='')
            {
                print_r($input_data);die;
            } 
        
           /******************* 添加数据 ********************/
            $create_time = strtotime("now");
            $text = $input_data['text'];
            $keyword = $input_data['keyword'];
            $key = explode(" ",$keyword);
            $key = array_filter($key);            
            $count = count($key);
            if ($count > 4) {
                $this->jsAlert('关键词不能超过四个！','/index.php/manager/systemset/keywordadd');        
            }
            $content = [
                'text' => $input_data['text'],
                'create_time' => $create_time,
                'keyword' => $keyword
            ];                
            $text = WeixinKeywords::addAntis($content,'weixin_text'); 
            foreach ($key as $k => $value) 
            {
                $key = [
                    'keyword' => $value,
                    'create_time' => $create_time,
                ];
                $keyid= WeixinKeywords::addAntis($key,'weixin_keywords');
                $bind = [
                    'keyword_id' => $keyid,
                    'content_id' => $text,
                ];
                WeixinKeywords::addAntis($bind,'weixin_keywords_bind');              
            }            
            if ($keyword&&$text) {
                $this->jsAlert('添加成功！','/index.php/manager/systemset/keywordlist');               
            }
            else
            {
                $this->jsAlert('添加失败！','/index.php/manager/systemset/keywordlist');   
            }                             
        }        
        return $this->fetch(); 
      
    }
    /*************************************************  
    *ClassName:     keyWordUpd
    *Description:   关键词编辑
    *************************************************/
    public function keyWordUpd()
    {  
        $input_data = Request::instance()->param();    
        if($_POST)
        {
            $rule = [
               'keyword'  => 'require',
                'text'  => 'require|max:1000',                                    
            ];       
            $msg = [            
                'keyword.require' =>  '关键词必须填写',
                'text.require' =>  '内容必须填写',
                'text.max' =>  '内容不能超过1000字',                                       
            ];

            $validate = new Validate($rule, $msg);
            $result   = $validate->check($input_data);
            if (!$result)
            {
                $this->jsAlert($validate->getError());die;
            } 
          
            /******************* 修改数据 ********************/
            $content=[];
            $text = $input_data['text'];
            $keyword = $input_data['keyword'];          
            $key = explode(" ",$keyword);
            $key = array_filter($key);
            $count = count($key);
            if ($count > 4) {
                $this->jsAlert('关键词不能超过四个！','/index.php/manager/systemset/keyword_add');        
            }

            $content = [
                'text' => $input_data['text'],
                'keyword' => $input_data['keyword'],
            ];
            $id = Request::instance()->param('id');                    
            $row = WeixinKeywords::upd(array('id'=>$id),$content);
            $del = WeixinKeywords::delKeyword($input_data['id']);
            foreach ($key as $k => $value) 
            {                   
                $result = WeixinKeywords::getKeyOne($value);                    
                if (!empty($result)) 
                {
                    $id = $result['id'];                        
                    $data['keyword_id'] = $id;
                    $data['content_id'] = $input_data['id'];
                    WeixinKeywords::addAntis($data,'weixin_keywords_bind');                    
                }
                else
                {
                    $rows['keyword'] = $value;
                    $rows['create_time'] = time();
                    $back_id = WeixinKeywords::addAntis($rows,'weixin_keywords');
                    if($back_id)
                    {
                        $bind = ['keyword_id' => $back_id,'content_id' => $input_data['id']];
                        WeixinKeywords::addAntis($bind,'weixin_keywords_bind');
                    }                   
                }                                            
            }            
            if (!empty($row)) 
            {
                $this->jsAlert('修改成功！','/index.php/manager/systemset/keywordupd?id=' .$input_data['id']);               
            }
            else
            {
                $this->jsAlert('修改失败！','/index.php/manager/systemset/keywordupd?id=' .$input_data['id']);   
            }                             
        }
        $keyword_list = WeixinKeywords::getTextOne(Request::instance()->param('id'));
        $keyword = WeixinKeywords::getAll(Request::instance()->param('id'));
        $sum = 0;
        $count = count($keyword);
        for($i = 0; $i < $count; $i++)
        {
            $sum .= $keyword[$i]['keyword'] .' ';
        }
        $sum = substr($sum,1);
        $this->assign('text',$keyword_list['text']);
        $this->assign('id',$keyword_list['id']);
        $this->assign('keyword',$sum);         
        return $this->fetch(); 
    } 

    /*************************************************  
    *ClassName:     keywordDel
    *Description:   关键词删除
    *************************************************/
    public function keywordDel()
    {  
        $id = Request::instance()->param('id');
        $keyword = Request::instance()->param('keyword'); 
        if(WeixinKeywords::del($id))
        {   
            WeixinKeywords::keyWordsDel($keyword);
            $this->jsAlert('删除成功！','/index.php/manager/systemset/keywordlist');
        }
        else
        {
            $this->jsAlert('删除失败！','/index.php/manager/systemset/keywordlist');
        }
    }
    
    
    /**
     * jsAlert JS弹出框
     * @karl
     * @DateTime 2016-08-12T09:29:04+0800
     * @param    string                   $info [description]
     * @param    string                   $url  [description]
    */
    public function jsAlert($info, $url="")
    {
         this->assign('info', $info);
        $this->assign('url', $url);
        echo $this->fetch(APP_PATH.request()->module().'/view/common/alert.html');
        die;
    }     

}