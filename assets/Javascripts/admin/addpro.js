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
                    $("input#volume").val(dataObj.volume);
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

function setrank(){
	 $(".set500").click(function(){
		 $("#rank").val(500);
	 });
	  $(".set499").click(function(){
		 $("#rank").val(499);
	 });
}

function getvolume(){
	 $(".getVolume").click(function(){
		var iid = $("#iid").val();		
		$.get("/iteminfo.html",{
		    iid:iid
		},function(data){
			var dataObj=eval("("+data+")"); //ת��Ϊjson����
			$url = "/?c=virtualapi&a=getvolume&iid=" + dataObj.iid + "&shop=" + dataObj.shopshow;
			$.ajax({
			   url:$url,
			   async:false,
			   success:function(datavolumeObj){
				   var datavolObj=eval("("+datavolumeObj+")");
					$("#volume").val(datavolObj.show);
			   }
		   });
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
			$("input#volume").val(dataObj.volume);
			$("input#commissionrate").val(dataObj.commission_rate);
		    if(dataObj.carriage){
		    	//alert('baoyou');
		    	$("input#postage").checked = true;
		    }else{
		    	//alert('bubaoyou');
		    	$("input#nopostage").checked = true;
		    }
			
			/* ������� */
//			if(dataObj.volume < 10)
//				$("span.reporttips").append("<em class='tips'>����С��10,</em>");
//			else
//				var xlisok = 1;

			/* END - ������� */
			
			/* ������� */
//			var rateisok = -1;
//			if(dataObj.shopshow){//C��
//				$url = "/?c=virtualapi&a=checkrate&iid=" + dataObj.iid;
//				
//				$.ajax({
//					url:$url,
//					async:false,
//					success:function(dataleft){
//						var dataleftObj=eval("("+dataleft+")");
//						if(dataleftObj.show>0)
//							rateisok = 1;
//						else
//							$("span.reporttips").append("<em class='tips'>��������û��һש,</em>");
//
//					}
//				}); 
//			}else{
//				rateisok = 1;
//			}
			

			/* END - ������� */
			
			/* Ӷ���� */
//			if(dataObj.commission_rate>0){
//				if(dataObj.commission_rate < 10)
//					$("span.reporttips").append("<em class='tips'>Ӷ��С��10%,</em>");
//				else
//					var yjisok = 1;
//			}else{
//				$("span.reporttips").append("<em class='tips'>Ӷ��δ����,</em>");
//			}
			/* END - Ӷ���� */
			
			/* ���LOGO��� */
//			if(dataObj.shopshow){//C��
//				$url = "/?c=virtualapi&a=checklogo&where=left&shop=c&iid=" + dataObj.iid;
//			}else{
//				$url = "/?c=virtualapi&a=checklogo&where=left&shop=t&iid=" + dataObj.iid;
//			}
//			var leftok = -1;
//			$.ajax({
//				url:$url,
//				async:false,
//				success:function(dataleft){
//					var dataleftObj=eval("("+dataleft+")");
//					if(dataleftObj.show>0)
//						leftok = 1;
//					else
//						$("span.reporttips").append("<em class='tips'>���LOGOδ����,</em>");
//					
//				}
//			}); 
			/* END - ���LOGO��� */
			
			/* ����ҳLOGO��� */
//			if(dataObj.shopshow){//C��
//				$url = "/?c=virtualapi&a=checklogo&where=dec&shop=c&iid=" + dataObj.iid;
//			}else{
//				$url = "/?c=virtualapi&a=checklogo&where=dec&shop=t&iid=" + dataObj.iid;
//			}
//			var decok = -1;
//			$.ajax({
//				url:$url,
//				async:false,
//				success:function(datadec){
//					var datadecObj=eval("("+datadec+")");
//					if(datadecObj.show>0)
//						decok = 1;
//					else
//						$("span.reporttips").append("<em class='tips'>����ҳLOGOδ����,</em>");
//					
//				}
//			});
			/* END - ����ҳLOGO��� */
//			if(leftok>0 && decok>0)
//				$("input#userReport").attr("disabled",false);
		});
	});
}