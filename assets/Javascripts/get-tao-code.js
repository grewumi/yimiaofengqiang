/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function huoqu(){
	$("button#huoqu").click(function(){
		var url = $("#url").val();
		//�ж���ȯ���ӻ�����Ʒ����
		if(url != "")
		{
			//ȯ����
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
						var dataObj = eval("(" + data + ")"); //ת��Ϊjson����
						if(dataObj.havequan === "false"){
							alert("ȯû����");
							return;
						}
						$("#pic").val(dataObj.logo);
						$("#text").val(dataObj.text);
						$("#sourceurl").val(url);  
					}
				});
			}
			//�µ�����
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
						var dataObj = eval("(" + data + ")"); //ת��Ϊjson����
						//alert(dataObj.data);
						if(dataObj.ok === "false"){
							alert("δ֪����");
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


