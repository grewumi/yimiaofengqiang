/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function ajaxqd(){
    var username = $("#ymfq_user").val();
    $.ajax(
        { 
            url:"/ajaxqd.html", 
            data:{
                username:username, 
            },
            success: function(data){
                var dataObj=eval("("+data+")"); //ת��Ϊjson����
            }
    });
}
function qiandao(){   
    $("div.checkin-btn").click(function(){
        var username = $("#ymfq_user").val();
        $.ajax(
        { 
            url:"/qiandao.html", 
            data:{
                username:username, 
            },
            success: function(data){
                var dataObj=eval("("+data+")"); //ת��Ϊjson����
                alert(dataObj.stat);
            }
        });
    });
}

