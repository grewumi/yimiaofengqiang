function huoqu(){
	$("input#huoqu").click(function(){
		var iid = $("#iid").val();
		$.get("/iteminfo.html",{
		    iid:iid
		},function(data){
			var dataObj=eval("("+data+")"); //ת��Ϊjson����
			//alert(data);
		    $("input#title").val(dataObj.title);
		    $("input#oprice").val(dataObj.oprice);
		    $("input#link").val(dataObj.link);
		    $("input#pic").val(dataObj.pic);
		    $("input#ww").val(dataObj.nick);
			$("input#commissionrate").val(dataObj.commission_rate);
		    if(dataObj.carriage){
		    	$(":radio[name='carriage'][value='1']").attr("checked","checked");
		    }else{
		    	$(":radio[name='carriage'][value='0']").attr("checked","checked");
		    }
			//alert(dataObj.shopshow);
			if(dataObj.shopshow)// �Ƿ���è
				$(":radio[name='shopshow'][value='1']").attr("checked","checked");
			else
				$(":radio[name='shopshow'][value='0']").attr("checked","checked");
				
			if(dataObj.shopv)// �Ƿ�VIP
				$(":radio[name='shopv'][value='1']").attr("checked","checked");
			else
				$(":radio[name='shopv'][value='0']").attr("checked","checked");
		});
	});
}
function userhuoqu(){
	$("input#yijianhuoqu").click(function(){
		var iid = $("#iid").val();
		$.get("/iteminfo.html",{
		    iid:iid
		},function(data){
			var dataObj=eval("("+data+")"); //ת��Ϊjson����
			//alert(data);
		    $("input#title").val(dataObj.title);
		    $("input#oprice").val(dataObj.oprice);
		    $("input#link").val(dataObj.link);
		    $("input#pic").val(dataObj.pic);
		    $("input#ww").val(dataObj.nick);
		    if(dataObj.carriage){
		    	//alert('baoyou');
		    	$("input#postage").checked = true;
		    }else{
		    	//alert('bubaoyou');
		    	$("input#nopostage").checked = true;
		    }
			/* Ӷ���� */
			if(dataObj.commission_rate>0){
				if(dataObj.commission_rate < 10)
					$("span.reporttips").append("<em class='tips'>Ӷ��С��10%</em>");
				else
					var yjisok = 1;
			}else{
				$("span.reporttips").append("<em class='tips'>Ӷ��δ����</em>");
			}
			/* END - Ӷ���� */
			if(yjisok)
				$("input#userReport").attr("disabled",false);
		});
	});
}