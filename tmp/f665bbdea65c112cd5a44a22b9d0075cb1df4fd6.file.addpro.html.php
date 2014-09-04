<?php /* Smarty version Smarty-3.0.8, created on 2014-09-04 16:49:42
         compiled from "D:\WebSite\yimiaofengqiang/tpl\admin/addpro.html" */ ?>
<?php /*%%SmartyHeaderCode:27259540827a6529f01-18715780%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f665bbdea65c112cd5a44a22b9d0075cb1df4fd6' => 
    array (
      0 => 'D:\\WebSite\\yimiaofengqiang/tpl\\admin/addpro.html',
      1 => 1409819860,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '27259540827a6529f01-18715780',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gbk">
<title>后台  - 商品添加</title>
<link rel="stylesheet" href="/assets/stylesheets/admin/head.css" />
<link rel="stylesheet" href="/assets/stylesheets/admin/modpro.css" />
<script type="text/javascript"  src="/assets/Javascripts/jquery-2.0.3.min.js"></script>
<script type="text/javascript"  src="/assets/Javascripts/admin/addpro.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	huoqu();
	setrank();
	getvolume();
});
</script>
</head>
<body>
	<?php $_template = new Smarty_Internal_Template("admin/head.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?>
	<div class="admin-modpro-around">
		<div class="admin-modpro">
			
			<form method="post" action="">
				<div class="modpro-info">
					
					<div class="row">
						<label class="tips"><?php if ($_smarty_tpl->getVariable('submitTips')->value){?><?php echo $_smarty_tpl->getVariable('submitTips')->value;?>
<?php }?></label>					
						<div class="clearfix"></div>
					</div>
					
					<div class="row">
						<label>商品ID:</label><input id="iid" name="iid" type="text" onKeyUp="value=value.replace(/[^\d]/gi,'');" onpaste="value=value.replace(/[^\d]/gi,'');" />
						<input class="btn" id="huoqu" type="button" value="一键获取" />
						<div class="clearfix"></div>
					</div>
					<div class="row">
						<label>标题:</label><input class="long" name="title" id="title" type="text" />
						<a class="jiabt">加标头</a>
						<div class="clearfix"></div>
					</div>
					<div class="row">
						<label>类别:</label>
						<select name="type">
							<option value="1">全部</option>
							<option value="88">每日爆款</option>
							<option value="89">每日精选</option>
							<option value="85">广告位</option>
							<option value="86">一秒疯抢招商</option>
							<option value="87">明日预告</option>
						</select>
                                                <select name="classification">
						<?php  $_smarty_tpl->tpl_vars['v'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('classifications')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['v']->key => $_smarty_tpl->tpl_vars['v']->value){
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['v']->key;
?>						
							<option value="<?php echo $_smarty_tpl->tpl_vars['v']->value['type'];?>
" <?php if ($_smarty_tpl->getVariable('pro')->value['classification']==$_smarty_tpl->tpl_vars['v']->value['type']){?>selected="true"<?php }?>><?php echo $_smarty_tpl->tpl_vars['v']->value['name'];?>
</option>
						<?php }} ?>
						</select>
						<select name="cat">
						<?php  $_smarty_tpl->tpl_vars['v'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('proCats')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['v']->key => $_smarty_tpl->tpl_vars['v']->value){
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['v']->key;
?>
							<option value="<?php echo $_smarty_tpl->tpl_vars['v']->value['type'];?>
"  <?php if ($_smarty_tpl->getVariable('pro')->value['cat']==$_smarty_tpl->tpl_vars['v']->value['type']){?>selected="true"<?php }?>><?php echo $_smarty_tpl->tpl_vars['v']->value['name'];?>
</option>
						<?php }} ?>	
						</select>
						<div class="clearfix"></div>
					</div>
					<?php if ($_smarty_tpl->getVariable('mode')->value=='try'){?>
					<div class="row">
						<label>价值:</label><input class="short" id="oprice" name="oprice" type="text" />
						<div class="clearfix"></div>
					</div>
					<div class="row">
						<label>概率:</label><input class="short" name="gailv" type="text" />
						<div class="clearfix"></div>
					</div>
					<div class="row">
						<label>提供数量:</label><input class="short" name="volume" id="volume" type="text" />
						<div class="clearfix"></div>
					</div>
					<?php }elseif($_smarty_tpl->getVariable('mode')->value=='pro'){?>
					<div class="row">
						<label>原价:</label><input class="short" id="oprice" name="oprice" type="text" />
						<div class="clearfix"></div>
					</div>
					<div class="row">
						<label>现价:</label><input class="short" name="nprice" type="text" />
						<div class="clearfix"></div>
					</div>
					<div class="row">
						<label>销量:</label><input class="short" name="volume" id="volume" type="text" />
						<span><a class="getVolume">获取销量</a></span>
						<div class="clearfix"></div>
					</div>
					<?php }?>
					<div class="row">
						<label>商品链接:</label><input class="long" id="link" type="text" />
						<span></span>
						<div class="clearfix"></div>
					</div>
					<div class="row">
						<label>开始日期:</label><input class="short" name="st" type="text" value="<?php echo $_smarty_tpl->getVariable('st')->value;?>
" />
						<div class="clearfix"></div>
					</div>
					<div class="row">
						<label>结束日期:</label><input class="short" name="et" type="text" value="<?php echo $_smarty_tpl->getVariable('et')->value;?>
" />
						<div class="clearfix"></div>
					</div>
					<div class="row">
						<label>商品图片:</label><input class="long" id="pic" name="pic" type="text" />
						<div class="clearfix"></div>
					</div>
					<div class="row">
							<label class="gengduotupian">更多图片:</label>
							<div class="pro-pic-list">
								
							</div>
							<div class="clearfix"></div>
					</div>
					<div class="row shopshow">
						<label>淘宝/天猫:</label>
						<input type="radio" name="shopshow" value="1" checked /><label>淘宝:</label>
						<input type="radio" name="shopshow" value="0" /><label>天猫:</label>
						<div class="clearfix"></div>
					</div>
					<div class="row shopv">
						<label>是否VIP价格:</label>
						<input type="radio" name="shopv" value="1" /><label>VIP:</label>
						<input type="radio" name="shopv" value="0" checked /><label>非VIP:</label>
						<div class="clearfix"></div>
					</div>	
					<div class="row">
						<label>是否包邮:</label>
						<input type="radio" name="carriage" value="1" checked /><label>包邮:</label>
						<input type="radio" name="carriage" value="0" /><label>不包邮:</label>
						<div class="clearfix"></div>
					</div>
					<div class="row">
						<label>更新排前:</label>
						<input type="radio" name="forward" value="1" /><label>排前:</label>
						<input type="radio" name="forward" value="0" checked /><label>不排前:</label>
						<div class="clearfix"></div>
					</div>
					<div class="row">
						<label>排序:</label><input class="short" id="rank" name="rank" type="text" value="500" />
						<em class="rank"><a class="set499">499</a><a class="set500">500</a></em>
						<div class="clearfix"></div>
					</div>
					<div class="row">
						<label>旺旺:</label><input class="short" id="ww" name="ww" type="text" />
						<div class="clearfix"></div>
					</div>
					<div class="row">
						<label>佣金:</label><input class="short" id="commissionrate" name="commissionrate" type="text" />
						<div class="clearfix"></div>
					</div>
					<div class="row">
						<label>备注:</label><textarea class="long" name="remark"></textarea>
						<div class="clearfix"></div>
					</div>
					<div class="row">
						<label>提交:</label><input type="submit" name="modPro" value="提交" />
						<div class="clearfix"></div>
					</div>
				</div>
			</form>	
			
			<div class="modinfo-pic">
				
			</div>	
			
			<div class="clearfix"></div>
		</div>
		
		<div class="clearfix"></div>
	</div>
</body>
</html>