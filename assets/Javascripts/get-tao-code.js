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
		}
		
//		$.get("/iteminfo.html",{
//		    iid:iid
//		},function(data){
//                    var dataObj=eval("("+data+")"); //ת��Ϊjson����
//			//alert(data);
//                    if(parseInt(dataObj.iid)<0){
//                         alert('��Ʒδ��ʼ�����¼ܻ���û�п�ͨ�Կͣ�');
//                         return;
//                    }
//		    $("input#title").val(dataObj.title);
//		    $("input#oprice").val(dataObj.oprice);
//                    $("input#nprice").val(dataObj.nprice);
//		    $("input#link").val(dataObj.link);
//		    $("input#pic").val(dataObj.pic);
//		    $("input#ww").val(dataObj.nick);
//                    $("input#shopname").val(dataObj.shopname);
//                    $("input#commissionrate").val(dataObj.commission_rate);
//                    $("input#volume").val(dataObj.volume);
//                    $("input#slink").val(dataObj.slink);
//                    $("#cat option[value='" + dataObj.cat + "']").attr("selected", true); 
//		    if(dataObj.carriage){
//		    	$(":radio[name='carriage'][value='1']").attr("checked","checked");
//		    }else{
//		    	$(":radio[name='carriage'][value='0']").attr("checked","checked");
//		    }
//                    //alert(dataObj.shopshow);
//                    if(dataObj.shopshow)// �Ƿ���è
//                            $(":radio[name='shopshow'][value='1']").attr("checked","checked");
//                    else
//                            $(":radio[name='shopshow'][value='0']").attr("checked","checked");
//
//                    if(dataObj.shopv)// �Ƿ�VIP
//                            $(":radio[name='shopv'][value='1']").attr("checked","checked");
//                    else
//                            $(":radio[name='shopv'][value='0']").attr("checked","checked");
//                        
//                    var imgs = dataObj.item_imgs.split(",");
//                    var imghtml = "";
//                    for(var i = 0; i < imgs.length; i++){
//                        imghtml += "<img onclick='changepic(this)' class='sect' src='" + imgs[i] + "' />";
//                    }
//                    $(".pro-pic-list").empty();
//                    $(".pro-pic-list").append(imghtml);
//		});
        });
}


