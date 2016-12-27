<?php
namespace app\manager\controller;
use app\common\model\Shipping;
use app\common\model\Regions;
use think\Controller;
use think\Validate;
use think\Request;

class Logistics extends Manager
{
    /*************************************************  
    *ClassName:    distributionlst
    *Description:   公司列表
    *************************************************/
    public function distributionlst()
    {  
        $data=Shipping::getShippingList();
        if($data['error_code']!=0){
             $data['data']='';
             $data['arr']='';
        }  
        $this->assign('data',$data);
        return  $this->fetch();      
    }

    /*************************************************  
    *ClassName:    infoset
    *Description:    添加公司
    *************************************************/
    public function infoset()
    {  
        $input_data = Request::instance()->param();
        if($_POST)
        {
            /******************* 验证信息 ********************/
            $rule = [
                'company_name'  => 'require|max:30|unique:shipping_companies',
                'description'  => 'require',
                ];
            $msg = [
                'company_name.max'      =>  '公司名称最长为30字符',
                'company_name.unique'      =>  '公司名称不能重复',
                'company_name.require'  =>  '公司名称不能为空',
                'description.require'      =>  '公司描述不能为空',
             ];
            $validate = new Validate($rule, $msg);
            $result   = $validate->check($input_data);
            if (!$result)
            {
               $this->jsAlert($validate->getError());die;
            } 
             /******************* 添加数据 ********************/
            $data = [
                  'company_name' => $input_data['company_name'],
                  'description'=> $input_data['description'],
                 ];         
            $shippingdata=Shipping::add($data);
            if($shippingdata['error_code']==0)
            {
               $this->jsAlert('保存成功！','/index.php/manager/logistics/distributionlst');
            }
            else
            {
               $this->jsAlert('保存失败！','/index.php/manager/logistics/distributionlst');
            }          
        } 
        return  $this->fetch();       
    }

    
     /*************************************************  
    *ClassName:    infoedit
    *Description:    修改公司
    *************************************************/
    public function infoedit()
    {  
        $input_data = Request::instance()->param();
        $id = Request::instance()->param('id');
        $res=Shipping::getOne($id);
         if($_POST)
        {
            /******************* 验证信息 ********************/
            $rule = [
                'company_name'  => 'require|max:30',
                'description'  => 'require',
                ];
            $msg = [
                'company_name.max'      =>  '公司名称最长为30字符',
                'company_name.require'  =>  '公司名称不能为空',
                'description.require'      =>  '公司描述不能为空',
             ];
            $validate = new Validate($rule, $msg);
            $result   = $validate->check($input_data);
            if (!$result)
            {
               $this->jsAlert($validate->getError());die;
            } 
             /******************* 修改数据 ********************/
            $data = [
                  'shipping_com_id' => $id ,
                  'company_name' => $input_data['company_name'],
                  'description'=> $input_data['description'],
                 ];         
            $shippingdata=Shipping::edit($data);
            if($shippingdata['error_code']==0)
            {
               $this->jsAlert('修改成功！','/index.php/manager/logistics/distributionlst');
            }
            else
            {
               $this->jsAlert('修改失败！','/index.php/manager/logistics/distributionlst');
            } 
        }        
        /******************* 分配默认原数据 ********************/
        $this->assign('data',$res['data']);
        return  $this->fetch();       
    }

     /*************************************************  
    *ClassName:    infodel
    *Description:    删除公司
    *************************************************/
    public function infodel()
    {  
        $shipping_com_id = Request::instance()->param('shipping_com_id');
         /******************* 删除数据 ********************/      
        $shippingdata=Shipping::del($shipping_com_id);
        if($shippingdata['error_code']==0)
        {
           $this->jsAlert('删除成功！','/index.php/manager/logistics/distributionlst');
        }
        else
        {
           $this->jsAlert('删除失败！','/index.php/manager/logistics/distributionlst');
        }       
    }

    /*************************************************  
    *ClassName:    regionlst
    *Description:    区域列表
    *************************************************/
    public function regionlst()
    {  
         $shipping_com_id = Request::instance()->param('shipping_com_id');   //两个页面远程传送id
         $this->assign('shipping_com_id',$shipping_com_id);
         $res=Shipping::getShipping($shipping_com_id);  
         if($res['error_code']!=0){
             $res['data']='';
             $res['arr']='';
          }  
          $this->assign('res',$res['data']);
         return  $this->fetch();      
    }

    /*************************************************  
    *ClassName:    regionlst
    *Description:    区域删除
    *************************************************/
    public function regionDel()
    {  
         $shipping_id = Request::instance()->param('shipping_id'); 
         $res=Shipping::regionDel($shipping_id);  
          if($res['error_code']==0)
            {
               $this->jsAlert('删除成功！','/index.php/manager/logistics/distributionlst');
            }
            else
            {
               $this->jsAlert('删除失败！','/index.php/manager/logistics/distributionlst');
            } 
    }


       /*************************************************  
    *ClassName:    distribution
    *Description:    添加配送方式
    *************************************************/
    public function distribution()
    {
        $shipping_com_id = Request::instance()->param('shipping_com_id');   //两个页面远程传送id
        $this->assign('shipping_com_id',$shipping_com_id);
        $input_data = Request::instance()->param();
        if($_POST)
        {
             /******************* 验证信息 ********************/
            $rule = [
                'shipping_name'  => 'require|max:30|unique:shipping',
                'type'  => 'require',
                'shipping_des'  => 'require|max:255',
                ];
            $msg = [
                'shipping_name.require'  =>  '配送名称不能为空',
                'shipping_name.max'      =>  '配送名称最长为30字符',
                'shipping_name.unique'      =>  '配送名称不能重复',
                'type.require'  => '配送方式不能为空',
                'shipping_des.require'  => '描述不能为空',
                'shipping_des.max'  => '描述不能超过255个字符',
             ];
            $validate = new Validate($rule, $msg);
            $result   = $validate->check($input_data);
            if (!$result)
            {
               $this->jsAlert($validate->getError());die;
            } 
            // if($input_data['weight_price_in']<0 ||  $input_data['weight_out']<0 || $input_data['weight_in']<0 || $input_data['weight_price_out']<0 ||
            //   !is_numeric($input_data['weight_price_in']) || !is_numeric($input_data['weight_out']) || !is_numeric($input_data['weight_in']) ||
            //   !is_numeric($input_data['weight_price_out'])
            //   ){
            //    $this->jsAlert('费用必须为数字！','/index.php/manager/logistics/distributionlst');
            //    die;
            // }
          /******************* 添加数据 ********************/
            $data = [
                  'shipping_com_id' => $input_data['shipping_com_id'],
                  'shipping_name'=> $input_data['shipping_name'],
                  'shipping_des'=> $input_data['shipping_des'],
                  'type'=> $input_data['type'],
                  'num_price'=> $input_data['num_price'],
                  'num_price_out'=> $input_data['num_price_out'],
                  'num_price_max'=> $input_data['num_price_max'],
                  'weight_in'=> $input_data['weight_in'],
                  'weight_price_in'=> $input_data['weight_price_in'],
                  'weight_out'=> $input_data['weight_out'],
                  'weight_price_out'=> $input_data['weight_price_out'],
                  'weight_out'=> $input_data['weight_out'],
                  'weight_out'=> $input_data['weight_out'],
                  'weight_out'=> $input_data['weight_out'],
                 ];  
            if(array_key_exists("region_id2",$input_data)){
                $region_id2=$input_data['region_id2'];
            }else{
                $region_id2=false;
            }
            $shippingdata=Shipping::addShipping($data,$input_data['region_id'],$region_id2);
             if($shippingdata['error_code']==0)
            {
               $this->jsAlert('保存成功！','/index.php/manager/logistics/distributionlst');
            }
            else
            {
               $this->jsAlert('保存失败！','/index.php/manager/logistics/distributionlst');
            } 
        }
        $res = Regions::getLevel(1);
        $this->assign('data',$res['data']);
        return $this->fetch();
    }

       /*************************************************  
    *ClassName:    distributionEdit
    *Description:    修改配送方式
    *************************************************/
    public function distributionEdit()
    {
        $input_data = Request::instance()->param();
        $shipping_id=$input_data['id'];
        $data=Shipping::getShippingOne($shipping_id);
        $this->assign('data',$data['data']);
        $this->assign('city',$data['city']);
        $this->assign('names',$data['names']);
         if($_POST)
        {
             /******************* 验证信息 ********************/
            $rule = [
                'shipping_name'  => 'require|max:30|unique:shipping',
                'type'  => 'require',
                'shipping_des'  => 'require|max:255',
                ];
            $msg = [
                'shipping_name.require'  =>  '配送名称不能为空',
                'shipping_name.max'      =>  '配送名称最长为30字符',
                'shipping_name.unique'      =>  '配送名称不能重复',
                'type.require'  => '配送方式不能为空',
                'shipping_des.require'  => '描述不能为空',
                'shipping_des.max'  => '描述不能超过255个字符',
             ];
            $validate = new Validate($rule, $msg);
            $result   = $validate->check($input_data);
            if (!$result)
            {
               $this->jsAlert($validate->getError());die;
            } 
          /******************* 添加数据 ********************/
            $data = [
                  'shipping_id' => $input_data['shipping_id'],
                  'shipping_com_id' => $input_data['shipping_com_id'],
                  'shipping_name'=> $input_data['shipping_name'],
                  'shipping_des'=> $input_data['shipping_des'],
                  'type'=> $input_data['type'],
                  'num_price'=> $input_data['num_price'],
                  'num_price_out'=> $input_data['num_price_out'],
                  'num_price_max'=> $input_data['num_price_max'],
                  'weight_in'=> $input_data['weight_in'],
                  'weight_price_in'=> $input_data['weight_price_in'],
                  'weight_out'=> $input_data['weight_out'],
                  'weight_price_out'=> $input_data['weight_price_out'],
                  'weight_out'=> $input_data['weight_out'],
                  'weight_out'=> $input_data['weight_out'],
                  'weight_out'=> $input_data['weight_out'],
                 ];  
            if(array_key_exists("region_id2",$input_data)){
                $region_id2=$input_data['region_id2'];
            }else{
                $region_id2=false;
            }
            $shippingdata=Shipping::editShipping($data,$input_data['region_id'],$region_id2);
             if($shippingdata['error_code']==0)
            {
               $this->jsAlert('保存成功！','/index.php/manager/logistics/distributionlst');
            }
            else
            {
               $this->jsAlert('保存失败！','/index.php/manager/logistics/distributionlst');
            } 
        }

        $res = Regions::getLevel(1);
        $this->assign('res',$res['data']);
        return $this->fetch();
    }

    /*************************************************
     * Function:      ajaxAddress
     * Description:   地址获取
     * @param:        void
     * Return:        void
     *************************************************/
    public function ajaxAddress()
    {
        $input = Request::instance()->param();
        $html = '';
        $res = Regions::getChildren($input['id']);
        if ($res['error_code'] == 0) {
            foreach ($res['data'] as $val) {
                $html.= '<option value="'.$val["id"].'">'.$val["name"].'</option>';
            }
        }
        return $html;
    }
    /*************************************************
     * Function:      ajaxReady
     * Description:   已选地址
     * @param:        void
     * Return:        void
     *************************************************/
    public function ajaxReady()
    {
        $input = Request::instance()->param();
        $html = '';
        $res = Regions::getName($input['id']);
        if ($res['error_code'] == 0) {
                //$html.= '<option value="'.$val["id"].'">'.$val["name"].'</option>';
                $html='&nbsp'.'<input style="display-online" name="region_id2[]" type="hidden" value="'.$input['id'].'"/>'.$res['data']["name"].'&nbsp';
        }
        return $html;
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