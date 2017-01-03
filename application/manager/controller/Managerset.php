<?php
namespace app\manager\controller;

use app\common\model\User;
use app\common\model\base\Coms;
use app\common\model\base\UsersVoucher;
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
          $infodata = [
                  'purchase_return_commission' => $input_data['purchase_return_commission'],
                  'least_money_limit'=> $input_data['least_money_limit']
          ];
          $data = [
                  'rebate_rate_lv1' => $input_data['rebate_rate_lv1'],
                  'rebate_rate_lv2' => $input_data['rebate_rate_lv2'],
                  'rebate_rate_lv3' => $input_data['rebate_rate_lv3'],
                  'voucher_rate_lv1' => $input_data['voucher_rate_lv1'],
                  'voucher_rate_lv2' => $input_data['voucher_rate_lv2'],
                  'voucher_rate_lv3' => $input_data['voucher_rate_lv3']
          ];
          $data2 = [
                  'rebate_rate_lv1' => $input_data['rebate_rate_2lv1'],
                  'rebate_rate_lv2' => $input_data['rebate_rate_2lv2'],
                  'rebate_rate_lv3' => $input_data['rebate_rate_2lv3'],
                  'voucher_rate_lv1' => $input_data['voucher_rate_2lv1'],
                  'voucher_rate_lv2' => $input_data['voucher_rate_2lv2'],
                  'voucher_rate_lv3' => $input_data['voucher_rate_2lv3']
          ];
          if (Coms::setRebateInfos(1,$data)['error_code'] = 0 || Coms::setRebateInfos(2,$data2)['error_code'] = 0 || Coms::set($infodata) ) {
            $this->jsAlert('保存成功！','/index.php/manager/Managerset/distributionset');
          }
      }else{
        $this->assign('data',Coms::getRebateInfos()['data']);
        $this->assign('purchase_return_commission',Coms::getValue('purchase_return_commission')['data']);
        $this->assign('least_money_limit',Coms::getValue('least_money_limit')['data']);
        return  $this->fetch();
      }   
    }

    /*************************************************  
    *ClassName:     voucherSet
    *Description:   财富券设置
    *************************************************/  
    public function voucherSet()
    {  
      $input = Request::instance()->param();
      if ($_POST) {
        foreach ($input['id'] as $k => $v) {
          if ($v == 0) {
            if ($input['money'][$k] !='' && $input['voucher'][$k]!='' ) {
              $add[] = [
                'money' => $input['money'][$k],
                'voucher' => $input['voucher'][$k],
                'type' => 'buy'
              ];
            }
          }else{
            $update = [
              'money' => $input['money'][$k],
              'voucher' => $input['voucher'][$k],
              'type' => 'buy'
            ];
            $rowu = UsersVoucher::voucherSetEdit(['id' => $v],$update);
          }
        }
        if (isset($add)) {
          if (count($add)) {
            $rowa = UsersVoucher::voucherSetAdd($add);
          }
        }
        $rows = Coms::set(['voucher_use_explain' => $input['content']]);
        if ($rowa['error_code'] = 0 || $rowu['error_code'] = 0 || $rows['error_code'] = 1 ) {
          $this->jsAlert('保存成功！','/index.php/manager/managerset/voucherset');
        }else{
          $this->jsAlert('保存失败！','/index.php/manager/managerset/voucherset');
        }
      }else{
        $row = UsersVoucher::voucherSetList(['type' => 'buy']);
        $res = Coms::getValue('voucher_use_explain');
        if ($res['error_code'] == 0) {
          $content = $res['data'];
        }else{
          $content = '';
        }
        $this->assign('content',$content);
        $this->assign('list',$row['data']);
        return  $this->fetch();
      }
    }

    /*************************************************  
    *ClassName:     rechargeSet
    *Description:   充值设置
    *************************************************/  
    public function rechargeSet()
    {  
      $input = Request::instance()->param();
      if ($_POST) {
        foreach ($input['id'] as $k => $v) {
          if ($v == 0) {
            if ($input['money'][$k] !='' && $input['voucher'][$k]!='' ) {
              $add[] = [
                'money' => $input['money'][$k],
                'voucher' => $input['voucher'][$k],
                'type' => 'recharge'
              ];
            }
          }else{
            $update = [
              'money' => $input['money'][$k],
              'voucher' => $input['voucher'][$k],
              'type' => 'recharge'
            ];
            $rowu = UsersVoucher::voucherSetEdit(['id' => $v],$update);
          }
        }
        if (isset($add)) {
          if (count($add)) {
            $rowa = UsersVoucher::voucherSetAdd($add);
          }
        }
        $rows = Coms::set(['threshold_money_set' => $input['threshold_money_set']]);
        $rowg = Coms::set(['give_voucher_set' => $input['give_voucher_set']]);
        if ($rowa['error_code'] = 0 || $rowu['error_code'] = 0 || $rows['error_code'] = 1 || $rowg['error_code'] = 1 ) {
          $this->jsAlert('保存成功！','/index.php/manager/managerset/rechargeset');
        }else{
          $this->jsAlert('保存失败！','/index.php/manager/managerset/rechargeset');
        }
      }else{
        $row = UsersVoucher::voucherSetList(['type' => 'recharge']);
        $ress = Coms::getValue('threshold_money_set');
        $resg = Coms::getValue('give_voucher_set');
        if ($ress['error_code'] == 0) {
          $threshold = $ress['data'];
        }else{
          $threshold = '';
        }
        if ($resg['error_code'] == 0) {
          $give = $resg['data'];
        }else{
          $give = '';
        }
        $this->assign('give',$give);
        $this->assign('threshold',$threshold);
        $this->assign('list',$row['data']);
        return  $this->fetch();
      }
    }

    /*************************************************  
    *ClassName:     ajaxVoucherDel
    *Description:   删除财富券设置
    *************************************************/
    public function ajaxVoucherDel()
    {
      $input = Request::instance()->param();
      $rule = [
        'id'  => 'require|number',
      ];

      $msg = [
        'id.number'      =>  'id只能是数字',
      ];
      $validate = new Validate($rule, $msg);
      $result   = $validate->check($input);
      $res = UsersVoucher::voucherSetDel(['id' => $input['id']]);
      if ($res['error_code'] == 0) {
        $arr['error_code'] = 0;
      }else{
        $arr['error_code'] = 1;
      }
      return json($arr);
    }
    
    /*************************************************  
    *ClassName:     commonProblem
    *Description:   常见问题
    *************************************************/  
    public function commonProblem()
    { 
      $input = Request::instance()->param();
      if ($_POST) {
        $row = Coms::set(['common_problem' => $input['content']]);
        if ($row['error_code'] = 1 ) {
          $this->jsAlert('保存成功！','/index.php/manager/managerset/commonproblem');
        }else{
          $this->jsAlert('保存失败！','/index.php/manager/managerset/commonproblem');
        }
      }else{
        $res = Coms::getValue('common_problem');
        if ($res['error_code'] == 0) {
          $content = $res['data'];
        }else{
          $content = '';
        }
        $this->assign('content',$content);
        return  $this->fetch();
      }
      
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
          }else{
            // 上传失败获取错误信息
            echo $file->getError();die;
          }
        }
      }
        return  $this->fetch();
    }    
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