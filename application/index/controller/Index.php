<?php
namespace app\index\controller;

use think\Controller;
use app\common\model\base\UsersWeixin;
use app\common\model\base\Users;
use app\common\model\base\Coms;
use app\common\model\weixin\WeixinKeywords;
use app\common\model\QRcode;
use app\common\model\weixin\DiyMenu;
use weixin\hongbao\Hongbao;
use think\Db;
/*************************************************  
*ClassName:     Index
*Description:   前台控制器基类
*Others:        
*************************************************/
class Index
{
    //官网首页
    public function index()
    {
       // print_r(UsersWeixin::getOne('444')['data']['attention']);
    	// if(UsersWeixin::getOne('xxxxxxx')){
    	// 	UsersWeixin::getOne('xxxxxxx');
    	// }else{
    	// 	echo "string2";
    	// }
    //  print_r(Users::add(['open_id'=>'444']));
    //	print_r(WeixinKeywords::getKeyWord('电影票')); 
        // $arr['content']='1';
        // $arr['type']='text';
        // $arr['flag']=0;
        // if($arr['content']==''){
        //     $arr['content']=1;
        // }
        // list($a,$b,$c)=[1,2,3];
        // print_r($a );
        //   print_r($b );
        //     print_r($c );
        // print_r(WeixinKeywords::getKeyOne('电影票'));
        //  print_r(WeixinKeywords::getBindOne(38));  
// $content='';
//     	//$content=[0 => ['标题标题标题','详情详情详情','http://www.3lian.com/d/file/201610/27/9d5570c1af152386cb28c642598d08eb.jpg','www.baidu.com']];
//         print_r($content);
       // print_r(WeixinKeywords::getArticlesOne(5));
// echo $_SERVER['SERVER_NAME'];  
// //获取来源网址,即点击来到本页的上页网址  

// echo $_SERVER['REQUEST_URI'];//获取当前域名的后缀  
// echo $_SERVER['HTTP_HOST'];//获取当前域名 
     // QRcode::png('www.baidu.com','./uploads/456.png',QR_ECLEVEL_L,7); 
     //   $this->my_image_resize('./uploads/123.jpg','./uploads/43251.png','420px','630px');
     //   DiyMenu::createQRCode(1000);
      //  copy("http://www.baidu.com/img/baidu_logo.gif","baidu.jpg");
      
       //  $arr = explode('qrscene_','qrscene_fqrdsfdsgsdfgsdsf');
        
       //  if(count($arr)>1){
       // echo "1";
       //  }else{
       //   echo "2";
       //  }
      // print_r(Db::table('ys_users_parent')->count('distinct parent'));
           //   $weixin_info['open_id']='1232321321';
           //  $weixin_info['attention']=1;
           // print_r(UsersWeixin::add($weixin_info)['uid']);
        echo 'ffff';
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
        //$type=exif_imagetype($src_file);
        // $type='IMAGETYPE_PNG';
        // $support_type=array(IMAGETYPE_JPEG , IMAGETYPE_PNG , IMAGETYPE_GIF);
        // if(!in_array($type, $support_type,true)) {
        // echo "this type of image does not support! only support jpg , gif or png";
        // exit();
        // }
        //Load image
        // switch($type) {
        // case IMAGETYPE_JPEG :
        // $src_img=imagecreatefromjpeg($src_file);
        // break;
        // case IMAGETYPE_PNG :
        // $src_img=imagecreatefrompng($src_file);
        // break;
        // case IMAGETYPE_GIF :
        // $src_img=imagecreatefromgif($src_file);
        // break;
        // default:
        // echo "Load image error!";
        // exit();
        // }
        $src_img=imagecreatefrompng($src_file);
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
         imagepng($new_img,$dst_file,0);
         // switch($type) {
         // case IMAGETYPE_JPEG :
         // imagejpeg($new_img, $dst_file,100); // 存储图像
         // break;
         // case IMAGETYPE_PNG :
         // imagepng($new_img,$dst_file,100);
         // break;
         // case IMAGETYPE_GIF :
         // imagegif($new_img,$dst_file,100);
         // break;
         // default:
         // break;
         // }
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
         imagepng($new_img,$dst_file,100);
         // switch($type) {
         // case IMAGETYPE_JPEG :
         // imagejpeg($new_img, $dst_file,100); // 存储图像
         // break;
         // case IMAGETYPE_PNG :
         // imagepng($new_img,$dst_file,100);
         // break;
         // case IMAGETYPE_GIF :
         // imagegif($new_img,$dst_file,100);
         // break;
         // default:
         // break;
         //}
         }// if3
         }// end function     
}
