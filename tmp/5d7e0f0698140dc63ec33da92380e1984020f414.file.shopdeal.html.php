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
<meta name="keywords" content="һ�����,<?php echo $_smarty_tpl->getVariable('pro')->value['ptname'];?>
,<?php echo $_smarty_tpl->getVariable('pro')->value['ptname'];?>
��վ,<?php echo $_smarty_tpl->getVariable('pro')->value['ptname'];?>
Uվ,<?php echo $_smarty_tpl->getVariable('pro')->value['ptname'];?>
����,<?php echo $_smarty_tpl->getVariable('pro')->value['ptname'];?>
������ɱ����,<?php echo $_smarty_tpl->getVariable('pro')->value['ptname'];?>
9.9����,��վ,<?php echo $_smarty_tpl->getVariable('pro')->value['nick'];?>
�콢��_<?php echo $_smarty_tpl->getVariable('pro')->value['nick'];?>
�Ա���,<?php echo $_smarty_tpl->getVariable('pro')->value['nick'];?>
��è�콢��,<?php echo $_smarty_tpl->getVariable('pro')->value['nick'];?>
רӪ��,<?php echo $_smarty_tpl->getVariable('pro')->value['nick'];?>
ר����, <?php echo $_smarty_tpl->getVariable('pro')->value['nick'];?>
�Ż�ȯ��ô��">
<meta name="Description" content="��<?php echo $_smarty_tpl->getVariable('pro')->value['ptname'];?>
uվ�Ƽ�,<?php echo $_smarty_tpl->getVariable('pro')->value['nick'];?>
�콢��_<?php echo $_smarty_tpl->getVariable('pro')->value['nick'];?>
�Ա���_<?php echo $_smarty_tpl->getVariable('pro')->value['nick'];?>
��è�콢��_<?php echo $_smarty_tpl->getVariable('pro')->value['nick'];?>
רӪ�� - һ�������ҳ_9.9�����ؼ۾�ѡ,�Ա��ػ���Ѷ����ƽ̨">
<title>һ�����_<?php echo $_smarty_tpl->getVariable('pro')->value['ptname'];?>
��վ_<?php echo $_smarty_tpl->getVariable('pro')->value['ptname'];?>
����,��ѡ<?php echo $_smarty_tpl->getVariable('pro')->value['ptname'];?>
uվ������Ʒ - <?php echo $_smarty_tpl->getVariable('pro')->value['nick'];?>
�콢��_<?php echo $_smarty_tpl->getVariable('pro')->value['nick'];?>
�Ա���_<?php echo $_smarty_tpl->getVariable('pro')->value['nick'];?>
��è�콢��_<?php echo $_smarty_tpl->getVariable('pro')->value['nick'];?>
רӪ�� - һ�������ҳ_9.9�����ؼ۾�ѡ,�Ա��ػ���Ѷ����ƽ̨</title>
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
		if( $(window).scrollTop() >200 ){  //�жϹ�����߶ȳ���200px,����ʾ  
			$("#gotop").fadeIn(400); //����     
		}else{      
			$("#gotop").stop().fadeOut(400); //������ػ���û�г���,�͵���.�������stop()ֹ֮ͣǰ����,������������   
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
			<span class="time">��ʼʱ�䣺<?php echo $_smarty_tpl->getVariable('pro')->value['postdt'];?>
</span>
			<del class="oprice">�г���:��<?php echo $_smarty_tpl->getVariable('pro')->value['oprice'];?>
Ԫ</del>
			<span class="nprice">��<?php echo $_smarty_tpl->getVariable('pro')->value['nprice'];?>
</span><br/>
                        <?php if ($_smarty_tpl->getVariable('single')->value){?>
                            <a isconvert=1 data-itemid="<?php echo $_smarty_tpl->getVariable('pro')->value['iid'];?>
" target="_blank" style="cursor:pointer">
                        <?php }else{ ?>
                            <a href="<?php echo $_smarty_tpl->getVariable('pro')->value['link'];?>
" target="_blank">
                        <?php }?>
                            <span class="buy" style="position:relative;">��������</span>
                        </a>
                        <a isconvert=1 data-sellerid="<?php echo $_smarty_tpl->getVariable('pro')->value['sid'];?>
" target="_blank" style="cursor:pointer;color:gray;text-decoration:none">ȥ���̿���</a>
			<div class="clearfix"></div>
		</div>
                <div class="hr"></div>
		<div class="items-like">
			<p>һ���������Ƽ�....</p>
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
								<del>��<?php echo $_smarty_tpl->tpl_vars['v']->value['oprice'];?>
</del>
								<span>��<?php echo $_smarty_tpl->tpl_vars['v']->value['nprice'];?>
</span>
								<a target="_blank" href="http://www.yimiaofengqiang.com/main/deal/id/<?php echo $_smarty_tpl->tpl_vars['v']->value['id'];?>
.html">��������</a>
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