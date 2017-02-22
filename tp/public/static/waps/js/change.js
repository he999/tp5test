function cancel_order(id,ele){
    if (ele == 1) {
        var content = '是否确定完成备货';
    }
    else{
        var content = '是否确认收货';
    }
     mui.confirm(content, function(e) {
          if (e.index == 1) {
             orderStatusUp(id);
          }
         
      })
}

function  orderStatusUp(id){    
    $.ajax({
        type : "POST",
        url:"/index.php/wap/admin/orderstatusup",
        data :{id:id} ,
        dataType:'json',                       
        success: function(data){    
            // 加入购物车后再跳转到 购物车页面
           if (data.error_code == 0) {
                 mui.toast('操作成功');
                 setTimeout("window.location.reload()",800);
                
           }
           else{
                mui.toast('操作失败');
                setTimeout("window.location.reload()",800);
           }
        }
    });  
}

var page = 1;
function getGoodsList(){
    $('.get_more').show();
    var a = parseInt($("#zh").val());
    $.ajax({
        type : "get",
        url:"/index.php/wap/admin/ajaxgetmore/sta/"+a+"/pages/"+page,
        dataType:'json',
        success: function(data)
        {   console.log(data);
            if (data.error_code == 0) {
                       var html = '';
                        $.each(data.data,function(k,v){
                        html += '<div class="order_list"><h2><a href="javascript:void(0);"><span>订单号:'+v.order_id+'</span><strong><img src="/static/waps/images/icojiantou1.png"></strong></a></h2><a href="/index.php/wap/admin/orderinfo/order_id/'+v.order_id+'">';
                       //循环 
                        $.each(v.data,function(kk,vv){
                           html += '<dl style="position: relative"><dt><img src="'+vv.goods_img+'"></dt><dd class="name"><strong>'+vv.goods_name+'</strong><dd class="pice">￥'+vv.market_price+'元<em>x'+vv.buy_num+'</em></dd><dd class="pice"> <em> </em> </dd></dl>';
                        })
                        html += '</a><div class="anniu" style="width:95%">';
                        if (v.order_status == 1) {
                        html += ' <span onClick="cancel_order('+v.order_id+',1)">完成备货</span> </div>  </div>   '  ;        
                        } 
                        else if(v.order_status == 2){
                         html += ' <span >待取货</span> </div>  </div>   '  ;
                        }
                        else{
                          html += ' <span >已完成</span> </div>  </div>   '  ;
                        }              
                     })    

                    page++;
                    $('.get_more').hide();
                    $(".order").append(html);
                
            }
            else{
                $('.get_more').hide();
                $('#getmore').remove();
            }
        }
    }); 
}

             