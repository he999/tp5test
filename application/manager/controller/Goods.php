<?php
namespace app\manager\controller;

use app\common\model\GoodsCates;
use app\common\model\GoodsImages;
use app\common\model\GoodsSpecs;
use app\common\model\Goods as Goodss;
use app\common\model\GoodsComment;
use think\Session;
use think\Cookie;
use think\Controller;
use think\Validate;
use think\Response;
use think\Request;
use think\Db;

class Goods extends Manager
{

    /*************************************************  
    *ClassName:     goodsCateList
    *Description:   添加商品分类
    *************************************************/
    public function goodsCateList()
    {  
        $cate_data = GoodsCates::getAll();
        $tree = GoodsCates::buildTree($cate_data);
        $this->assign('list', $tree);
        return  $this->fetch();
    }  

    
    /*************************************************  
    *ClassName:     goodsCateAdd
    *Description:   添加商品分类
    *************************************************/
    public function goodsCateAdd()
    {  
       $input_data = Request::instance()->param();
       if($_POST)
       {
        /******************* 验证信息 ********************/
        
       $rule = [
            'catename'  => 'require|max:100|unique:goods_cates',
          ];

       $msg = [
            'catename.max'      =>  '名称最长为100位',
            'catename.require'  =>  '名称必须填写',
            'catename.unique'  =>  '名称不能重复',
           ];

       $validate = new Validate($rule, $msg);
       $result   = $validate->check($input_data);
       if (!$result)
       {
          $this->jsAlert($validate->getError(),'/index.php/manager/goods/goodscateadd');die;

       }

        $data = [
                'parentid' => $input_data['parentid'],
                'catename'=> $input_data['catename'],
                 ];
    
            if(GoodsCates::add($data))
            {     
               $this->jsAlert('添加成功','/index.php/manager/goods/goodscatelist'); 
            }
            else
            {
                $this->jsAlert('添加失败','/index.php/manager/goods/goodscatelist'); 
            }     
            
      
      
      }  
        $cate_data = GoodsCates::getByParentId('0')['data'];
        $this->assign('list', $cate_data);
        return  $this->fetch();
        
    }  
    
    /*************************************************  
    *ClassName:     goodsDel
    *Description:   商品删除
    *************************************************/
    public function goodsDel()
    {  
        $goods_id = Request::instance()->param('goods_id');
         /******************* 删除数据 ********************/      
        $good_info=Goodss::edit($goods_id,['is_del'=>1]);
        if($good_info['error_code']==0)
        {
           $this->jsAlert('删除成功！','/index.php/manager/goods/goodslist');
        }
        else
        {
           $this->jsAlert('删除失败！','/index.php/manager/goods/goodslist');
        } 
    }

    /*************************************************  
    *ClassName:     goodscateDel
    *Description:   商品类型删除
    *************************************************/
    public function goodscateDel()
    {  
        $cate_id = Request::instance()->param('id');
         /******************* 删除数据 ********************/      
        $data=Goodss::goodscateDel($cate_id);
        if($data['error_code']==0)
        {
           $this->jsAlert('删除成功！','/index.php/manager/goods/goodscatelist');
        }
        else
        {
           $this->jsAlert('删除失败！','/index.php/manager/goods/goodscatelist');
        } 
    }

     /*************************************************  
    *ClassName:     goodsCateEdit
    *Description:   商品分类编辑
    *************************************************/
    public function goodsCateEdit()
    {  
      $input_data = Request::instance()->param();
      if($_POST)
       {
        /******************* 验证信息 ********************/
       $rule = [
            'catename'  => 'require|max:100',
         
          ];

       $msg = [
            'catename.max'      =>  '名称最长为100位',
            'catename.require'  =>  '名称必须填写',      
           ];

       $validate = new Validate($rule, $msg);
       $result   = $validate->check($input_data);

       if (!$result)
       {
          $this->jsAlert($validate->getError(),'/index.php/manager/goods/goodscateedit/cateid/'.$input_data['cateid']);die;
       }
      
        $data = [
                'cateid' => $input_data['cateid'],
                'catename'=> $input_data['catename'],
              ];
      
        if(GoodsCates::update($data))
        {     
           GoodsCates::updates($data);
           $this->jsAlert('修改成功','/index.php/manager/goods/goodscatelist'); 
        }
        else
        {
            $this->jsAlert('修改失败','/index.php/manager/goods/goodscateedit/cateid/'.$input_data['cateid']);die;
        }     
        
   }  
        $cate_data = GoodsCates::getAll();
        $tree = GoodsCates::buildTree($cate_data);
        $this->assign('cateid', Request::instance()->param('cateid'));
        $this->assign('nameinfo', Request::instance()->param('cateid'));
        $categories_info=GoodsCates::getById(Request::instance()->param('cateid'));
        $this->assign('categories_info', $categories_info);
        $this->assign('list', $tree);
        return  $this->fetch();
        
    } 
    /*************************************************  
    *ClassName:     goodsList
    *Description:   商品列表
    *************************************************/
    public function goodsList()
    {  
      $input_data = Request::instance()->param();
      $where=array();
      $url=array();
        if($_GET){
           if(!empty($input_data['goods_sn'])){
                 $url['goods_sn']=$input_data['goods_sn'];
                 $where['goods_sn']=array("like",'%'.$input_data['goods_sn'].'%');
           }
           // if(!empty($input_data['catename'])){
           //       $url['catename']=$input_data['catename'];
           //       $where['catename']=array("like",'%'.$input_data['catename'].'%');
           // }
           if(!empty($input_data['goods_name'])){
                 $url['goods_name']=$input_data['goods_name'];
                 $where['goods_name']=array("like",'%'.$input_data['goods_name'].'%');
           }
           if(!empty($input_data['time_start']) && !empty($input_data['time_end'])){
                  $url['time_start']=$input_data['time_start'];
                  $url['time_end']=$input_data['time_end']; 
                  $start=strtotime($input_data['time_start']);
                  $end=strtotime($input_data['time_end'])+24*60*60;
                  $where['last_update']=array("between time",[$start,$end]);
           }
      }
      $res = Goodss::getGoodsList($where,$url);
      if ($res['error_code'] == 0) {
        $data = $res['data'];
      }else{
        $data = '';
      }
      $this->assign('list',$data); 
      return  $this->fetch();
    } 
    
    //ajax传值选择分类
    public function childAajx()
    {
      $html = ' ';
      foreach (GoodsCates::getByParentId(Request::instance()->param('id'))['data'] as $key=>$val)
      {
      $html.= '<option value="'.$val["cateid"].'">'.$val["catename"].'</option>';
      }
      echo $html;
    }

    /*************************************************  
    *ClassName:     goodsAdd
    *Description:   添加商品
    *************************************************/
    public function goodsAdd()
    {  
       $input_data = Request::instance()->param();
       if($_POST)
       {
         
        /******************* 验证信息 ********************/
        $rule = [
            'goods_name'  => 'require|max:500',
            'goods_bar_code'  => 'require|max:100',
            'goods_sn'  => 'require|max:100',
          ];

       $msg = [
            'goods_name.max'      =>  '商品名称最长为500位',
            'goods_name.require'  =>  '商品名称必须填写', 
             'goods_bar_code.max'      =>  '商品条形码最长为100位',
            'goods_bar_code.require'  =>  '商品条形码必须填写', 
             'goods_sn.max'      =>  '商品编号最长为100位',
            'goods_sn.require'  =>  '商品编号必须填写',      
           ];
 
       $validate = new Validate($rule, $msg);
       $result   = $validate->check($input_data);
       if (!$result)
       {
          $this->jsAlert($validate->getError(),'/index.php/manager/goods/goodsadd');die;

       }
        $data['goods'] = [
                'goods_name' => $input_data['goods_name'],
                'goods_bar_code'=> $input_data['goods_bar_code'],
                'goods_sn'=> $input_data['goods_sn'],
                'intro'=> $input_data['intro'],
                'goods_unit'=> $input_data['goods_unit'],
                'weight'=> $input_data['weight'],
                'sales_sum'=> $input_data['sales_sum'],
                'cateid'=> $input_data['cateid'],
                'parentid' => $input_data['parentid'],
                'catename'=> $input_data['catename'],
                'shop_price'=> $input_data['shop_price'],
                'market_price'=> $input_data['market_price'],
                'sum_brokerage'=> $input_data['sum_brokerage'],
                'spec1_name'=> $input_data['spec1_name'],
                'spec2_name'=> $input_data['spec2_name'],
                'spec3_name'=> $input_data['spec3_name'],
                'content'=> $input_data['content'],
                'stock'=> $input_data['goods_stock'], 
                'sum_voucher' => $input_data['sum_voucher'] 
                ];
  
      for ($i=0; $i <count($input_data['price']) ; $i++) { 
          if($input_data['price'][$i]!=''&&$input_data['stock'][$i]!=''){
              $data['specs'][] =[
                'spec_1' => $input_data['spec_1'][$i],
                'spec_2' => $input_data['spec_2'][$i],
                'spec_3' => $input_data['spec_3'][$i],
                'price' => $input_data['price'][$i],
                'stock' => $input_data['stock'][$i]];
              
          }
       }

         $files['0'] = request()->file("image");
        $files['1'] = request()->file("image2");
        $files['2'] = request()->file("image3");
         $files['3'] = request()->file("image4");
        if(!empty($files))
        {
               foreach($files as $k => $file)
                {
                  if($k==0){
                    if($file){
                      $info = $file->rule('uniqid')->move(ROOT_PATH . 'public' . DS . 'uploads');
                      if($info)
                      {
                          
                         $data['goods']['original_img'] ='/uploads/'.$info->getFilename();
                          $data['goods']['cover_img'] ='/uploads/'.$info->getFilename();
                      }
                    }
                  }else{
                      if($file){
                      $info = $file->rule('uniqid')->move(ROOT_PATH . 'public' . DS . 'uploads');
                      if($info)
                      {
                      
                           $data['images'][] = [
                                      'image_url' => '/uploads/'.$info->getFilename(),
                                      'update_time' => time(),
                                      'create_time' => time()
                                   ];
                      }
                    }
                  }
                 
                  }  
         }
            if(Goodss::addGoods($data))
            {     
               $this->jsAlert('添加成功','/index.php/manager/goods/goodslist'); 
            }
            else
            {
                $this->jsAlert('添加失败','/index.php/manager/goods/goodslist'); 
            }     
            
      
      
      }  

          $cate_data = GoodsCates::getByParentId('0')['data'];
          $this->assign('fenlei', $cate_data);
          return  $this->fetch();
        
    } 
     
    /*************************************************  
    *ClassName:     goodsEdit
    *Description:   添加商品
    *************************************************/
    public function goodsEdit()
    {  
       $input_data = Request::instance()->param();
       if($_POST)
       {
         
        /******************* 验证信息 ********************/
        $rule = [
            'goods_name'  => 'require|max:500',
            'goods_bar_code'  => 'require|max:100',
            'goods_sn'  => 'require|max:100',
         
          ];
 
       $msg = [
            'goods_name.max'      =>  '商品名称最长为500位',
            'goods_name.require'  =>  '商品名称必须填写', 
             'goods_bar_code.max'      =>  '商品条形码最长为100位',
            'goods_bar_code.require'  =>  '商品条形码必须填写', 
             'goods_sn.max'      =>  '商品编号最长为100位',
            'goods_sn.require'  =>  '商品编号必须填写',      
           ];
 
       $validate = new Validate($rule, $msg);
       $result   = $validate->check($input_data);
       if (!$result)
       {
          $this->jsAlert($validate->getError(),'/index.php/manager/goods/goodslist');die;

       }
        $data = [
                'goods_name' => $input_data['goods_name'],
                'goods_bar_code'=> $input_data['goods_bar_code'],
                'goods_sn'=> $input_data['goods_sn'],
                'intro'=> $input_data['intro'],
                'goods_unit'=> $input_data['goods_unit'],
                'weight'=> $input_data['weight'],
                'sales_sum'=> $input_data['sales_sum'],
                'cateid'=> $input_data['cateid'],
                'parentid' => $input_data['parentid'],
                'catename'=> $input_data['catename'],
                'shop_price'=> $input_data['shop_price'],
                'market_price'=> $input_data['market_price'],
                'sum_brokerage'=> $input_data['sum_brokerage'],
                'spec1_name'=> $input_data['spec1_name'],
                'spec2_name'=> $input_data['spec2_name'],
                'spec3_name'=> $input_data['spec3_name'],
                'content'=>isset($input_data['content'])?$input_data['content']:'',
                'stock'=> $input_data['goods_stock'], 
                'sum_voucher' => $input_data['sum_voucher']
                ];
  
      for ($i=0; $i <count($input_data['price']) ; $i++) { 
          
          if($input_data['price'][$i]!=''&&$input_data['stock'][$i]!=''){
           
            if($input_data['spec_id'][$i]!=0){
               $spec_info[]=[
                'spec_1' => $input_data['spec_1'][$i],
                'spec_2' => $input_data['spec_2'][$i],
                'spec_3' => $input_data['spec_3'][$i],
                'price' => $input_data['price'][$i],
                'stock' => $input_data['stock'][$i],
                'spec_id' => $input_data['spec_id'][$i]
                ];
                GoodsSpecs::edit($spec_info);
             }else{
              $spec_info2[] =[
                'spec_1' => $input_data['spec_1'][$i],
                'spec_2' => $input_data['spec_2'][$i],
                'spec_3' => $input_data['spec_3'][$i],
                'price' => $input_data['price'][$i],
                'goods_id' =>$input_data['goods_id'],
                'stock' => $input_data['stock'][$i]
                ];
                 GoodsSpecs::add($input_data['goods_id'],$spec_info2);
            } 
         }
     }

         $files['0'] = request()->file("image");
        $files['1'] = request()->file("image2");
        $files['2'] = request()->file("image3");
         $files['3'] = request()->file("image4");
        if(!empty($files))
        {
               foreach($files as $k => $file)
                {
                  if($k==0){
                    if($file){
                      $info = $file->rule('uniqid')->move(ROOT_PATH . 'public' . DS . 'uploads');
                      if($info)
                      {
                          
                          $data['original_img'] ='/uploads/'.$info->getFilename();
                          $data['cover_img'] ='/uploads/'.$info->getFilename();
                      }
                    }
                  }else{
                      if($file){
                      $info = $file->rule('uniqid')->move(ROOT_PATH . 'public' . DS . 'uploads');
                      if($info)
                      {
                        if(isset($input_data['img'.$k])){ 
                           $img[] = [
                                      'image_url' => '/uploads/'.$info->getFilename(),
                                      'update_time' => time(),
                                       'img_id' => $input_data['img'.$k]
                                   ];
                                    GoodsImages::edit($img);
                          }else{
                             $img2[] = [
                                      'image_url' => '/uploads/'.$info->getFilename(),
                                      'update_time' => time(),
                                      'create_time' => time() 
                                      
                                   ];
                                 GoodsImages::add($input_data['goods_id'],$img2);
                          }
                      }
                    }
                  }
                 
                  }  
         }
     
            if(Goodss::edit($input_data['goods_id'],$data))
            {     
               $this->jsAlert('添加成功','/index.php/manager/goods/goodsedit/goods_id/'.$input_data['goods_id']); 
            }
            else
            {
                $this->jsAlert('添加失败','/index.php/manager/goods/goodslist'); 
            }     
            
      
      
      } 
          $goods_id = Request::instance()->param('goods_id');
          $list = Goodss::getOne($goods_id)['data'];
          $this->assign('list', $list);
          $cate_data = GoodsCates::getByParentId('0')['data'];
          $this->assign('fenlei', $cate_data);
          $xiaofenlei = GoodsCates::getByParentId($list['parentid'])['data'];
          $this->assign('xiaofenlei', $xiaofenlei);
          $img_info = GoodsImages::getList($goods_id);
          $this->assign('img_info',$img_info);
          $specs_info = GoodsSpecs::getSpecs($goods_id);
          if($specs_info['error_code']==0){
             $this->assign('specs_info',$specs_info['data']);
          }else{
              $this->assign('specs_info','');
          }
          return  $this->fetch();
    }
    //属性删除
    public function specDelAajx()
    {
      $spec_id = Request::instance()->param('spec_id');
      $spec[]=['spec_id'=>$spec_id,'is_del'=>1];
       if(GoodsSpecs::edit($spec)){
         return 1;
        }else{
         return 2;
        }
    }
    
    /**
     * verifyAjax  是否存在商品条形码 商品编号
     * @param    array                   $data 输入数据
     * @return   int                     成功返回ok
     */
    public function verifyAjax()
    {
        $request = Request::instance()->param();
        if(isset($request['goods_bar_code'])){
            $where['goods_bar_code'] = $request['goods_bar_code'];
        }
        if(isset($request['goods_bar_code'])){
           $good_info = Goodss::getListOne($where);
           if($good_info['error_code']==0){
             return 1;
           }else{
            return 2;
           }
        }
        if(isset($request['goods_sn'])){
            $where['goods_sn'] = $request['goods_sn'];
        }
        if(isset($request['goods_sn'])){
            $good_info = Goodss::getListOne($where);
           if($good_info['error_code']==0){
             return 1;
           }else{
            return 2;
           }
        }
    }
    /*************************************************  
    *ClassName:     common
    *Description:   用户回复
    *************************************************/
    public function common()
    {  

          return  $this->fetch();
        
    }
    
      /*************************************************  
    *ClassName:     commonDel
    *Description:   用户评论删除
    *************************************************/
    public function commonDel()
    {  
        $comment_id = Request::instance()->param('comment_id');
         /******************* 删除数据 ********************/      
        $common_info=GoodsComment::del($comment_id);
        if($common_info['error_code']==0)
        {
           $this->jsAlert('删除成功！','/index.php/manager/goods/commonlist');
        }
        else
        {
           $this->jsAlert('删除失败！','/index.php/manager/goods/commonlist');
        }    
        
    }
    /*************************************************  
    *ClassName:     commonList
    *Description:    用户评论显示
    *************************************************/
    public function commonList()
    { 
      $this->assign('list',GoodsComment::getCommentList([],3,[]));
     // print_r(GoodsComment::getCommentList([],3,[]));die;
      return  $this->fetch();
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
   public function my_image_resize($src_file, $dst_file , $new_width , $new_height) {
        $new_width= intval($new_width);
        $new_height=intval($new_width);
         if($new_width <1 || $new_height <1) {
         echo "params width or height error !";
         exit();
         }
         if(!file_exists($src_file)) {
         echo $src_file . " is not exists !";
         exit();
         }
         // 图像类型
         $type=exif_imagetype($src_file);
         $support_type=array(IMAGETYPE_JPEG , IMAGETYPE_PNG , IMAGETYPE_GIF);
         if(!in_array($type, $support_type,true)) {
         echo "this type of image does not support! only support jpg , gif or png";
         exit();
         }
         //Load image
         switch($type) {
         case IMAGETYPE_JPEG :
         $src_img=imagecreatefromjpeg($src_file);
         break;
         case IMAGETYPE_PNG :
         $src_img=imagecreatefrompng($src_file);
         break;
         case IMAGETYPE_GIF :
         $src_img=imagecreatefromgif($src_file);
         break;
         default:
         echo "Load image error!";
         exit();
         }
         $w=imagesx($src_img);
         $h=imagesy($src_img);
         $ratio_w=1.0 * $new_width / $w;
         $ratio_h=1.0 * $new_height / $h;
         $ratio=1.0;
         // 生成的图像的高宽比原来的都小，或都大 ，原则是 取大比例放大，取大比例缩小（缩小的比例就比较小了）
         if( ($ratio_w < 1 && $ratio_h < 1) || ($ratio_w > 1 && $ratio_h > 1)) {
         if($ratio_w < $ratio_h) {
         $ratio = $ratio_h ; // 情况一，宽度的比例比高度方向的小，按照高度的比例标准来裁剪或放大
         }else {
         $ratio = $ratio_w ;
         }
         // 定义一个中间的临时图像，该图像的宽高比 正好满足目标要求
         $inter_w=(int)($new_width / $ratio);
         $inter_h=(int) ($new_height / $ratio);
         $inter_img=imagecreatetruecolor($inter_w , $inter_h);
         //var_dump($inter_img);
         imagecopy($inter_img, $src_img, 0,0,0,0,$inter_w,$inter_h);
         // 生成一个以最大边长度为大小的是目标图像$ratio比例的临时图像
         // 定义一个新的图像
         $new_img=imagecreatetruecolor($new_width,$new_height);
         //var_dump($new_img);exit();
         imagecopyresampled($new_img,$inter_img,0,0,0,0,$new_width,$new_height,$inter_w,$inter_h);
         switch($type) {
         case IMAGETYPE_JPEG :
         imagejpeg($new_img, $dst_file,100); // 存储图像
         break;
         case IMAGETYPE_PNG :
         imagepng($new_img,$dst_file,100);
         break;
         case IMAGETYPE_GIF :
         imagegif($new_img,$dst_file,100);
         break;
         default:
         break;
         }
         } // end if 1
         // 2 目标图像 的一个边大于原图，一个边小于原图 ，先放大平普图像，然后裁剪
         // =if( ($ratio_w < 1 && $ratio_h > 1) || ($ratio_w >1 && $ratio_h <1) )
         else{
         $ratio=$ratio_h>$ratio_w? $ratio_h : $ratio_w; //取比例大的那个值
         // 定义一个中间的大图像，该图像的高或宽和目标图像相等，然后对原图放大
         $inter_w=(int)($w * $ratio);
         $inter_h=(int) ($h * $ratio);
         $inter_img=imagecreatetruecolor($inter_w , $inter_h);
         //将原图缩放比例后裁剪
         imagecopyresampled($inter_img,$src_img,0,0,0,0,$inter_w,$inter_h,$w,$h);
         // 定义一个新的图像
         $new_img=imagecreatetruecolor($new_width,$new_height);
         imagecopy($new_img, $inter_img, 0,0,0,0,$new_width,$new_height);
         switch($type) {
         case IMAGETYPE_JPEG :
         imagejpeg($new_img, $dst_file,100); // 存储图像
         break;
         case IMAGETYPE_PNG :
         imagepng($new_img,$dst_file,100);
         break;
         case IMAGETYPE_GIF :
         imagegif($new_img,$dst_file,100);
         break;
         default:
         break;
         }
         }// if3
         }// end function     

}