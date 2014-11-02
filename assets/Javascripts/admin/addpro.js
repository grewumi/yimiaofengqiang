function huoquadd(){
	$("input#huoqu").click(function(){
		var iid = $("#iid").val();
		$.get("/iteminfo.html",{
		    iid:iid
		},function(data){
                    var dataObj=eval("("+data+")"); //转换为json对象
			//alert(data);
                    if(parseInt(dataObj.iid)<0){
                         alert('商品未开始或已下架！');
                         return;
                    }
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
                    if(dataObj.shopshow)// 是否天猫
                            $(":radio[name='shopshow'][value='1']").attr("checked","checked");
                    else
                            $(":radio[name='shopshow'][value='0']").attr("checked","checked");

                    if(dataObj.shopv)// 是否VIP
                            $(":radio[name='shopv'][value='1']").attr("checked","checked");
                    else
                            $(":radio[name='shopv'][value='0']").attr("checked","checked");
                    $("span#kyx").empty();    
                    $("span#kyx").append("<a target='_blank' href='http://item.taobao.com/item.htm?id=" + iid + "'>看一下</a>");    
		});
        });
}
function huoqu(){
	$("input#huoqu").click(function(){
		var iid = $("#iid").val();
		$.get("/iteminfo.html",{
		    iid:iid
		},function(data){
                    var dataObj=eval("("+data+")"); //转换为json对象
			//alert(data);
                    if(parseInt(dataObj.iid)<0){
                         alert('商品未开始或已下架！');
                         return;
                    }
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
                    if(dataObj.shopshow)// 是否天猫
                            $(":radio[name='shopshow'][value='1']").attr("checked","checked");
                    else
                            $(":radio[name='shopshow'][value='0']").attr("checked","checked");

                    if(dataObj.shopv)// 是否VIP
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
			var dataObj=eval("("+data+")"); //转换为json对象
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
			var dataObj=eval("("+data+")"); //转换为json对象
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
			
			/* 销量检测 */
//			if(dataObj.volume < 10)
//				$("span.reporttips").append("<em class='tips'>销量小于10,</em>");
//			else
//				var xlisok = 1;

			/* END - 销量检测 */
			
			/* 信誉检测 */
//			var rateisok = -1;
//			if(dataObj.shopshow){//C店
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
//							$("span.reporttips").append("<em class='tips'>店铺信誉没有一砖,</em>");
//
//					}
//				}); 
//			}else{
//				rateisok = 1;
//			}
			

			/* END - 信誉检测 */
			
			/* 佣金检测 */
//			if(dataObj.commission_rate>0){
//				if(dataObj.commission_rate < 10)
//					$("span.reporttips").append("<em class='tips'>佣金小于10%,</em>");
//				else
//					var yjisok = 1;
//			}else{
//				$("span.reporttips").append("<em class='tips'>佣金未设置,</em>");
//			}
			/* END - 佣金检测 */
			
			/* 左侧LOGO检测 */
//			if(dataObj.shopshow){//C店
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
//						$("span.reporttips").append("<em class='tips'>左侧LOGO未设置,</em>");
//					
//				}
//			}); 
			/* END - 左侧LOGO检测 */
			
			/* 详情页LOGO检测 */
//			if(dataObj.shopshow){//C店
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
//						$("span.reporttips").append("<em class='tips'>详情页LOGO未设置,</em>");
//					
//				}
//			});
			/* END - 详情页LOGO检测 */
//			if(leftok>0 && decok>0)
//				$("input#userReport").attr("disabled",false);
		});
	});
}