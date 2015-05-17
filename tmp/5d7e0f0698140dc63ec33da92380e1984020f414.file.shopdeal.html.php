<?php /* Smarty version Smarty-3.0.8, created on 2015-05-15 18:05:09
         compiled from "D:\WebSite\yimiaofengqiang/tpl\front/shopdeal.html" */ ?>
<?php /*%%SmartyHeaderCode:113555555c4d5cbada2-41553519%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5d7e0f0698140dc63ec33da92380e1984020f414' => 
    array (
      0 => 'D:\\WebSite\\yimiaofengqiang/tpl\\front/shopdeal.html',
      1 => 1431684308,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '113555555c4d5cbada2-41553519',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gbk">
<meta name="keywords" content="一秒疯抢,<?php echo $_smarty_tpl->getVariable('pro')->value['ptname'];?>
,<?php echo $_smarty_tpl->getVariable('pro')->value['ptname'];?>
优站,<?php echo $_smarty_tpl->getVariable('pro')->value['ptname'];?>
U站,<?php echo $_smarty_tpl->getVariable('pro')->value['ptname'];?>
官网,<?php echo $_smarty_tpl->getVariable('pro')->value['ptname'];?>
独家秒杀包邮,<?php echo $_smarty_tpl->getVariable('pro')->value['ptname'];?>
9.9包邮,优站,<?php echo $_smarty_tpl->getVariable('pro')->value['nick'];?>
旗舰店_<?php echo $_smarty_tpl->getVariable('pro')->value['nick'];?>
淘宝店,<?php echo $_smarty_tpl->getVariable('pro')->value['nick'];?>
天猫旗舰店,<?php echo $_smarty_tpl->getVariable('pro')->value['nick'];?>
专营店,<?php echo $_smarty_tpl->getVariable('pro')->value['nick'];?>
专卖店, <?php echo $_smarty_tpl->getVariable('pro')->value['nick'];?>
优惠券怎么领">
<meta name="Description" content="由<?php echo $_smarty_tpl->getVariable('pro')->value['ptname'];?>
u站推荐,<?php echo $_smarty_tpl->getVariable('pro')->value['nick'];?>
旗舰店_<?php echo $_smarty_tpl->getVariable('pro')->value['nick'];?>
淘宝店_<?php echo $_smarty_tpl->getVariable('pro')->value['nick'];?>
天猫旗舰店_<?php echo $_smarty_tpl->getVariable('pro')->value['nick'];?>
专营店 - 一秒疯抢首页_9.9包邮特价精选,淘宝特惠资讯导航平台">
<title>一秒疯抢_<?php echo $_smarty_tpl->getVariable('pro')->value['ptname'];?>
优站_<?php echo $_smarty_tpl->getVariable('pro')->value['ptname'];?>
官网,精选<?php echo $_smarty_tpl->getVariable('pro')->value['ptname'];?>
u站优质商品 - <?php echo $_smarty_tpl->getVariable('pro')->value['nick'];?>
旗舰店_<?php echo $_smarty_tpl->getVariable('pro')->value['nick'];?>
淘宝店_<?php echo $_smarty_tpl->getVariable('pro')->value['nick'];?>
天猫旗舰店_<?php echo $_smarty_tpl->getVariable('pro')->value['nick'];?>
专营店 - 一秒疯抢首页_9.9包邮特价精选,淘宝特惠资讯导航平台</title>
<link rel="stylesheet" href="/assets/stylesheets/front/head.css" />
<link rel="stylesheet" href="/assets/stylesheets/front/foot.css" />
<link rel="stylesheet" href="/assets/stylesheets/front/deal.css" />
<script type="text/javascript"  src="/assets/Javascripts/jquery-2.0.3.min.js"></script>
<script type="text/javascript"  src="/assets/Javascripts/jquery.lazyload.js"></script>
<script type="text/javascript">
$(document).ready(function(e) {
	$(window).scroll(function() {
		if($(window).scrollTop() >  $(".head-around").height()+80){
			$(".head-nav-around").addClass("scoll_nav")
		}else{
			$(".head-nav-around").removeClass( "scoll_nav")
		}
		if( $(window).scrollTop() >200 ){  //判断滚动后高度超过200px,就显示  
			$("#gotop").fadeIn(400); //淡出     
		}else{      
			$("#gotop").stop().fadeOut(400); //如果返回或者没有超过,就淡入.必须加上stop()停止之前动画,否则会出现闪动   
		}
	});
        $("img.lazy").lazyload({
            placeholder: "http://img03.taobaocdn.com/imgextra/i3/1602138689/T2__REXchOXXXXXXXX-1602138689.gif", 
            effect: "fadeIn", 
            failurelimit: 10
        });
})
</script>
</head>
<body>
<?php $_template = new Smarty_Internal_Template("front/head.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?>
<div class="deal-main">
	<div class="deal-left">
		<div class="item-deal">
			<div class="item-img" style="position:relative;">
				<img class="lazy" data-original="<?php echo $_smarty_tpl->getVariable('pro')->value['pic'];?>
" />
			</div>
			<div class="item-tit" style="position:relative;">
				<?php echo $_smarty_tpl->getVariable('pro')->value['title'];?>

			</div>
			<span class="time">开始时间：<?php echo $_smarty_tpl->getVariable('pro')->value['postdt'];?>
</span>
			<del class="oprice">市场价:￥<?php echo $_smarty_tpl->getVariable('pro')->value['oprice'];?>
元</del>
			<span class="nprice">￥<?php echo $_smarty_tpl->getVariable('pro')->value['nprice'];?>
</span><br/>
                        <?php if ($_smarty_tpl->getVariable('single')->value){?>
                            <a isconvert=1 data-itemid="<?php echo $_smarty_tpl->getVariable('pro')->value['iid'];?>
" target="_blank" style="cursor:pointer">
                        <?php }else{ ?>
                            <a href="<?php echo $_smarty_tpl->getVariable('pro')->value['link'];?>
" target="_blank">
                        <?php }?>
                            <span class="buy" style="position:relative;">立即购买</span>
                        </a>
                        <a isconvert=1 data-sellerid="<?php echo $_smarty_tpl->getVariable('pro')->value['sid'];?>
" target="_blank" style="cursor:pointer;color:gray;text-decoration:none">去店铺看看</a>
			<div class="clearfix"></div>
		</div>
                <div class="hr"></div>
		<div class="items-like">
			<p>一秒疯抢相关推荐....</p>
			<div class="items">
				<ul>
					<?php  $_smarty_tpl->tpl_vars['v'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('dujia')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['v']->key => $_smarty_tpl->tpl_vars['v']->value){
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['v']->key;
?>
						<li>
							<a target="_blank" href="http://www.yimiaofengqiang.com/main/deal/id/<?php echo $_smarty_tpl->tpl_vars['v']->value['id'];?>
.html">
								<img class="lazy" data-original="<?php echo $_smarty_tpl->tpl_vars['v']->value['pic'];?>
"/>
							</a>
							<div class="price">
								<del>￥<?php echo $_smarty_tpl->tpl_vars['v']->value['oprice'];?>
</del>
								<span>￥<?php echo $_smarty_tpl->tpl_vars['v']->value['nprice'];?>
</span>
								<a target="_blank" href="http://www.yimiaofengqiang.com/main/deal/id/<?php echo $_smarty_tpl->tpl_vars['v']->value['id'];?>
.html">立即购买</a>
							</div>
							
						</li>
					<?php }} ?>
					<div class="clearfix"></div>
				</ul>
			</div>
		</div>
		<div class="clearfix"></div>
	</div>
	<div class="deal-right">
		
	</div>
	<div class="clearfix"></div>
</div>
<?php $_template = new Smarty_Internal_Template("front/foot.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?>
</body>
</html>