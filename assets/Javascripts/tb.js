var jq = document.createElement('script');
jq.src = 'http://www.yimiaofengqiang.com/assets/Javascripts/jquery-2.0.3.min.js';
var dom = (document.getElementsByTagName('head')[0] || document.body);
dom.appendChild(jq);

setTimeout("run();", 50);
function run(){
    if (typeof(jQuery) == 'undefined'){
        setTimeout("run();", 50);  
    }else{
        var item_id,
            price,
            html;
        
        if(typeof g_config != 'undefined' &&  typeof g_config.itemId != 'undefined' || getUrlParam('item_id') || getUrlParam('id')){
            
            if(typeof g_config != 'undefined' &&  typeof g_config.itemId != 'undefined'){
                item_id = g_config.itemId;
            }else if(getUrlParam('item_id')){
                item_id = getUrlParam('item_id');
            }else if(getUrlParam('id')){
                item_id = getUrlParam('id');
            }

            $.get("http://d.qumai.org/tool/action.php?action=autologin&type=getall&iid=" + item_id,function(data,status){
                var dataObj=eval("("+data+")"); //转换为json对象
                if(dataObj.commissionRate)
                    html = "<strong style='font-size:24px;color:red;'>commissionRate:" + dataObj.commissionRate + "%</strong>";
                else
                    html = "<strong style='font-size:24px;color:red;'>no commissionRate</strong>";
                $('.tb-wrap').prepend(html);
            });

            
        }else{
            alert('is not pro detail page!');
        }
    }
}


function getUrlParam(name) { 
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null)
    return unescape(r[2]);
    return null;
} 