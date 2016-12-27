/**
 * 工具包
 */
/**
 * jsAlert js弹出框
 * @karl
 * @DateTime 2016-08-12T06:35:13+0800
 * @param    {String}                 info 显示信息
 * @param    {String}                 url  跳转网址
 */
function jsAlert(info, url)
{
    if (url) $('#alert #url').val(url);//给会话中的隐藏属性URL赋值  
    $('#alert .info').text(info);
    $('#alert').modal('show');
}


/**
 * jsConfirm js弹出框
 * @karl
 * @DateTime 2016-08-12T06:35:13+0800
 * @param    {String}                 info 显示信息
 * @param    {String}                 url  跳转网址
 */
function jsConfirm(info,url)
{
    if (url) $('#confirm #url').val(url);//给会话中的隐藏属性URL赋值  
    $('#confirm .info').text(info);
    $('#confirm').modal('show'); 
}

/**
 * alertSubmit 确认后跳转
 * @karl
 * @DateTime 2016-08-12T06:12:24+0800
 */
function alertSubmit()
{
    var url=$.trim($("#alert #url").val());//获取会话中的隐藏属性URL  
    window.location.href=url;      
}

/**
 * alertSubmit 确认后跳转
 * @karl
 * @DateTime 2016-08-12T06:12:24+0800
 */
function confirmSubmit()
{
    var url=$.trim($("#confirm #url").val());//获取会话中的隐藏属性URL  
    window.location.href=url;        
}

/**
 * getCookie 得到cookie
 * @karl
 * @DateTime 2016-09-10T10:05:58+0800
 * @param    string                 c_name cookie名称
 * @return   string                 
 */
function getCookie(c_name)
{
    if (document.cookie.length>0) {
        c_start = document.cookie.indexOf(c_name + "=")
        if (c_start!=-1)
        { 
            c_start = c_start + c_name.length+1;
            c_end = document.cookie.indexOf(";",c_start);
            if (c_end==-1) c_end = document.cookie.length;
                return unescape(document.cookie.substring(c_start, c_end));
        }
    }
    return "";
}

/**
 * [setCookie 设置cookie]
 * @karl
 * @DateTime 2016-09-10T10:17:53+0800
 * @param    string                 c_name     cookie名称
 * @param    string                 value      cookie值
 * @param    int                    expiredays 到期时间
 */
function setCookie(c_name,value,expiredays)
{
    var exdate = new Date();
    exdate.setDate(exdate.getDate()+expiredays);
    document.cookie = c_name+ "=" + escape(value) + ((expiredays==null) ? "" : ";expires="+exdate.toGMTString()) + ";path=/";
}