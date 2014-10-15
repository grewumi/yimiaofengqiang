var s = document.createElement('script');
s.src = 'http://tao.as/tools/jquery-1.9.1.min.js';
var h = document.getElementsByTagName('head')[0];
h.insertBefore(s, h.firstChild);

setTimeout("run();", 50);
function run(){
    if (typeof(jQuery) == 'undefined'){
        setTimeout("run();", 50);  
    }else{
       if(typeof g_config != 'undefined' &&  typeof g_config.itemId != 'undefined' || getUrlParam('item_id') || getUrlParam('id')){
            
            if(typeof g_config != 'undefined' &&  typeof g_config.itemId != 'undefined'){
                item_id = g_config.itemId;
            }else if(getUrlParam('item_id')){
                item_id = getUrlParam('item_id');
            }else if(getUrlParam('id')){
                item_id = getUrlParam('id');
            }
            
            if(document.getElementById('ymfqrate') == null){
                html = "<iframe id='ymfqrate' width='430' height='50'  frameborder='0' scrolling='no' src='http://www.yimiaofengqiang.com/include/getcommission.php?item_id='" + item_id + "></iframe>";
                $('.tb-wrap').prepend(html);
            }else{
                $("#ymfqrate").attr('src',"http://www.yimiaofengqiang.com/include/getcommission.php?item_id=" + item_id);
            }
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