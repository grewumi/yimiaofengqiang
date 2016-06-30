/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function huoqu(){
	$("button#huoqu").click(function(){
		var url = $("#url").val();
		//判断是券链接还是商品链接
		if(url != "")
		{
			//券链接
			if(url.indexOf("coupon")>=0)
			{
				$.ajax(
				{ 
					url:"/quan.html", 
					type:'POST',
					data:{
					    url:url, 
					},
					success: function(data){
						var dataObj = eval("(" + data + ")"); //转换为json对象
						if(dataObj.havequan === "false"){
							alert("券没有了");
							return;
						}
						$("#pic").val(dataObj.logo);
						$("#text").val(dataObj.text);
						$("#sourceurl").val(url);  
					}
				});
			}
			//下单链接
			if(url.indexOf("s.click.taobao.com")>=0)
			{
				$.ajax(
				{ 
					url:"/sclick.html", 
					type:'POST',
					data:{
					    url:url, 
					},
					success: function(data){
						var dataObj = eval("(" + data + ")"); //转换为json对象
						//alert(dataObj.data);
						if(dataObj.ok === "false"){
							alert("未知错误");
							return;
						}
						$("#pic").val(dataObj.logo);
						$("#text").val(dataObj.text);
						$("#sourceurl").val(url);  
					}
				});
			}
		}
        });
}


