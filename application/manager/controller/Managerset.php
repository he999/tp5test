<?php
namespace app\manager\controller;

use app\common\model\User;
use app\common\model\base\Coms;
use app\common\model\Foucs;
use think\Session;
use think\Cookie;
use think\Controller;
use think\Validate;
use think\Response;
use think\Request;
use think\Db;

/*************************************************  
*ClassName:     Managerset
*Description:   管理设置控制器类
*************************************************/
class Managerset extends Manager
{

    /*************************************************  
    *ClassName:     InfoSet
    *Description:   信息设置
    *************************************************/
    public function infoSet()
    {
       $input_data = Request::instance()->param();
       if($_POST)
       {
        /******************* 验证信息 ********************/
       $rule = [
            'web_name'  => 'require|max:100',
            'com_address'  => 'require|max:100',
            'web_title'  => 'require|max:100',
            'service_tel'  => 'require|max:100',
            'contact_service'  => 'require|max:100'
           ];

       $msg = [
            'web_name.max'      =>  '网站名称最长为100位',
            'web_name.require'  =>  '网站名称必须填写',
            'com_address.max'      =>  '公司地址最长为100位',
            'com_address.require'  =>  '公司地址必须填写',
            'web_title.max'      =>  '网站标题最长为100位',
            'web_title.require'  =>  '网站标题必须填写',
            'service_tel.max'      =>  '客服电话最长为100位',
            'service_tel.require'  =>  '客服电话必须填写', 
            'contact_service.max'      =>  '联系客服最长为100位',
            'contact_service.require'  =>  '联系客服必须填写' 
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
        if (empty($arr)) 
        {
           $data = [
              'web_name' => $input_data['web_name'],
              'com_address'=> $input_data['com_address'],
              'web_title'=> $input_data['web_title'],
              'service_tel'=> $input_data['service_tel'],
              'contact_service' => $input_data['contact_service']
             ];
        }
        else
        {
           $info = $file->rule('uniqid')->move(ROOT_PATH . 'public' . DS . 'uploads');
           $data = [
                    'web_name' => $input_data['web_name'],
                    'com_address'=> $input_data['com_address'],
                    'web_title'=> $input_data['web_title'],
                    'service_tel'=> $input_data['service_tel'],
                    'contact_service' => $input_data['contact_service'],
                      'web_logo'=> '/uploads/'.$info->getFilename(),
                   ];
        }   
         if(Coms::set($data))
         {
           $this->jsAlert('保存成功！','/index.php/manager/managerset/infoset');
         }
         else
         {
           $this->jsAlert('保存失败！','/index.php/manager/managerset/infoset');
         }


      }  
          $this->assign('list',Coms::getInfos());
          return  $this->fetch();
        
    }

    /*************************************************  
    *ClassName:     parameterset
    *Description:   参数设置
    *************************************************/    
    public function parameterSet()
    {  
       $input_data = Request::instance()->param();
       if($_POST)
       {
        /******************* 验证信息 ********************/
       $rule = [
            'goods_img_width_small'  => 'require|max:100|number|between:0,1000',
            'goods_img_height_small'  => 'require|max:100|number|between:0,1000',
            'goods_img_width_big'  => 'require|max:100|number|between:0,1000',
            'goods_img_height_big'  => 'require|max:100|number|between:0,1000',
           ];

       $msg = [
            'goods_img_width_small.max'      =>  '商品图片小缩略图宽度最长为100位',
            'goods_img_width_small.require'  =>  '商品图片小缩略图宽度必须填写',
            'goods_img_width_small.number'      =>  '商品图片小缩略图宽度只能是数字',
            'goods_img_height_small.max'      =>  '商品图片小缩略图高度最长为100位',
            'goods_img_height_small.require'  =>  '商品图片小缩略图高度必须填写',
            'goods_img_height_small.number'      =>  '商品图片小缩略图高度只能是数字',
            'goods_img_width_big.max'      =>  '商品图片大缩略图宽度最长为100位',
            'goods_img_width_big.require'  =>  '商品图片大缩略图宽度必须填写',
            'goods_img_width_big.number'      =>  '商品图片大缩略图宽度只能是数字',
            'goods_img_height_big.max'      =>  '商品图片大缩略图高度最长为100位',
            'goods_img_height_big.require'  =>  '商品图片大缩略图高度必须填写', 
            'goods_img_height_big.number'      =>  '商品图片大缩略图高度只能是数字',
            ];

       $validate = new Validate($rule, $msg);
       $result   = $validate->check($input_data);
       if (!$result)
       {
          $this->jsAlert($validate->getError());die;
       } 
       /******************* 添加数据 ********************/
         $data = [
                  'goods_img_width_small' => $input_data['goods_img_width_small'],
                  'goods_img_height_small'=> $input_data['goods_img_height_small'],
                  'goods_img_width_big'=> $input_data['goods_img_width_big'],
                  'goods_img_height_big'=> $input_data['goods_img_height_big'],
                 ];
           
         if(Coms::set($data))
         {
           
           $this->jsAlert('保存成功！','/index.php/manager/Managerset/parameterset');
         
         }
         else
         {
           $this->jsAlert('保存失败！','/index.php/manager/Managerset/parameterset');
         }


       }
        $this->assign('list',Coms::getInfos());
        return  $this->fetch();
        
    }

    /*************************************************  
    *ClassName:     distributionset
    *Description:   分销设置
    *************************************************/      
    public function distributionset()
    {
       $input_data = Request::instance()->param();
       if($_POST)
       {
        /******************* 验证信息 ********************/
       $rule = [
            'recommend_integral'  => 'require|max:100|number|between:0,1000',
            'attention_integral'  => 'require|max:100|number|between:0,1000',
            'least_money_limit'  => 'require|max:100|number|between:0,1000',
            'yi_fanyong_bili'  => 'require|max:100|number|between:0,1000',
            'er_fanyong_bili'  => 'require|max:100|number|between:0,1000',
            'san_fanyong_bili'  => 'require|max:100|number|between:0,1000',
            'yi_fanyong_jifen'  => 'require|max:100|number|between:0,1000',
            'er_fanyong_jifen'  => 'require|max:100|number|between:0,1000',
            'san_fanyong_jifen'  => 'require|max:100|number|between:0,1000',
            ];

       $msg = [
            'recommend_integral.max'      =>  '推荐关注送积分最长为100位',
            'recommend_integral.require'  =>  '推荐关注送积分必须填写',
            'recommend_integral.number'      =>  '推荐关注送积分只能是数字',
            'attention_integral.max'      =>  '关注送积分最长为100位',
            'attention_integral.require'  =>  '关注送积分必须填写',
            'attention_integral.number'      =>  '关注送积分只能是数字',
            'least_money_limit.max'      =>  '最小提款额度最长为100位',
            'least_money_limit.require'  =>  '最小提款额度必须填写',
            'least_money_limit.number'      =>  '最小提款额度只能是数字',
            'yi_fanyong_bili.max'      =>  '一级返佣比例最长为100位',
            'yi_fanyong_bili.require'  =>  '一级返佣比例必须填写', 
            'yi_fanyong_bili.number'      =>  '一级返佣比例只能是数字',
            'er_fanyong_bili.max'      =>  '二级返佣比例最长为100位',
            'er_fanyong_bili.require'  =>  '二级返佣比例必须填写', 
            'er_fanyong_bili.number'      =>  '二级返佣比例只能是数字',
            'san_fanyong_bili.max'      =>  '三级返佣比例最长为100位',
            'san_fanyong_bili.require'  =>  '三级返佣比例必须填写', 
            'san_fanyong_bili.number'      => '三级返佣比例只能是数字',
            'yi_fanyong_jifen.max'      =>  '一级返佣积分最长为100位',
            'yi_fanyong_jifen.require'  =>  '一级返佣积分必须填写', 
            'yi_fanyong_jifen.number'      =>  '一级返佣积分只能是数字',
            'er_fanyong_jifen.max'      =>  '二级返佣积分最长为100位',
            'er_fanyong_jifen.require'  =>  '二级返佣积分必须填写', 
            'er_fanyong_jifen.number'      =>  '二级返佣积分只能是数字',
            'san_fanyong_jifen.max'      =>  '三级返佣积分最长为100位',
            'san_fanyong_jifen.require'  =>  '三级返佣积分必须填写', 
            'san_fanyong_jifen.number'      => '三级返佣积分只能是数字',
           ];
 
       $validate = new Validate($rule, $msg);
       $result   = $validate->check($input_data);
       if (!$result)
       {
          $this->jsAlert($validate->getError());die;
       } 
       /******************* 添加数据 ********************/
         $data = [
                  'recommend_integral' => $input_data['recommend_integral'],
                  'least_money_limit'=> $input_data['least_money_limit'],
                  'attention_integral'=> $input_data['attention_integral'],
                  'yi_fanyong_bili'=> $input_data['yi_fanyong_bili'],
                  'er_fanyong_bili' => $input_data['er_fanyong_bili'],
                  'san_fanyong_bili'=> $input_data['san_fanyong_bili'],
                  'yi_fanyong_jifen'=> $input_data['yi_fanyong_jifen'],
                  'er_fanyong_jifen'=> $input_data['er_fanyong_jifen'],
                  'san_fanyong_jifen' => $input_data['san_fanyong_jifen'],
                 ];
         foreach ($input_data as $key => $value) {
             if(!preg_match("/^[1-9][0-9]*$/",$value))//当不为整数时
             {
               $this->jsAlert('请输入整数，不能带小数点','/index.php/manager/Managerset/distributionset');
              }
           }  
         if(Coms::set($data))
         {
           $this->jsAlert('保存成功！','/index.php/manager/Managerset/distributionset');
         }
         else
         {
           $this->jsAlert('保存失败！','/index.php/manager/Managerset/distributionset');
         }
      }

        $this->assign('list',Coms::getInfos());
        return  $this->fetch();
    }

    /*************************************************  
    *ClassName:     advertisementList
    *Description:   广告列表
    *************************************************/  
    public function advertisementList()
    {  
      $list = Foucs::getList();
      if($list['error_code']==0)
      {
         $this->assign('list',$list['data']);
      }
      else
      {
        $this->assign('list','');
      }
      return  $this->fetch();
    }

    /*************************************************  
    *ClassName:     advertisementEdit
    *Description:   广告编辑
    *************************************************/      
    public function advertisementEdit()
    {  
       $input_data = Request::instance()->param();
       if($_POST)
       {
        /******************* 验证信息 ********************/
       $rule = [
            'name'  => 'require|max:100',
            'sort'  => 'require|max:100|between:0,1000',
        ];
 
       $msg = [
            'name.max'      =>  '广告名称最长为100位',
            'name.require'  =>  '广告名称必须填写',
            'sort.max'      =>  '排序最长为100位',
            'sort.require'  =>  '排序必须填写',
            'sort.number'      =>  '排序只能是数字',
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
       if(empty($arr)) 
       {  
            $data = ['fid' => $input_data['fid'],
                    'name' => $input_data['name'],
                    'describe'=> $input_data['describe'],
                    'sort'=> $input_data['sort'],  
                    'time'=> time(), 
                 ];
       }
       else
       {
       $info = $file->rule('uniqid')->move(ROOT_PATH . 'public' . DS . 'uploads','');
          if($info)
          { 
             $data = ['fid' => $input_data['fid'],
                      'image_url'=> '/uploads/'.$info->getFilename(),
                      'name' => $input_data['name'],
                      'describe'=> $input_data['describe'],
                      'sort'=> $input_data['sort'],  
                      'time'=> time(), 
                 ];
           }
           else
           {
                // 上传失败获取错误信息
                echo $file->getError();die;
           }
      }       
         Foucs::edit($data);
         $this->jsAlert('保存成功！','/index.php/manager/Managerset/advertisementlist');
     }
 
        $list = Foucs::getList(['fid'=>Request::instance()->param('fid')]);
        if($list['error_code']==0)
        {
           $this->assign('list',$list['data']);
        }
        else
        {
           $this->assign('list','');
        }
          return  $this->fetch();
    }

    /*************************************************  
    *ClassName:     advertisementAdd
    *Description:   广告添加
    *************************************************/         
    public function advertisementAdd()
    {
       $input_data = Request::instance()->param();
       if($_POST)
       {
        /******************* 验证信息 ********************/
       $rule = [
            'name'  => 'require|max:100',
            'sort'  => 'require|max:100|between:0,1000',
             ];
 
       $msg = [
            'name.max'      =>  '广告名称最长为100位',
            'name.require'  =>  '广告名称必须填写',
            'sort.max'      =>  '排序最长为100位',
            'sort.require'  =>  '排序必须填写',
            'sort.number'      =>  '排序只能是数字',
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
      if (empty($arr)) 
      {
        $this->jsAlert('请上传图片','/index.php/manager/Managerset/advertisementadd'); 
      }
      else
      {
       $info = $file->rule('uniqid')->move(ROOT_PATH . 'public' . DS . 'uploads','');
          if($info)
          { 
             $data = [
                    'image_url'=> '/uploads/'.$info->getFilename(),
                    'name' => $input_data['name'],
                   'describe'=> $input_data['describe'],
                    'sort'=> $input_data['sort'],  
                    'time'=> time(), 
                 ];
              
           if(Foucs::add($data))
           {
             $this->jsAlert('保存成功！','/index.php/manager/Managerset/advertisementlist');
           }
           else
           {
             $this->jsAlert('保存失败！','/index.php/manager/Managerset/advertisementlist');
           }

         }
         else
         {
            // 上传失败获取错误信息
            echo $file->getError();die;
         }
      }
  }

        return  $this->fetch();
    }    

    // static public function imgname($name = ''){
    //   if ($name) {
    //     $name = 'codetpl';
    //   }
    //   return $name;
    // }

    /*************************************************  
    *ClassName:     二维码模板 
    *Description:   广告添加
    *************************************************/ 
    public function codeTemplate()
    {
      $input_data = Request::instance()->param();
      $file = request()->file('image');  
      if($_POST)
      {
        if (!is_uploaded_file($file) && empty($file) && !isset($input_data['ok']) ) {
          $this->jsAlert('请选择模板或上传图片！','/index.php/manager/managerset/codetemplate');
        }
        if ( !is_uploaded_file($file) && empty($file) ) {
          $res = Coms::set(['code_template_image' => $input_data['ok'] ]);
          if ($res['result']['error_code'] == 1) {
            $this->jsAlert('保存成功！','/index.php/manager/managerset/codetemplate');
          }else{
            $this->jsAlert('未修改！','/index.php/manager/managerset/codetemplate');
          }
        }else{
          $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads/codetemplate/','codetpl');
          if ($info) {
            $rwo = Coms::set(['code_template_image' => '/uploads/codetemplate/'.$info->getFilename()]);
            if ($rwo) {
              $this->jsAlert('保存成功！','/index.php/manager/managerset/codetemplate');
            }
          }else{
            // 上传失败获取错误信息
            echo $file->getError();die;
          }
        }
      }else{
        $infoimg = Coms::getValue('code_template_image');
        if ($infoimg['error_code'] == 0) {
          $image = $infoimg['data'];
        }else{
          $image = '/static/images/grzl_2.jpg';
        }
        $this->assign('image',$image);
        return  $this->fetch();
      }
      
    }

    /*************************************************  
    *ClassName:     advertisementDel
    *Description:   广告添加
    *************************************************/         
    public function advertisementDel()
    {
       $id = Request::instance()->param('fid');
       $data= Foucs::advertisementDel($id);
       if($data['error_code']==0)
         {
           $this->jsAlert('删除成功！','/index.php/manager/Managerset/advertisementlist');
         }
      else
       {
           $this->jsAlert('删除失败！','/index.php/manager/Managerset/advertisementlist');
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
        $this->assign('info', $info);
        $this->assign('url', $url);
        echo $this->fetch(APP_PATH.request()->module().'/view/common/alert.html');
        die;
    }     

     
    

      

    
}